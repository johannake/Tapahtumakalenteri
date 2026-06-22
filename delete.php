<!-- Tapahtuman poistaminen -->
<?php
require 'db.php';

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
}

header('Location: index.php');
exit;