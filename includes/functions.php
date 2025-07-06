<?php
/**
 * Get booked seats for a specific bus
 * 
 * @param int $bus_id
 * @return array Array of booked seat numbers
 */
function getBookedSeats($bus_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT seats FROM bookings WHERE bus_id = ?");
    $stmt->execute([$bus_id]);
    
    $booked_seats = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $seats = json_decode($row['seats']);
        $booked_seats = array_merge($booked_seats, $seats);
    }
    
    return $booked_seats;
}

/**
 * Redirect with a flash message
 * 
 * @param string $url Redirect URL
 * @param string $type Message type (success, error, warning)
 * @param string $message Message content
 */
function redirectWithMessage($url, $type, $message) {
    $_SESSION[$type] = $message;
    header("Location: $url");
    exit;
}

/**
 * Generate a seat map HTML
 * 
 * @param int $bus_id
 * @param array $selected_seats Currently selected seats
 * @return string HTML for seat map
 */
function generateSeatMap($bus_id, $selected_seats = []) {
    $booked_seats = getBookedSeats($bus_id);
    $html = '<div class="grid grid-cols-4 md:grid-cols-10 gap-2">';
    
    for ($seat = 1; $seat <= 40; $seat++) {
        $is_booked = in_array($seat, $booked_seats);
        $is_selected = in_array($seat, $selected_seats);
        
        $classes = 'text-center py-2 rounded border cursor-pointer transition ';
        
        if ($is_booked) {
            $classes .= 'bg-gray-300 text-gray-500 cursor-not-allowed ';
        } elseif ($is_selected) {
            $classes .= 'bg-blue-500 text-white ';
        } else {
            $classes .= 'bg-green-100 hover:bg-green-200 ';
        }
        
        $html .= '<div class="'.$classes.'" data-seat="'.$seat.'">'.$seat.'</div>';
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Calculate booking summary
 * 
 * @param int $num_seats Number of seats
 * @param float $fare_per_seat Fare per seat
 * @param int $points_used Points used
 * @param int $available_points Available loyalty points
 * @return array [base_fare, discount, total_fare]
 */
function calculateBookingSummary($num_seats, $fare_per_seat, $points_used, $available_points) {
    $base_fare = $num_seats * $fare_per_seat;
    $max_points = min($points_used, $available_points);
    $discount = min($max_points / 100, $base_fare);
    $total_fare = $base_fare - $discount;
    
    return [
        'base_fare' => $base_fare,
        'discount' => $discount,
        'total_fare' => $total_fare,
        'points_used' => $max_points
    ];
}
?>