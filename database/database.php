<?php 
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "sekolah";

    $connection = mysqli_connect($hostname, $username, $password, $database);

    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }
?>