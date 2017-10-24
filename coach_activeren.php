<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$coachnr 	= check_clubnr ($_GET["coachnr"]);

if ($coachnr != "false")
	{
		$coach = $db->get_array("SELECT * FROM IC_coaches WHERE coachnr=" . $coachnr);
		if ($coach[0][actief] == 0)
			{
				$db->run_query("UPDATE IC_coaches SET actief='1' WHERE coachnr=" . $coachnr);
			}
		else
			{
				$db->run_query("UPDATE IC_coaches SET actief='0' WHERE coachnr=" . $coachnr);
			}
		
	}

destruct( 'db' );

header ("location: index.php?target=clubgegevens");


?>