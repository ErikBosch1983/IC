<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$lidnr 				= check_deelnemer($_GET["lidnr"]);
$competitienr 	= check_competitiedagnr ($_GET["competitienr"]);
$gewichtsklassenr 	= check_gewichtsklassenr ($_GET["gewichtsklassenr"]);

if ($lidnr != "false" && $competitienr != "false" && $gewichtsklassenr != "false" && ($_GET["richting"] == "up" || $_GET["richting"] == "down"))
	{
		$huidigepoule = $db->get_array("SELECT * FROM IC_pouleindeling WHERE competitienr='" . $competitienr . "' AND lidnr=" . $lidnr);
		$poule1 = $huidigepoule[0]["poulenr"];

		$lagerepoules = $db->get_array("SELECT * FROM IC_poules
											RIGHT JOIN IC_pouleindeling ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
											WHERE IC_poules.poulenr<'" . $poule1 . "'
												AND IC_pouleindeling.competitienr='" . $competitienr . "'
												AND IC_poules.poulestatus='1'
												AND IC_poules.gewichtsklassenr='" . $gewichtsklassenr . "' 
											GROUP BY IC_poules.poulenr
											Having COUNT(IC_pouleindeling.volgorde)<5
											ORDER BY IC_poules.poulenr DESC
											LIMIT 1");
		$hogerepoules = $db->get_array("SELECT * FROM IC_poules
											RIGHT JOIN IC_pouleindeling ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
											WHERE IC_poules.poulenr>'" . $poule1 . "'
												AND IC_pouleindeling.competitienr='" . $competitienr . "'
												AND IC_poules.poulestatus='1'
												AND IC_poules.gewichtsklassenr='" . $gewichtsklassenr . "' 
											GROUP BY IC_poules.poulenr
											Having COUNT(IC_pouleindeling.volgorde)<5
											ORDER BY IC_poules.poulenr ASC
											LIMIT 1");
		if ($_GET["richting"] == "down")
			{
				$poule2 = $lagerepoules[0]["poulenr"];
			}
		if ($_GET["richting"] == "up")
			{
				$poule2 = $hogerepoules[0]["poulenr"];
			}
		$db->run_query("UPDATE IC_pouleindeling SET poulenr='" . $poule2 . "' WHERE lidnr='" . $lidnr . "' AND competitienr='" . $competitienr . "'");
		
		////
		// Herstellen volgordes van de poules
		////
		
		$oudepoule = $db->get_array("SELECT * FROM IC_pouleindeling WHERE poulenr='" . $poule1 . "' ORDER BY volgorde ASC");
		$i = 1;
		foreach ($oudepoule as $oudlid)
			{
				$db->run_query("UPDATE IC_pouleindeling SET volgorde='" . $i . "' WHERE indelingnr='" . $oudlid["indelingnr"] . "'");
				$i = $i + 1;
			}
		$nieuwepoule = $db->get_array("SELECT * FROM IC_pouleindeling WHERE poulenr='" . $poule2 . "' ORDER BY volgorde ASC, indelingnr ASC");
		$i = 1;
		foreach ($nieuwepoule as $nieuwlid)
			{
				$db->run_query("UPDATE IC_pouleindeling SET volgorde='" . $i . "' WHERE indelingnr='" . $nieuwlid["indelingnr"] . "'");
				$i = $i + 1;
			}
		header ("location: index.php?target=competitiedag&competitiedagnr=" . $competitienr);
	}
else
	{
		header ("location: index.php");
	}

	
destruct( 'db' );

?>