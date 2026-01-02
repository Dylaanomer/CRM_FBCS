<?php
$mysqlserver = "localhost";
$mysqluser = "root";
$mysqlpass = "";
$mysqldb = "fbcs.nl_licenties_PHP";


$conn = new mysqli($mysqlserver, $mysqluser, $mysqlpass, $mysqldb);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
