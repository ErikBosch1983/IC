<?php

$deelnemernr = check_deelnemer ((int)$_GET["lidnr"]);
if ($deelnemernr != "false")
	{
		$deelnemer = $db->get_array("SELECT * FROM IC_deelnemers LEFT JOIN IC_clubs ON (IC_deelnemers.clubnr = IC_clubs.clubnr) WHERE IC_deelnemers.lidnr=" . $deelnemernr);
		echo "<h1>Gegevens van " . naam($deelnemernr) . "</h1>
		<form name=\"deelnemer_update\" method=\"post\" action=\"deelnemer_update.php\">
			<input type=\"hidden\" name=\"clubnr\" value=\"" . $user[0][clubnr] . "\">
			<input type=\"hidden\" name=\"lidnr\" value=\"" . $deelnemer[0][lidnr] . "\">
			<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
				<tr>
					<td>Voornaam:</td>
					<td><input type=\"text\" name=\"voornaam\" value=\"" . $deelnemer[0][voornaam] . "\"></td>
				</tr>
				<tr>
					<td>Tussenvoegsels:</td>
					<td><input type=\"text\" name=\"tussenvoegsels\" value=\"" . $deelnemer[0][tussenvoegsels] . "\"></td>
				</tr>
				<tr>
					<td>Achternaam:</td>
					<td><input type=\"text\" name=\"achternaam\" value=\"" . $deelnemer[0][achternaam] . "\"></td>
				</tr>
				<tr>
					<td>Geboortedatum:</td>
					<td>" . datumkiezer("dag", "maand", "jaar", $deelnemer[0][geboortedatum]) . "</td>
				</tr>
				<tr>
					<td>Geslacht:</td>
					<td>Man: <input type=\"radio\" name=\"geslacht\" value=\"0\""; if ($deelnemer[0][geslacht] == 0){echo " checked=\"checked\"";} echo ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Vrouw: <input type=\"radio\" name=\"geslacht\" value=\"1\""; if ($deelnemer[0][geslacht] == 1){echo " checked=\"checked\"";} echo "></td>
				</tr>
				<tr>
					<td>Gewicht:</td>
					<td><input type=\"text\" size=\"5\" name=\"gewicht\" value=\"" . $deelnemer[0][gewicht] . "\">&nbsp;Kg</td>
				</tr>
				<tr>
					<td>Graduatie:</td>
					<td><select name=\"graduatie\">
							<option value=\"6\""; if ($deelnemer[0][graduatie] == 6){echo " selected";} echo ">Wit</option>
							<option value=\"5\""; if ($deelnemer[0][graduatie] == 5){echo " selected";} echo ">Geel</option>
							<option value=\"4\""; if ($deelnemer[0][graduatie] == 4){echo " selected";} echo ">Oranje</option>
						</select></td>
				</tr>
				<tr>
					<td>Club:</td>
					<td>" . $deelnemer[0][clubnaam] . "</td>
				</tr>";
		$ratings = $db->num_rows("SELECT * FROM IC_ratings WHERE lidnr=" . $deelnemernr);
		if ($ratings == 1) // Alleen de initiele inschatting voor deze judoka in DB. Altijd aanpasbaar. ratingcheck meegegeven om aanpasbaarheid door te geven.
			{
				if ($deelnemer[0][rating] == 1000 || $deelnemer[0][rating] == 1400) // Inschatting is gedaan met de radioknopjes dus ook weer terug zetten.
					{
						echo "
						<tr>
							<td>Rating:</td>
							<td>Laag (1000) <input type=\"radio\" name=\"rating\" value=\"1000\""; if ($deelnemer[0][rating] == 1000){echo " checked=\"checked\"";} echo ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Hoog (1400) <input type=\"radio\" name=\"rating\" value=\"1400\""; if ($deelnemer[0][rating] == 1400){echo " checked=\"checked\"";} echo ">
								<input type=\"hidden\" name=\"ratingcheck\" value=\"true\"></td>
						</tr>";
					}
				else // Inschatting is gedaan door de coach. textveld plaatsen.
					{
						echo "
						<tr>
							<td>Rating:</td>
							<td><input type=\"text\" name=\"rating\" value=\"" . $deelnemer[0][rating] . "\">
								<input type=\"hidden\" name=\"ratingcheck\" value=\"true\"></td>
						</tr>";
					}
			}
		else // meerdere ratingentrys betekend dat deze judoka al heeft gejudoëd. Rating weergeven maar niet aanpasbaar.
			{
				echo "	<tr>
							<td>Rating:</td>
							<td>" . $deelnemer[0][rating] . "<input type=\"hidden\" name=\"ratingcheck\" value=\"false\"></td>
						</tr>"; 
				
			}
		echo "	<tr>
					<td><input type=\"submit\" value=\"Gegevens aanpassen\"></td>
				</tr>
			</table>
		</form>";
		
		$aantalpoules = $db->num_rows("
							SELECT * FROM IC_pouleindeling
								LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
								LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
								LEFT JOIN IC_clubs ON (IC_competities.clubnr = IC_clubs.clubnr)
							WHERE IC_pouleindeling.lidnr='" . $deelnemernr . "' AND IC_poules.poulestatus>2");
		if ($aantalpoules != 0)
			{
				echo "<h2>Gespeelde wedstrijden</h2>";
				$poules = $db->get_array("
							 SELECT * FROM IC_pouleindeling
								LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
								LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
								LEFT JOIN IC_clubs ON (IC_competities.clubnr = IC_clubs.clubnr)
							WHERE IC_pouleindeling.lidnr='" . $deelnemernr . "' AND IC_poules.poulestatus='3'
							ORDER BY IC_competities.datum DESC");
				foreach ($poules as $poule)
					{
						$poulesize = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE poulenr=" . $poule["poulenr"]);
						if ($poulesize == 3 || $poulesize == 4)
							{
								echo "	<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
											<tr>
												<td colspan=\"7\">" . strftime("%e %B %Y", strtotime($poule["datum"])) . " bij " . $poule["clubnaam"] . "</td>
												<td>Gew</td>
												<td>Wed</td>
												<td>Plaats</td>
											</tr>";
								$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling 
															 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
																WHERE IC_pouleindeling.poulenr=" . $poule["poulenr"]);
								foreach ($deelnemers as $deelnemer)
									{
										$gewonnen = 0;
										$punten = 0;
										$rating = bepaal_rating ($deelnemer["lidnr"], $poule["datum"]);
										echo "	<tr>
													<td>" . naam ($deelnemer["lidnr"]) . " [" . $rating . "]<br>van ".  $deelnemer["clubnaam"] . "</td>";
										for ($i = 1; $i < 7; $i++)
											{
												$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden
																				LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																				WHERE IC_wedstrijden.poulenr='" . $poule["poulenr"] . "' 
																					AND IC_wedstrijden.volgorde='" . $i . "' 
																					AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
												$aantalwedstrijd = $db->num_rows("SELECT * FROM IC_wedstrijden
																					LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																					WHERE IC_wedstrijden.poulenr='" . $poule["poulenr"] . "' 
																						AND IC_wedstrijden.volgorde='" . $i . "' 
																						AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
												if ($aantalwedstrijd == 0) // Geen wedstrijd voor deze judoka, print lege zel
													{
														echo "<td bgcolor=\"#d3d3d3\"></td>";
													}
												else // wel wedstrijd
													{
														if ($wedstrijd[0]["uitslag"] > 0)
															{
																$gewonnen = $gewonnen + 1;
																$punten = $punten + $wedstrijd[0]["uitslag"];
															}
														echo "<td width=\"25\"><center>" . $wedstrijd[0]["uitslag"] . "</center></td>";
													}
											}
										echo "<td><center>" . $gewonnen . "</center></td><td><center>" . $punten . "</center></td><td><center>"  . $deelnemer["plaats"] . "</center></td></tr>";
									}
								echo "</table><br><br>";
							}
						if ($poulesize == 5)
							{
								echo "	<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
											<tr>
												<td colspan=\"11\">" . strftime("%e %B %Y", strtotime($poule["datum"])) . " bij " . $poule["clubnaam"] . "</td>
												<td>Gew</td>
												<td>Wed</td>
												<td>Plaats</td>
											</tr>";
								$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling 
															 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
																WHERE IC_pouleindeling.poulenr=" . $poule["poulenr"]);
								foreach ($deelnemers as $deelnemer)
									{
										$gewonnen = 0;
										$punten = 0;
										echo "	<tr>
													<td>" . naam ($deelnemer["lidnr"]) . " (".  $deelnemer["clubnaam"] . ") </td>";
										for ($i = 1; $i < 11; $i++)
											{
												$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden
																				LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																				WHERE IC_wedstrijden.poulenr='" . $poule["poulenr"] . "' 
																					AND IC_wedstrijden.volgorde='" . $i . "' 
																					AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
												$aantalwedstrijd = $db->num_rows("SELECT * FROM IC_wedstrijden
																					LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																					WHERE IC_wedstrijden.poulenr='" . $poule["poulenr"] . "' 
																						AND IC_wedstrijden.volgorde='" . $i . "' 
																						AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
												if ($aantalwedstrijd == 0) // Geen wedstrijd voor deze judoka, print lege zel
													{
														echo "<td bgcolor=\"#d3d3d3\"></td>";
													}
												else // wel wedstrijd
													{
														if ($wedstrijd[0]["uitslag"] > 0)
															{
																$gewonnen = $gewonnen + 1;
																$punten = $punten + $wedstrijd[0]["uitslag"];
															}
														echo "<td><center>" . $wedstrijd[0]["uitslag"] . "</center></td>";
													}
											}
										echo "<td><center>" . $gewonnen . "</center></td><td><center>" . $punten . "</center></td><td><center>"  . $deelnemer["plaats"] . "</center></td></tr>";
									}
								echo "</table><br><br>";
							}
						if ($poulesize == 6)
							{
								echo "	<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
											<tr>
												<td colspan=\"16\">" . strftime("%e %B %Y", strtotime($poule["datum"])) . " bij " . $poule["clubnaam"] . "</td>
												<td>Gew</td>
												<td>Wed</td>
												<td>Plaats</td>
											</tr>";
								$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling 
															 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
																WHERE IC_pouleindeling.poulenr=" . $poule["poulenr"]);
								foreach ($deelnemers as $deelnemer)
									{
										$gewonnen = 0;
										$punten = 0;
										echo "	<tr>
													<td>" . naam ($deelnemer["lidnr"]) . " (".  $deelnemer["clubnaam"] . ") </td>";
										for ($i = 1; $i < 16; $i++)
											{
												$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden
																				LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																				WHERE IC_wedstrijden.poulenr='" . $poule["poulenr"] . "' 
																					AND IC_wedstrijden.volgorde='" . $i . "' 
																					AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
												$aantalwedstrijd = $db->num_rows("SELECT * FROM IC_wedstrijden
																					LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																					WHERE IC_wedstrijden.poulenr='" . $poule["poulenr"] . "' 
																						AND IC_wedstrijden.volgorde='" . $i . "' 
																						AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
												if ($aantalwedstrijd == 0) // Geen wedstrijd voor deze judoka, print lege zel
													{
														echo "<td bgcolor=\"#d3d3d3\"></td>";
													}
												else // wel wedstrijd
													{
														if ($wedstrijd[0]["uitslag"] > 0)
															{
																$gewonnen = $gewonnen + 1;
																$punten = $punten + $wedstrijd[0]["uitslag"];
															}
														echo "<td><center>" . $wedstrijd[0]["uitslag"] . "</center></td>";
													}
											}
										echo "<td><center>" . $gewonnen . "</center></td><td><center>" . $punten . "</center></td><td><center>"  . $deelnemer["plaats"] . "</center></td></tr>";
									}
								echo "</table><br><br>";
							}
					}
			}
	}
	

?>