<?php

session_start();

include_once "../db.php";
global $db;

// Remove session variables
unset($_SESSION["username"]);
unset($_SESSION["password"]);

// Remove tokens
if (isset($_COOKIE['pput'])) {
    // Remove the token from the database
    $token = $_COOKIE['pput'];
    $db->query("DELETE FROM tokens WHERE token = '$token';");

    // Remove the cookie
    unset($_COOKIE['pput']);
    setcookie('pput', '', -1, '/');
}

header("location: login/");