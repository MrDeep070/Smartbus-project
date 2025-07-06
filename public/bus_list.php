<?php
include '../includes/db.php';
include '../includes/header.php';

// Fetch buses based on search
$stmt = $pdo->prepare("SELECT * FROM buses 
                      WHERE source = ? AND destination = ? AND DATE(departure_time) = ?");
$stmt->execute([$_GET['from'], $_GET['to'], $_GET['date']]);
$buses = $stmt->fetchAll();
?>

<!-- Display buses in cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
    <?php foreach ($buses as $bus): ?>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-bold"><?= $bus['bus_no'] ?> (<?= $bus['type'] ?>)</h3>
        <p>Departure: <?= date('h:i A', strtotime($bus['departure_time'])) ?></p>
        <p class="font-bold text-green-600 mt-2">₹<?= $bus['fare'] ?> per seat</p>
        <a href="booking.php?bus_id=<?= $bus['id'] ?>" 
           class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Book Now
        </a>
    </div>
    <?php endforeach; ?>
</div>