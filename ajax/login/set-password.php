<?php

include 'dbh.php';

//get post variables
$username = $_POST["username"];
$password = $_POST["password"];
$passwordNew = $_POST["passwordNew"];
$password_check = $_POST["password_check"];

//only these usernames are allowed
if (!ctype_alnum($username)) {
  echoResponse("error", "invalid username");
}

if (!preg_match('/^[A-Za-z0-9_~\-!@#\$%\^&*\(\)]+$/',$passwordNew)) {
  echoResponse("error", "invalid password");
}

//encrypt password
$encrypted_password = password_hash($passwordNew, PASSWORD_BCRYPT);

//verify login
$sql = "SELECT password FROM users where username LIKE '$username';";
if (!$result = $conn->query($sql)) {
  echoResponse("error", $sql."<br/>".$conn->error);
}

if ($result->num_rows < 1) {
  echoResponse("error", "could not find user");
}

$row = $result->fetch_assoc(); //there should only be one result so there is no while loop required

$hash= $row["password"];
if (!password_verify($password, $hash)) {
  echoResponse("error", "invalid password");
}

//insert new user with encrypted password
$sql = "UPDATE users SET password = '$encrypted_password' WHERE username = '$username';";
if (!$conn->query($sql)) {
  echoResponse("error", $sql."<br/>".$conn->error);
}

echoResponse("success", "password updated");

function echoResponse($status, $msg) {
  global $conn;
  $conn->close();
  $res = array("status" => $status,
                "msg" => $msg);
  echo json_encode($res);
  exit;
}
?>