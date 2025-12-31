<?php

// this file handles requests for unused codes, but also updates how many codes have been used
// if the user cancels using the code, the code can be given in a GET parameter following in a decreased 'gebruikt' count in the db

include 'dbh.php';

$sql = "SELECT `type` FROM `codes` WHERE (gebruikt < aantal AND ongeldig = 0) GROUP BY type;";
if (!$result = $conn->query($sql)) echoResponse("error", $sql.$conn->error);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) $types[] = $row["type"];

  echoResponse('success', $types);
} else {
  echoResponse('error', '0 results');
}

?>