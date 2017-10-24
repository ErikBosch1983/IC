<?php
$graduatienamen = array (6 => "Wit", 5 => "Geel", 4 => "Oranje");

// 12 jaar oud is geboren op:
$datum = date ("Y-m-d", strtotime(date("Y-m-d", time()) . "-12 years"));

// Dropdown voor clubs maken
$clubsdropdown = "<select name=\"clubnr\"><option value=\"\">Selecteer de club</option>";
$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubnr<>2 ORDER BY clubnaam");
foreach ($clubs as $club)
	{
		$clubsdropdown .= "<option value=\"" . $club[clubnr] . "\">" . $club[clubnaam] . "</option>";
	}
$clubsdropdown .= "</select>";

// Als er een actieve competitiedag is, judoka's daar meteeen voor inschrijven.
$competitiedag = $db->get_array("SELECT * FROM IC_competities WHERE dagstatus=1");
$aantalcomp = $db->num_rows("SELECT * FROM IC_competities WHERE dagstatus=1");
if ($aantalcomp == 1) // inschrijven bij competitie
	{
		$inschrijving = "<input type=\"hidden\" name=\"inschrijving\" value=\"true\">
						<input type=\"hidden\" name=\"competitienr\" value=\"" . $competitiedag[0]["competitienr"] . "\">";
	}
else
	{
		$inschrijving = "<input type=\"hidden\" name=\"inschrijving\" value=\"false\">";
	}



echo "	<h1>Deelnemers aan de IC</h1>
		<h2>Deelnemer toevoegen</h2>
		<form name=\"deelnemer_add\" method=\"post\" action=\"deelnemer_add2.php\">
			<input type=\"hidden\" name=\"clubnr\" value=\"" . $user[0][clubnr] . "\">
			" . $inschrijving . "
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
					<td>Club:</td>
					<td>" . $clubsdropdown . "</td>
				</tr>
				<tr>
					<td><input type=\"submit\" value=\"Deelnemer toevoegen\"></td>
				</tr>
			</table>
		</form>";
		
$deelnemers = $db->get_array("SELECT * FROM IC_deelnemers WHERE geboortedatum>'" . $datum . "' ORDER BY achternaam, voornaam");
$aantaldeelnemers = $db->num_rows("SELECT * FROM IC_deelnemers WHERE geboortedatum>'" . $datum . "' ORDER BY achternaam, voornaam");

/*
echo "<h2>Niveau verdeling</h2>";

$niveauhoog 	= $db->get_array("SELECT * FROM IC_deelnemers ORDER BY rating DESC LIMIT 1");
$niveaulaag		= $db->get_array("SELECT * FROM IC_deelnemers ORDER BY rating ASC LIMIT 1");
$niveaubottom 	= floor	($niveaulaag[0]["rating"] / 20) * 20 - 20;
$niveautop 		= ceil	($niveauhoog[0]["rating"] / 20) * 20 + 20;
echo $niveaubottom . "<br>" . $niveautop . "<br><br>";

$dichtheid = array();
for ($i = $niveaubottom; $i<= $niveautop; $i + 20)
	{
		$j = $i + 20;
		$aantal = $db->num_rows("SELECT lidnr FROM IC_deelnemers WHERE rating>='" . $i . "' AND rating<'" . $j . "'");
		$dichtheid[$i] = $aantal;
	}
print_r($dichtheid);

*/
echo "<h2>Huidige deelnemers</h2>";

if ($aantaldeelnemers != 0)
	{
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
				<tr>
					<td>Naam</td>
					<td>Geboortedatum</td>
					<td>Geslacht</td>
					<td>Gew.klasse</td>
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
						<td><a href=\"index.php?target=deelnemer&lidnr=" . $deelnemer[lidnr] . "\">" . naam2($deelnemer["lidnr"]) . "</a></td>
						<td>" . strftime("%e %h %Y", strtotime($deelnemer[geboortedatum])) . "</td>
						<td>"; if ($deelnemer["geslacht"] == 0){echo "Man";}else{echo"Vrouw";} echo "
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

$deelnemers = $db->get_array("SELECT * FROM IC_deelnemers WHERE geboortedatum<='" . $datum . "' ORDER BY achternaam, voornaam");
$aantaldeelnemers = $db->num_rows("SELECT * FROM IC_deelnemers WHERE geboortedatum<='" . $datum . "' ORDER BY achternaam, voornaam");

echo "<h2>Oud deelnemers</h2>";

if ($aantaldeelnemers != 0)
	{
		
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
				<tr>
					<td>Naam</td>
					<td>Geboortedatum</td>
					<td>Geslacht</td>
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
						<td>"; if ($deelnemer["geslacht"] == 0){echo "Man";}else{echo"Vrouw";} echo "
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