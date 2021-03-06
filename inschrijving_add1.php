<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$lidnr = check_deelnemer($_POST["lidnr"]);
$competitiedagnr = check_competitiedagnr ($_POST["competitienr"]);

if ($lidnr != "false" || $competitiedagnr != "false") 
	{
		$controle = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE lidnr='" . $lidnr . "' AND competitienr='" . $competitiedagnr . "'");
		if ($controle == 0)
			{		
				$seizoen = $db->get_array("SELECT * FROM IC_competities WHERE competitienr=" . $competitiedagnr);
				$lidgegevens = $db->get_array("SELECT * FROM IC_deelnemers WHERE lidnr=" . $lidnr);
				$gewichtsklasse = $db->get_array("SELECT * FROM IC_gewichtseizoen
														LEFT JOIN IC_gewichtsklassen ON (IC_gewichtseizoen.gewichtsklassenr = IC_gewichtsklassen.gewichtsklassenr)
														WHERE IC_gewichtsklassen.upperbound>'" . $lidgegevens[0]["gewicht"] . "' AND IC_gewichtseizoen.seizoennr='" . $seizoen[0]["seizoennr"] . "'
														ORDER BY IC_gewichtsklassen.upperbound asc
														LIMIT 1");
														
				// $gewichtsklassenr = $db->insert_id("INSERT INTO IC_gewichtsklassen (upperbound) VALUES ('$upperbound')");
				$db->run_query("
								INSERT INTO IC_pouleindeling (lidnr, poulenr, competitienr, gewichtsklassenr, plaats, volgorde) 
								VALUES ('$lidnr', '0', '" . $competitiedagnr . "', '" . $gewichtsklasse[0]["gewichtsklassenr"] . "', '0', '0')");
			}
	}
	
destruct( 'db' );

header ("location: index2.php");


?>