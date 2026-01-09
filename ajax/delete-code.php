<?php

include 'dbh.php';

$code = addslashes($_POST["code"]);
$remove = "DELETE FROM codes WHERE code = '$code'";

if (!$conn->query($remove)) {
  echoResponse('error', $sql.$conn->error);
}

$remove = "DELETE FROM gegevens WHERE code LIKE '$code%'";

if (!$conn->query($remove)) {
  echoResponse('error', $sql.$conn->error);
}

echoResponse('success', 'deleted code successfully');

?>