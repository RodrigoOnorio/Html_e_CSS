<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_FILES['file'])) {
        echo json_encode(['success' => false, 'message' => 'Arquivo não fornecido']);
        exit();
    }

    $file = $_FILES['file'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'video/mp4', 'video/avi', 'audio/mpeg', 'audio/wav', 'application/pdf', 'text/plain', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $maxSize = 20 * 1024 * 1024; // 20MB

    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
        exit();
    }

    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'Arquivo muito grande (máximo 20MB)']);
        exit();
    }

    try {
        // Criar diretório de uploads se não existir
        $uploadDir = __DIR__ . '/../../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Gerar nome único para o arquivo
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;

        // Mover arquivo para o diretório de uploads
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // Retornar URL do arquivo
            $fileUrl = '/tentativa2/uploads/' . $fileName;
            
            // Determinar tipo do arquivo
            $fileType = 'file';
            if (strpos($file['type'], 'image/') === 0) {
                $fileType = 'image';
            } elseif (strpos($file['type'], 'video/') === 0) {
                $fileType = 'video';
            } elseif (strpos($file['type'], 'audio/') === 0) {
                $fileType = 'audio';
            }

            echo json_encode([
                'success' => true,
                'fileUrl' => $fileUrl,
                'fileName' => $file['name'],
                'fileSize' => $file['size'],
                'mimeType' => $file['type'],
                'fileType' => $fileType
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao mover arquivo']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
}
?>
