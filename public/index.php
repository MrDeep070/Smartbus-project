<?php
require_once '../includes/header.php';
?>

<div class="bg-blue-600 text-white py-20">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">Book Bus Tickets in Seconds</h1>
        <p class="text-xl mb-10 max-w-2xl mx-auto">SmartBus - The fastest way to book bus tickets online. Safe, reliable, and affordable travel.</p>
        <a href="/public/bus_list.php" 
           class="bg-white text-blue-600 font-bold py-3 px-8 rounded-full text-lg hover:bg-gray-100 transition duration-300">
            Book Your Ticket Now
        </a>
    </div>
</div>

<div class="py-16 bg-gray-50">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-16">Why Choose SmartBus?</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Secure Booking</h3>
                <p class="text-gray-600">Your information is protected with bank-level security and encrypted payments.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Save Time</h3>
                <p class="text-gray-600">Book tickets in under 60 seconds with our streamlined booking process.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg shadow-md text-center">
                <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-4">Earn Rewards</h3>
                <p class="text-gray-600">Get loyalty points on every booking that you can redeem for discounts.</p>
            </div>
        </div>
    </div>
</div>

<div class="py-16">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-16">Popular Routes</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php
            $popularRoutes = [
                ['from' => 'Mumbai', 'to' => 'Pune', 'fare' => 450],
                ['from' => 'Delhi', 'to' => 'Jaipur', 'fare' => 650],
                ['from' => 'Bangalore', 'to' => 'Chennai', 'fare' => 750],
                ['from' => 'Kolkata', 'to' => 'Digha', 'fare' => 550]
            ];
            
            foreach ($popularRoutes as $route): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold"><?= $route['from'] ?> to <?= $route['to'] ?></h3>
                            <p class="text-gray-600">Multiple buses daily</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-sm font-bold px-3 py-1 rounded-full">
                            ₹<?= $route['fare'] ?>
                        </span>
                    </div>
                    <a href="/public/bus_list.php?from=<?= urlencode($route['from']) ?>&to=<?= urlencode($route['to']) ?>" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-md transition duration-300">
                        Book Now
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
require_once '../includes/footer.php';
?>