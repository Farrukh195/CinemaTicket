<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: (int)($_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id=?");
$stmt->execute([$id]);
$movie = $stmt->fetch();
if (!$movie) { http_response_code(404); exit('Филм ёфт нашуд.'); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $title = trim($_POST['title'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $rating = trim($_POST['rating'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $poster = $movie['poster'];
    $oldPoster = $movie['poster'];

    if ($title === '' || $genre === '' || $duration < 1) {
        $error = 'Лутфан, майдонҳои ҳатмиро дуруст пур кунед.';
    } else {
        try {
            $url = trim($_POST['poster_url'] ?? '');
            if (isset($_FILES['poster_file']) && $_FILES['poster_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $poster = upload_poster($_FILES['poster_file']);          // yangi rasm (kompyuterdan)
            } elseif ($url !== '') {
                if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url)) {
                    throw new RuntimeException('Истиноди расм нодуруст аст (бояд бо http:// ё https:// сар шавад).');
                }
                $poster = $url;                                          // yangi rasm (havola)
            } elseif (!empty($_POST['remove_poster'])) {
                $poster = null;                                          // rasmni olib tashlash
            }

            $stmt = $pdo->prepare("UPDATE movies SET title=?, genre=?, duration=?, rating=?, poster=?, description=?, is_active=? WHERE id=?");
            $stmt->execute([$title, $genre, $duration, $rating, $poster, $description, $isActive, $id]);

            if ($poster !== $oldPoster) delete_local_poster($oldPoster);  // eski rasm faylini tozalash

            set_flash('Филм навсозӣ шуд.');
            header('Location: movies.php');
            exit;
        } catch (RuntimeException $ex) {
            $error = $ex->getMessage();
        }
    }
    // xato bo'lsa, forma kiritilgan qiymatlar bilan qayta chiqadi
    $movie = array_merge($movie, [
        'title' => $title, 'genre' => $genre, 'duration' => $duration,
        'rating' => $rating, 'description' => $description, 'is_active' => $isActive,
    ]);
}

$pageTitle = 'Таҳрири филм';
$assetPrefix = '../';
$homePrefix = '../';
require '../includes/header.php';

// Rasm yo'lini admin/ papkasidan ko'rinadigan qilish
$posterSrc = '';
if (!empty($movie['poster'])) {
    $posterSrc = preg_match('#^https?://#i', $movie['poster']) ? $movie['poster'] : '../' . $movie['poster'];
}
?>
<div class="auth-card wide">
    <h1>Таҳрири филм</h1>

    <?php if ($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">

        <label>Расми ҳозира
            <?php if ($posterSrc): ?>
                <img src="<?= e($posterSrc) ?>" alt="" style="width:160px;height:230px;object-fit:cover;border-radius:12px;">
            <?php else: ?>
                <span class="muted">Расм нест</span>
            <?php endif; ?>
        </label>

        <label>Расми навро аз компютер интихоб кунед
            <input type="file" name="poster_file" accept="image/jpeg,image/png,image/webp,image/gif">
            <small>JPG, PNG, WEBP ё GIF. Ҳаҷми ҳадди аксар: 5 МБ. Агар интихоб кунед, расми кӯҳна иваз мешавад.</small>
        </label>

        <label>Ё истиноди расм (ихтиёрӣ)
            <input name="poster_url" type="url" placeholder="https://...">
        </label>

        <?php if ($posterSrc): ?>
        <label style="display:flex;gap:8px;align-items:center;">
            <input type="checkbox" name="remove_poster" value="1" style="width:auto;"> Хориҷ кардани расм
        </label>
        <?php endif; ?>

        <label>Номи филм
            <input name="title" value="<?= e($movie['title']) ?>" required>
        </label>

        <label>Жанр
            <input name="genre" value="<?= e($movie['genre']) ?>" required>
        </label>

        <label>Давомнокӣ (дақиқа)
            <input type="number" name="duration" min="1" value="<?= (int)$movie['duration'] ?>" required>
        </label>

        <label>Рейтинг
            <input name="rating" value="<?= e($movie['rating']) ?>" placeholder="8.5">
        </label>

        <label>Дар бораи филм
            <textarea name="description" rows="5"><?= e($movie['description']) ?></textarea>
        </label>

        <label style="display:flex;gap:8px;align-items:center;">
            <input type="checkbox" name="is_active" value="1" style="width:auto;" <?= $movie['is_active'] ? 'checked' : '' ?>> Дар сайт намоён бошад (фаъол)
        </label>

        <button class="button" type="submit">Нигоҳ доштан</button>
        <a class="button" href="movies.php" style="background:#573c72;">Бекор кардан</a>
    </form>
</div>
<?php require '../includes/footer.php'; ?>
