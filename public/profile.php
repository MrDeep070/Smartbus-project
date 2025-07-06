<?php
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

require_once '../includes/db.php';
$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch user's bookings
$bookings_stmt = $pdo->prepare("
    SELECT b.*, bus.bus_no, bus.source, bus.destination, bus.departure_time
    FROM bookings b
    JOIN buses bus ON b.bus_id = bus.id
    WHERE b.user_id = ?
    ORDER BY b.booking_date DESC
    LIMIT 5
");
$bookings_stmt->execute([$user_id]);
$recent_bookings = $bookings_stmt->fetchAll();
?>

<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8">Your Profile</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-24 h-24 mx-auto mb-4"></div>
                    <h2 class="text-xl font-bold"><?= htmlspecialchars($user['name']) ?></h2>
                    <p class="text-gray-600"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="font-medium">Loyalty Points</span>
                            <span class="text-xl font-bold text-blue-700"><?= $user['loyalty_points'] ?></span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">100 points = ₹1 discount</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-bold mb-4">Personal Information</h2>
                <form action="/app/auth.php?action=update_profile" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name']) ?>"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>"
                                   class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500" disabled>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">Recent Bookings</h2>
                
                <?php if (empty($recent_bookings)): ?>
                    <p class="text-gray-600 py-4">You haven't made any bookings yet.</p>
                    <a href="/public/bus_list.php" class="text-blue-600 hover:underline">Book your first ticket</a>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_bookings as $booking): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="font-bold"><?= $booking['source'] ?> to <?= $booking['destination'] ?></h3>
                                        <p class="text-sm text-gray-600">
                                            <?= date('M d, Y', strtotime($booking['booking_date'])) ?> | 
                                            <?= date('h:i A', strtotime($booking['departure_time'])) ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold">₹<?= number_format($booking['amount_paid'], 2) ?></p>
                                        <p class="text-sm text-gray-600">Bus: <?= $booking['bus_no'] ?></p>
                                    </div>
                                </div>
                                <div class="mt-2 flex justify-between text-sm">
                                    <span>Seats: <?= implode(', ', json_decode($booking['seats'])) ?></span>
                                    <span>Points used: <?= $booking['points_redeemed'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-6 text-center">
                        <a href="/public/my_bookings.php" class="text-blue-600 hover:underline">View all bookings</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>