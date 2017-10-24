<?php
# Class includen
include 'classes.php';
include 'functions.php';
include 'phpmailer/class.phpmailer.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

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

echo $datum . "<br>" . $geslacht . "<br>"  . $gewicht . "<br>"  . $clubnr . "<br>"  . $graduatie . "<br>"  . $rating . "<br>";

if ($datum === "false" || $geslacht === "false" || $gewicht === "false" || $clubnr === "false" || $graduatie === "false" || $rating === "false")

	{
		echo "<br>klopt niet";
	}
else
	{
		echo "<br>Klopt";
		$lidnr = $db->insert_id("INSERT INTO IC_deelnemers (clubnr, voornaam, achternaam, tussenvoegsels, gewicht, rating, graduatie, geslacht, geboortedatum) VALUES 
												('$clubnr', '$voornaam', '$achternaam', '$tussenvoegsels', '$gewicht', '$rating', '$graduatie', '$geslacht', '$datum')");
		
		$db->run_query("INSERT INTO IC_graduaties (lidnr, kyugraad, datum) VALUES ('$lidnr', '$graduatie', '$curdate')");
		
		$db->run_query("INSERT INTO IC_gewichten (lidnr, gewicht, datum) VALUES ('$lidnr', '$gewicht', '$curdate')");
		
		$db->run_query("INSERT INTO IC_ratings (`lidnr`, `change`, `wedstrijdnr`) VALUES ('$lidnr', '$rating', '0')");
		
		if ($_POST["inschrijving"] == "true")
			{
				echo "<br>klopt ook";
				$competitiedagnr = $_POST["competitienr"];
				$seizoen = $db->get_array("SELECT * FROM IC_competities WHERE competitienr=" . $competitiedagnr);
				$lidgegevens = $db->get_array("SELECT * FROM IC_deelnemers WHERE lidnr=" . $lidnr);
				$gewichtsklasse = $db->get_array("SELECT * FROM IC_gewichtseizoen
														LEFT JOIN IC_gewichtsklassen ON (IC_gewichtseizoen.gewichtsklassenr = IC_gewichtsklassen.gewichtsklassenr)
														WHERE IC_gewichtsklassen.upperbound>'" . $lidgegevens[0]["gewicht"] . "' AND IC_gewichtseizoen.seizoennr='" . $seizoen[0]["seizoennr"] . "'
														ORDER BY IC_gewichtsklassen.upperbound asc
														LIMIT 1");
														
				// $gewichtsklassenr = $db->insert_id("INSERT INTO IC_gewichtsklassen (upperbound) VALUES ('$upperbound')");
				$db->run_query("INSERT INTO IC_pouleindeling (lidnr, poulenr, competitienr, gewichtsklassenr, plaats, volgorde) VALUES 
									('$lidnr', '0', '$competitiedagnr', '" . $gewichtsklasse[0]["gewichtsklassenr"] . "', '0', '0')");
			}
	}

	
destruct( 'db' );

// header ("location: index.php?target=deelnemers");


?>