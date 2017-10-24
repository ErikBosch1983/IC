<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$voornaam = clean($_POST["voornaam"]);
$achternaam = clean($_POST["achternaam"]);
$tussenvoegsels = clean($_POST["tussenvoegsels"]);
$coachnr 	= check_clubnr ($_POST["coachnr"]);

if ($coachnr != "false")
	{
		$db->run_query("UPDATE IC_coaches SET voornaam='$voornaam', achternaam='$achternaam', tussenvoegsels='$tussenvoegsels' WHERE coachnr=" . $coachnr);
	}

destruct( 'db' );

header ("location: index.php?target=eigengegevens");


?>