<?php

$host = "mysql";
$database = "test_only";
$user = "root";
$password = "root";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);

    $pdo->query("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY,
                                                login VARCHAR(255), 
                                                phone VARCHAR(20),
                                                email VARCHAR(150),
                                                password VARCHAR(100))");
} catch (PDOException $e) {
    echo $e->getMessage();
}
