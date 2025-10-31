<?php
require __DIR__ . '/utils.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// CSRF opcional: permite upload apenas se houver sessão ativa
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Nenhum arquivo enviado']);
    exit;
}

$file = $_FILES['file'];
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'Falha no upload', 'code' => $file['error']]);
    exit;
}

$maxSize = 20 * 1024 * 1024; // 20 MB
if ($file['size'] > $maxSize) {
    http_response_code(413);
    echo json_encode(['error' => 'Arquivo muito grande (máx 20MB)']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';

// Mapear tipo
function tipo_from_mime(string $mime): string {
    if (strpos($mime, 'image/') === 0) return 'image';
    if (strpos($mime, 'video/') === 0) return 'video';
    if (strpos($mime, 'audio/') === 0) return 'audio';
    return 'file';
}

$tipo = tipo_from_mime($mime);

$baseDir = __DIR__ . '/uploads';
if (!is_dir($baseDir)) {
    @mkdir($baseDir, 0777, true);
}

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$safeExt = preg_replace('/[^a-zA-Z0-9]/', '', $ext);
$name = bin2hex(random_bytes(8));
$filename = $name . ($safeExt ? ('.' . $safeExt) : '');
$dest = $baseDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha ao salvar arquivo']);
    exit;
}

$url = 'uploads/' . $filename;
echo json_encode([
    'success' => true,
    'url' => $url,
    'mime' => $mime,
    'tipo' => $tipo,
    'nome' => $file['name'],
    'tamanho' => (int)$file['size'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>