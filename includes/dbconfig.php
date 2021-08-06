<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'kite';

// get a new mysqli connection
$conn = @new mysqli($db_host, $db_user, $db_password, $db_name);

if ($conn->connect_error) {
    echo 'Errno: '.$conn->connect_errno;
    echo '<br>';
    echo 'Error: '.$conn->connect_error;
    exit();
}

// start a new session
session_start();