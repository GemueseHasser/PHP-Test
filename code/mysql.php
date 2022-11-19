<?php

$host = "127.0.0.1";
$database = "contactbook";
$user = "root";
$password = "toor";

try {
    $mysql = new PDO("mysql:host=$host;dbname=$database", $user, $password);
} catch (PDOException $e) {
    echo "SQL-Error: " . $e->getMessage();
}
