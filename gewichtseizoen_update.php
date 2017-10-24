<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$seizoennr 			= check_seizoennr ($_GET["seizoennr"]);
$gewichtsklassenr 	= check_gewichtsklassenr ($_GET["gewichtsklassennr"]);

if ($seizoennr != "false" && $gewichtsklassenr != "false")
	{
		$aantal = $db->num_rows("SElECT * FROM IC_gewichtseizoen WHERE seizoennr='" . $seizoennr . "' AND gewichtsklassenr='" . $gewichtsklassenr . "'");
		if ($aantal == 0)
			{
				$db->run_query("INSERT INTO IC_gewichtseizoen (seizoennr, gewichtsklassenr) VALUES ('$seizoennr', '$gewichtsklassenr')");
			}
		elseif ($aantal == 1)
			{
				$db->run_query("DELETE FROM IC_gewichtseizoen WHERE seizoennr='" . $seizoennr . "' AND gewichtsklassenr='" . $gewichtsklassenr . "'");
			}
	}

destruct( 'db' );

header ("location: index.php?target=seizoenen");


?>