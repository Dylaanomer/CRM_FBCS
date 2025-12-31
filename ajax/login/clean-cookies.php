<?php 

include 'dbh.php';

$now = new DateTime();
$now = $now->format('Y-m-d H:i:s');

$sql = "DELETE FROM cookies WHERE expires < '$now';";
if (!$conn->query($sql)) {//log sql errors
  echo "error: ".$sql."\n".$conn->error."\n";
} else {
  echo "success: removed all cookies that expired before $now";
}

$conn->close;
exit;

?>