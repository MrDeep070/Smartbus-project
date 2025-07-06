<?php
require_once '../includes/db.php';
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51ABC...'); // Replace with your Stripe key

// Calculate payment amount
$bus_id = $_POST['bus_id'];
$seats = json_decode($_POST['seats']);
$points_used = intval($_POST['points']);

$bus_stmt = $pdo->prepare("SELECT fare FROM buses WHERE id = ?");
$bus_stmt->execute([$bus_id]);
$bus = $bus_stmt->fetch();

$total_fare = count($seats) * $bus['fare'];
$discount = min($points_used / 100, $total_fare);
$final_amount = $total_fare - $discount;

// Create Stripe session
try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'inr',
                'product_data' => ['name' => 'Bus Ticket'],
                'unit_amount' => $final_amount * 100,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://yourdomain.com/public/payment_success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'http://yourdomain.com/public/booking.php?bus_id='.$bus_id,
        'metadata' => [
            'user_id' => $_SESSION['user_id'],
            'bus_id' => $bus_id,
            'seats' => json_encode($seats),
            'points_used' => $points_used
        ]
    ]);

    header("Location: " . $session->url);
    exit;
} catch (Exception $e) {
    $_SESSION['error'] = "Payment processing error: " . $e->getMessage();
    header("Location: /public/booking.php?bus_id=".$bus_id);
    exit;
}
?>