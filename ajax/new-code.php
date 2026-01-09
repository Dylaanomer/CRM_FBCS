<?php

include 'dbh.php';

if (!isset($_POST['code']) || !isset($_POST['type']) || !isset($_POST['datein']) || !isset($_POST['aantal']) || !isset($_POST['initiaal'])) {
  echoResponse('error', 'missing parameter');
}

$code = addslashes($_POST['code']);
$type = addslashes($_POST["type"]);
$datein = addslashes($_POST['datein']);
$aantal = addslashes($_POST["aantal"]);
$initiaal = addslashes($_POST["initiaal"]);

if (!$code || !$type || !$datein || !$aantal || !$initiaal) {
  echoResponse('error', 'empty parameter');
}

$insert = "INSERT INTO codes (code,type,datein,aantal,initiaal,winver,regedit,antivirus,office,herstelpunt,CCleanerMBAMKRVTAdwCleaner) VALUES ('$code','$type','$datein','$aantal','$initiaal',0,0,0,0,0,0);";

if (!$conn->query($insert)) {
  echoResponse('error', $sql.$conn->error);
}

echoResponse('success', 'added new code');

?>