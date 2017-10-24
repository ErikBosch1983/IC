<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$coachnr 	= check_clubnr ($_POST["coachnr"]);

$error = 0;

if ($coachnr != "false")
	{
		if (empty($_POST["email"]))
			{
				$error = 1;
			}
		else
			{
				$email = $_POST["email"];
				if (!filter_var($email, FILTER_VALIDATE_EMAIL))
					{
						$error = 2;
					}
				else
					{
						$emails = $db->num_rows("SELECT email FROM IC_coaches WHERE email='" . $email . "' AND coachnr<>" . $coachnr);
						if ($emails != 0)
							{
								$error = 5;
							}
						else
							{
								$db->run_query("UPDATE IC_coaches SET email='$email' WHERE coachnr=" . $coachnr);
							}
					}
			}
	}

destruct( 'db' );
if ($error != 0)
	{
		header ("location: index.php?target=eigengegevens&err=" . $error);
	}
else
	{
		header ("location: index.php?target=eigengegevens");
	}


?>