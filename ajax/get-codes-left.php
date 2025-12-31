<?php

include 'dbh.php';

$type = addslashes($_GET['type']);

$sql = "SELECT aantal, gebruikt FROM codes WHERE gebruikt < aantal AND ongeldig = 0 AND type = '$type';";
if (!$result = $conn->query($sql)) {
  echoResponse('error', $sql.$conn->error);
}

if ($result->num_rows > 0) {
  $left = 0;

  while ($row = $result->fetch_assoc()) {
    $aantal = intval($row['aantal']);
    $gebruikt = intval($row['gebruikt']);

    $left += ($aantal - $gebruikt);
  }

  echoResponse('aantal', $left);
} else {
  echoResponse('aantal', 0);
}

?>