<?php 
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    $connection = new mysqli('localhost', 'root', '', 'maroonmarket');
    
    if (!$connection){
        die(mysqli_error($connection));
    }
?>