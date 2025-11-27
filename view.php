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
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>View Note</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($note['title']) ?></h1>
        <p><a href="index.php">← Back to list</a></p>
        <div class="meta">Created: <?= htmlspecialchars($note['created_at']) ?></div>
        <div class="content">
            <?= nl2br(htmlspecialchars($note['content'])) ?>
        </div>
        <p>
            <a class="btn" href="edit.php?id=<?= $note['id'] ?>">Edit</a>
            <a class="btn danger" href="delete.php?id=<?= $note['id'] ?>">Delete</a>
        </p>
    </div>
</body>
</html>
