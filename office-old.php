<?php

require 'ajax/login/cookie.php';

$valid_cookie = json_decode(checkLoginCookie());

if ($valid_cookie->{"status"} !== "success") {
	require 'login-page.php';
	echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		  <script src="js/login.js"></script>';
	exit;
}

include 'dbh.php';

$search = $_POST["search"];

if(isset($_POST["zoek"])) {
	$sql = "SELECT code, type, klant, datein, dateout FROM office_oud WHERE klant LIKE '%$search%' ORDER BY dateout DESC LIMIT 50;";
	$result = $conn->query($sql);

} else {
	$sql = "SELECT code, type, klant, datein, dateout FROM office_oud ORDER BY dateout DESC LIMIT 50;";
	$result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html>
<head>
<title> Oude Office Codes </title>
<link rel="icon" type="image/png" href="img/favicon.png">
<!-- STYLESHEETS -->
<link rel="stylesheet" type="text/css" href="css/shared.css">
<link rel="stylesheet" type="text/css" href="css/form-avast.css">
<!-- FONTS -->
<link href="https://fonts.googleapis.com/css?family=Baloo" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Francois+One&display=swap" rel="stylesheet">
<!-- font table -->
<link href="https://fonts.googleapis.com/css?family=Blinker&display=swap" rel="stylesheet">
</head>

<body>
<div id="nav">
	<a class="nav" href="index.php"> <img src="img/fbcs.png" alt="kan het bestand niet vinden" id="logo"/> </a>
	<ul class="nav">
		<li class="nav"> <a class="nav" href="index.php"> Codes </a> </li>
		<li class="nav"> <a class="nav" href="avast-old.php"> Avast oud </a> </li>
		<li class="nav"> <a class="nav active" href="office-old.php"> Office oud </a> </li>
		<li> <div class="login"> Welkom, <?php echo htmlspecialchars($_COOKIE["name"]); ?> <br>
			<a id="logout" class="login" href="login/logout.php" class="btn btn-danger">Log uit</a>
			</li>
	</ul>
</div>

<div id="center">
<p class="green"> <?php echo $succes ?> </p>
<form method="post" class="form1">
		<h1> Zoeken </h1>
			<ul>
				<li>
					<input class="forcode field-style" maxlength="50" type="text" name="search" placeholder="Zoeken" required /> 
				</li><li>
					<input type="submit" name="zoek" value="Zoeken" />
				</li>
			</ul>
		</form>
<table>

	<colgroup>
    <col style="width:40%">
    <col style="width:10%">
    <col style="width:30%">
    <col style="width:10%">
    <col style="width:10%">
	</colgroup>

	<tr>
		<th>Code</th>
		<th>Type</th>
		<th>Klant</th>
		<th>Gekocht</th>
		<th>Gebruikt</th>
	</tr>

	<?php

		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				echo '<tr><td>
						<div class="bold padding"> ' . $row["code"]. ' </div>
						</td><td>
						<div class="padding">' . $row["type"]. ' </div>
						</td><td>
						<div class="padding">' . $row["klant"]. ' </div>
						</td><td>
						<div class="padding">' . $row["datein"]. ' </div>
						</td><td>
						<div class="padding">' . $row["dateout"]. ' </div>
					</td></tr>';
			}
			echo "</table>";
		} else {
			echo "0 results";
		}
		$conn->close();

?>

</div>
</div>

<div id="footer">
	Developed by Luc
</div>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="js/main.js"></script>

</html>