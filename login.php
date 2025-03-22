<?php
session_start();
if(isset($_SESSION['user_id'])):
header('Location:home.php');
exit();
endif;

require_once 'config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['Email']) && empty($_POST['Password'])) {
        $errors[] = 'All fields are required';
    } else {
        if (empty($_POST['Email'])) {
            $errors[] = 'Email is required';
        }elseif(!filter_var($_POST['Email'], FILTER_VALIDATE_EMAIL)){
            $errors[] = ' Unvalid email';
        }
        if (empty($_POST['Password'])) {
            $errors[] = 'Password is required';
        }
    }

    if (empty($errors)) {
        $email = filter_var($_POST['Email'],FILTER_SANITIZE_EMAIL);
        $password = $_POST['Password'];
        
        $stmt = $connect->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $errors[] = 'User not found, check you email is currect';
        } elseif (!password_verify($password, $user['password'])) {
            $errors[] = 'Incorrect password';
        } else {
            $_SESSION['user_id'] = $user['id'];
            header("Location: home.php");
            exit();
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
        <h2>Login</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if(isset( $_SESSION['error'])): ?>
            <div class="error-message">
                    <p><?php echo  $_SESSION['error'];?></p>
            </div>
            <?php unset($_SESSION['error']);?>
        <?php endif; ?>
        

        <form method="POST" action="login.php">
            <input type="text" name="Email" placeholder="Email">
            <input type="password" name="Password" placeholder="Password">
            <button type="submit">Login</button>
        </form>

        <p class="switch">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>