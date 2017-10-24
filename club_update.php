<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$clubnr 	= check_clubnr ($_POST["clubnr"]);
$clubnaam	= clean ($_POST["clubnaam"]);


if ($clubnr != "false")
	{
		$db->run_query("UPDATE IC_clubs SET clubnaam='$clubnaam' WHERE clubnr=" . $clubnr);
	}

destruct( 'db' );

header ("location: index.php?target=clubgegevens");


?>