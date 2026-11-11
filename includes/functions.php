<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['user']['role'] ?? '') === 'admin';
}
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}
function require_admin(): void {
    if (!is_admin()) {
        header('Location: ../login.php');
        exit;
    }
}
function set_flash(string $message, string $type='success'): void {
    $_SESSION['flash'] = ['message'=>$message, 'type'=>$type];
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf'] ?? '';
        if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
            http_response_code(403);
            exit('Дархост тасдиқ нашуд. Саҳифаро нав кунед.');
        }
    }
}

// ===== Film rasmini yuklash (admin/add_movie va admin/edit_movie uchun) =====
function upload_poster(array $file): string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Расм бор нашуд. Бори дигар кӯшиш кунед.');
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Ҳаҷми расм набояд аз 5 МБ зиёд бошад.');
    }
    $info = @getimagesize($file['tmp_name']);
    $allowed = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if ($info === false || !isset($allowed[$info[2]])) {
        throw new RuntimeException('Танҳо расми воқеӣ бо формати JPG, PNG, WEBP ё GIF интихоб кунед.');
    }
    $dir = dirname(__DIR__) . '/assets/images/';
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        throw new RuntimeException('Папкаи расмҳоро сохтан мумкин нашуд.');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        throw new RuntimeException('Расмро нигоҳ доштан мумкин нашуд. Иҷозатҳои папкаи XAMPP-ро санҷед.');
    }
    return 'assets/images/' . $name;
}

// Faqat assets/images/ ichidagi eski rasmni o'chiradi (tashqi havolalarga tegmaydi)
function delete_local_poster(?string $path): void {
    if (!$path || strpos($path, 'assets/images/') !== 0) return;
    $full = dirname(__DIR__) . '/assets/images/' . basename($path);
    if (is_file($full)) @unlink($full);
}
