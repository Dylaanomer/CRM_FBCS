<?php 

include 'dbh.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function generateRandomString($length) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

//get post variables
$username = addslashes($_POST["username"]);
$password = addslashes($_POST["password"]);
$remember = filter_var($_POST['remember'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE); //filter_var can be used to filter for boolean!

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

//check if cookies table exists
$sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_NAME = 'cookies' AND TABLE_SCHEMA LIKE '$mysqldb');";
if (!$result = $conn->query($sql)) {
  echoResponse("error", $sql."<br/>".$conn->error);
}
//create the table if it does not exist
if ($result->num_rows == 0) {
  $sql = 'CREATE TABLE cookies (username varchar(20) NOT NULL, uuid varchar(64), expires DATETIME NOT NULL, session BIT);';
  if (!$conn->query($sql)) {
    echoResponse("error", $sql."<br/>".$conn->error);
  }
}

$path = '/';
$domain = '';
$secure = false;

$uuid = generateRandomString(64); //this will be the uuid/cookie

if ($remember === TRUE) {
  //get next week in seconds and DateTime
  $week = new DateInterval("P7D");
  $nextWeek = new DateTime();
  $nextWeek->add($week);
  $expireDB = $nextWeek->format("Y-m-d H:i:s");

  $expires = time() + (86400 * 7); //86400 is one day in seconds

  $session = 0; // this is not a session - valid for a week
} else {
  //get next week in seconds and DateTime
  $hour = new DateInterval("PT1H");
  $nextHour = new DateTime();
  $nextHour->add($hour);
  $expireDB = $nextHour->format("Y-m-d H:i:s");

  $expires = 0; // set expires to 0 to have the cookie only valid for the session

  $session = 1; // this is a session
}

if (!setcookie("name", $username, $expires, $path, $domain, $secure)) {
  echoResponse("error", "could not set name cookie");
}

if (!setcookie("uuid", $uuid, $expires, $path, $domain, $secure)) {
  echoResponse("error", "could not set uuid cookie");
}

//insert new cookie for user
$sql = "INSERT INTO cookies (username, uuid, expires, session) VALUES ('$username','$uuid','$expireDB', $session);";
if (!$conn->query($sql)) {
  echoResponse("error", $sql."<br/>".$conn->error);
}

echoResponse("success", "login was successful");

function echoResponse($status, $msg) {
  global $conn;
  $conn->close();
  $res = array("status" => $status,
                "msg" => $msg);
  echo json_encode($res);
  exit;
}

?>