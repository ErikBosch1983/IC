<?php
$poulenr = check_poulenr ($_GET["poulenr"]);

$wedstrijdcount = array (2=> 4, 3 => 7, 4 => 7, 5 => 11, 6 => 16);

if ($poulenr != "false")
	{
		echo "<h1>Poule overzicht</h1>";
		$poule = $db->get_array("SELECT * FROM IC_poules
									LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
									LEFT JOIN IC_clubs ON (IC_competities.clubnr = IC_clubs.clubnr)
								WHERE IC_poules.poulenr='" . $poulenr . "'");
		
		$poulesize = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE poulenr=" . $poulenr);

		echo "	<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
					<tr>
						<td colspan=\"" . $wedstrijdcount[$poulesize] . "\">" . strftime("%e %B %Y", strtotime($poule[0]["datum"])) . " bij " . $poule[0]["clubnaam"] . "</td>
						<td>Gew</td>
						<td>Wed</td>
						<td>Plaats</td>
					</tr>";
		$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling 
										LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
										LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
										WHERE IC_pouleindeling.poulenr=" . $poulenr . "
										ORDER BY IC_pouleindeling.volgorde");
		foreach ($deelnemers as $deelnemer)
			{
				$gewonnen = 0;
				$punten = 0;
				$rating = bepaal_rating ($deelnemer["lidnr"], $poule[0]["datum"]);
				echo "	<tr>
							<td>" . naam ($deelnemer["lidnr"]) . " [" . $rating . "]<br>van ".  $deelnemer["clubnaam"] . "</td>";
				for ($i = 1; $i < $wedstrijdcount[$poulesize]; $i++)
					{
						$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden
														LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
														WHERE IC_wedstrijden.poulenr='" . $poulenr . "' 
															AND IC_wedstrijden.volgorde='" . $i . "' 
															AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
						if (empty($wedstrijd)) // Geen wedstrijd voor deze judoka, print lege zel
							{
								echo "<td bgcolor=\"#d3d3d3\" width=\"25\"></td>";
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
		if ($poule[0]["poulestatus"] != 3)
			{
				echo "	<tr>
							<td></td>";
				for ($i = 1; $i < $wedstrijdcount[$poulesize]; $i++)
					{
						$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden
														LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
														WHERE IC_wedstrijden.poulenr='" . $poulenr . "' 
															AND IC_wedstrijden.volgorde='" . $i . "'");
						if (empty($wedstrijd))
							{
								echo "<td></td>";
							}
						else
							{
								echo "<td><a href=\"uitslag_delete.php?wedstrijdnr=" . $wedstrijd[0]["wedstrijdnr"] . "\">X</a></td>";
							}
					}
					
				echo "		<td></td><td></td><td></td>
						</tr>";
			}
		echo "
			</table><br>";
		echo "<a href=\"poule_formulier_aanmaken.php?poulenr=" . $poulenr . "\">Poule formulier aanmaken</a><br><br>";
		
		////
		// Lijst met wedstrijden 
		////
		
		$matches = $db->get_array("SELECT * FROM IC_matches WHERE size='" . $poulesize . "' ORDER BY volgorde");
		$i = 0;
		foreach ($matches as $match)
			{
				$wedstrijden = $db->get_array("SELECT * FROM IC_wedstrijden WHERE poulenr='" . $poulenr . "' AND volgorde='" . $match["volgorde"] . "'");
				if (empty ($wedstrijden))
					{
						$i = $i +1;
						$spelerrood = $db->get_array("SELECT * FROM IC_pouleindeling
														LEFT JOIN IC_deelnemers ON (IC_pouleindeling.lidnr = IC_deelnemers.lidnr)
														WHERE IC_pouleindeling.poulenr='" . $poulenr . "'
															AND IC_pouleindeling.volgorde='" . $match["spelerrood"] . "'");

						$spelerwit = $db->get_array("SELECT * FROM IC_pouleindeling
														LEFT JOIN IC_deelnemers ON (IC_pouleindeling.lidnr = IC_deelnemers.lidnr)
														WHERE IC_pouleindeling.poulenr='" . $poulenr . "'
															AND IC_pouleindeling.volgorde='" . $match["spelerwit"] . "'");
						
						echo "	<form name=\"wedstrijd_invullen\" method=\"post\" action=\"uitslag_add.php\">
									<input type=\"hidden\" name=\"poulenr\" value=\"" . $poulenr . "\">
									<input type=\"hidden\" name=\"volgorde\" value=\"" . $match["volgorde"] . "\">
									<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
										<tr>
											<td></td>
											<td>Ippon</td>
											<td>Waza-ari</td>
											<td>Beslissing</td>
											<td></td>
										</tr>
										<tr>
											<td>" . naam($spelerwit[0]["lidnr"]) . "</td>
											<td><center><input type=\"radio\" name=\"score\" value=\"wit10\"></center></td>
											<td><center><input type=\"radio\" name=\"score\" value=\"wit7\"></center></td>
											<td><center><input type=\"radio\" name=\"score\" value=\"wit1\"></center></td>
											<td></td>
										</tr>
										<tr>
											<td>" . naam($spelerrood[0]["lidnr"]) . "</td>
											<td><center><input type=\"radio\" name=\"score\" value=\"rood10\"></center></td>
											<td><center><input type=\"radio\" name=\"score\" value=\"rood7\"></center></td>
											<td><center><input type=\"radio\" name=\"score\" value=\"rood1\"></center></td>
											<td><input type=\"submit\" value=\"Invoeren\"></td>
										</tr>
									</table>
								</form><br>";
					}
			}
		if ($i == 0 && $poule[0]["poulestatus"] == 2)
			{
				echo "<br><a href=\"poule_bepaal_score.php?poulenr=" . $poulenr . "\">score bepalen</a>";
			}
		if ($poule[0]["poulestatus"] == 4)
			{
				
				$onbekenden = $db->get_array("SELECT * FROM IC_pouleindeling
											 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
												LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
												WHERE IC_pouleindeling.poulenr = '" . $poulenr . "' AND IC_pouleindeling.plaats='100'
												ORDER BY IC_pouleindeling.volgorde");
				$poulesize2 = count($onbekenden);
				$wedstrijdcount2 = array (2 => 2, 3 => 4, 4 => 7, 5 => 11, 6 => 16);
				
				echo "	<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
							<tr>
								<td colspan=\"" . $wedstrijdcount2[$poulesize2] . "\">" . strftime("%e %B %Y", strtotime($poule[0]["datum"])) . " bij " . $poule[0]["clubnaam"] . "</td>
								<td>Gew</td>
								<td>Wed</td>
								<td>Plaats</td>
							</tr>";
				
				foreach ($onbekenden as $deelnemer)
					{
						$gewonnen = 0;
						$punten = 0;
						$rating = bepaal_rating ($deelnemer["lidnr"], $poule["datum"]);
						echo "	<tr>
									<td>" . naam ($deelnemer["lidnr"]) . " [" . $rating . "]<br>van ".  $deelnemer["clubnaam"] . "</td>";
						for ($i = 1; $i < $wedstrijdcount2[$poulesize2]; $i++)
							{
								$j = $wedstrijdcount[$poulesize] - 1 + $i;
								$wedstrijd = $db->get_array("SELECT * FROM IC_wedstrijden
																LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																WHERE IC_wedstrijden.poulenr='" . $poulenr . "' 
																	AND IC_wedstrijden.volgorde='-" . $i . "' 
																	AND IC_wedstrijdlid.lidnr=" . $deelnemer["lidnr"]);
								if (empty($wedstrijd)) // Geen wedstrijd voor deze judoka, print lege zel
									{
										echo "<td bgcolor=\"#d3d3d3\" width=\"25\"></td>";
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
				if ($poule[0]["poulestatus"] != 3)
					{
						echo "	<tr>
									<td></td>";
						for ($i = 1; $i < $wedstrijdcount2[$poulesize2]; $i++)
							{
								$j = $wedstrijdcount[$poulesize] - 1 + $i;
								$wedstrijd2 = $db->get_array("SELECT * FROM IC_wedstrijden
																LEFT JOIN IC_wedstrijdlid ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																WHERE IC_wedstrijden.poulenr='" . $poulenr . "' 
																	AND IC_wedstrijden.volgorde='-" . $i . "'");
								if (empty($wedstrijd2))
									{
										echo "<td></td>";
									}
								else
									{
										echo "<td><a href=\"uitslag_delete.php?wedstrijdnr=" . $wedstrijd2[0]["wedstrijdnr"] . "\">X</a></td>";
									}
							}
							
						echo "		<td></td><td></td><td></td>
								</tr>";
					}
				echo "
					</table>";
			}
		////
		// Lijst met wedstrijden 
		////
		
		$matches = $db->get_array("SELECT * FROM IC_matches WHERE size='-" . $poulesize2 . "' ORDER BY volgorde");
		if (!empty ($matches))
			{
				$i = 0;
				foreach ($matches as $match)
					{
						$wedstrijden = $db->get_array("SELECT * FROM IC_wedstrijden WHERE poulenr='" . $poulenr . "' AND volgorde='" . $match["volgorde"] . "'");
						
						if (empty ($wedstrijden))
							{
								$i = $i +1;
								$spelerrood	 = $onbekenden[$match["spelerrood"]]["lidnr"];
								$spelerwit	 = $onbekenden[$match["spelerwit"]]["lidnr"];
													
								echo "	<form name=\"wedstrijd_invullen\" method=\"post\" action=\"uitslag_add2.php\">
											<input type=\"hidden\" name=\"poulenr\" value=\"" . $poulenr . "\">
											<input type=\"hidden\" name=\"volgorde\" value=\"" . $match["volgorde"] . "\">
											<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
												<tr>
													<td></td>
													<td>Ippon</td>
													<td>Waza-ari</td>
													<td>Beslissing</td>
													<td></td>
												</tr>
												<tr>
													<td>" . naam($spelerwit) . "</td>
													<td><center><input type=\"radio\" name=\"score\" value=\"wit10\"></center></td>
													<td><center><input type=\"radio\" name=\"score\" value=\"wit7\"></center></td>
													<td><center><input type=\"radio\" name=\"score\" value=\"wit1\"></center></td>
													<td></td>
												</tr>
												<tr>
													<td>" . naam($spelerrood) . "</td>
													<td><center><input type=\"radio\" name=\"score\" value=\"rood10\"></center></td>
													<td><center><input type=\"radio\" name=\"score\" value=\"rood7\"></center></td>
													<td><center><input type=\"radio\" name=\"score\" value=\"rood1\"></center></td>
													<td><input type=\"submit\" value=\"Invoeren\"></td>
												</tr>
											</table>
										</form>";
							}
					}
			}
		if ($i == 0 && $poule[0]["poulestatus"] == 4)
			{
				echo "<br><a href=\"poule_bepaal_score2.php?poulenr=" . $poulenr . "\">score bepalen</a>";
			}
	}
else
	{
		echo "incorrect poulenr";
	}


?>