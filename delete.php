<?php
session_start();
require 'config/db.php';
require 'includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['snippet_id'])) {
    $snippet_id = filter_var($_POST['snippet_id'], FILTER_VALIDATE_INT);
    $user_id=$_SESSION['user_id']?$_SESSION['user_id'] :'';
    if (!$user_id) {
        $_SESSION['error'] = "Unauthorized access";
        header("Location: login.php");
        exit();
    }
    try {
        $stmt = $connect->prepare("DELETE FROM snippets WHERE id = ? AND user_id = ?");
        $stmt->execute([$snippet_id, $user_id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['delete_messge'] = "Snippet deleted successfully";
        } else {
            $_SESSION['delete_messge'] = "Snippet not found or you don't have permission";
        }
    } catch (PDOException $e) {
        $_SESSION['delete_messge'] = "Deletion error: " . $e->getMessage();
    }
    
    header("Location: snippets.php");
    exit();
}