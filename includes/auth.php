<?php
session_start();
if(!isset($_SESSION['user_id'])):
 $_SESSION['error'] = "Unauthorized access";
header('Location:login.php');
exit();
endif;
?>