<?php
require_once '../includes/db.php';
require_once '../vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51ABC...'); // Replace with your Stripe key

$session_id = $_GET['session_id'];
$session = \Stripe\Checkout\Session::retrieve($session_id);

if ($session->payment_status === 'paid') {
    // Save booking to database
    $metadata = $session->metadata;
    
    $pdo->beginTransaction();
    try {
        // Create booking
        $stmt = $pdo->prepare("INSERT INTO bookings 
            (user_id, bus_id, seats, amount_paid, points_redeemed) 
            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $metadata->user_id,
            $metadata->bus_id,
            $metadata->seats,
            $session->amount_total / 100,
            $metadata->points_used
        ]);
        
        // Update loyalty points
        $points_earned = floor($session->amount_total / 20); // 1 point per ₹20
        $update_stmt = $pdo->prepare("UPDATE users 
            SET loyalty_points = loyalty_points - ? + ? 
            WHERE id = ?");
        $update_stmt->execute([
            $metadata->points_used,
            $points_earned,
            $metadata->user_id
        ]);
        
        $pdo->commit();
        $_SESSION['success'] = "Booking confirmed!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error saving booking: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Payment not completed!";
}

header("Location: /public/my_bookings.php");
exit;
?>