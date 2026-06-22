<!-- Lisää tapahtuma -->
<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare(
        "INSERT INTO events (title, description, event_date, event_time)
         VALUES (:title, :desc, :date, :time)"
    );
    $stmt->execute([
        ':title' => $_POST['title'],
        ':desc'  => $_POST['description'],
        ':date'  => $_POST['event_date'],
        ':time'  => $_POST['event_time'],
    ]);
    header('Location: index.php');
    exit;
}
?>

<form method="POST" style="display: flex; flex-direction: column; gap: 15px; max-width: 300px;">
    <label for="title">Nimi:</label>
    <input type="text"  name="title"  placeholder="Tapahtuman nimi" required>
    <label for="event_date">Päivämäärä:</label>
    <input type="date"  name="event_date"  required style="width: 150px;">
    <label for="event_time">Kellonaika:</label>
    <input type="time"  name="event_time" style="width: 100px;">
    <label for="description">Kuvaus:</label>
    <textarea name="description" placeholder="Kuvaus"></textarea>
    <button type="submit">Lisää tapahtuma</button>
</form>