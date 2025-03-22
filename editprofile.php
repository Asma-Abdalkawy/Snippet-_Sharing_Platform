<?php
require 'config/db.php';
require 'includes/auth.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$errors = [];
$success = '';

// Get current user data
$stmt = $connect->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars(trim($_POST['Name']));
    $email = htmlspecialchars(trim($_POST['Email']));
    $password = htmlspecialchars(trim($_POST['Password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));

    // Validate required fields
    if (empty($name) && empty($email)) {
        $errors[] = 'Name and email are required';
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
    }
    
    $stmt = $connect->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $stmt->execute([$email, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        $errors[] = 'Email already registered';
    }

    if (!empty($password)) {
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
    }

    if (empty($errors)) {
        try {
    
            $query = 'UPDATE users SET username = ?, email = ?';
            $params = [$name, $email];

          
            if (!empty($password)) {
                $query .= ', password = ?';
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }

            $query .= ' WHERE id = ?';
            $params[] = $_SESSION['user_id'];

            $stmt = $connect->prepare($query);
            $stmt->execute($params);

            $_SESSION['edit_profile'] = "Profile updated successfully";
            header('Location: profile.php');
            exit();
        } catch (PDOException $error) {
            $errors[] = 'Error updating data: ' . $error->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once 'includes/header.php' ?>
    <title>Update Profile</title>
</head>
<body>
    <div class="container">
        <h2>Update Profile</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="Name" placeholder="Full Name" 
                   value="<?= htmlspecialchars($user['username']) ?>" >
            
            <input type="email" name="Email" placeholder="Email"
                   value="<?= htmlspecialchars($user['email']) ?>" >
            
            <input type="password" name="Password" placeholder="New Password (leave blank to keep current)">
            
            <input type="password" name="confirm_password" placeholder="Confirm New Password">
            
            <button type="submit">Update Profile</button>
        </form>

        <p class="switch">
            <a href="profile.php">Back to Profile</a> | 
            <a href="#">Change Password</a>
        </p>
    </div>
</body>
</html>