<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$wedstrijdnr 	= check_wedstrijdnr ($_GET["wedstrijdnr"]);

if ($wedstrijdnr != "false")
	{
		$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden
										LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_wedstrijden.poulenr)
										WHERE IC_wedstrijden.wedstrijdnr='" . $wedstrijdnr . "'");
		if ($wedstrijd[0]["poulestatus"] != 3)
			{
				echo "poule mag aangepast worden.";
				$db->run_query("DELETE FROM IC_wedstrijden WHERE wedstrijdnr='" . $wedstrijdnr . "'");
				$db->run_query("DELETE FROM IC_wedstrijdlid WHERE wedstrijdnr='"  . $wedstrijdnr . "'");
				header ("location: index.php?target=poule&poulenr=" . $wedstrijd[0]["poulenr"]);
			}
										
	}
	

?>