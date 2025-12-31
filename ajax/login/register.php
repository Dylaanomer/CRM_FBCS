<?php 

include 'dbh.php';

//get post variables
$username = $_POST["username"];
$password = $_POST["password"];
$password_check = $_POST["password_check"];

//only these usernames are allowed
if (!ctype_alnum($username)) {
  echoResponse("error", "invalid username");
}

if (!preg_match('/^[A-Za-z0-9_~\-!@#\$%\^&*\(\)]+$/',$password)) {
  echoResponse("error", "invalid password");
}

//encrypt password
$encrypted_password = password_hash($password, PASSWORD_BCRYPT);

//check if users table exists
$sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_NAME LIKE 'users' AND TABLE_SCHEMA LIKE '$mysqldb');";
$result = $conn->query($sql);
if (!$conn->query($sql)) {
  echoResponse("error", $sql."<br/>".$conn->error);
}

//create the table if it does not exist
if ($result->num_rows == 0) {
  $sql = 'CREATE TABLE users (username varchar(20) NOT NULL, password varchar(60) NOT NULL, updated TIMESTAMP NOT NULL default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (username));';
  if (!$conn->query($sql)) {
    echoResponse("error", $sql."<br/>".$conn->error);
  }
}

//insert new user with encrypted password
$sql = "INSERT INTO users (username, password) VALUES ('$username','$encrypted_password');";
if (!$conn->query($sql)) {
  echoResponse("error", $sql."<br/>".$conn->error);
}

echoResponse("success", "user created");

function echoResponse($status, $msg) {
  global $conn;
  $conn->close();
  $res = array("status" => $status,
                "msg" => $msg);
  echo json_encode($res);
  exit;
}
?>