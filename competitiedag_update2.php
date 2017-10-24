<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$competitiedagnr = check_competitiedagnr ($_POST["competitiedagnr"]);

$competitiedag = $db->get_array("SELECT * FROM IC_competities LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr) WHERE IC_competities.competitienr=" . $competitiedagnr);

if ($competitiedag[0][status] == 1 && $competitiedag[0][dagstatus] == 0)
	{
		echo "UPDATE IC_competities SET dagstatus='1' WHERE competitienr=" . $competitiedagnr;
		$db->run_query("UPDATE IC_competities SET dagstatus='1' WHERE competitienr=" . $competitiedagnr);
		header ("location: index.php?target=competitiedag&competitiedagnr=" . $competitiedagnr);
	}
else
	{
		header ("location: index.php?target=competitiedag&competitiedagnr=" . $competitiedagnr);
	}


destruct( 'db' );

?>