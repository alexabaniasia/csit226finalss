<?php 
    $connection = new mysqli('localhost', 'root', '', 'maroonmarket');
    
    if (!$connection){
        die('Could not connect: ' . mysqli_connect_error());
    }
?>