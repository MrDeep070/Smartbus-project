<?php
/**
 * Calculate points earned for a booking
 * 
 * @param float $amount_paid Total amount paid
 * @return int Points earned
 */
function calculatePointsEarned($amount_paid) {
    // 1 point for every ₹20 spent
    return floor($amount_paid / 20);
}

/**
 * Apply loyalty points discount to a booking
 * 
 * @param float $total_fare Total fare before discount
 * @param int $points_used Points to redeem
 * @return array [discount_amount, final_amount]
 */
function applyLoyaltyDiscount($total_fare, $points_used) {
    $discount = min($points_used / 100, $total_fare);
    $final_amount = $total_fare - $discount;
    return [$discount, $final_amount];
}

/**
 * Update user's loyalty points after a booking
 * 
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @param int $points_used Points redeemed
 * @param float $amount_paid Final amount paid
 * @return void
 */
function updateLoyaltyPoints($pdo, $user_id, $points_used, $amount_paid) {
    $points_earned = calculatePointsEarned($amount_paid);
    $net_points = $points_earned - $points_used;
    
    $stmt = $pdo->prepare("UPDATE users 
                          SET loyalty_points = loyalty_points + ? 
                          WHERE id = ?");
    $stmt->execute([$net_points, $user_id]);
    
    // Update session points if current user
    if ($_SESSION['user_id'] == $user_id) {
        $_SESSION['loyalty_points'] += $net_points;
    }
}
?>