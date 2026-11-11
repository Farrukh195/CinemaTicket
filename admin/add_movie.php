<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_admin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $rating = trim($_POST['rating'] ?? '');
    $poster = trim($_POST['poster_url'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '' || $genre === '' || $duration < 1) {
        $error = 'Лутфан, майдонҳои ҳатмиро дуруст пур кунед.';
    } else {
        // Kompyuterdan rasm tanlangan bo‘lsa, avval uning turini va hajmini tekshiramiz.
        if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['poster_file'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'Расм бор нашуд. Бори дигар кӯшиш кунед.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Ҳаҷми расм набояд аз 5 МБ зиёд бошад.';
            } else {
                $imageInfo = @getimagesize($file['tmp_name']);
                $allowed = [
                    IMAGETYPE_JPEG => 'jpg',
                    IMAGETYPE_PNG  => 'png',
                    IMAGETYPE_WEBP => 'webp',
                    IMAGETYPE_GIF  => 'gif'
                ];

                if ($imageInfo === false || !isset($allowed[$imageInfo[2]])) {
                    $error = 'Танҳо расми воқеӣ бо формати JPG, PNG, WEBP ё GIF интихоб кунед.';
                } else {
                    $uploadDir = dirname(__DIR__) . '/assets/images/';

                    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
                        $error = 'Папкаи расмҳоро сохтан мумкин нашуд.';
                    } else {
                        $fileName = bin2hex(random_bytes(16)) . '.' . $allowed[$imageInfo[2]];
                        $destination = $uploadDir . $fileName;

                        if (move_uploaded_file($file['tmp_name'], $destination)) {
                            $poster = 'assets/images/' . $fileName;
                        } else {
                            $error = 'Расмро нигоҳ доштан мумкин нашуд. Иҷозатҳои папкаи XAMPP-ро санҷед.';
                        }
                    }
                }
            }
        }

        if ($error === '') {
            $stmt = $pdo->prepare(
                "INSERT INTO movies(title, genre, duration, rating, poster, description)
                 VALUES(?,?,?,?,?,?)"
            );
            $stmt->execute([$title, $genre, $duration, $rating, $poster, $description]);
            set_flash('Филм бомуваффақият илова шуд.');
            header('Location: movies.php');
            exit;
        }
    }
}

$pageTitle = 'Иловаи филми нав';
$assetPrefix = '../';
$homePrefix = '../';
require '../includes/header.php';
?>
<div class="auth-card wide">
    <h1>Иловаи филми нав</h1>

    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

        <label>Номи филм
            <input name="title" required>
        </label>

        <label>Жанр
            <input name="genre" required placeholder="Драма, Комедия...">
        </label>

        <label>Давомнокӣ (дақиқа)
            <input type="number" name="duration" min="1" required>
        </label>

        <label>Рейтинг
            <input name="rating" placeholder="8.5">
        </label>

        <label>Расми филмро аз компютер интихоб кунед
            <input type="file" name="poster_file" accept="image/jpeg,image/png,image/webp,image/gif">
            <small>JPG, PNG, WEBP ё GIF. Ҳаҷми ҳадди аксар: 5 МБ.</small>
        </label>

        <label>Ё истиноди расмро ворид кунед (ихтиёрӣ)
            <input name="poster_url" type="url" placeholder="https://...">
        </label>

        <label>Дар бораи филм
            <textarea name="description" rows="5"></textarea>
        </label>

        <button class="button" type="submit">Нигоҳ доштани филм</button>
    </form>
</div>
<?php require '../includes/footer.php'; ?>
