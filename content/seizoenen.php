<?php
////
// Seizoen statussen:
// 0 => in aanmaak
// 1 => lopend
// 2 => voltooid
////
////
// Competitiedag statussen
// 0 => ingepland
// 1 => inschrijving open
// 2 => competitie afgerond
////

$clubsdropdown = "<select name=\"clubnr\"><option value=\"\">Selecteer de organiserende club</option>";
$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubnr<>2 ORDER BY clubnaam");
foreach ($clubs as $club)
	{
		$clubsdropdown .= "<option value=\"" . $club[clubnr] . "\">" . $club[clubnaam] . "</option>";
	}
$clubsdropdown .= "</select>";

$locatiedropdown = "<select name=\"locatienr\" id=\"selecter\"><option value=\"\">Selecteer de locatie</option>";
$locaties = $db->get_array("SELECT * FROM IC_locaties ORDER BY plaats");
foreach ($locaties as $locatie)
	{
		$locatiedropdown .= "<option value=\"" . $locatie[locatienr] . "\">" . $locatie[plaats] . ", " . $locatie[naam] . ", " . $locatie[adres] . "</option>";
	}
$locatiedropdown .= "<option value=\"0\">Andere locatie</option></select>";

$seizoenaanmaak	= $db->get_array("SELECT * FROM IC_seizoenen WHERE status=0");
$seizoenlopend	= $db->get_array("SELECT * FROM IC_seizoenen WHERE status=1");
$seizoenoud		= $db->get_array("SELECT * FROM IC_seizoenen WHERE status=2");
$seizoenaanmaakaantal	= $db->num_rows("SELECT * FROM IC_seizoenen WHERE status=0");
$seizoenlopendaantal	= $db->num_rows("SELECT * FROM IC_seizoenen WHERE status=1");
$seizoenoudaantal		= $db->num_rows("SELECT * FROM IC_seizoenen WHERE status=2");

$dagstatus = array (0 => "Ingepland", 1 => "Open", 2 => "Voltooid");

echo "	<h1>Competitie Seizoenen</h1>";

$status = 2;
if ($seizoenlopendaantal == 1)
	{
		echo 		"<h2>Huidig seizoen</h2><h3>Periode: " .
						$seizoenlopend[0][beginjaar] . " - " . $seizoenlopend[0][eindjaar] . "</h3>";
		$competitiedagen = $db->get_array("SELECT * FROM IC_competities
										  		LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr) 
												LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)
												WHERE IC_competities.seizoennr='" . $seizoenlopend[0][seizoennr] . "' ORDER BY IC_competities.datum ASC");
		echo "<table border=\"0\" cellpadding=\"3\" cellspacing-\"0\">";
		foreach ($competitiedagen as $competitiedag)
			{
				echo "<tr>
						<td><a href=\"index.php?target=competitiedag&competitiedagnr=" . $competitiedag[competitienr] . "\">" . strftime ("%e %b %Y", strtotime ($competitiedag[datum])) . "</a></td>
						<td>" . $competitiedag[clubnaam] . "</td>
						<td>in " . $competitiedag[naam] . "</td>
						<td>" . $dagstatus[$competitiedag[dagstatus]] . "</td>
					</tr>";
				if ($competitiedag["dagstatus"] != 2)
					{
						$status = $competitiedag["dagstatus"];
					}
			}
		echo "</table>";
		if ($status == 2)
			{
				echo "<h3>Seizoen afronden</h3>";
			}
	}
	
if ($seizoenaanmaakaantal == 1)
	{
		echo 	"<br>
					<h2>Seizoen in aanmaak</h2>
					<h3>Periode : " .
						$seizoenaanmaak[0][beginjaar] . " - " . $seizoenaanmaak[0][eindjaar] . "</h3>";
		$competitiedagen = $db->get_array("SELECT * FROM IC_competities 
										  		LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
												LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)  
												WHERE IC_competities.seizoennr=" . $seizoenaanmaak[0][seizoennr]);
		$aantalcompetitiedagen = $db->num_rows("SELECT * FROM IC_competities 
											   	LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr) 
												LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)
												WHERE IC_competities.seizoennr=" . $seizoenaanmaak[0][seizoennr]);
		
		if ($aantalcompetitiedagen != 0)
			{
				echo "<table border=\"0\" cellpadding=\"3\" cellspacing-\"0\">";
				foreach ($competitiedagen as $competitiedag)
					{
						echo "<tr>
								<td><a href=\"index.php?target=competitiedag&competitiedagnr=" . $competitiedag[competitienr] . "\">" . strftime ("%e %b %Y", strtotime ($competitiedag[datum])) . "</a></td>
								<td>" . $competitiedag[clubnaam] . "</td>
								<td>in " . $competitiedag[naam] . "</td>
								<td>" . $dagstatus[$competitiedag[dagstatus]] . "</td>
							</tr>";
						if ($competitiedag["dagstatus"] != 2)
							{
								$status = $competitiedag["dagstatus"];
							}
					}
				echo "</table>";
			}
		else
			{
				echo "Er zijn nog geen bekende competitiedagen voor dit seizoen.<br>";
			}
		echo "	<br><form name=\"competitiedag_add\" method=\"post\" action=\"competitiedag_add.php\">
						
						<input type=\"hidden\" name=\"seizoennr\" value=\"" . $seizoenaanmaak[0][seizoennr] . "\">
						<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
							<tr>
								<td>Datum: </td>
								<td>" . datumkiezer("dag", "maand", "jaar", date("Y-m-d")) . "</td>
							</tr>
							<tr>	
								<td>Organiserende club:</td>
								<td>" . $clubsdropdown . "</td>
							</tr>
							<tr>
								<td>Locatie:</td>
								<td>" . $locatiedropdown . "</td>
							</tr>
							<tr>
								<td id=\"extra1\" style=\"display:none;\">Locatienaam:</td>
								<td id=\"extra2\" style=\"display:none;\"><input type=\"text\" name=\"naam\" value=\"\"></td>
							</tr>
							<tr>
								<td id=\"extra3\" style=\"display:none;\">Adres:</td>
								<td id=\"extra4\" style=\"display:none;\"><input type=\"text\" name=\"adres\" value=\"\"></td>
							</tr>
							<tr>
								<td id=\"extra5\" style=\"display:none;\">Postcode:</td>
								<td id=\"extra6\" style=\"display:none;\"><input type=\"text\" name=\"postcode\" value=\"\"></td>
							</tr>
							<tr>
								<td  id=\"extra7\" style=\"display:none;\">Plaats:</td>
								<td  id=\"extra8\" style=\"display:none;\"><input type=\"text\" name=\"plaats\" value=\"\"></td>
							</tr>
							<tr>
								<td><input type=\"submit\" value=\"Competitiedag toevoegen\"></td>
								<td></td>
							</tr>
						</table>
					</form>
			<h3>Gewichtsklassen</h3>";
		$gewichtsklassen = $db->get_array("SELECT * FROM IC_gewichtseizoen 
										  	LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_gewichtseizoen.gewichtsklassenr)
											WHERE IC_gewichtseizoen.seizoennr='" . $seizoenaanmaak[0][seizoennr] . "'
											ORDER BY upperbound ASC");
		$gewichtsklassenaantal = $db->num_rows("SELECT * FROM IC_gewichtseizoen 
										  	LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_gewichtseizoen.gewichtsklassenr)
											WHERE IC_gewichtseizoen.seizoennr='" . $seizoenaanmaak[0][seizoennr] . "'
											ORDER BY upperbound ASC");
		if ($gewichtsklassenaantal != 0)
			{
				foreach ($gewichtsklassen as $gewichtsklasse)
					{
						echo "-" . $gewichtsklasse[upperbound]. "&nbsp;&nbsp;
							<a href=\"gewichtseizoen_update.php?gewichtsklassennr=" . $gewichtsklasse[gewichtsklassenr] . "&seizoennr=" . $seizoenaanmaak[0][seizoennr] . "\"><font color=\"green\">V</font></a><br>";
					}
			}
	
		$overigegewichtsklassen = $db->get_array("SELECT * FROM IC_gewichtsklassen 
												 	WHERE NOT EXISTS (SELECT * FROM IC_gewichtseizoen 
														WHERE IC_gewichtsklassen.gewichtsklassenr=IC_gewichtseizoen.gewichtsklassenr 
															AND seizoennr=" . $seizoenaanmaak[0][seizoennr] . ") ORDER BY upperbound ASC");
		$overigegewichtsklassenaantal = $db->num_rows("SELECT * FROM IC_gewichtsklassen
													  	WHERE NOT EXISTS (SELECT * FROM IC_gewichtseizoen
															WHERE IC_gewichtsklassen.gewichtsklassenr=IC_gewichtseizoen.gewichtsklassenr
																AND seizoennr=" . $seizoenaanmaak[0][seizoennr] . ") ORDER BY upperbound ASC");
		echo "<br>";
		if ($overigegewichtsklassenaantal != 0)
			{
				foreach ($overigegewichtsklassen as $overigegewichtsklasse)
					{
						echo "-" . $overigegewichtsklasse[upperbound]. "&nbsp;&nbsp;
							<a href=\"gewichtseizoen_update.php?gewichtsklassennr=" . $overigegewichtsklasse[gewichtsklassenr] . "&seizoennr=" . $seizoenaanmaak[0][seizoennr] . "\"><font color=\"red\">X</font></a><br>";
					}
			}
		echo "<form name=\"gewichtsklasse_add\" method=\"post\" action=\"gewichtsklasse_add.php\">
				<input type=\"hidden\" name=\"seizoennr\" value=\"" . $seizoenaanmaak[0][seizoennr] . "\">
				Maximum gewicht: <input type=\"text\" size=\"5\" name=\"upperbound\" value=\"\">&nbsp;&nbsp;&nbsp;<input type=\"submit\" value=\"Gewichtsklasse toevoegen\"></form>";
		////
		// Seizoen definitief maken alleen als er geen seizoen met status 1 (lopend) bestaat
		////
		echo "<h3>Seizoen definitief maken</h3>";
		$lopendseizoenaantal = $db->num_rows("SELECT * FROM IC_seizoenen WHERE status=1");
		if ($lopenseizoenaantal == 0)
			{	
				echo "	<form name=\"seizoen_update\" method=\"post\" action=\"seizoen_update.php\">
							<input type=\"hidden\" name=\"seizoennr\" value=\"" . $seizoenaanmaak[0][seizoennr] . "\">
							<input type=\"submit\" value=\"Seizoen definitief maken\">
						</form>";
			}
		else
			{
				echo "Er is al een seizoen met status \"lopend\" Dit seizoen kan pas die status krijgen als het andere seizoen is afgerond.";
			}
	}
else
	{
		echo "	<br><h2>Nieuw seizoen samenstellen</h2>
				<form name=\"seizoen_add\" method=\"post\" action=\"seizoen_add.php\">
					<table cellpadding=\"3\" border=\"0\" cellspacing=\"0\">
						<tr>
							<td>Beginjaar:</td>
							<td><input type=\"text\" name=\"beginjaar\" value=\"\"></td>
						</tr>
						<tr>
							<td>Eindjaar:</td>
							<td><input type=\"text\" name=\"eindjaar\" value=\"\"></td>
						</tr>
						<tr>
							<td><input type=\"submit\" value=\"Seizoen toevoegen\"></td>
						</tr>
					</table>
				</form>";
	}
if ($seizoenoudaantal != 0)
	{
		echo "<h2>Voorgaande seizoenen</h2>";
		foreach ($seizoenoud as $seizoen)
			{
				echo "<h3>Periode: " . $seizoen[beginjaar] . " - " . $seizoen[eindjaar] . "</h3>";
				$competitiedagen = $db->get_array("SELECT * FROM IC_competities 
										  		LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
												LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)  
												WHERE IC_competities.seizoennr=" . $seizoen[seizoennr]);
				$aantalcompetitiedagen = $db->num_rows("SELECT * FROM IC_competities 
											   	LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr) 
												LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)
												WHERE IC_competities.seizoennr=" . $seizoen[seizoennr]);
				if ($aantalcompetitiedagen != 0)
					{
						echo "<table border=\"0\" cellpadding=\"3\" cellspacing-\"0\">";
						foreach ($competitiedagen as $competitiedag)
							{
								echo "<tr>
										<td><a href=\"index.php?target=competitiedag&competitiedagnr=" . $competitiedag[competitienr] . "\">" . strftime ("%e %b %Y", strtotime ($competitiedag[datum])) . "</a></td>
										<td>" . $competitiedag[clubnaam] . "</td>
										<td>in " . $competitiedag[naam] . "</td>
										<td>" . $dagstatus[$competitiedag[dagstatus]] . "</td>
									</tr>";
								if ($competitiedag["dagstatus"] != 2)
									{
										$status = $competitiedag["dagstatus"];
									}
							}
						echo "</table>";
					}
				else
					{
						echo "Geen competitiedagen bekend in dit seizoen.";
					}
			}
	}


?>