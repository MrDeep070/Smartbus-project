<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /public/bus_list.php');
    exit;
}

// Validate input
$bus_id = filter_input(INPUT_POST, 'bus_id', FILTER_VALIDATE_INT);
$seats = json_decode($_POST['seats'] ?? '[]');
$points_used = filter_input(INPUT_POST, 'points', FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 0, 'max_range' => $_SESSION['loyalty_points']]
]);

if (!$bus_id || empty($seats) || $points_used === false) {
    $_SESSION['error'] = "Invalid booking data. Please try again.";
    header('Location: /public/booking.php?bus_id='.$bus_id);
    exit;
}

// Get bus details
$bus_stmt = $pdo->prepare("SELECT * FROM buses WHERE id = ?");
$bus_stmt->execute([$bus_id]);
$bus = $bus_stmt->fetch();

if (!$bus) {
    $_SESSION['error'] = "Bus not found.";
    header('Location: /public/bus_list.php');
    exit;
}

// Check seat availability
$booked_seats = getBookedSeats($bus_id);
$available_seats = array_diff(range(1, 40), $booked_seats);

// Validate selected seats
foreach ($seats as $seat) {
    if (!in_array($seat, $available_seats)) {
        $_SESSION['error'] = "Seat $seat is no longer available. Please select different seats.";
        header('Location: /public/booking.php?bus_id='.$bus_id);
        exit;
    }
}

// Calculate fares and discounts
$total_fare = count($seats) * $bus['fare'];
$discount = min($points_used / 100, $total_fare);
$final_amount = $total_fare - $discount;

// Create pending booking in session
$_SESSION['pending_booking'] = [
    'bus_id' => $bus_id,
    'seats' => $seats,
    'points_used' => $points_used,
    'total_fare' => $total_fare,
    'discount' => $discount,
    'final_amount' => $final_amount
];

// Redirect to payment
header('Location: /app/payment.php');
exit;
?>