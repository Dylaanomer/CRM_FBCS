<?php

/* SHOW ALL PHP ERRORS */
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

require 'ajax/login/cookie.php';

// $valid_cookie = json_decode(checkLoginCookie());

// if ($valid_cookie->{"status"} !== "success") {
//   require 'login-page.php';
//   echo '<script>console.error(' . $valid_cookie->{"msg"} . ' );</script>
//   		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
//         <script src="js/login.js"></script>';
//   exit;
// }
?>

<!DOCTYPE html>
<html>
<head>
<title>CRM FBCS BEHEER</title>
<link rel="icon" type="image/png" href="img/favicon.png">
<!-- STYLESHEETS -->
<link rel="stylesheet" type="text/css" href="css/shared.css">
<link rel="stylesheet" type="text/css" href="css/form.css">
<link rel="stylesheet" type="text/css" href="css/codes-table.css">
<link rel="stylesheet" type="text/css" href="css/filter.css">
<link rel="stylesheet" type="text/css" href="css/popups.css">
<link rel="stylesheet" type="text/css" href="css/use-code.css">
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
		<li class="nav"> <a class="nav" href="index-onderhoud.php"> Onderhoud </a> </li>
		<li class="nav"> <a class="nav" href="index-pc.php"> Nieuwe PC klaarmaken </a> </li>
		<li class="nav"> <a class="nav" href="klantverzoeken.php"> Klant Verzoeken </a></li>
		<li class="nav"> <a class="nav active" href="index-admin.php">Beheer</a> </li>
		<li> <div class="login"> Welkom, <?php echo htmlspecialchars($_COOKIE["name"]); ?> <br>
			<a id="logout" class="login" href="login/logout.php">Uitloggen</a>
			</li>
	</ul>
</div>

<div id="popupBG">
	<div id="popup"> </div>
</div>

<div id="center">
	<div id="usecodediv">
		<div class="usecode">
			<label for="type">Beheer</label>
		</div>
	</div>


	<div id="filter">
		<div class="filter-search">
			<input type="text" id="search-code" name="search" class="search" placeholder="Zoeken">
		</div>
	</div>

	<div id="codes">
		<div class="codes-header">
			<div>Code</div>
			<div>Klant</div>
			<div>PC</div>
			<div>Datum</div>
		</div>
		<div> <div> laden... </div> </div>
	</div>

<button id="loadmore"> Laad meer Onderhoudjes </button>

<!---	<h1> Verwijder code uit database </h1>
	<form method="post">
		<input class="remove" type="text" name="code" placeholder="voer code in"/><br>
	<input type="submit" name="remove" value="Verwijder"/>
	</form>-->
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="js/main.js"></script>
<script src="js/render-codes.js"></script>
<script src="js/edit-code.js"></script>
<script src="js/add-code.js"></script>
<script src="js/use-code.js"></script>
<script src="js/user-options.js"></script>

</html>