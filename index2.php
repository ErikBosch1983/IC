<?php
include "functions.php";
include "classes.php";
include "header.php";
setlocale(LC_ALL, 'nl_NL');

$errorberichten = array (1 => "Mail adres is onbekend", 2 => "Het ingegeven wachtwoord is onjuist." ,3 => "Uw inlog rechten zijn door iemand van uw club op non-actief gezet.");
$errorbericht = (int)$_GET["er"];
							
$target = "content/" . check_redirect ($_GET["target"]) . ".php";

session_start();

if (@$_SESSION['IClogin'] == "true")
	{
		if ($_SESSION['ICusernr'] == 31)
			{
				$inlog = $_SESSION['ICusernr'];
				
				$user = $db->get_array("SELECT * FROM IC_coaches LEFT JOIN IC_clubs ON (IC_coaches.clubnr = IC_clubs.clubnr) WHERE IC_coaches.coachnr=" . $inlog);
				if ($user[0][tussenvoegsels] == "")
					{
						$usernaam = $user[0][voornaam] . " " .$user[0][achternaam];
					}
				else
					{
						$usernaam = $user[0][voornaam] . " " .$user[0][tussenvoegsels] . " " .$user[0][achternaam];
					}
				$competitiedag = $db->get_array(
					"SELECT * FROM IC_competities
						LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)
						LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
						LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
					WHERE IC_competities.dagstatus='1'");
				$aantalcompetitiedag = $db->num_rows(
					"SELECT * FROM IC_competities
						LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)
						LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
						LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
					WHERE IC_competities.dagstatus='1'");
				echo "
						<html>
						<head>
							<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
							<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />
							<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />
							<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
							<script type=\"text/javascript\" src=\"menu_jquery.js\"></script>
							<title>Interne competitie Noord-Limburg</title>
						</head>
						<body>
							<div id=\"container\">
								<div id=\"header\">
									<img src=\"afbeeldingen/logo-ic.png\" style=\"float: left; padding-right: 10px; display: block; position: absolute; left: -50px;\"  />
									<center><font size=\"40\">Interne Competitie<br>Noord-Limburg</font></center>
								</div>
								<div id=\"content2\" valign=\"top\">
									<h1>" .  $usernaam . "</h3>";
				if ($aantalcompetitiedag == 0)
					{
						echo "De inschrijving is nog niet geopend.<br>
								<a href=\"index2.php\">Ververs deze pagina zodra de inschrijving geöpend is.</a>";
					}
				elseif ($aantalcompetitiedag == 1)
					{
						echo "	<h3>Seizoen : " . $competitiedag[0][beginjaar] . " - " . $competitiedag[0][eindjaar] . "</h3>
								<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
									<tr>
										<td>Datum: </td>
										<td>" . strftime ("%e %B %Y", strtotime ($competitiedag[0][datum])) . "</td>
									</tr>
									<tr>	
										<td>Organiserende club:</td>
										<td>" . $competitiedag[0][clubnaam] . "</td>
									</tr>
									<tr>
										<td>Locatie:</td>
										<td>" . $competitiedag[0][naam] . ", " . $competitiedag[0][adres] . " " . $competitiedag[0][postcode] . " te " . $competitiedag[0][plaats] . "</td>
									</tr>
								</table><br>";
						if ($target != "content/home.php")
							{
								include ($target);
							}
						else
							{
								$clubs = $db->get_array("SELECT * FROM IC_clubs ORDER BY clubnaam");
								foreach ($clubs as $club)
									{
										echo "<a href=\"index2.php#" . $club["clubnr"] . "\">" . $club["clubnaam"] . "</a><br>";
									}
								foreach ($clubs as $club)
									{
										echo "<a name=\"" . $club["clubnr"] . "\"><br>" . $club["clubnaam"] . "<br>";
										
										// 12 jaar oud is geboren op:
										$datum = date ("Y-m-d", strtotime(date("Y-m-d", time()) . "-12 years"));
										
										$deelnemers = $db->get_array("
													SELECT * FROM IC_deelnemers
													WHERE NOT EXISTS (SELECT * FROM IC_pouleindeling WHERE IC_pouleindeling.lidnr = IC_deelnemers.lidnr AND competitienr=" . $competitiedag[0]["competitienr"] . ")
														AND clubnr='" . $club["clubnr"] . "'
														AND geboortedatum>'" . $datum . "'
													ORDER BY achternaam, voornaam");
										$aantaldeelnemers = $db->num_rows("
													SELECT * FROM IC_deelnemers
													WHERE NOT EXISTS (SELECT * FROM IC_pouleindeling WHERE IC_pouleindeling.lidnr = IC_deelnemers.lidnr AND competitienr=" . $competitiedag[0]["competitienr"] . ")
														AND clubnr='" . $club["clubnr"] . "'
														AND geboortedatum>'" . $datum . "'
													ORDER BY achternaam, voornaam");
										if ($aantaldeelnemers > 0)
											{
												foreach ($deelnemers as $deelnemer)
													{
														echo "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index2.php?target=judokainschrijven&lidnr=" . $deelnemer["lidnr"]  . "\">" . naam($deelnemer["lidnr"]) . "</a><br>";
													}
											}
									}
							}
					}
				else
					{
						echo "Ergens is iets vreselijk fout gegaan. Neem contact op met de systeembeheerder.";
					}
				echo "			</div>
								<div id=\"footer\">
									<p><center>Ingelogd als " . $usernaam . " <a href=\"uitloggen.php\">uitloggen</a><center></p>
								</div>
							</div>
						</body>
						</html>";
			}
		elseif ($_SESSION['ICusernr'] == 27 || $_SESSION['ICusernr'] == 28 || $_SESSION['ICusernr'] == 29 || $_SESSION['ICusernr'] == 30)
			{
			}
		else
			{
				header ("location: index.php");
			}

		
	}
elseif (@$_SESSION['IClogin'] == "false")
	{
		header ("location: index.php");
	}
else
	{
		header ("location: index.php");
	}
?>