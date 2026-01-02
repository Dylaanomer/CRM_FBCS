<?php

include 'dbh.php';

if (!isset($_POST['code'])) {
	echoResponse('error', 'code is not set');
} else if (!$_POST['code']) {
	echoResponse('error', 'code is empty');
}

$code = addslashes($_POST['code']);
$type = addslashes($_POST['type']);
$ongeldig = addslashes($_POST['ongeldig']);
$verwijder = addslashes($_POST['verwijder']);

if ($ongeldig === "1") {
	$code = substr($code, 0 , -2);
	$sql = "UPDATE codes SET ongeldig = NOT ongeldig WHERE code = '$code';";
	if (!$conn->query($sql)) {
		echoResponse("error", $sql.$conn->error);
	}
	echoResponse("success", "code has been set to ongeldig");
} else if ($verwijder === "1") {
	$sql = "DELETE FROM gegevens WHERE code = '$code';"; // remove the used code from gegevens
	if (!$conn->query($sql)) {
		echoResponse("error", $sql.$conn->error);
	}
	$code = substr($code, 0 , -2);
	$sql = "UPDATE codes SET gebruikt = gebruikt - 1 WHERE code = '$code';"; // lower gebruikt by 1
	if (!$conn->query($sql)) {
		echoResponse("error", $sql.$conn->error);
	}
	echoResponse("success", "code has been deleted");
} else if (substr($code, -2 , -1) === ".") {
	if (!isset($_POST['datein']) || !isset($_POST['aanhef']) || !isset($_POST['naam']) || !isset($_POST['pctype']) || !isset($_POST['pc']) || !isset($_POST['dateout']) || !isset($_POST['initiaal'])) {
		echoResponse('error', 'missing parameter');
	}

	$datein = addslashes($_POST['datein']);
	$aanhef = addslashes($_POST['aanhef']);
	$naam = addslashes($_POST['naam']);
	$pctype = addslashes($_POST['pctype']);
	$pc = addslashes($_POST['pc']);
	$dateout = addslashes($_POST['dateout']);
	$initiaal = addslashes($_POST['initiaal']);

	if (!$datein || !$aanhef || !$naam || !$pctype || !$pc || !$dateout || !$initiaal) {
		echoResponse('error', 'empty parameter');
	}


	$sql = "SELECT * FROM gegevens WHERE code = '$code';";
	if (!$result = $conn->query($sql)) {
		echoResponse("error", $sql.$conn->error);
	}

	if ($result->num_rows > 0) {
		$sql = "UPDATE gegevens
						SET aanhef = '$aanhef',
						naam = '$naam',
						pctype = '$pctype',
						pc = '$pc',
						dateout = '$dateout',
						initiaal = '$initiaal'
						WHERE code = '$code';";
		if (!$conn->query($sql)) {
			echoResponse("error", $sql.$conn->error);
		}

		$code = substr($code, 0 , -2);

		$sql = "UPDATE codes
						SET type = '$type',
						datein = '$datein'
						WHERE code = '$code';";
		if (!$conn->query($sql)) {
			echoResponse("error", $sql.$conn->error);
		}

		echoResponse("success", "code updated");
	} else {
		$sql = "INSERT INTO gegevens
						(code,aanhef,naam,pctype,pc,initiaal,dateout)
						VALUES
						('$code', '$aanhef', '$naam', '$pctype', '$pc', '$initiaal', '$dateout');";
		if (!$conn->query($sql)) {
			echoResponse("error", $sql.$conn->error);
		}

		echoResponse("success", "code added");
	}
}

?>