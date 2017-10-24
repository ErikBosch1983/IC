<?php
# Class includen
include 'classes.php';
include 'functions.php';
include 'phpmailer/class.phpmailer.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );
$error = 0;

$curdate = date("Y-m-d"); 
$lidnr = check_deelnemer((int)$_POST["lidnr"]);
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

$aantal = $db->num_rows("SELECT * FROM IC_ratings WHERE lidnr=" . $lidnr);
if ($aantal == 1)
	{
		$db->run_query("UPDATE IC_deelnemers SET 
						   voornaam='" . $voornaam . "',
						   achternaam='" . $achternaam . "',
						   tussenvoegsels='" . $tussenvoegsels . "',
						   gewicht='" . $gewicht . "',
						   rating='" . $rating . "',
						   graduatie='" . $graduatie . "',
						   geslacht='" . $geslacht . "',
						   geboortedatum='" . $datum . "'
					   WHERE lidnr=" . $lidnr);
		$db->run_query("UPDATE IC_ratings SET `change`='" . $rating . "' WHERE lidnr=" . $lidnr);
	}
else
	{
				$db->run_query("UPDATE IC_deelnemers SET 
						   voornaam='" . $voornaam . "',
						   achternaam='" . $achternaam . "',
						   tussenvoegsels='" . $tussenvoegsels . "',
						   gewicht='" . $gewicht . "',
						   graduatie='" . $graduatie . "',
						   geslacht='" . $geslacht . "',
						   geboortedatum='" . $datum . "'
					   WHERE lidnr=" . $lidnr);
	}

////
// Zoeken op entry met datum vandaag. als die niet bestaan aanmaken. Anders updaten.
////

$aantal = $db->num_rows("SELECT * FROM IC_graduaties WHERE lidnr=" . $lidnr . " AND datum='" . $curdate . "'");
if ($aantal == 1)
	{
			$db->run_query("UPDATE IC_graduaties SET kyugraad='" . $graduatie . "' WHERE lidnr=" . $lidnr . " AND datum='" . $curdate . "'");
	}
else
	{
			$db->run_query("INSERT INTO IC_graduaties (lidnr, kyugraad, datum) VALUES ('$lidnr', '$graduatie', '$curdate')");
	}

$aantal = $db->num_rows("SELECT * FROM IC_gewichten WHERE lidnr=" . $lidnr . " AND datum='" . $curdate . "'");
if ($aantal == 1)
	{
		$db->run_query("UPDATE IC_gewichten SET gewicht='" . $gewicht . "' WHERE lidnr=" . $lidnr . " AND datum='" . $curdate . "'");		

	}
else
	{
		$db->run_query("INSERT INTO IC_gewichten (lidnr, gewicht, datum) VALUES ('$lidnr', '$gewicht', '$curdate')");
	}



destruct( 'db' );

header ("location: index.php?target=deelnemer&lidnr=" . $lidnr);


?>