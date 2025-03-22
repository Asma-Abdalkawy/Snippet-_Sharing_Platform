<?php
// PDO => php data object 
try {
    $dest = 'mysql:host=localhost:3310;dbname=snippets_db';
    $user = 'root';
    $pass = '';
    $connect = new PDO($dest, $user, $pass);
    // echo "connect";
} catch (PDOException $error) {
    echo $error->getMessage();
}
