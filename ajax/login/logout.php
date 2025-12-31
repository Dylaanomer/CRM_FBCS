<?php 

include 'dbh.php';

$name = $_COOKIE["name"];
$uuid = $_COOKIE["uuid"];

//delete cookie from database
$sql = "DELETE FROM cookies WHERE (username LIKE '$name' AND uuid LIKE '$uuid');";
if (!$result = $conn->query($sql)) {
  echoResponse("error", $sql."<br/>".$conn->error);
}

//remove current cookies
$path = '/';
$domain = 'licenties.fbcs.nl';
$secure = true;

setcookie("name", "", time()-3600, $path, $domain, $secure);
setcookie("uuid", "", time()-3600, $path, $domain, $secure);
  
//echoResponse("error", "could not remove cookie from browser", 500);

echoResponse("success", "logged out");

function echoResponse($status, $msg) {
  global $conn;
  $conn->close();
  $res = array("status" => $status,
                "msg" => $msg);
  echo json_encode($res);
  exit;
}

?>