<?php
# Class includen
include 'classes.php';
include 'functions.php';
include 'phpmailer/class.phpmailer.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );
$error = 0;

$voornaam = clean($_POST["voornaam"]);
$achternaam = clean($_POST["achternaam"]);
$tussenvoegsels = clean($_POST["tussenvoegsels"]);

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
				$emails = $db->num_rows("SELECT email FROM IC_coaches WHERE email='" . $email . "'");
				if ($emails != 0)
					{
						$error = 5;
					}
			}
	}
$clubnr = (int)$_POST["clubnr"];
if ($clubnr == "false")
	{
		$error = 3;
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
		
		$db->run_query("INSERT INTO IC_coaches (clubnr, voornaam, achternaam, tussenvoegsels, email, wachtwoord, actief, admin) VALUES 
												('$clubnr', '$voornaam', '$achternaam', '$tussenvoegsels', '$email', '$hash', '1', '0')");
		
		// Versturen van email

		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		
		$body = "Beste " . $voornaam . ",\n\nU bent aangemeld bij het administratiesysteem van de Interne Competitie Noord-Limburg. In dit systeem kunt u gegevens van uw club aanpassen, deelnemers toevoegen, resultaten bekijekn enz enz.\nHet systeem is te vinden op http://www.judokanblerick.nl/IC\n\nU kunt inloggen met de volgende gegevens:\n\nEmail-adres: " . $email . "\nWachtwoord: " . $wachtwoord . "\n\nWij adviseren het wachtwoord meteen even te veranderen in iets anders dan dit automatisch gegenereerde wachtwoord.\n\nMochten er vragen zijn over het gebruik van het systeem, neem dan even contact op met Erik Bosch.";
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
		$mail->Subject 		= "Inloggegevens voor het interne competitie systeem";
		$mail->Body 		= $body;

		if(!$mail->Send())
			{
				header ("location: index.php?target=clubs&er=4");
			}
		else
			{
				header ("location: index.php?target=clubs");
			}
		
	}
else
	{
		header ("location: index.php?target=clubs&er=" . $error);
	}
destruct( 'db' );




?>