<?php
////
// Seizoen statussen:
// 0 => in aanmaak
// 1 => lopend
// 2 => voltooid
////
////
// Poule statussen:
// 0 => poule in aanmaak
// 1 => opgesteld
// 2 => opgeropen (matnummer)
// 3 => Wedstrijden afgerond
// 4 => Scores verwerkt (ratings aangepast)
////
$competitiedagnr =  check_competitiedagnr ($_GET["competitiedagnr"]);
if ($competitiedagnr == "false")
	{
		echo "Er klopt iets niet met de invoer.";
	}
else
	{
		$competitiedag = $db->get_array("SELECT * FROM IC_competities
											LEFT JOIN IC_locaties ON (IC_locaties.locatienr = IC_competities.locatienr)
											LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
											LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
											WHERE IC_competities.competitienr=" . $competitiedagnr);
		
		$clubsdropdown = "<select name=\"clubnr\">";
		$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubnr<>2 ORDER BY clubnaam");
		foreach ($clubs as $club)
			{
				if ($club[clubnr] == $competitiedag[0][clubnr])
					{
						$clubsdropdown .= "<option value=\"" . $club[clubnr] . "\" selected>" . $club[clubnaam] . "</option>";
					}
				else
					{
						$clubsdropdown .= "<option value=\"" . $club[clubnr] . "\">" . $club[clubnaam] . "</option>";
					}
			}
		$clubsdropdown .= "</select>";
		
		$locatiedropdown = "<select name=\"locatienr\" id=\"selecter\">";
		$locaties = $db->get_array("SELECT * FROM IC_locaties ORDER BY plaats");
		foreach ($locaties as $locatie)
			{
				if ($locatie[locatienr] == $competitiedag[0][locatienr])
					{
						$locatiedropdown .= "<option value=\"" . $locatie[locatienr] . "\" selected>" . $locatie[plaats] . ", " . $locatie[naam] . ", " . $locatie[adres] . "</option>";
					}
				else
					{
						$locatiedropdown .= "<option value=\"" . $locatie[locatienr] . "\">" . $locatie[plaats] . ", " . $locatie[naam] . ", " . $locatie[adres] . "</option>";
					}
			}
		$locatiedropdown .= "<option value=\"0\">Andere locatie</option></select>";
		
		if ($competitiedag[0][status] == 0) // seizoen is in aanmaak
			{
				echo "	<h1>Competitie dag</h1>
						<h3>Seizoen : " . $competitiedag[0][beginjaar] . " - " . $competitiedag[0][eindjaar] . "</h3>
						<form name=\"competitiedag_update\" method=\"post\" action=\"competitiedag_update.php\">
							<input type=\"hidden\" name=\"competitiedagnr\" value=\"" . $competitiedagnr . "\">
							<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
								<tr>
									<td>Datum: </td>
									<td>" . datumkiezer("dag", "maand", "jaar", $competitiedag[0][datum]) . "</td>
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
									<td><input type=\"submit\" value=\"Competitiedag aanpassen\"></td>
									<td></td>
								</tr>
							</table>
						</form>";
			}
		elseif ($competitiedag[0][status] == 1) // seizoen is lopend
			{
				// Controleren op oudere competitiedagen waar dagstatus != 2 (die zijn nog niet afgerond.
				$aantaldagen = $db->num_rows("SELECT * FROM IC_competities WHERE dagstatus<>2 AND datum<'" . $competitiedag[0][datum] . "'");
				$dag = $db->num_rows("SELECT * FROM IC_competities WHERE dagstatus=1 AND datum<'" . $competitiedag[0][datum] . "'");
				
				echo "
				<h3>Seizoen : " . $competitiedag[0][beginjaar] . " - " . $competitiedag[0][eindjaar] . "</h3>
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
				</table>";
				 
				if ($aantaldagen == 0)
					{
						if ($competitiedag[0][dagstatus] == 1) // inschijving geopend
							{
								$datum = date ("Y-m-d", strtotime(date("Y-m-d", time()) . "-12 years"));
								$deelnemersdrop = $db->get_array("SELECT * FROM IC_deelnemers
																 	LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
																	WHERE NOT EXISTS (SELECT * FROM IC_pouleindeling WHERE IC_pouleindeling.lidnr = IC_deelnemers.lidnr AND competitienr=" . $competitiedagnr . ")
																		AND IC_deelnemers.geboortedatum>'" . $datum . "' 
																	ORDER BY IC_clubs.clubnr, IC_deelnemers.achternaam, IC_deelnemers.voornaam");
								
								$deelnemerdropdown = "<select name=\"lidnr\"><option value=\"\">Selecteer de gewenste deelnemer</option>";
								if (!empty($deelnemersdrop))
									{
										foreach ($deelnemersdrop as $deelnemerdrop)
											{
												$deelnemerdropdown .= "<option value=\"" . $deelnemerdrop["lidnr"] . "\">" . naam ($deelnemerdrop["lidnr"]) . ", van " . $deelnemerdrop["clubnaam"] .  "</option>";
											}
									}
								$deelnemerdropdown .= "</select>";
								
								echo "	<h3>Deelnemer toevoegen</h3>
										<form name=\"inschrijving_add\" method=\"post\" action=\"inschrijving_add2.php\">
											<input type=\"hidden\" name=\"competitiedagnr\" value=\"" . $competitiedagnr . "\">
											" . $deelnemerdropdown . "
											<input type=\"submit\" value=\"Toevoegen\">
										</form>";
								
								////
								// Competitiedag afronden.
								////
								
								$poulecontrole = $db->get_array("SELECT * FROM IC_poules WHERE poulestatus>'0' AND poulestatus<'3' AND competitienr='" . $competitiedagnr . "'");
								$inschrijfcontrole = $db->get_array("SELECT * FROM IC_pouleindeling WHERE competitienr='" . $competitiedagnr . "' AND plaats='0'");
								
								if ($poulecontrole == 0 && $inschrijfcontrole == 0)
									{
										echo "	<h3>Competitiedag sluiten</h3>
												Alle uitslagen zijn bekend. De competitie kan worden gesloten.
												<form name=\"competitie_sluiten\" method=\"post\" action=\"competitie_sluiten.php\">
													<input type=\"hidden\" name=\"competitienr\" value=\"" . $competitiedagnr . "\">
													<input type=\"submit\" value=\"Competitiedag afsluiten\">
												</form>";
									}
								
								$gewichtsklassen = $db->get_array ("SELECT * FROM IC_competities
																   		RIGHT JOIN IC_gewichtseizoen ON (IC_gewichtseizoen.seizoennr = IC_competities.seizoennr)
																		LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_gewichtseizoen.gewichtsklassenr)
																		WHERE IC_competities.competitienr='" . $competitiedagnr . "'
																		ORDER BY IC_gewichtsklassen.upperbound ASC");
								if(!empty($gewichtsklassen))
									{
										$i = 1;
										foreach ($gewichtsklassen as $gewichtsklasse)
											{
												echo "<br>-" . $gewichtsklasse["upperbound"] . "
													<a href=\"index.php?target=poules_opstellen&competitiedag=" . $competitiedagnr . "&gewichtsklassenr=" . $gewichtsklasse["gewichtsklassenr"] . "&knip=1200\">
														Poules opstellen</a><br>";
														
												$poules = $db->get_array("SELECT * FROM IC_poules
																		 	RIGHT JOIN IC_pouleindeling ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
																			LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																			LEFT JOIN IC_clubs ON  (IC_clubs.clubnr = IC_deelnemers.clubnr)
																			WHERE IC_poules.gewichtsklassenr='" . $gewichtsklasse["gewichtsklassenr"] . "'
																				AND IC_pouleindeling.competitienr='" . $competitiedagnr . "'
																				AND IC_poules.poulestatus>'0'
																			ORDER BY IC_poules.poulenr, IC_pouleindeling.volgorde");
												
												$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling
																			 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																				LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
																				WHERE IC_pouleindeling.competitienr='" . $competitiedagnr . "'
																					AND IC_pouleindeling.gewichtsklassenr='" . $gewichtsklasse["gewichtsklassenr"] . "'
																					AND IC_pouleindeling.poulenr='0'
																				ORDER BY IC_deelnemers.achternaam, IC_deelnemers.voornaam");
												
												if (!empty($deelnemers))
													{
														foreach ($deelnemers as $deelnemer)
															{	
																if ($deelnemer["graduatie"] == 5)
																	{
																		echo "<font color=\"orange\">";
																	}
																if ($deelnemer["graduatie"] == 4)
																	{
																		echo "<font color=\"red\">";
																	}
																echo naam ($deelnemer["lidnr"]) . ", (" . $deelnemer["clubnaam"] . ") -" . $deelnemer["rating"] . " / " . $deelnemer["graduatie"];
																if ($deelnemer["graduatie"] != 6)
																	{
																		echo "</font>";
																	}
																echo  "<br>";
															}
													}
													
												$poulenr = 0;
												if (!empty($poules))
													{
														foreach ($poules as $poule)
															{
																if ($poulenr != $poule["poulenr"])
																	{
																		if ($poulenr != 0)
																			{
																				echo "</table><br>";
																			}
																		echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
																				<tr>
																					<td colspan=\"5\">";
																if ($poule["poulestatus"] == 1)
																	{																					
																		echo "			<form name=\"poule_doorzetten\" method=\"post\" action=\"poule_doorzetten.php\">
																							<input type=\"hidden\" name=\"poulenr\" value=\"" . $poule["poulenr"] . "\">
																							<select name=\"matnr\">
																								<option value=\"\">Selecteer de mat</option>
																								<option value=\"1\">Mat 1</option>
																								<option value=\"2\">Mat 2</option>
																								<option value=\"3\">Mat 3</option>
																								<option value=\"4\">Mat 4</option>
																							</select>
																							<input type=\"submit\" value=\"Oproepen\">
																						</form>";
																	}
																elseif ($poule["poulestatus"] == 2)
																	{
																		echo "			Mat " . $poule["mat"]  . "<br>
																						<a href=\"index.php?target=poule&poulenr=" . $poule["poulenr"] . "\">Poule invullen</a>";
																	}
																else
																	{
																		echo "			<a href=\"index.php?target=poule&poulenr=" . $poule["poulenr"] . "\">Bekijk de poule</a>";
																	}
																echo "				</td>
																				</tr>";
																		$i = $i+1;
																		$poulenr = $poule["poulenr"];
																	}
																echo "<tr><td width=\"20\">&nbsp;</td><td width=\"200\">";
																if ($poule["graduatie"] == 5)
																	{
																		echo "<font color=\"orange\">";
																	}
																if ($poule["graduatie"] == 4)
																	{
																		echo "<font color=\"red\">";
																	}
																echo naam($poule["lidnr"]) . "</font></td>
																			<td width=\"200\">" . $poule["clubnaam"] . "</td>
																			<td width=\"40\" align=\"right\">" . bepaal_rating($poule["lidnr"], $competitiedag[0][datum]) . "</td>
																			<td width=\"40\">";
																$lagerepoules = $db->get_array("SELECT * FROM IC_poules
																							   		RIGHT JOIN IC_pouleindeling ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
																									WHERE IC_poules.poulenr<'" . $poule["poulenr"] . "'
																										AND IC_pouleindeling.competitienr='" . $competitiedagnr . "'
																										AND IC_poules.poulestatus='1'
																										AND IC_poules.gewichtsklassenr='" . $gewichtsklasse["gewichtsklassenr"] . "' 
																									GROUP BY IC_poules.poulenr
																									Having COUNT(IC_pouleindeling.volgorde)<5");
																$hogerepoules = $db->get_array("SELECT * FROM IC_poules
																							   		RIGHT JOIN IC_pouleindeling ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
																									WHERE IC_poules.poulenr>'" . $poule["poulenr"] . "'
																										AND IC_pouleindeling.competitienr='" . $competitiedagnr . "'
																										AND IC_poules.poulestatus='1'
																										AND IC_poules.gewichtsklassenr='" . $gewichtsklasse["gewichtsklassenr"] . "' 
																									GROUP BY IC_poules.poulenr
																									Having COUNT(IC_pouleindeling.volgorde)<5");
																if (!empty($lagerepoules) && $poule["poulestatus"] == 1)
																	{
																		echo "&nbsp;&nbsp;<a href=\"verplaats_judoka_van_poule.php?competitienr=" . $competitiedagnr . "&gewichtsklassenr=" . $gewichtsklasse["gewichtsklassenr"] . "&richting=down&lidnr=" . $poule["lidnr"] . "\">&#x2191;</a>";
																	}
																if (!empty($hogerepoules) && $poule["poulestatus"] == 1)
																	{
																		echo "&nbsp;&nbsp;<a href=\"verplaats_judoka_van_poule.php?competitienr=" . $competitiedagnr . "&gewichtsklassenr=" . $gewichtsklasse["gewichtsklassenr"] . "&richting=up&lidnr=" . $poule["lidnr"] . "\">&#x2193;</a>";
																	}
																if ($poule["poulestatus"] > 2)
																	{
																		echo "<center>" . $poule["plaats"] . "</center>";
																	}
																echo "</td>
																		</tr>";
															}
													}
												echo "</table>";
											}
									}
							}
						elseif ($competitiedag[0][dagstatus] == 2) // competitie is voltooid
							{
								$gewichtsklassen = $db->get_array ("SELECT * FROM IC_competities
																   		RIGHT JOIN IC_gewichtseizoen ON (IC_gewichtseizoen.seizoennr = IC_competities.seizoennr)
																		LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_gewichtseizoen.gewichtsklassenr)
																		WHERE IC_competities.competitienr='" . $competitiedagnr . "'
																		ORDER BY IC_gewichtsklassen.upperbound ASC");
								if(!empty($gewichtsklassen))
									{
										$i = 1;
										foreach ($gewichtsklassen as $gewichtsklasse)
											{
												echo "<br>-" . $gewichtsklasse["upperbound"] . "<br>";
														
												$poules = $db->get_array("SELECT * FROM IC_poules
																		 	RIGHT JOIN IC_pouleindeling ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
																			LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																			LEFT JOIN IC_clubs ON  (IC_clubs.clubnr = IC_deelnemers.clubnr)
																			WHERE IC_poules.gewichtsklassenr='" . $gewichtsklasse["gewichtsklassenr"] . "'
																				AND IC_pouleindeling.competitienr='" . $competitiedagnr . "'
																				AND IC_poules.poulestatus>'0'
																			ORDER BY IC_poules.poulenr, IC_pouleindeling.volgorde");
												$poulenr = 0;
												if (!empty($poules))
													{
														foreach ($poules as $poule)
															{
																if ($poulenr != $poule["poulenr"])
																	{
																		if ($poulenr != 0)
																			{
																				echo "</table><br>";
																			}
																		echo "<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
																				<tr>
																					<td colspan=\"5\"><a href=\"index.php?target=poule&poulenr=" . $poule["poulenr"] . "\">Bekijk de poule</a></td>
																				</tr>";
																		$i = $i+1;
																		$poulenr = $poule["poulenr"];
																	}
																echo "<tr><td width=\"20\">&nbsp;</td><td width=\"200\">";
																if ($poule["graduatie"] == 5)
																	{
																		echo "<font color=\"orange\">";
																	}
																if ($poule["graduatie"] == 4)
																	{
																		echo "<font color=\"red\">";
																	}
																echo naam($poule["lidnr"]) . "</font></td>
																			<td width=\"200\">" . $poule["clubnaam"] . "</td>
																			<td width=\"40\">" . $poule["rating"] . "</td>
																			<td width=\"40\"><center>" . $poule["plaats"] . "</center></td>
																		</tr>";
															}
													}
												echo "</table>";
											}
									}
								
							}
						else // competitie openen
							{
								echo "	<h3>Inschrijving starten</h3>
										<form name=\"inschrijving_starten\" method=\"post\" action=\"competitiedag_update2.php\">
											<input type=\"hidden\" name=\"competitiedagnr\" value=\"" . $competitiedagnr . "\">
											<input type=\"submit\" value=\"Inschrijving starten\">
										</form>";
							}
					}
				else
					{
						echo "Er zijn nog competitiedagen voor deze waarop nog niet gestreden is. Deze zullen eerst moeten worden voltooid.";
					}
			}
		else // seizoen is voltooid
			{
				echo "niet meer invullen - Uitslagen weergeven.";
			}
	}

?>