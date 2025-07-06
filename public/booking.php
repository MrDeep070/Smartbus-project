<?php
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /public/login.php");
    exit;
}

$bus_id = $_GET['bus_id'];
$bus_stmt = $pdo->prepare("SELECT * FROM buses WHERE id = ?");
$bus_stmt->execute([$bus_id]);
$bus = $bus_stmt->fetch();

// Get booked seats
$booked_stmt = $pdo->prepare("SELECT seats FROM bookings WHERE bus_id = ?");
$booked_stmt->execute([$bus_id]);
$booked_seats = [];
foreach ($booked_stmt->fetchAll() as $row) {
    $booked_seats = array_merge($booked_seats, json_decode($row['seats']));
}
?>

<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Book Seats: <?= $bus['bus_no'] ?></h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Seat Map -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Select Seats</h2>
            <div class="grid grid-cols-4 gap-4" id="seatMap">
                <?php for ($i = 1; $i <= 40; $i++): ?>
                    <?php $isBooked = in_array($i, $booked_seats); ?>
                    <div class="seat-item text-center py-3 rounded border 
                        <?= $isBooked ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-100 hover:bg-green-200 cursor-pointer' ?>"
                        data-seat="<?= $i ?>" 
                        <?= $isBooked ? 'disabled' : '' ?>>
                        <?= $i ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <!-- Booking Summary -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Booking Summary</h2>
            <div class="space-y-4">
                <div>
                    <p class="font-medium">Route:</p>
                    <p><?= $bus['source'] ?> to <?= $bus['destination'] ?></p>
                </div>
                
                <div>
                    <p class="font-medium">Selected Seats:</p>
                    <div id="selectedSeats" class="min-h-[20px] text-red-600 font-medium"></div>
                </div>
                
                <div class="mt-4">
                    <label class="block font-medium mb-2">Redeem Loyalty Points</label>
                    <p class="text-sm text-gray-600 mb-2">Available: <span id="availablePoints"><?= $_SESSION['loyalty_points'] ?></span> points</p>
                    <input type="number" id="pointsInput" min="0" max="<?= $_SESSION['loyalty_points'] ?>" 
                           class="w-32 border p-2 rounded" value="0">
                    <p class="text-sm text-gray-600 mt-1">100 points = ₹1 discount</p>
                </div>
                
                <div class="border-t pt-4">
                    <div class="flex justify-between mb-2">
                        <span>Base Fare:</span>
                        <span id="baseFare">₹0.00</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span>Discount:</span>
                        <span id="discount">₹0.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total:</span>
                        <span id="totalFare">₹0.00</span>
                    </div>
                    
                    <form id="bookingForm" action="/app/payment.php" method="POST" class="mt-6">
                        <input type="hidden" name="bus_id" value="<?= $bus_id ?>">
                        <input type="hidden" name="seats" id="seatsInput">
                        <input type="hidden" name="points" id="pointsHidden">
                        <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded">
                            Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Seat selection logic
document.addEventListener('DOMContentLoaded', () => {
    const seatMap = document.getElementById('seatMap');
    const selectedSeats = new Set();
    const fare = <?= $bus['fare'] ?>;
    
    seatMap.addEventListener('click', (e) => {
        const seatItem = e.target.closest('.seat-item');
        if (!seatItem || seatItem.hasAttribute('disabled')) return;
        
        const seatNum = parseInt(seatItem.dataset.seat);
        
        if (selectedSeats.has(seatNum)) {
            selectedSeats.delete(seatNum);
            seatItem.classList.remove('bg-blue-300', 'text-white');
            seatItem.classList.add('bg-green-100');
        } else {
            selectedSeats.add(seatNum);
            seatItem.classList.remove('bg-green-100');
            seatItem.classList.add('bg-blue-300', 'text-white');
        }
        
        updateSummary();
    });
    
    // Points redemption
    const pointsInput = document.getElementById('pointsInput');
    pointsInput.addEventListener('input', updateSummary);
    
    function updateSummary() {
        // Update selected seats display
        const seatsDisplay = document.getElementById('selectedSeats');
        seatsDisplay.textContent = selectedSeats.size > 0 
            ? Array.from(selectedSeats).sort((a, b) => a - b).join(', ')
            : 'No seats selected';
        
        // Calculate fares
        const baseFare = selectedSeats.size * fare;
        document.getElementById('baseFare').textContent = `₹${baseFare.toFixed(2)}`;
        
        const pointsUsed = parseInt(pointsInput.value) || 0;
        const discount = Math.min(pointsUsed / 100, baseFare);
        document.getElementById('discount').textContent = `-₹${discount.toFixed(2)}`;
        
        const totalFare = baseFare - discount;
        document.getElementById('totalFare').textContent = `₹${totalFare.toFixed(2)}`;
        
        // Update hidden inputs
        document.getElementById('seatsInput').value = JSON.stringify([...selectedSeats]);
        document.getElementById('pointsHidden').value = pointsUsed;
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>