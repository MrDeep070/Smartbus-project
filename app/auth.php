session_start();
require_once '../includes/db.php';

// Registration Logic
if ($_GET['action'] === 'register') {
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['email'], $hashed_password]);
    header("Location: /public/login.php?success=1");
}

// Login Logic
if ($_GET['action'] === 'login') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: /public/bus_list.php");
    } else {
        header("Location: /public/login.php?error=1");
    }
}<?php
require_once '../includes/db.php';

if ($_GET['action'] === 'register') {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $password]);
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: /public/login.php");
    } catch (PDOException $e) {
        $_SESSION['error'] = "Email already exists!";
        header("Location: /public/register.php");
    }
    exit;
}

if ($_GET['action'] === 'login') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['loyalty_points'] = $user['loyalty_points'];
        header("Location: /public/index.php");
    } else {
        $_SESSION['error'] = "Invalid email or password!";
        header("Location: /public/login.php");
    }
    exit;
}

if ($_GET['action'] === 'logout') {
    session_destroy();
    header("Location: /public/index.php");
    exit;
}
?>