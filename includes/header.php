<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartBus Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body class="bg-gray-50">
<nav class="bg-blue-600 text-white p-4 shadow-lg">
    <div class="container mx-auto flex flex-col md:flex-row justify-between items-center">
        <a href="/public/index.php" class="text-2xl font-bold mb-4 md:mb-0">SmartBus</a>
        <div class="flex flex-wrap justify-center gap-2">
            <a href="/public/bus_list.php" class="hover:bg-blue-700 px-3 py-2 rounded">Search Buses</a>
            <a href="/public/my_bookings.php" class="hover:bg-blue-700 px-3 py-2 rounded">My Bookings</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="/public/profile.php" class="hover:bg-blue-700 px-3 py-2 rounded">Profile</a>
                <a href="/app/auth.php?action=logout" class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded">Logout</a>
            <?php else: ?>
                <a href="/public/login.php" class="bg-green-500 hover:bg-green-600 px-3 py-2 rounded">Login</a>
                <a href="/public/register.php" class="bg-blue-500 hover:bg-blue-600 px-3 py-2 rounded">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container mx-auto p-4">