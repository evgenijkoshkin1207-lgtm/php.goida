<?php
    $host = "localhost";
    $user = "root";
    $password = '';
    $datebase = 'privat';
    $mysqli = mysqli_connect($host, $user, $password, $datebase);
    if(mysqli_connect_errno()) echo mysqli_connect_error();
?>