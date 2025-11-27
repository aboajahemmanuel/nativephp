<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    redirect('index.php');
}

$stmt = $pdo->prepare('SELECT * FROM notes WHERE id = :id');
$stmt->execute([':id' => $id]);
$note = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$note) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $errors = [];
    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if (empty($errors)) {
        $u = $pdo->prepare('UPDATE notes SET title = :title, content = :content WHERE id = :id');
        $u->execute([':title' => $title, ':content' => $content, ':id' => $id]);
        redirect('index.php');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Note</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Note</h1>
        <p><a href="index.php">← Back to list</a></p>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Title<br>
                <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? $note['title']) ?>" required>
            </label>
            <label>Content<br>
                <textarea name="content" rows="6"><?= htmlspecialchars($_POST['content'] ?? $note['content']) ?></textarea>
            </label>
            <button class="btn" type="submit">Save</button>
        </form>
    </div>
</body>
</html>
