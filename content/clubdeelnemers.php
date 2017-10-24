<?php
$graduatienamen = array (6 => "Wit", 5 => "Geel", 4 => "Oranje");
echo "<h1>Deelnemers aan de IC</h1>";

$datum = date ("Y-m-d", strtotime(date("Y-m-d", time()) . "-12 years"));

echo "	<h2>Deelnemer toevoegen</h2>
		<form name=\"deelnemer_add\" method=\"post\" action=\"deelnemer_add.php\">
			<input type=\"hidden\" name=\"clubnr\" value=\"" . $user[0][clubnr] . "\">
			<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
				<tr>
					<td>Voornaam:</td>
					<td><input type=\"text\" name=\"voornaam\" value=\"\"></td>
				</tr>
				<tr>
					<td>Tussenvoegsels:</td>
					<td><input type=\"text\" name=\"tussenvoegsels\" value=\"\"></td>
				</tr>
				<tr>
					<td>Achternaam:</td>
					<td><input type=\"text\" name=\"achternaam\" value=\"\"></td>
				</tr>
				<tr>
					<td>Geboortedatum:</td>
					<td>" . datumkiezer("dag", "maand", "jaar", date("Y-m-d")) . "</td>
				</tr>
				<tr>
					<td>Geslacht:</td>
					<td>Man: <input type=\"radio\" name=\"geslacht\" value=\"0\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Vrouw: <input type=\"radio\" name=\"geslacht\" value=\"1\"></td>
				</tr>
				<tr>
					<td>Gewicht:</td>
					<td><input type=\"text\" size=\"5\" name=\"gewicht\" value=\"\" placeholder=\"27.5\">&nbsp;Kg</td>
				</tr>
				<tr>
					<td>Graduatie:</td>
					<td><select name=\"graduatie\">
							<option value=\"6\">Wit</option>
							<option value=\"5\">Geel</option>
							<option value=\"4\">Oranje</option>
						</select></td>
				</tr>
				<tr>
					<td>Rating:</td>
					<td>Laag <input type=\"radio\" name=\"rating\" value=\"1000\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hoog <input type=\"radio\" name=\"rating\" value=\"1400\"></td>
				</tr>
				<tr>
					<td><input type=\"submit\" value=\"Deelnemer toevoegen\"></td>
				</tr>
			</table>
		</form>";
		
$deelnemers = $db->get_array("SELECT * FROM IC_deelnemers WHERE clubnr=" . $user[0][clubnr] . " AND geboortedatum>'" . $datum . "' ORDER BY achternaam, voornaam");
$aantaldeelnemers = $db->num_rows("SELECT * FROM IC_deelnemers WHERE clubnr=" . $user[0][clubnr] . " AND geboortedatum>'" . $datum . "' ORDER BY achternaam, voornaam");

echo "<h2>Huidige deelnemers</h2>";

if ($aantaldeelnemers != 0)
	{
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
				<tr>
					<td>Naam</td>
					<td>Geboortedatum</td>
					<td>Gewichtsklasse</td>
					<td>Graduatie</td>
					<td>Rating</td>
				</tr>";
		foreach ($deelnemers as $deelnemer)
			{
				//// opzoeken gewichtsklasse
				$gewichtsklasse = $db->get_array("SELECT * FROM IC_gewichtsklassen
												 	LEFT JOIN IC_gewichtseizoen ON (IC_gewichtseizoen.gewichtsklassenr = IC_gewichtsklassen.gewichtsklassenr)
													LEFT JOIN IC_seizoenen ON (IC_gewichtseizoen.seizoennr = IC_seizoenen.seizoennr)
													WHERE IC_seizoenen.status = '1' AND IC_gewichtsklassen.upperbound>'" . $deelnemer[gewicht] . "' ORDER BY upperbound ASC LIMIT 1");
				echo "<tr>
						<td><a href=\"index.php?target=deelnemer&lidnr=" . $deelnemer[lidnr] . "\">" . $deelnemer[achternaam] . " " . $seelnemer[tussenvoegsels] .  ", ". $deelnemer[voornaam] . "</a></td>
						<td>" . strftime("%d %B %Y", strtotime($deelnemer[geboortedatum])) . "</td>
						<td>-" . $gewichtsklasse[0][upperbound] . " (" . $deelnemer[gewicht] . ")</td>
						<td>" . $graduatienamen[$deelnemer[graduatie]] . "</td>
						<td>" . $deelnemer[rating] . "</td>
					</tr>";
			}
		echo "</table><br>";
	}
else
	{
		echo "Er zijn geen deelnemers op naam van uw club in het systeem welke jonger zijn dan 12<br><br>";
	}

$deelnemers = $db->get_array("SELECT * FROM IC_deelnemers WHERE clubnr=" . $user[0][clubnr] . " AND geboortedatum<='" . $datum . "' ORDER BY achternaam, voornaam");
$aantaldeelnemers = $db->num_rows("SELECT * FROM IC_deelnemers WHERE clubnr=" . $user[0][clubnr] . " AND geboortedatum<='" . $datum . "' ORDER BY achternaam, voornaam");

echo "<h2>Oud deelnemers</h2>";

if ($aantaldeelnemers != 0)
	{
		
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
				<tr>
					<td>Naam</td>
					<td>Geboortedatum</td>
					<td>Gewichtsklasse</td>
					<td>Graduatie</td>
					<td>Rating</td>
				</tr>";
		foreach ($deelnemers as $deelnemer)
			{
				//// opzoeken gewichtsklasse
				$gewichtsklasse = $db->get_array("SELECT * FROM IC_gewichtsklassen
												 	LEFT JOIN IC_gewichtseizoen ON (IC_gewichtseizoen.gewichtsklassenr = IC_gewichtsklassen.gewichtsklassenr)
													LEFT JOIN IC_seizoenen ON (IC_gewichtseizoen.seizoennr = IC_seizoenen.seizoennr)
													WHERE IC_seizoenen.status = '1' AND IC_gewichtsklassen.upperbound>'" . $deelnemer[gewicht] . "' ORDER BY upperbound ASC LIMIT 1");
				echo "<tr>
						<td><a href=\"index.php?target=deelnemer&lidnr=" . $deelnemer[lidnr] . "\">" . $deelnemer[achternaam] . " " . $seelnemer[tussenvoegsels] .  ", ". $deelnemer[voornaam] . "</a></td>
						<td>" . strftime("%d %B %Y", strtotime($deelnemer[geboortedatum])) . "</td>
						<td>-" . $gewichtsklasse[0][upperbound] . " (" . $deelnemer[gewicht] . ")</td>
						<td>" . $graduatienamen[$deelnemer[graduatie]] . "</td>
						<td>" . $deelnemer[rating] . "</td>
					</tr>";
			}
		echo "</table><br>";
	}
else
	{
		echo "Er zijn geen deelnemers op naam van uw club in het systeem welke ouder zijn dan 12<br><br>";
	}

?>