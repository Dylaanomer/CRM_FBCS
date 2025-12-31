<?php

require 'dbh.php';

$code = addslashes($_POST['code']);

$sql = "UPDATE gegevens SET editing = NOT editing, updated = updated WHERE code LIKE '$code';";
if (!$conn->query($sql)) {
	echoResponse('error', $sql.$conn->error);
}

echoResponse('success', 'updated editing column');

?>