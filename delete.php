<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    redirect('index.php');
}

// POST performs delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('DELETE FROM notes WHERE id = :id');
    $stmt->execute([':id' => $id]);
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
    <title>Delete Note</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Delete Note</h1>
        <p>Are you sure you want to delete "<?= htmlspecialchars($note['title']) ?>"?</p>
        <form method="post">
            <button class="btn danger" type="submit">Yes, delete</button>
            <a class="btn" href="index.php">Cancel</a>
        </form>
    </div>
</body>
</html>
