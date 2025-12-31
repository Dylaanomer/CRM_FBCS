<?php
$mysqlserver = "wf.mariadb";
$mysqluser = "fbcs_nl";
$mysqlpass = "763c1a02122f";
$mysqldb = "fbcs.nl_crm_PHP";


$conn = new mysqli($mysqlserver, $mysqluser, $mysqlpass, $mysqldb);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/**
 * respond with a json encoded message
 * if the '$msg' is an array this will be printed
 * else the message will contain the key '$status' and value '$msg'
 */
function echoResponse($status, $msg) {
    global $conn;
    $conn->close();
    if (gettype($msg) === "array") {
        echo json_encode($msg);
    } else {
      echo json_encode(array($status => $msg));
    }
    exit;
}

?>