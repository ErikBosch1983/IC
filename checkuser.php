<?php
include 'classes.php';

// Database verbinding opzetten.
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

session_start();

$email = $_POST['email'];
$password = $_POST['wachtwoord'];

$coaches = $db->get_array("SELECT * FROM IC_coaches WHERE email='" . $email . "'");
$aantal = $db->num_rows("SELECT * FROM IC_coaches WHERE email='" . $email . "'");

if ($aantal == "1")
	{
		if (password_verify($password, $coaches[0]["wachtwoord"]))
			{
				if ($coaches[0]["actief"] == 1)
					{
						$_SESSION['IClogin'] = "true";
						$_SESSION['ICusernr'] = $coaches[0][coachnr];
						$_SESSION['clubnr'] = $coaches[0][clubnr];
						header("location: index.php");
					}
				else
					{
						$_SESSION['IClogin'] = "false";
						$_SESSION['ICusernr'] = "0";
						header("location: index.php?er=3");
					}
			}
		else
			{
				$_SESSION['IClogin'] = "false";
				$_SESSION['ICusernr'] = "0";
				header("location: index.php?er=2");
			}

	}
else
	{
		$_SESSION['IClogin'] = "false";
		$_SESSION['ICusernr'] = "0";
		header("location: index.php?er=1");
	}

?>

