<?php 
    $connection = new mysqli('localhost', 'root', '', 'maroonmarket');
    
    if (!$connection){
        die(mysqli_error($connection));
    }
?>