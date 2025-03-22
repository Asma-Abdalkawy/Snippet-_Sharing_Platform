<?php
session_start();
require_once 'config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['Name']);
    $email = trim($_POST['Email']);
    $password = trim($_POST['Password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($name) && empty($email) && empty($password) && empty($confirm_password)) {
        $errors[] = 'All fields are required';
    } else {
        if (empty($name)){
            $errors[]='Name is required';
        }elseif (strlen($name) < 3) {
            $errors[] = 'Name must be at least 3 characters';
        }
        
        if (empty($email)){
            $errors[]='Email is required';
        }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }
        
        if (empty($password)){
            $errors[]='Password is required';
        }elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }elseif (empty($confirm_password)){
            $errors[]='Confirm password is required';
        }elseif ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }
    }

    if (empty($errors)) {
        $stmt = $connect->prepare('SELECT email FROM users WHERE email = ?');
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email already registered';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            try {
                $stmt = $connect->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
                $stmt->execute([$name, $email, $hashedPassword]);
                
                header('Location: login.php');
                exit();
            } catch (PDOException $error) {
                 $errors[] = 'Error registeration: ' . $error->getMessage();
            }
           
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'includes/header.php' ?>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <input type="text" name="Name" placeholder="Full Name" >
            <input type="text" name="Email" placeholder="Email" >
            <input type="password" name="Password" placeholder="Password" >
            <input type="password" name="confirm_password" placeholder="Confirm Password" >
            <button type="submit">Register</button>
        </form>

        <p class="switch">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>