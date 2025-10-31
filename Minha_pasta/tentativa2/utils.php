<?php
// Utilitários: sessão, CSRF, sanitização, rate limiting simples
session_start();

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_validate(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function sanitize_text(string $text): string {
    // Remove nulos, normaliza espaços
    $text = preg_replace('/\x00/', '', $text);
    $text = trim($text);
    return $text;
}

function esc_html(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function rate_limit(string $key, int $max, int $windowSeconds): bool {
    $now = time();
    $bucket = $_SESSION['rate'][$key] ?? ['count' => 0, 'reset' => $now + $windowSeconds];
    if ($now > $bucket['reset']) {
        $bucket = ['count' => 0, 'reset' => $now + $windowSeconds];
    }
    $bucket['count']++;
    $_SESSION['rate'][$key] = $bucket;
    return $bucket['count'] <= $max;
}

?>  