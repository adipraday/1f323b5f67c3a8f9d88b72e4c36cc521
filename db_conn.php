<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "code_chalenge";


$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function dbconn() {
    $host = 'localhost'; 
    $dbname = 'code_chalenge';
    $username = 'root'; 
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        
        exit("Database connection failed: " . $e->getMessage());
    }
}

?>
