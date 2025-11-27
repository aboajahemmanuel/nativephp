<?php
require_once __DIR__ . '/db.php';
$pdo = getPDO();

$stmt = $pdo->query('SELECT * FROM notes ORDER BY created_at DESC');
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Simple PHP CRUD</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Notes</h1>
        <p><a class="btn" href="create.php">Create new note</a></p>

        <?php if (count($notes) === 0): ?>
            <p>No notes yet. Create one.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Title</th><th>Created</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($notes as $note): ?>
                    <tr>
                        <td><?= htmlspecialchars($note['title']) ?></td>
                        <td><?= htmlspecialchars($note['created_at']) ?></td>
                        <td>
                            <a href="view.php?id=<?= $note['id'] ?>">View</a>
                            <a href="edit.php?id=<?= $note['id'] ?>">Edit</a>
                            <a href="delete.php?id=<?= $note['id'] ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
