<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$dag 		= $_POST["dag"];
$maand 		= $_POST["maand"];
$jaar 		= $_POST["jaar"];
$clubnr 	= check_clubnr ($_POST["clubnr"]);
$locatienr 	= $_POST["locatienr"];
$competitiedagnr = check_competitiedagnr ($_POST["competitiedagnr"]);

$datum = datumbouwer ($dag, $maand, $jaar);

if ($datum == "false" || $clubnr == "false" || $seizoennr == "false" || $locatienr == "")
	{
		header ("location: index.php?target=home");
	}
else
	{
		if ($locatienr == 0)
			{
				$naam 		= clean ($_POST["naam"]); 
				$adres 		= clean ($_POST["adres"]);
				$postcode 	= clean ($_POST["postcode"]);
				$plaats 	= clean ($_POST["plaats"]);
				$locatienr = $db->insert_id("INSERT INTO IC_locaties (naam, adres, postcode, plaats) VALUES ('$naam', '$adres', '$postcode', '$plaats')");
			}
		$db->run_query("UPDATE IC_competities SET datum='$datum', locatienr='$locatienr', clubnr='$clubnr' WHERE competitienr=" . $competitiedagnr);
	}


destruct( 'db' );

header ("location: index.php?target=competitiedag&competitiedagnr=" . $competitiedagnr);


?>