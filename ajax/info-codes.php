<?php

require 'dbh.php';

if (!isset($_GET['type'])) echoResponse('error', 'missing parameter');

$type = addslashes($_GET['type']);

if (!$type) echoResponse('error', 'empty parameter');

if(isset($_GET['search'])) { // search for codes
  $search = addslashes($_GET['search']);

  if (strlen($search) < 3) echoResponse('error', 'search query too short');

	$sql = "SELECT g.code, c.type, g.aanhef, g.naam, g.pctype, g.pc, g.dateout, c.ongeldig, g.updated
          FROM gegevens g 
          INNER JOIN codes c 
          ON c.code = SUBSTRING(g.code, 1, CHAR_LENGTH(g.code) - 2) 
          WHERE c.type LIKE '$type%' AND
          (g.naam LIKE '%$search%' OR 
          g.pc LIKE '%$search%' OR
          g.code LIKE '%$search%')
          ORDER BY g.updated DESC;";
} else if (isset($_GET['since'])) {
  $since = addslashes($_GET['since']);

  $sql = "SELECT g.code, c.type, g.aanhef, g.naam, g.pctype, g.pc, g.dateout, c.ongeldig, g.updated
          FROM gegevens g 
          INNER JOIN codes c 
          ON c.code = SUBSTRING(g.code, 1, CHAR_LENGTH(g.code) - 2) 
          WHERE (
            c.type LIKE '$type%' 
            AND g.updated < '$since'
          )
          ORDER BY g.updated DESC
          LIMIT 50;";
} else { // get 50 last updated codes
  $sql = "SELECT g.code, c.type, g.aanhef, g.naam, g.pctype, g.pc, g.dateout, c.ongeldig, g.updated
          FROM gegevens g 
          INNER JOIN codes c 
          ON c.code = SUBSTRING(g.code, 1, CHAR_LENGTH(g.code) - 2) 
          WHERE c.type LIKE '$type%' 
          ORDER BY g.updated DESC
          LIMIT 50;";
}

if (!$codes = $conn->query($sql)) echoResponse("error", $sql.$conn->error);

if ($codes->num_rows > 0) {
  $data = array();

  while($row = $codes->fetch_assoc()) $data[] = array(
    'code' => $row['code'],
    'type' => $row['type'],
    'aanhef' => $row['aanhef'],
    'naam' => $row['naam'],
    'pctype' => $row['pctype'],
    'pc' => $row['pc'],
    'dateout' => $row['dateout'],
    'updated' => $row['updated'],
    'ongeldig' => $row['ongeldig']
  );

  echoResponse('success', $data);
} else {
  echoResponse('error', "no results");
}

?>