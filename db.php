<?php

$db_config = parse_ini_file("db_config.ini");

$DB_HOST = $db_config['host'];
$DB_PORT = $db_config['port'];
$DB_USER = $db_config['user'];
$DB_PASSWORD = $db_config['password'];
$DB_NAME = $db_config['name'];

$db = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
if (mysqli_connect_errno()) {
    exit('An error occured while trying to connect to MySQL: ' . mysqli_connect_error());
}
