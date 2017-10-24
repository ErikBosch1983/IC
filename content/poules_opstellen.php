<?php
$competitienr = check_competitiedagnr($_GET["competitiedag"]);
$gewichtsklassenr = check_gewichtsklassenr($_GET["gewichtsklassenr"]);
$gewichtlozen = $db->num_rows("SELECT * FROM IC_pouleindeling
							  		LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
									WHERE IC_deelnemers.gewicht='0.0'
										AND IC_pouleindeling.competitienr='" . $competitienr . "'");
echo "<h1>Poules opstellen</h1>";
if ($competitienr != "false" && $gewichtsklassenr != "false" && $gewichtlozen==0)
	{
		$knip = (int)$_GET["knip"];
		if ($knip != 0)
			{
				$aantalh = $db->num_rows("SELECT * FROM IC_pouleindeling
											LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
											LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
											LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
											WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
												AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
												AND IC_deelnemers.rating>=" . $knip . "
											ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
				$aantall = $db->num_rows("SELECT * FROM IC_pouleindeling
											LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
											LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
											LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
											WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
												AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
												AND IC_deelnemers.rating<" . $knip . "
											ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
				if ($aantalh <= 2 || $aantall <= 2)
					{
						$knip = 0;
					}
			}
		if ($knip == 0)
			{
				echo "<h2>Zonder knip</h2>";
				
				$kniprating = 0;
				
				////
				// Bepaal het aantal poules dat gemaakt gaat worden
				////
				
				$aantal = $db->num_rows("SELECT * FROM IC_pouleindeling
										LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
										LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
										LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
										LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
										WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
											AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
											AND (IC_pouleindeling.poulenr='0' OR (IC_pouleindeling.poulenr<>'0' AND IC_poules.poulestatus='0'))
										ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
				if ($aantal == 0)
					{
						echo "Nieumand om in te delen";
					}
				else
					{	
						$poules = floor ($aantal / 4);
						if ($poules == 0)
							{
								$poules = 1;
							}
						if ($aantal == 6 || $aantal == 7)
							{
								$poules = 2;
							}
						if ($aantal == 11)
							{
								$poules = 3;
							}

						
						////
						// Controleren of er een poule in aanmaak is en bepalen of er een nieuwe poule gemaakt moet worden
						////
						
						$pouleinaanmaak = $db->get_array("SELECT * FROM IC_poules
																	RIGHT JOIN IC_pouleindeling ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
																	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																	LEFT JOIN IC_clubs ON (IC_deelnemers.clubnr = IC_clubs.clubnr)
																	WHERE IC_poules.competitienr='" . $competitienr . "'
																		AND IC_poules.gewichtsklassenr='" . $gewichtsklassenr . "'
																		AND IC_poules.poulestatus='0'");
						if (!empty($pouleinaanmaak))
							{
								$nieuwpoule = 0;
								$poulenr = $pouleinaanmaak[0]["poulenr"];
								echo "Poule bestaat";
					
								if ($aantal >= 12)
									{
										if (count($pouleinaanmaak) == 4)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 11)
									{
										if (count($pouleinaanmaak) == 3)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 10)
									{
										if (count($pouleinaanmaak) == 5)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 9)
									{
										if (count($pouleinaanmaak) == 4)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 8)
									{
										if (count($pouleinaanmaak) == 4)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 7)
									{
										if (count($pouleinaanmaak) == 3)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 6)
									{
										if (count($pouleinaanmaak) == 3)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
							}
						else
							{
								$nieuwpoule = 1;
								$poulenr = 0;
								echo "Poule opstarten";
							}
		
	
						echo "	Aantal deelnemers: " . $aantal. "<br>
								Aantal poules: " . $poules . "<br>";
						
						$graduatie = 0;
						if ($nieuwpoule == 0)
							{
								echo "	<h3>Poule in aanmaak" . count($pouleinaanmaak) . "</h3>
										<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
								$deelnemerss = array ();
								foreach ($pouleinaanmaak as $poulelid)
									{
										echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
										$deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
									}
								echo "</table><br>";
								
								$deelnemerss = array ();
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$deelnemerss[$club["clubnr"]] = 0;
									}
								echo "<br><br>";
								print_r($deelnemerss);
								echo "<br><br>";
								foreach ($pouleinaanmaak as $poulelid)
									{
										echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
										$deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
									}
								echo "</table><br>";
								
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$aantalclub = $db->num_rows("SELECT * FROM IC_pouleindeling
																		LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																		WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
																			AND (IC_pouleindeling.poulenr='0' OR IC_pouleindeling.poulenr='" . $poulelid["poulenr"] . "') 
																			AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
																			AND IC_deelnemers.clubnr=" . $club["clubnr"] . "
																			AND IC_deelnemers.rating<'" . $kniprating . "'
																		ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
										if ($aantalclub > 0)
											{
												$judokaasinpoule = $deelnemerss[$club["clubnr"]];
												$judokaasmoetinpoule = round ($aantalclub/$poules);
												echo $club["clubnaam"] . " heeft " . $aantalclub . " deelnemers, dat is " . $judokaasmoetinpoule . " per poule en al " . $judokaasinpoule . " in de poule<br>";
												if (round ($aantalclub/$poules) > $deelnemerss[$club["clubnr"]])
													{
														if ($clublistin == "")
															{
																$clublistin = $club["clubnr"];
															}
														else
															{
																$clublistin .= "en" . $club["clubnr"];
															}
													}
												if ($judokaasinpoule >= $judokaasmoetinpoule && $judokaasinpoule>0)
													{
														if ($clublistuit == "")
															{
																$clublistuit = $club["clubnr"];
															}
														else
															{
																$clublistuit .= "en" . $club["clubnr"];
															}
													}
												echo "verplicht" . $clublistin . "<br>";
												echo "uitsluiten" . $clublistuit . "<br>";
											}
									}
								if ($clublistin != "")
									{
										$clubreq = 1;
										$clublist = $clublistin;
									}
								elseif ($clublistuit != "")
									{
										$clubreq = 2;
										$clublist = $clublistuit;
									}
								else
									{
										$clubreq = 0;
										$clublist = "";
									}
								
								$aantalwitinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "inpoule", $poulenr, 0, 0);
								$aantalgeelinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "inpoule", $poulenr, 0, 0);
								$aantaloranjeinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "inpoule", $poulenr, 0, 0);
								echo "<br>" . $aantalwitinpoule . "/" . $aantalgeelinpoule . "/" . $aantaloranjeinpoule; 
								$aantalwitinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "inclusief", $poulenr, 0, 0);
								$aantalgeelinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "inclusief", $poulenr, 0, 0);
								$aantaloranjeinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "inclusief", $poulenr, 0, 0);
								echo "<br>" . $aantalwitinclusief . "/" . $aantalgeelinclusief . "/" . $aantaloranjeinclusief . "<br>"; 
								
								if ($poules > 2)
									{
										// nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.
										echo "nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.";
										if ($aantalwitinpoule != 0)
											{
												$gradreq = 2;
												$graduatie = 4;
												$drop = 2;
											}
										elseif ($aantaloranjeinpoule != 0)
											{
												$gradreq = 2;
												$graduatie = 6;
												$drop = 2;
											}
										else
											{
												$gradreq = 0;
												$graduatie = 6;
												$drop = 1;
											}
									}
								if ($poules == 2)
									{
										// De voorlaatste poule wordt ingevuld. Zorgen dat witte en oranje banders gescheiden blijven.
										$drop = 2;
										if ($aantalwitinclusief != 0 && $aantaloranjeinclusief != 0)
											{
												echo "Zowel wit als oranje komen voor";
												if (bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "exclusief", $poulenr, 0, 0) > 0)
													{
														// Er zijn nog niet ingedeelde witte banders, die moeten hier in.
														$gradreq = 1;
														$graduatie = 6;
														// clubeis wordt meegenomen, maar die is ondergeschikt aan graduatie eis
														$drop = 2;
													}
												else
													{
														// De witte banders zijn op. Oranje banders uitsluiten
														$gradreq = 2;
														$graduatie = 4;
														$drop = 2;
													}
											}
										else
											{
												// Er komeen geen witte en oranje banders voor. Geen eisen voor de graduaties.
												echo "Geen wit en oranje";
												$gradreq = 0;
											}
									}
								if ($poules == 1)
									{
										// De laatste poule wordt ingevuld. Geen eisen
										echo "De laatste poule wordt ingevuld. Geen eisen";
										$drop = 0;
										$gradreq = 0;
										$clubreq = 0;
									}
							}
						else
							{
								// Er wordt een nieuwe poule aangemaakt
								$aantalwitexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "exclusief", $poulenr, 0, 0);
								$aantalgeelexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "exclusief", $poulenr, 0, 0);
								$aantaloranjeexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "exclusief", $poulenr, 0, 0);
								echo "<br>" . $aantalwitexclusief . "/" . $aantalgeelexclusief . "/" . $aantaloranjeexclusief . "<br>"; 
								
								$deelnemerss = array ();
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$deelnemerss[$club["clubnr"]] = 0;
									}
								echo "<br><br>";
								print_r($deelnemerss);
								echo "<br><br>";
								foreach ($pouleinaanmaak as $poulelid)
									{
										echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
										$deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
									}
								echo "</table><br>";
								
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$aantalclub = $db->num_rows("SELECT * FROM IC_pouleindeling
																		LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																		WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
																			AND (IC_pouleindeling.poulenr='0' OR IC_pouleindeling.poulenr='" . $poulelid["poulenr"] . "') 
																			AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
																			AND IC_deelnemers.clubnr=" . $club["clubnr"] . "
																			AND IC_deelnemers.rating<'" . $kniprating . "'
																		ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
										if ($aantalclub > 0)
											{
												$judokaasinpoule = $deelnemerss[$club["clubnr"]];
												$judokaasmoetinpoule = round ($aantalclub/$poules);
												echo $club["clubnaam"] . " heeft " . $aantalclub . " deelnemers, dat is " . $judokaasmoetinpoule . " per poule en al " . $judokaasinpoule . " in de poule<br>";
												if (round ($aantalclub/$poules) > $deelnemerss[$club["clubnr"]])
													{
														if ($clublistin == "")
															{
																$clublistin = $club["clubnr"];
															}
														else
															{
																$clublistin .= "en" . $club["clubnr"];
															}
													}
												if ($judokaasinpoule >= $judokaasmoetinpoule && $judokaasinpoule>0)
													{
														if ($clublistuit == "")
															{
																$clublistuit = $club["clubnr"];
															}
														else
															{
																$clublistuit .= "en" . $club["clubnr"];
															}
													}
												echo "verplicht" . $clublistin . "<br>";
												echo "uitsluiten" . $clublistuit . "<br>";
											}
									}
								if ($clublistin != "")
									{
										$clubreq = 1;
										$clublist = $clublistin;
									}
								elseif ($clublistuit != "")
									{
										$clubreq = 2;
										$clublist = $clublistuit;
									}
								else
									{
										$clubreq = 0;
										$clublist = "";
									}
								
								
								if ($poules > 2)
									{
										// nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.
										// Geen graduatie eisen
										$gradreq = 0;
										$graduatie = 0;
										$drop = 0;
									}
								if ($poules == 2)
									{
										// De voorlaatste poule wordt opgestart. Zorgen dat witte en oranje banders gescheiden blijven.
										$drop = 2;
										if ($aantalwitexclusief != 0 && $aantaloranjeexclusief != 0)
											{
												echo "Zowel wit als oranje komen voor";
												if ($aantaloranjeexclusief > 0)
													{
														// Er zijn nog niet ingedeelde witte banders, die moeten hier in.
														$gradreq = 1;
														$graduatie = 6;
														// clubeis wordt meegenomen, maar die is ondergeschikt aan graduatie eis
														$drop = 2;
													}
											}
										else
											{
												// Er komeen geen witte en oranje banders voor. Geen eisen voor de graduaties.
												echo "Geen wit en oranje";
												$gradreq = 0;
											}
									}
								if ($poules == 1)
									{
										// De laatste poule wordt ingevuld. Geen eisen
										echo "De laatste poule wordt ingevuld. Geen eisen";
										$drop = 0;
										$gradreq = 0;
										$clubreq = 0;
									}
							}
		
								
		
						echo "<br>" .  $competitienr. "-" . $gewichtsklassenr. "-" . $nieuwpoule. "-" . $poulenr. "-" . $knip. "-" . $kniprating. "-" . $gradreq. "-" . $graduatie. "-" . $clubreq . "-" . $clublist. "-" . $drop . "<br><br>";
						zoek_judoka ($competitienr, $gewichtsklassenr, $nieuwpoule, $poulenr, $knip, $kniprating, $gradreq, $graduatie, $clubreq, $clublist, $drop);
						
						echo "<br>";
						
						$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling
										LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
										LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
										LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
										WHERE IC_pouleindeling.competitienr='" . $competitienr . "' AND IC_pouleindeling.poulenr='0' AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
										ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");		
						if (!empty($deelnemers))
							{
								echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"3\">";
								foreach ($deelnemers as $deelnemer)
									{
										echo "	<tr><td>" . naam ($deelnemer["lidnr"]) . "</td><td>" . $deelnemer["clubnaam"] . "</td><td>" . $deelnemer["rating"] . "</td><td>" . $deelnemer["graduatie"] . "</td></tr>";
									}
								echo "</table>";
							}
						else
							{
								$db->run_query("UPDATE IC_poules set poulestatus='1' WHERE poulenr=" . $poulenr);
								echo "Iedereen ingedeeld, poule gesloten";
							}

					}
			}
		else
			{
				echo "Pouleindeling met knip";
				$knip = 1;
				$kniprating = (int)$_GET["knip"];
				
				////
				// Eerst iedereen onder de knip indelen.
				////
				
				$aantal = $db->num_rows("SELECT * FROM IC_pouleindeling
											LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
											LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
											LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
											LEFT JOIN IC_poules ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
											WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
												AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
												AND (IC_pouleindeling.poulenr='0' OR (IC_pouleindeling.poulenr<>'0' AND IC_poules.poulestatus='0'))
												AND IC_deelnemers.rating<" . $kniprating . "
											ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
				if ($aantal == 0)
					{
						echo "Niemand om in te delen";
						$knip = 2;
						$aantal = $db->num_rows("SELECT * FROM IC_pouleindeling
												  LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
												  LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
												  LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
												  LEFT JOIN IC_poules ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
												  WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
													  AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
													  AND IC_deelnemers.rating>=" . $kniprating . "
													  AND (IC_pouleindeling.poulenr='0' OR (IC_pouleindeling.poulenr<>'0' AND IC_poules.poulestatus='0'))
												  ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
					  if ($aantal == 0)
						  {
							  echo "Niemand om in te delen";
						  }
					  else
						  {	
							  $poules = floor ($aantal / 4);
							  if ($poules == 0)
								  {
									  $poules = 1;
								  }
							  if ($aantal == 6 || $aantal == 7)
								  {
									  $poules = 2;
								  }
							  if ($aantal == 11)
								  {
									  $poules = 3;
								  }
	  
							  
							  ////
							  // Controleren of er een poule in aanmaak is en bepalen of er een nieuwe poule gemaakt moet worden
							  ////
							  
							  $pouleinaanmaak = $db->get_array("SELECT * FROM IC_poules
																		  RIGHT JOIN IC_pouleindeling ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
																		  LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																		  LEFT JOIN IC_clubs ON (IC_deelnemers.clubnr = IC_clubs.clubnr)
																		  WHERE IC_poules.competitienr='" . $competitienr . "'
																			  AND IC_poules.gewichtsklassenr='" . $gewichtsklassenr . "'
																			  AND IC_poules.poulestatus='0'");
							  if (!empty($pouleinaanmaak))
								  {
									  $nieuwpoule = 0;
									  $poulenr = $pouleinaanmaak[0]["poulenr"];
									  echo "Poule bestaat";
						  
									  if ($aantal >= 12)
										  {
											  if (count($pouleinaanmaak) == 4)
												  {
													  $nieuwpoule = 2;
													  echo "Nieuwe Poule opstarten, oude sluiten";
												  }
										  }
									  elseif ($aantal == 11)
										  {
											  if (count($pouleinaanmaak) == 3)
												  {
													  $nieuwpoule = 2;
													  echo "Nieuwe Poule opstarten, oude sluiten";
												  }
										  }
									  elseif ($aantal == 10)
										  {
											  if (count($pouleinaanmaak) == 5)
												  {
													  $nieuwpoule = 2;
													  echo "Nieuwe Poule opstarten, oude sluiten";
												  }
										  }
									  elseif ($aantal == 9)
										  {
											  if (count($pouleinaanmaak) == 4)
												  {
													  $nieuwpoule = 2;
													  echo "Nieuwe Poule opstarten, oude sluiten";
												  }
										  }
									  elseif ($aantal == 8)
										  {
											  if (count($pouleinaanmaak) == 4)
												  {
													  $nieuwpoule = 2;
													  echo "Nieuwe Poule opstarten, oude sluiten";
												  }
										  }
									  elseif ($aantal == 7)
										  {
											  if (count($pouleinaanmaak) == 3)
												  {
													  $nieuwpoule = 2;
													  echo "Nieuwe Poule opstarten, oude sluiten";
												  }
										  }
									  elseif ($aantal == 6)
										  {
											  if (count($pouleinaanmaak) == 3)
												  {
													  $nieuwpoule = 2;
													  echo "Nieuwe Poule opstarten, oude sluiten";
												  }
										  }
								  }
							  else
								  {
									  $nieuwpoule = 1;
									  $poulenr = 0;
									  echo "Poule opstarten";
								  }
			  
		  
							  echo "	Aantal deelnemers: " . $aantal. "<br>
									  Aantal poules: " . $poules . "<br>";
							  
							  $graduatie = 0;
							  if ($nieuwpoule == 0)
								  {
									  echo "	<h3>Poule in aanmaak" . count($pouleinaanmaak) . "</h3>
											  <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
									  $deelnemerss = array ();
									  $clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
									  foreach ($clubs as $club)
										  {
											  $deelnemerss[$club["clubnr"]] = 0;
										  }
									  echo "<br><br>";
									  print_r($deelnemerss);
									  echo "<br><br>";
									  foreach ($pouleinaanmaak as $poulelid)
										  {
											  echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
											  $deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
										  }
									  echo "</table><br>";
									  
									  $deelnemerss = array ();
									  $clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
									  foreach ($clubs as $club)
										  {
											  $deelnemerss[$club["clubnr"]] = 0;
										  }
									  echo "<br><br>";
									  print_r($deelnemerss);
									  echo "<br><br>";
									  foreach ($pouleinaanmaak as $poulelid)
										  {
											  echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
											  $deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
										  }
									  echo "</table><br>";
									  
									  $clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
									  foreach ($clubs as $club)
										  {
											  $aantalclub = $db->num_rows("SELECT * FROM IC_pouleindeling
																			  LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																			  WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
																				  AND (IC_pouleindeling.poulenr='0' OR IC_pouleindeling.poulenr='" . $poulelid["poulenr"] . "') 
																				  AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
																				  AND IC_deelnemers.clubnr=" . $club["clubnr"] . "
																				  AND IC_deelnemers.rating<'" . $kniprating . "'
																			  ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
											  if ($aantalclub > 0)
												  {
													  $judokaasinpoule = $deelnemerss[$club["clubnr"]];
													  $judokaasmoetinpoule = round ($aantalclub/$poules);
													  echo $club["clubnaam"] . " heeft " . $aantalclub . " deelnemers, dat is " . $judokaasmoetinpoule . " per poule en al " . $judokaasinpoule . " in de poule<br>";
													  if (round ($aantalclub/$poules) > $deelnemerss[$club["clubnr"]])
														  {
															  if ($clublistin == "")
																  {
																	  $clublistin = $club["clubnr"];
																  }
															  else
																  {
																	  $clublistin .= "en" . $club["clubnr"];
																  }
														  }
													  if ($judokaasinpoule >= $judokaasmoetinpoule && $judokaasinpoule>0)
														  {
															  if ($clublistuit == "")
																  {
																	  $clublistuit = $club["clubnr"];
																  }
															  else
																  {
																	  $clublistuit .= "en" . $club["clubnr"];
																  }
														  }
													  echo "verplicht" . $clublistin . "<br>";
													  echo "uitsluiten" . $clublistuit . "<br>";
												  }
										  }
									  if ($clublistin != "")
										  {
											  $clubreq = 1;
											  $clublist = $clublistin;
										  }
									  elseif ($clublistuit != "")
										  {
											  $clubreq = 2;
											  $clublist = $clublistuit;
										  }
									  else
										  {
											  $clubreq = 0;
											  $clublist = "";
										  }
									  
									  $aantalwitinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "inpoule", $poulenr, 2, $kniprating);
									  $aantalgeelinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "inpoule", $poulenr, 2, $kniprating);
									  $aantaloranjeinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "inpoule", $poulenr, 2, $kniprating);
									  echo "<br>" . $aantalwitinpoule . "/" . $aantalgeelinpoule . "/" . $aantaloranjeinpoule; 
									  $aantalwitinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "inclusief", $poulenr, 2, $kniprating);
									  $aantalgeelinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "inclusief", $poulenr, 2, $kniprating);
									  $aantaloranjeinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "inclusief", $poulenr, 2, $kniprating);
									  echo "<br>" . $aantalwitinclusief . "/" . $aantalgeelinclusief . "/" . $aantaloranjeinclusief . "<br>"; 
									  
									  if ($poules > 2)
										  {
											  // nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.
											  echo "nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.";
											  if ($aantalwitinpoule != 0)
												  {
													  $gradreq = 2;
													  $graduatie = 4;
													  $drop = 2;
												  }
											  if ($aantaloranjeinpoule != 0)
												  {
													  $gradreq = 2;
													  $graduatie = 6;
													  $drop = 2;
												  }
										  }
									  if ($poules == 2)
										  {
											  // De voorlaatste poule wordt ingevuld. Zorgen dat witte en oranje banders gescheiden blijven.
											  $drop = 2;
											  if ($aantalwitinclusief != 0 && $aantaloranjeinclusief != 0)
												  {
													  echo "Zowel wit als oranje komen voor";
													  if (bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "exclusief", $poulenr, 2, $kniprating) > 0)
														  {
															  // Er zijn nog niet ingedeelde witte banders, die moeten hier in.
															  $gradreq = 1;
															  $graduatie = 6;
															  // clubeis wordt meegenomen, maar die is ondergeschikt aan graduatie eis
															  $drop = 2;
														  }
													  else
														  {
															  // De witte banders zijn op. Oranje banders uitsluiten
															  $gradreq = 2;
															  $graduatie = 4;
															  $drop = 2;
														  }
												  }
											  else
												  {
													  // Er komeen geen witte en oranje banders voor. Geen eisen voor de graduaties.
													  echo "Geen wit en oranje";
													  $gradreq = 0;
												  }
										  }
									  if ($poules == 1)
										  {
											  // De laatste poule wordt ingevuld. Geen eisen
											  echo "De laatste poule wordt ingevuld. Geen eisen";
											  $drop = 0;
											  $gradreq = 0;
											  $clubreq = 0;
										  }
								  }
							  else
								  {
									  // Er wordt een nieuwe poule aangemaakt
									  $aantalwitexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "exclusief", $poulenr, 2, $kniprating);
									  $aantalgeelexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "exclusief", $poulenr, 2, $kniprating);
									  $aantaloranjeexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "exclusief", $poulenr, 2, $kniprating);
									  echo "<br>" . $aantalwitexclusief . "/" . $aantalgeelexclusief . "/" . $aantaloranjeexclusief . "<br>"; 
									  
									  $deelnemerss = array ();
									  $clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
									  foreach ($clubs as $club)
										  {
											  $deelnemerss[$club["clubnr"]] = 0;
										  }
									  echo "<br><br>";
									  print_r($deelnemerss);
									  echo "<br><br>";
									  foreach ($pouleinaanmaak as $poulelid)
										  {
											  echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
											  $deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
										  }
									  echo "</table><br>";
									  
									  $clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
									  foreach ($clubs as $club)
										  {
											  $aantalclub = $db->num_rows("SELECT * FROM IC_pouleindeling
																			  LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																			  WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
																				  AND (IC_pouleindeling.poulenr='0' OR IC_pouleindeling.poulenr='" . $poulelid["poulenr"] . "') 
																				  AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
																				  AND IC_deelnemers.clubnr=" . $club["clubnr"] . "
																				  AND IC_deelnemers.rating<'" . $kniprating . "'
																			  ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
											  if ($aantalclub > 0)
												  {
													  $judokaasinpoule = $deelnemerss[$club["clubnr"]];
													  $judokaasmoetinpoule = round ($aantalclub/$poules);
													  echo $club["clubnaam"] . " heeft " . $aantalclub . " deelnemers, dat is " . $judokaasmoetinpoule . " per poule en al " . $judokaasinpoule . " in de poule<br>";
													  if (round ($aantalclub/$poules) > $deelnemerss[$club["clubnr"]])
														  {
															  if ($clublistin == "")
																  {
																	  $clublistin = $club["clubnr"];
																  }
															  else
																  {
																	  $clublistin .= "en" . $club["clubnr"];
																  }
														  }
													  if ($judokaasinpoule >= $judokaasmoetinpoule && $judokaasinpoule>0)
														  {
															  if ($clublistuit == "")
																  {
																	  $clublistuit = $club["clubnr"];
																  }
															  else
																  {
																	  $clublistuit .= "en" . $club["clubnr"];
																  }
														  }
													  echo "verplicht" . $clublistin . "<br>";
													  echo "uitsluiten" . $clublistuit . "<br>";
												  }
										  }
									  if ($clublistin != "")
										  {
											  $clubreq = 1;
											  $clublist = $clublistin;
										  }
									  elseif ($clublistuit != "")
										  {
											  $clubreq = 2;
											  $clublist = $clublistuit;
										  }
									  else
										  {
											  $clubreq = 0;
											  $clublist = "";
										  }
									  
									  
									  if ($poules > 2)
										  {
											  // nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.
											  // Geen graduatie eisen
											  $gradreq = 0;
											  $graduatie = 0;
											  $drop = 0;
										  }
									  if ($poules == 2)
										  {
											  // De voorlaatste poule wordt opgestart. Zorgen dat witte en oranje banders gescheiden blijven.
											  $drop = 2;
											  if ($aantalwitexclusief != 0 && $aantaloranjeexclusief != 0)
												  {
													  echo "Zowel wit als oranje komen voor";
													  if ($aantaloranjeexclusief > 0)
														  {
															  // Er zijn nog niet ingedeelde witte banders, die moeten hier in.
															  $gradreq = 1;
															  $graduatie = 6;
															  // clubeis wordt meegenomen, maar die is ondergeschikt aan graduatie eis
															  $drop = 2;
														  }
												  }
											  else
												  {
													  // Er komeen geen witte en oranje banders voor. Geen eisen voor de graduaties.
													  echo "Geen wit en oranje";
													  $gradreq = 0;
												  }
										  }
									  if ($poules == 1)
										  {
											  // De laatste poule wordt ingevuld. Geen eisen
											  echo "De laatste poule wordt ingevuld. Geen eisen";
											  $drop = 0;
											  $gradreq = 0;
											  $clubreq = 0;
										  }
								  }
			  
									  
			  
							  echo "<br>" .  $competitienr. "-" . $gewichtsklassenr. "-" . $nieuwpoule. "-" . $poulenr. "-" . $knip. "-" . $kniprating. "-" . $gradreq. "-" . $graduatie. "-" . $clubreq . "-" . $clublist. "-" . $drop . "<br><br>";
							  zoek_judoka ($competitienr, $gewichtsklassenr, $nieuwpoule, $poulenr, $knip, $kniprating, $gradreq, $graduatie, $clubreq, $clublist, $drop);
							  
							  echo "<br>";
							  
							  $deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling
											  LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
											  LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
											  LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
											  WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
												  AND IC_pouleindeling.poulenr='0'
												  AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
												  AND IC_deelnemers.rating>='" . $kniprating . "'
											  ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");		
							  if (!empty($deelnemers))
								  {
									  echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"3\">";
									  foreach ($deelnemers as $deelnemer)
										  {
											  echo "	<tr><td>" . naam ($deelnemer["lidnr"]) . "</td><td>" . $deelnemer["clubnaam"] . "</td><td>" . $deelnemer["rating"] . "</td><td>" . $deelnemer["graduatie"] . "</td></tr>";
										  }
									  echo "</table>";
								  }
							  else
								  {
									  $db->run_query("UPDATE IC_poules set poulestatus='1' WHERE poulenr=" . $poulenr);
									  echo "Boven de knip alles ingeldeeld. Poule gesloten.";
								  }
						  }

					}
				else
					{	
						$poules = floor ($aantal / 4);
						if ($poules == 0)
							{
								$poules = 1;
							}
						if ($aantal == 6 || $aantal == 7)
							{
								$poules = 2;
							}
						if ($aantal == 11)
							{
								$poules = 3;
							}

						
						////
						// Controleren of er een poule in aanmaak is en bepalen of er een nieuwe poule gemaakt moet worden
						////
						
						$pouleinaanmaak = $db->get_array("SELECT * FROM IC_poules
																	RIGHT JOIN IC_pouleindeling ON (IC_pouleindeling.poulenr = IC_poules.poulenr)
																	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																	LEFT JOIN IC_clubs ON (IC_deelnemers.clubnr = IC_clubs.clubnr)
																	WHERE IC_poules.competitienr='" . $competitienr . "'
																		AND IC_poules.gewichtsklassenr='" . $gewichtsklassenr . "'
																		AND IC_poules.poulestatus='0'");
						if (!empty($pouleinaanmaak))
							{
								$nieuwpoule = 0;
								$poulenr = $pouleinaanmaak[0]["poulenr"];
								echo "Poule bestaat";
					
								if ($aantal >= 12)
									{
										if (count($pouleinaanmaak) == 4)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 11)
									{
										if (count($pouleinaanmaak) == 3)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 10)
									{
										if (count($pouleinaanmaak) == 5)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 9)
									{
										if (count($pouleinaanmaak) == 4)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 8)
									{
										if (count($pouleinaanmaak) == 4)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 7)
									{
										if (count($pouleinaanmaak) == 3)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
								elseif ($aantal == 6)
									{
										if (count($pouleinaanmaak) == 3)
											{
												$nieuwpoule = 2;
												echo "Nieuwe Poule opstarten, oude sluiten";
											}
									}
							}
						else
							{
								$nieuwpoule = 1;
								$poulenr = 0;
								echo "Poule opstarten";
							}
		
	
						echo "	Aantal deelnemers: " . $aantal. "<br>
								Aantal poules: " . $poules . "<br>";
						
						$graduatie = 0;
						if ($nieuwpoule == 0)
							{
								echo "	<h3>Poule in aanmaak" . count($pouleinaanmaak) . "</h3>
										<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
								$deelnemerss = array ();
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$deelnemerss[$club["clubnr"]] = 0;
									}
								echo "<br><br>";
								print_r($deelnemerss);
								echo "<br><br>";
								foreach ($pouleinaanmaak as $poulelid)
									{
										echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
										$deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
									}
								echo "</table><br>";
								
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$aantalclub = $db->num_rows("SELECT * FROM IC_pouleindeling
																		LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																		WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
																			AND (IC_pouleindeling.poulenr='0' OR IC_pouleindeling.poulenr='" . $poulelid["poulenr"] . "') 
																			AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
																			AND IC_deelnemers.clubnr=" . $club["clubnr"] . "
																			AND IC_deelnemers.rating<'" . $kniprating . "'
																		ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
										if ($aantalclub > 0)
											{
												$judokaasinpoule = $deelnemerss[$club["clubnr"]];
												$judokaasmoetinpoule = round ($aantalclub/$poules);
												echo $club["clubnaam"] . " heeft " . $aantalclub . " deelnemers, dat is " . $judokaasmoetinpoule . " per poule en al " . $judokaasinpoule . " in de poule<br>";
												if (round ($aantalclub/$poules) > $deelnemerss[$club["clubnr"]])
													{
														if ($clublistin == "")
															{
																$clublistin = $club["clubnr"];
															}
														else
															{
																$clublistin .= "en" . $club["clubnr"];
															}
													}
												if ($judokaasinpoule >= $judokaasmoetinpoule && $judokaasinpoule>0)
													{
														if ($clublistuit == "")
															{
																$clublistuit = $club["clubnr"];
															}
														else
															{
																$clublistuit .= "en" . $club["clubnr"];
															}
													}
												echo "verplicht" . $clublistin . "<br>";
												echo "uitsluiten" . $clublistuit . "<br>";
											}
									}
								if ($clublistin != "")
									{
										$clubreq = 1;
										$clublist = $clublistin;
									}
								elseif ($clublistuit != "")
									{
										$clubreq = 2;
										$clublist = $clublistuit;
									}
								else
									{
										$clubreq = 0;
										$clublist = "";
									}
								echo "<br><br> clubreq=" . $clubreq . "list=" . $clublist ."<br><br>";
								
								$aantalwitinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "inpoule", $poulenr, 1, $kniprating);
								$aantalgeelinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "inpoule", $poulenr, 1, $kniprating);
								$aantaloranjeinpoule = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "inpoule", $poulenr, 1, $kniprating);
								echo "<br>" . $aantalwitinpoule . "/" . $aantalgeelinpoule . "/" . $aantaloranjeinpoule; 
								$aantalwitinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "inclusief", $poulenr, 1, $kniprating);
								$aantalgeelinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "inclusief", $poulenr, 1, $kniprating);
								$aantaloranjeinclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "inclusief", $poulenr, 1, $kniprating);
								echo "<br>" . $aantalwitinclusief . "/" . $aantalgeelinclusief . "/" . $aantaloranjeinclusief . "<br>"; 
								
								if ($poules > 2)
									{
										// nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.
										echo "nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.";
										if ($aantalwitinpoule != 0)
											{
												$gradreq = 2;
												$graduatie = 4;
												$drop = 2;
											}
										elseif ($aantaloranjeinpoule != 0)
											{
												$gradreq = 2;
												$graduatie = 6;
												$drop = 2;
											}
										else
											{
												$gradreq = 0;
												$graduatie = 6;
												$drop = 1;
											}
									}
								if ($poules == 2)
									{
										// De voorlaatste poule wordt ingevuld. Zorgen dat witte en oranje banders gescheiden blijven.
										$drop = 2;
										if ($aantalwitinclusief != 0 && $aantaloranjeinclusief != 0)
											{
												echo "Zowel wit als oranje komen voor";
												if (bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "exclusief", $poulenr, 1, $kniprating) > 0)
													{
														// Er zijn nog niet ingedeelde witte banders, die moeten hier in.
														$gradreq = 1;
														$graduatie = 6;
														// clubeis wordt meegenomen, maar die is ondergeschikt aan graduatie eis
														$drop = 2;
													}
												else
													{
														// De witte banders zijn op. Oranje banders uitsluiten
														$gradreq = 2;
														$graduatie = 4;
														$drop = 2;
													}
											}
										else
											{
												// Er komeen geen witte en oranje banders voor. Geen eisen voor de graduaties.
												echo "Geen wit en oranje";
												$gradreq = 0;
											}
									}
								if ($poules == 1)
									{
										// De laatste poule wordt ingevuld. Geen eisen
										echo "De laatste poule wordt ingevuld. Geen eisen";
										$drop = 0;
										$gradreq = 0;
										$clubreq = 0;
									}
							}
						else
							{
								// Er wordt een nieuwe poule aangemaakt
								$aantalwitexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "6", "exclusief", $poulenr, 1, $kniprating);
								$aantalgeelexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "5", "exclusief", $poulenr, 1, $kniprating);
								$aantaloranjeexclusief = bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, "4", "exclusief", $poulenr, 1, $kniprating);
								echo "<br>" . $aantalwitexclusief . "/" . $aantalgeelexclusief . "/" . $aantaloranjeexclusief . "<br>"; 
								
								$deelnemerss = array ();
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$deelnemerss[$club["clubnr"]] = 0;
									}
								echo "<br><br>";
								print_r($deelnemerss);
								echo "<br><br>";
								foreach ($pouleinaanmaak as $poulelid)
									{
										echo "<tr><td>" . naam($poulelid["lidnr"]) . "</td><td>" . $poulelid["clubnaam"] . "</td><td>" . $poulelid["rating"] . "</td><td>" . $poulelid["graduatie"] . "</td></tr>";
										$deelnemerss[$poulelid["clubnr"]] = $deelnemerss[$poulelid["clubnr"]] + 1;
									}
								echo "</table><br>";
								
								$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1'");
								foreach ($clubs as $club)
									{
										$aantalclub = $db->num_rows("SELECT * FROM IC_pouleindeling
																		LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
																		WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
																			AND (IC_pouleindeling.poulenr='0' OR IC_pouleindeling.poulenr='" . $poulelid["poulenr"] . "') 
																			AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
																			AND IC_deelnemers.clubnr=" . $club["clubnr"] . "
																			AND IC_deelnemers.rating<'" . $kniprating . "'
																		ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");
										if ($aantalclub > 0)
											{
												$judokaasinpoule = $deelnemerss[$club["clubnr"]];
												$judokaasmoetinpoule = round ($aantalclub/$poules);
												echo $club["clubnaam"] . " heeft " . $aantalclub . " deelnemers, dat is " . $judokaasmoetinpoule . " per poule en al " . $judokaasinpoule . " in de poule<br>";
												if (round ($aantalclub/$poules) > $deelnemerss[$club["clubnr"]])
													{
														if ($clublistin == "")
															{
																$clublistin = $club["clubnr"];
															}
														else
															{
																$clublistin .= "en" . $club["clubnr"];
															}
													}
												if ($judokaasinpoule >= $judokaasmoetinpoule && $judokaasinpoule>0)
													{
														if ($clublistuit == "")
															{
																$clublistuit = $club["clubnr"];
															}
														else
															{
																$clublistuit .= "en" . $club["clubnr"];
															}
													}
												echo "verplicht" . $clublistin . "<br>";
												echo "uitsluiten" . $clublistuit . "<br>";
											}
									}
								if ($clublistin != "")
									{
										$clubreq = 1;
										$clublist = $clublistin;
									}
								elseif ($clublistuit != "")
									{
										$clubreq = 2;
										$clublist = $clublistuit;
									}
								else
									{
										$clubreq = 0;
										$clublist = "";
									}
								
								
								if ($poules > 2)
									{
										// nog een aantal poules in te delen. Voorkomen dat wit en ornaje bij elkaar komen.
										// Geen graduatie eisen
										$gradreq = 0;
										$graduatie = 0;
										$drop = 0;
									}
								if ($poules == 2)
									{
										// De voorlaatste poule wordt opgestart. Zorgen dat witte en oranje banders gescheiden blijven.
										$drop = 2;
										if ($aantalwitexclusief != 0 && $aantaloranjeexclusief != 0)
											{
												echo "Zowel wit als oranje komen voor";
												if ($aantaloranjeexclusief > 0)
													{
														// Er zijn nog niet ingedeelde witte banders, die moeten hier in.
														$gradreq = 1;
														$graduatie = 6;
														// clubeis wordt meegenomen, maar die is ondergeschikt aan graduatie eis
														$drop = 2;
													}
											}
										else
											{
												// Er komeen geen witte en oranje banders voor. Geen eisen voor de graduaties.
												echo "Geen wit en oranje";
												$gradreq = 0;
											}
									}
								if ($poules == 1)
									{
										// De laatste poule wordt ingevuld. Geen eisen
										echo "De laatste poule wordt ingevuld. Geen eisen";
										$drop = 0;
										$gradreq = 0;
										$clubreq = 0;
									}
							}
		
								
		
						echo "<br>" .  $competitienr. "-" . $gewichtsklassenr. "-" . $nieuwpoule. "-" . $poulenr. "-" . $knip. "-" . $kniprating. "-" . $gradreq. "-" . $graduatie. "-" . $clubreq . "-" . $clublist. "-" . $drop . "<br><br>";
						echo zoek_judoka ($competitienr, $gewichtsklassenr, $nieuwpoule, $poulenr, $knip, $kniprating, $gradreq, $graduatie, $clubreq, $clublist, $drop);
						
						echo "<br>";
						
						$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling
										LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_pouleindeling.gewichtsklassenr)
										LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
										LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
										WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
											AND IC_pouleindeling.poulenr='0'
											AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . "
											AND IC_deelnemers.rating<'" . $kniprating . "'
										ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC");		
						if (!empty($deelnemers))
							{
								echo "<table border=\"0\" cellpadding=\"1\" cellspacing=\"3\">";
								foreach ($deelnemers as $deelnemer)
									{
										echo "	<tr><td>" . naam ($deelnemer["lidnr"]) . "</td><td>" . $deelnemer["clubnaam"] . "</td><td>" . $deelnemer["rating"] . "</td><td>" . $deelnemer["graduatie"] . "</td></tr>";
									}
								echo "</table>";
							}
						else
							{
								$db->run_query("UPDATE IC_poules set poulestatus='1' WHERE poulenr=" . $poulenr);
								echo "Iedereen voor de knip ingedeeld. Nu erna";
								

							}

					}

			}

	}
elseif ($gewichtlozen > 0 && $competitienr != "false" && $gewichtsklassenr != "false")
	{
		echo "Er staan gewichtslozen in de lijst";
	}
else
	{
		echo "competitienr of gewichtsklassenr kloppen niet.";
	}



?>