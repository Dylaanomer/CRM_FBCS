<?php

// this file handles requests for unused codes, but also updates how many codes have been used
// if the user cancels using the code, the code can be given in a GET parameter following in a decreased 'gebruikt' count in the db

include 'dbh.php';

$type = addslashes($_GET['type']);

if (isset($_GET['code'])) { // if a GET parameter 'code' is set the 'gebruikt' count for this code should be lowered by 1
  $code = addslashes($_GET['code']);

  $code = substr($code, 0 , -2);

  $sql = "UPDATE codes SET gebruikt = gebruikt - 1 WHERE code = '$code';";
  if (!$conn->query($sql)) {
    echoResponse("error", $sql.$conn->error);
  }
  echoResponse("success", "lowered gebruikt count");
}

$sql = "SELECT code, type, datein, gebruikt FROM codes WHERE gebruikt < aantal AND ongeldig = 0 AND type = '$type' ORDER BY datein ASC LIMIT 1;";
if (!$result = $conn->query($sql)) {
  echoResponse("error", $sql.$conn->error);
}

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();

  $code = $row['code'];
  $type = $row['type'];
  $datein = $row['datein'];
  $gebruikt = intval($row['gebruikt']);

  $sql = "UPDATE codes SET gebruikt = gebruikt + 1 WHERE code = '$code';";
  if (!$conn->query($sql)) {
    echoResponse('error', $sql.$conn->error);
  }

  $code = $code . "." . $gebruikt;

  echoResponse('success', array('code' => $code,
                          'type' => $type,
                          'datein' => $datein));
} else {
  echoResponse('error', '0 results');
}

?>