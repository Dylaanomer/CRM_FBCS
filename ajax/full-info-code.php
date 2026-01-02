<?php

/* SHOW ALL PHP ERRORS */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'dbh.php';

$code = addslashes($_GET['code']);

$sql = "SELECT g.code, c.type, c.datein, g.aanhef, g.naam, g.pctype, g.pc, g.initiaal, g.dateout, g.editing, c.ongeldig, c.aantal, c.initiaal AS initiaalCode
				FROM gegevens g
				INNER JOIN codes c ON
				c.code LIKE SUBSTRING(g.code, 1, CHAR_LENGTH(g.code) - 2)
				WHERE g.code = '$code';";

if (!$result = $conn->query($sql)) {
  echoResponse('error', $sql.$conn->error);
}

if ($result->num_rows > 0) {
	$row = $result->fetch_assoc();

	$arr = array('code' => $row['code'],
                  'type' => $row['type'],
                  'datein' => $row['datein'],
									'aanhef' => $row['aanhef'],
									'naam' => $row['naam'],
									'pctype' => $row['pctype'],
									'pc' => $row['pc'],
									'initiaal' => $row['initiaal'],
									'dateout' => $row['dateout'],
									'editing' => $row['editing'],
									'ongeldig' => $row['ongeldig'],
									'aantal' => $row['aantal'],
									'initiaalCode' => $row['initiaalCode']);

	echoResponse('success', $arr);
} else {
  echoResponse('error', 'no results');
}

?>