<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$poulenr 	= check_poulenr ($_POST["poulenr"]);
$mat		= (int)$_POST["matnr"];

if ($poulenr != "false" && ($mat == 1 || $mat == 2 || $mat == 3 || $mat == 4))
	{
		$poule = $db->get_array("SELECT * FROM IC_poules WHERE poulenr=" . $poulenr);
		if ($poule[0]["poulestatus"] == 1)
			{
				$db->run_query("UPDATE IC_poules SET poulestatus='2', mat='" . $mat . "' WHERE poulenr='" . $poulenr . "'");
			}
		else
			{
				header ("location: index.php");
			}
	}
else
	{
		header ("location: index.php");
	}


destruct( 'db' );

header ("location: index.php?target=competitiedag&competitiedagnr=" . $poule[0]["competitienr"]);


?>