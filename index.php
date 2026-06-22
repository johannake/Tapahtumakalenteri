<!-- Näytttää tapahtumat listana ja pienoiskalenterina -->
<?php
require 'db.php';

// Valittu kuukausi ja vuosi
$year  = $_GET['year']  ?? date('Y');
$month = $_GET['month'] ?? date('m');

$currentDate = new DateTime("$year-$month-01");

// Kuukauden tapahtumat
$stmt = $pdo->prepare(
    "SELECT * FROM events
     WHERE YEAR(event_date) = :year AND MONTH(event_date) = :month
     ORDER BY event_date, event_time"
);
$stmt->execute([':year' => $year, ':month' => $month]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lista päivistä, joissa on tapahtuma)
$eventDays = [];
foreach ($events as $e) {
    $dayNum = (int)date('j', strtotime($e['event_date']));
    $eventDays[$dayNum] = true;
}

// Suomenkieliset kuukausien nimet 
$kuukaudet = [
    1 => 'Tammikuu', 2 => 'Helmikuu', 3 => 'Maaliskuu', 4 => 'Huhtikuu',
    5 => 'Toukokuu', 6 => 'Kesäkuu', 7 => 'Heinäkuu', 8 => 'Elokuu',
    9 => 'Syyskuu', 10 => 'Lokakuu', 11 => 'Marraskuu', 12 => 'Joulukuu'
];

$kuukauden_nimi = $kuukaudet[(int)$month];

// Selauslinkkien laskenta
$prevDate = (clone $currentDate)->modify('-1 month');
$prevYear  = $prevDate->format('Y');
$prevMonth = $prevDate->format('m');

$nextDate = (clone $currentDate)->modify('+1 month');
$nextYear  = $nextDate->format('Y');
$nextMonth = $nextDate->format('m');

//Pienoiskalenterin laskenta
$daysInMonth = (int)$currentDate->format('t'); // Kuukaudessa olevien päivien määrä
$firstDayOfWeek = (int)$currentDate->format('N'); // 1 (maanantai) - 7 (sunnuntai)

?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalenteri</title>
    <link rel="stylesheet" href="style.css">
    <link rel = "icon" type = "image/png" href = "SmallLogo.png">
</head>
<body>
<div class="main-container">

    <h1><?= $kuukauden_nimi . ' ' . $year ?></h1>
      
    <!-- Kuukauden vaihto -->
    <div class="nav-links">
        <a href="?year=<?= $prevYear ?>&month=<?= $prevMonth ?>">← Edellinen</a>
        <a href="?year=<?= $nextYear ?>&month=<?= $nextMonth ?>">Seuraava →</a>
    </div>
    
    <a href="add.php" class="btn-add">+ Lisää tapahtuma</a>

    <!-- PIENOISKALENTERI -->
    <div class="calendar-layout">
        
        <div class="layout-left">
            <table class="mini-calendar">
                <thead>
                    <tr>
                        <th>Ma</th><th>Ti</th><th>Ke</th><th>To</th><th>Pe</th><th>La</th><th>Su</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php
                    for ($i = 1; $i < $firstDayOfWeek; $i++) {
                        echo '<td></td>';
                    }

                    $currentDayOfWeek = $firstDayOfWeek;
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        
                        $hasEventClass = isset($eventDays[$day]) ? 'has-event' : '';
                        $isTodayClass = ($year == date('Y') && $month == date('m') && $day == date('j')) ? 'today' : '';
                        
                        echo "<td class='$hasEventClass $isTodayClass'>$day</td>";

                        if ($currentDayOfWeek == 7) {
                            echo '</tr>';
                            if ($day < $daysInMonth) {
                                echo '<tr>';
                            }
                            $currentDayOfWeek = 1;
                        } else {
                            $currentDayOfWeek++;
                        }
                    }

                    while ($currentDayOfWeek <= 7 && $currentDayOfWeek != 1) {
                        echo '<td></td>';
                        $currentDayOfWeek++;
                    }
                    ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- OIKEA REUNA: TAPAHTUMALISTA -->
        <div class="layout-right">
            <?php if ($events): ?>
                <h2>Tapahtumat:</h2>
                <?php foreach ($events as $e): ?>
                    <div class="event">
                        <strong><?= htmlspecialchars($e['title']) ?></strong>
                        <span><?= (new DateTime($e['event_date']))->format('j.n.Y') ?> klo <?= (new DateTime($e['event_time']))->format('H:i') ?></span>
                        <p><?= htmlspecialchars($e['description']) ?></p>
                        <a href="delete.php?id=<?= $e['id'] ?>" class="btn-delete">Poista</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-events">Ei tapahtumia tässä kuussa.</p>
            <?php endif; ?>
        </div>

    </div> 

</div>
</body>
</html>
