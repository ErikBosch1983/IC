<?php
# Class includen
include 'classes.php';
include 'functions.php';
include 'phpmailer/class.phpmailer.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );
$error = 0;


$curdate = date("Y-m-d"); 
$voornaam = clean($_POST["voornaam"]);
$achternaam = clean($_POST["achternaam"]);
$tussenvoegsels = clean($_POST["tussenvoegsels"]);
$dag 		= $_POST["dag"];
$maand 		= $_POST["maand"];
$jaar 		= $_POST["jaar"];
$datum = datumbouwer ($dag, $maand, $jaar);
$geslacht = check_geslacht ($_POST["geslacht"]);
$gewicht = check_gewicht ($_POST["gewicht"]);
$clubnr = check_clubnr ($_POST["clubnr"]);
$graduatie = check_graduatie ($_POST["graduatie"]);
$rating = check_rating ((int)$_POST["rating"]);

$lidnr = $db->insert_id("INSERT INTO IC_deelnemers (clubnr, voornaam, achternaam, tussenvoegsels, gewicht, rating, graduatie, geslacht, geboortedatum) VALUES 
										('$clubnr', '$voornaam', '$achternaam', '$tussenvoegsels', '$gewicht', '$rating', '$graduatie', '$geslacht', '$datum')");

$db->run_query("INSERT INTO IC_graduaties (lidnr, kyugraad, datum) VALUES ('$lidnr', '$graduatie', '$curdate')");

$db->run_query("INSERT INTO IC_gewichten (lidnr, gewicht, datum) VALUES ('$lidnr', '$gewicht', '$curdate')");

$db->run_query("INSERT INTO IC_ratings (`lidnr`, `change`, `wedstrijdnr`) VALUES ('$lidnr', '$rating', '0')");


destruct( 'db' );

header ("location: index.php?target=clubdeelnemers");


?>