<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['ok' => false, 'error' => 'Não autorizado']);
    exit;
}

require_once 'conexao.php';
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message  = trim($_POST['message'] ?? '');
$filePath = null;

if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $dir = __DIR__ . '/uploads/';
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    $name = uniqid('file_', true) . '.' . $ext;
    move_uploaded_file($_FILES['file']['tmp_name'], $dir . $name);
    $filePath = 'uploads/' . $name;
}

if ($message === '' && !$filePath) {
    echo json_encode(['ok' => false, 'error' => 'Mensagem vazia']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO messages (user_id, username, message, file_path) VALUES (:uid, :un, :msg, :fp)");
$stmt->execute([':uid' => $user_id, ':un' => $username, ':msg' => $message, ':fp' => $filePath]);

$id = $pdo->lastInsertId();
$stmt = $pdo->prepare("SELECT * FROM messages WHERE id = :id");
$stmt->execute([':id' => $id]);
$msg = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['ok' => true, 'message' => $msg]);
