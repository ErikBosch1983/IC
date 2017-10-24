<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$poulenr 	= check_poulenr ($_POST["poulenr"]);
$volgorde	= (int)$_POST["volgorde"];
$score		= $_POST["score"];

if ($poulenr != "false")
	{
		if ($score == "wit10" || $score == "wit7" || $score == "wit1" || $score == "rood10" || $score == "rood7" || $score == "rood1")
			{
				$size = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE poulenr=" . $poulenr);
				$match = $db->get_array("SELECT * FROM IC_matches WHERE size='" . $size . "' AND volgorde='" . $volgorde . "'");
				
				$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden WHERE poulenr='" . $poulenr . "' AND volgorde='" . $volgorde . "'");
				if (empty($wedstrijden))
					{
						$wedstrijdnr = $db->insert_id("INSERT INTO IC_wedstrijden (poulenr, volgorde) VALUES ('$poulenr', '$volgorde')");
						
						$spelerrood = $db->get_array("SELECT * FROM IC_pouleindeling WHERE poulenr='" . $poulenr . "' AND volgorde='" . $match[0]["spelerrood"] . "'");
						$spelerwit = $db->get_array("SELECT * FROM IC_pouleindeling WHERE poulenr='" . $poulenr . "' AND volgorde='" . $match[0]["spelerwit"] . "'");
						
						if ($score == "wit10" || $score == "wit7" || $score == "wit1")
							{
								if ($score == "wit10")
									{
										$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerwit[0]["lidnr"] . "', '$wedstrijdnr', '10', '0')");
									}
								if ($score == "wit7")
									{
										$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerwit[0]["lidnr"] . "', '$wedstrijdnr', '7', '0')");
									}
								if ($score == "wit1")
									{
										$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerwit[0]["lidnr"] . "', '$wedstrijdnr', '1', '0')");
									}
								$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerrood[0]["lidnr"] . "', '$wedstrijdnr', '0', '1')");
							}
						else
							{
								if ($score == "rood10")
									{
										$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerrood[0]["lidnr"] . "', '$wedstrijdnr', '10', '1')");
									}
								if ($score == "rood7")
									{
										$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerrood[0]["lidnr"] . "', '$wedstrijdnr', '7', '1')");
									}
								if ($score == "rood1")
									{
										$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerrood[0]["lidnr"] . "', '$wedstrijdnr', '1', '1')");
									}
								$db->run_query("INSERT INTO IC_wedstrijdlid (lidnr, wedstrijdnr, uitslag, roodwit) VALUES ('" . $spelerwit[0]["lidnr"] . "', '$wedstrijdnr', '0', '0')");
							}
						
					}
				
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

header ("location: index.php?target=poule&poulenr=" . $poulenr);


?>