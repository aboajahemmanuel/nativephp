<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $errors = [];
    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO notes (title, content) VALUES (:title, :content)');
        $stmt->execute([':title' => $title, ':content' => $content]);
        redirect('index.php');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create Note</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Create Note</h1>
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
                <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </label>
            <label>Content<br>
                <textarea name="content" rows="6"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
            </label>
            <button class="btn" type="submit">Create</button>
        </form>
    </div>
</body>
</html>
