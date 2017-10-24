<?php
$lidnr = check_deelnemer($_GET["lidnr"]);

if ($lidnr === "false")
	{
		header ("location: index2.php");
	}
else
	{
		$competitiedag = $db->get_array ("
				SELECT * FROM IC_competities WHERE dagstatus='1'");
		$deelnemer = $db->get_array("
				SELECT * FROM IC_deelnemers
				LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
				WHERE IC_deelnemers.lidnr=" . $lidnr);
		$gewichtsklasse = $db->get_array("
				SELECT * FROM IC_gewichtsklassen
				LEFT JOIN IC_gewichtseizoen ON (IC_gewichtsklassen.gewichtsklassenr = IC_gewichtseizoen.gewichtsklassenr)
				WHERE IC_gewichtsklassen.upperbound>'" . $deelnemer[0]["gewicht"] . "' AND IC_gewichtseizoen.seizoennr='" . $competitiedag[0]["seizoennr"] . "'
				ORDER BY IC_gewichtsklassen.upperbound
				LIMIT 1");
		
		echo "	<h3>Judoka inschrijven</h3>
				<form name=\"inschrijving_add\" method=\"post\" action=\"inschrijving_add1.php\">
				<input type=\"hidden\" name=\"lidnr\" value=\"" . $deelnemer[0][lidnr] . "\">
				<input type=\"hidden\" name=\"competitienr\" value=\"". $competitiedag[0]["competitienr"] . "\">
				<input type=\"hidden\" name=\"gewichtsklassenr\" value=\"" . $gewichtsklasse[0]["gewichtsklassenr"] . "\">
				<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
					<tr>
						<td>Naam:</td>
						<td>" . naam($deelnemer[0]["lidnr"]) . "</td>
					</tr>
					<tr>
						<td>Geboortedatum:</td>
						<td>" . strftime("%e %B %Y", strtotime($deelnemer[0][geboortedatum])) . "</td>
					</tr>
					<tr>
						<td>Geslacht:</td>
						<td>"; if ($deelnemer[0][geslacht] == 0){echo " Man";} if ($deelnemer[0][geslacht] == 1){echo " Vrouw";} echo "</td>
					</tr>
					<tr>
						<td>Gewicht:</td>
						<td>" . $deelnemer[0][gewicht] . "&nbsp;Kg&nbsp;&nbsp;(-" . $gewichtsklasse[0]["upperbound"] . ")</td>
					</tr>
					<tr>
						<td>Graduatie:</td>
						<td>"; 	if ($deelnemer[0][graduatie] == 6){echo " Wit";} 
								if ($deelnemer[0][graduatie] == 5){echo " Geel";} 
								if ($deelnemer[0][graduatie] == 4){echo " Oranje";} 
		echo "			</td>
					</tr>
					<tr>
						<td>Club:</td>
						<td>" . $deelnemer[0][clubnaam] . "</td>
					</tr>
					<tr>
						<td>Rating:</td>
						<td>" . $deelnemer[0]["rating"] . "</td>
					</tr>
					<tr>
						<td><input type=\"submit\" value=\"Inschrijven\"></td>
					</tr>
				</table>
			</form>";
	}
?>