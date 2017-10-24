<?php
# Class includen
include 'classes.php';
include 'functions.php';
include 'phpmailer/class.phpmailer.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

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
				$emails = $db->num_rows("SELECT * FROM IC_coaches WHERE email='" . $email . "'");
				if ($emails != 1)
					{
						$error = 5;
					}
			}
	}

if ($error == 0)
	{
		////
		// Geen problemen. Wachtwoord opstellen, toevoegen in database en mail sturen met gegevens
		////
		
		// wachtwoord opstellen
		$wachtwoord = wachtwoordmaken (8);
		$hash = password_hash("$wachtwoord", PASSWORD_DEFAULT);
		
		// toevoegen in database
		$db->run_query("UPDATE IC_coaches SET wachtwoord='$hash' WHERE email='" . $_POST["email"] . "'");

		$emails = $db->get_array("SELECT * FROM IC_coaches WHERE email='" . $email . "'");

		// Versturen van email

		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		
		$body = "Beste " . $emails[0]["voornaam"] . ",\n\nU heeft een nieuw wachtwoord aangevraagd voor de site van de Interne Competitie. U kunt inloggen met de volgende gegevens:\n\nEmail-adres: " . $emails[0]["email"] . "\nWachtwoord: " . $wachtwoord . "\n\nWij adviseren het wachtwoord meteen even te veranderen in iets anders dan dit automatisch gegenereerde wachtwoord.\n\nMochten er vragen zijn over het gebruik van het systeem, neem dan even contact op met Erik Bosch.";
		
		$naam = $emails[0]["voornaam"] . " " . $emails[0]["tussenvoegsels"] . " " . $emails[0]["achternaam"];
		$mail->AddAddress($email, $naam);
		$mail->AddBCC("ehcp_bosch@hotmail.com", "Erik");
		
		$mail->Host       	= "mailout.one.com"; 
		$mail->SMTPAuth 	= true;                               // Enable SMTP authentication
		$mail->Username 	= 'bestuur@judokanblerick.nl';                 // SMTP username
		$mail->Password 	= 'BlackHeart1';                           // SMTP password
		$mail->SMTPSecure 	= 'tls';
		$mail->Port       	= 465;
		$mail->From 		= "internecompetitie@judokanblerick.nl";
		$mail->FromName 	= "Interne Competitie Noord Limburg";
		$mail->Subject 		= "Nieuw wachtwoord voor het interne competitie systeem";
		$mail->Body 		= $body;

		if(!$mail->Send())
			{
				header ("location: index.php?target=inloggen&er=4");
			}
		else
			{
				header ("location: index.php?target=inloggen");
			}
	}
else
	{
		header ("location: index.php?target=inloggen&er=" . $error);
	}
destruct( 'db' );




?>