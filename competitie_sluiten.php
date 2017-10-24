<?php
# Class includen
include 'classes.php';
include 'functions.php';
include 'phpmailer/class.phpmailer.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );


$competitienr = check_competitiedagnr ($_POST["competitienr"]);

if ($competitienr != "false")
	{
		$poulecontrole = $db->num_rows("SELECT * FROM IC_poules WHERE poulestatus>'0' AND poulestatus<'3' AND competitienr='" . $competitienr . "'");
		$inschrijfcontrole = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE competitienr='" . $competitienr . "' AND plaats='0'");
		$competitie = $db->get_array("SELECT kfactor, dagstatus FROM IC_competities WHERE competitienr=" . $competitienr);
		if ($poulecontrole == 0 && $inschrijfcontrole == 0 && $competitie[0]["dagstatus"] == 1)
			{
				echo "	Alle poules zijn gespeeld en alle judoka's verdeeld<br><br>";
				$wedstrijden = $db->get_array("
						SELECT * FROM IC_wedstrijden
							LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_wedstrijden.poulenr)
							LEFT JOIN IC_gewichtsklassen ON (IC_poules.gewichtsklassenr = IC_gewichtsklassen.gewichtsklassenr)
							LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
							WHERE IC_poules.competitienr='" . $competitienr . "'
							ORDER BY IC_gewichtsklassen.upperbound ASC, IC_poules.poulenr ASC");
				foreach ($wedstrijden as $wedstrijd)
					{
						$spelerrood = $db->get_array("SELECT * FROM IC_wedstrijdlid
													 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_wedstrijdlid.lidnr)
														LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
														WHERE IC_wedstrijdlid.roodwit='1' AND IC_wedstrijdlid.wedstrijdnr='" . $wedstrijd["wedstrijdnr"] . "'");
						$spelerwit = $db->get_array("SELECT * FROM IC_wedstrijdlid
													 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_wedstrijdlid.lidnr)
														LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
														WHERE IC_wedstrijdlid.roodwit='0' AND IC_wedstrijdlid.wedstrijdnr='" . $wedstrijd["wedstrijdnr"] . "'");
						$machtwit = $spelerwit[0]["rating"]/400;
						$machtrood = $spelerrood[0]["rating"]/400;
						$powerwit = round(pow(10, $machtwit), 0);
						$powerrood = round(pow(10, $machtrood), 0);
						$winkansrood = round ($powerrood/($powerwit + $powerrood), 2);
						$winkanswit = round ( $powerwit/($powerwit + $powerrood), 2);
						if ($spelerwit[0]["uitslag"] > 0)
							{
								$scorewit = 1;
							}
						else
							{
								$scorewit = 0;
							}
						if ($spelerrood[0]["uitslag"] > 0)
							{
								$scorerood = 1;
							}
						else
							{
								$scorerood = 0;
							}	
						
						$changewit =  round(($scorewit - $winkanswit) * $competitie[0]["kfactor"], 1);
						$changerood =  round(($scorerood - $winkansrood) * $competitie[0]["kfactor"],1);
						echo "-" . $wedstrijd["upperbound"] . " Wit: " . naam($spelerwit[0]["lidnr"]) . " rating: " . $spelerwit[0]["rating"] . " 
							winkans: " . $machtwit . $powerwit . " -- " . $winkanswit . " -- " . $spelerwit[0]["uitslag"] . "/". $scorewit . " -- " . $changewit ."<br>
							&nbsp;&nbsp;&nbsp&nbsp; Rood: " . naam($spelerrood[0]["lidnr"]) . " rating: " . $spelerrood[0]["rating"] . " 
							winkans: " . $machtrood . $powerrood . " -- " . $winkansrood . " -- " . $spelerrood[0]["uitslag"] . "/". $scorerood . " -- " . $changerood ."<br><br>";
				//		$db->run_query("INSERT INTO IC_ratings (`lidnr`, `change`, `wedstrijdnr`) VALUES ('" . $spelerwit[0]["lidnr"] . "', '". $changewit ."', '". $wedstrijd["wedstrijdnr"] ."')");
				//		$db->run_query("INSERT INTO IC_ratings (`lidnr`, `change`, `wedstrijdnr`) VALUES ('" . $spelerrood[0]["lidnr"] . "', '". $changerood ."', '". $wedstrijd["wedstrijdnr"] ."')");
					}
				echo "<br><br>";
				$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling WHERE competitienr=" . $competitienr);
				foreach ($deelnemers as $deelnemer)
					{
						echo naam($deelnemer["lidnr"]) . "<br>";
						$ratings = $db->get_array("SELECT `change` FROM IC_ratings  WHERE lidnr='" . $deelnemer["lidnr"] . "' ORDER BY wedstrijdnr ASC");
						$totaal = 0;
						foreach ($ratings as $rating)
							{
								echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $rating["change"] . "<br>";
								$totaal = $totaal + $rating["change"];
							}
						echo "&nbsp;&nbsp;&nbsp" . $totaal . " --- " . $rating["SUM(change)"] . "<br><br>";
						$db->run_query("UPDATE IC_deelnemers SET rating='" . $totaal . "' WHERE lidnr='" . $deelnemer["lidnr"] . "'");
					}
				$db->run_query("UPDATE IC_competities SET dagstatus='2' WHERE competitienr=" . $competitienr);
			}
		
	}
else
	{
		header ("location: index.php");
	}

?>