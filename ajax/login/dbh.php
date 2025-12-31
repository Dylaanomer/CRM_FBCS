<?php
$mysqlserver = "wf.mariadb";
$mysqluser = "fbcs_nl";
$mysqlpass = "763c1a02122f";
$mysqldb = "fbcs.nl_licenties_PHP";


$conn = new mysqli($mysqlserver, $mysqluser, $mysqlpass, $mysqldb);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>