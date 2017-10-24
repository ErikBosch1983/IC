<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$coachnr 	= check_clubnr ($_POST["coachnr"]);

if ($coachnr != "false")
	{
		$coaches = $db->get_array("SELECT wachtwoord FROM IC_coaches WHERE coachnr=" . $coachnr);
		if (password_verify($_POST["wachtwoord1"], $coaches[0]["wachtwoord"]))
			{
				if ($_POST["wachtwoord2"] == $_POST["wachtwoord3"])
					{
						$hash = password_hash($_POST["wachtwoord2"], PASSWORD_DEFAULT);
						$db->run_query("UPDATE IC_coaches SET wachtwoord='$hash' WHERE coachnr=" . $coachnr);
					}
				else
					{
						header ("location: index.php?target=eigengegevens&err=4");
					}
			}
		else
			{
				header ("location: index.php?target=eigengegevens&err=3");
			}
		
	}

destruct( 'db' );

header ("location: index.php?target=eigengegevens");


?>