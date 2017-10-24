<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$poulenr 	= check_poulenr ($_GET["poulenr"]);
echo $poulenr;
$resultaat = "true";
if ($poulenr != "false")
	{
		echo "lijstje";
		$plaats = 1;
		for ($i = 5; $i >= 0; $i--)
			{
				echo "<br><br>" . $i . "Gewonnnen wedstrijden:<br>";
				$deelnemers = $db->get_array("SELECT * FROM IC_wedstrijdlid
												LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
												WHERE IC_wedstrijdlid.uitslag>'0'
													AND IC_wedstrijden.poulenr='"  . $poulenr . "'
												GROUP BY IC_wedstrijdlid.lidnr
												HAVING COUNT(IC_wedstrijden.wedstrijdnr)=" . $i);
				echo "SELECT * FROM IC_wedstrijdlid
												LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
												WHERE IC_wedstrijdlid.uitslag>'0'
													AND IC_wedstrijden.poulenr='"  . $poulenr . "'
												GROUP BY IC_wedstrijdlid.lidnr
												HAVING COUNT(IC_wedstrijden.wedstrijdnr)=" . $i . "<br>";
				echo  count($deelnemers) . "<br>";
				if (count($deelnemers) == 0)
					{
						// Niemand die dit aantal wedstrijden heeft gewonnen.
					}
				elseif (count($deelnemers) == 1)
					{
						$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemers[0]["lidnr"] . "'");
						echo "<br>UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemers[0]["lidnr"] . "'";
						echo "<br>plaats is " . $plaats . "<br>";
						$plaats = $plaats + 1;
						
						echo "een deelnemer, plaats gemakkelijk bepaald";
					}
				elseif (count($deelnemers) == 2)
					{
						////
						// Gelijk aantal gewonnen wedstrijden tussen 2 spelers. Eerst controleren op wedstrijdpunten. Als dat ook gelijk is naar de onderlinge wedstrijd.
						////
						echo "twee deelnemers, Uitzoeken wie beter is<br>";
						print_r($deelnemers);
						echo "<br>";
						$deelnemers2 = $db->get_array("SELECT SUM(IC_wedstrijdlid.uitslag), lidnr FROM IC_wedstrijdlid
														LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
														WHERE IC_wedstrijdlid.uitslag>'0'
															AND IC_wedstrijden.poulenr='"  . $poulenr . "'
														GROUP BY IC_wedstrijdlid.lidnr
														HAVING COUNT(IC_wedstrijden.wedstrijdnr)='" . $i . "'
														ORDER BY SUM(IC_wedstrijdlid.uitslag) DESC");
						print_r($deelnemers2);
						$wedstrijd1 = $db->get_array("SELECT * FROM IC_wedstrijdlid
			 										  	LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
														WHERE (IC_wedstrijdlid.lidnr='" . $deelnemers2[0]["lidnr"] . "' OR IC_wedstrijdlid.lidnr='" . $deelnemers2[1]["lidnr"] . "')
															AND IC_wedstrijden.poulenr='" . $poulenr . "'
														GROUP BY IC_wedstrijden.wedstrijdnr
														HAVING COUNT(IC_wedstrijdlid.wedstrijdlidnr)=2");
						echo "<br><br>" . count($wedstrijd1) . "<br><br>";
						if ($deelnemers2[0]["SUM(IC_wedstrijdlid.uitslag)"] != $deelnemers2[1]["SUM(IC_wedstrijdlid.uitslag)"])
							{
								// verschil in score
								echo "<br>Verschil in score";
								foreach ($deelnemers2 as $deelnemer2)
									{
										$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemer2["lidnr"] . "'");
										echo "<br>plaats is " . $plaats . "<br>";
										$plaats = $plaats + 1;
										
									}
							}
						elseif (count($wedstrijd1) == 1) 
							{
								echo "1 onderlinge wedstrijd";
								$wedstrijd2 = $db->get_array("SELECT * FROM IC_wedstrijdlid WHERE wedstrijdnr='" . $wedstrijd1[0]["wedstrijdnr"]  ."' ORDER BY uitslag DESC");
								foreach ($wedstrijd2 as $wedstrijd2a)
									{
										echo "speler 1 heeft een hogere score, dan anderen dezelfde";
										$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $wedstrijd2a["lidnr"] . "'");
										echo "<br>UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $wedstrijd2a["lidnr"] . "'";
										echo "<br>plaats is " . $plaats . "<br>";
										$plaats = $plaats + 1;
									}
								
							}
						elseif (count($wedstrijd1) == 2) 
							{
								// onderlinge wedstrijd uitslag
								echo "2 wedstrijden tussen deze 2 judoka's";
								$resultaat = "false";
								$db->run_query("UPDATE IC_poules SET poulestatus='4' WHERE poulenr='" . $poulenr . "'");
								$db->run_query("UPDATE IC_pouleindeling SET plaats='100' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemers[0]["lidnr"] . "'");
								$db->run_query("UPDATE IC_pouleindeling SET plaats='100' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemers[1]["lidnr"] . "'");
								
							}
						else
							{
								$resultaat = "false";
							}
						
						
						
					}
				elseif (count($deelnemers) == 3)
					{
						$deelnemers2 = $db->get_array("SELECT SUM(IC_wedstrijdlid.uitslag), lidnr FROM IC_wedstrijdlid
														LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
														WHERE IC_wedstrijdlid.uitslag>'0'
															AND IC_wedstrijden.poulenr='"  . $poulenr . "'
														GROUP BY IC_wedstrijdlid.lidnr
														HAVING COUNT(IC_wedstrijden.wedstrijdnr)='" . $i . "'
														ORDER BY SUM(IC_wedstrijdlid.uitslag) DESC");
						print_r($deelnemers2);
						if ($deelnemers2[0]["SUM(IC_wedstrijdlid.uitslag)"] != $deelnemers2[1]["SUM(IC_wedstrijdlid.uitslag)"] && $deelnemers2[0]["SUM(IC_wedstrijdlid.uitslag)"] != $deelnemers2[2]["SUM(IC_wedstrijdlid.uitslag)"] && $deelnemers2[1]["SUM(IC_wedstrijdlid.uitslag)"] != $deelnemers2[2]["SUM(IC_wedstrijdlid.uitslag)"])
							{
								// verschil in score
								echo "<br>Verschil in score";
								foreach ($deelnemers2 as $deelnemer2)
									{
										$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemer2["lidnr"] . "'");
										echo "<br>plaats is " . $plaats . "<br>";
										$plaats = $plaats + 1;
										
									}
							}
						else
							{
								if ($deelnemers2[0]["SUM(IC_wedstrijdlid.uitslag)"] != $deelnemers2[1]["SUM(IC_wedstrijdlid.uitslag)"] && $deelnemers2[1]["SUM(IC_wedstrijdlid.uitslag)"] == $deelnemers2[2]["SUM(IC_wedstrijdlid.uitslag)"])
									{
										// speler 1 heeft een hogere score, dan anderen dezelfde	
										echo "<br>speler 1 heeft een hogere score, dan anderen dezelfde";
										$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemers2[0]["lidnr"] . "'");
										echo "<br>UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemers2[0]["lidnr"] . "'";
										echo "<br>plaats is " . $plaats . "<br>";
										$plaats = $plaats + 1;
										
										
										// Opzoeken wie de onderlinge wedstrijd van 2 en 3 heeft gewonnen
										$wedstrijd1 = $db->get_array("SELECT * FROM IC_wedstrijdlid
			 														  	LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																		WHERE (IC_wedstrijdlid.lidnr='" . $deelnemers2[1]["lidnr"] . "' OR IC_wedstrijdlid.lidnr='" . $deelnemers2[2]["lidnr"] . "')
																			AND IC_wedstrijden.poulenr='" . $poulenr . "'
																		GROUP BY IC_wedstrijden.wedstrijdnr
																		HAVING COUNT(IC_wedstrijdlid.wedstrijdlidnr)=2");
										if (count($wedstrijd1) == 1)
											{
												echo "1 onderlinge wedstrijd";
												$wedstrijd2 = $db->get_array("SELECT * FROM IC_wedstrijdlid WHERE wedstrijdnr='" . $wedstrijd1[0]["wedstrijdnr"]  ."' ORDER BY uitslag DESC");
												foreach ($wedstrijd2 as $wedstrijd2a)
													{
														echo "speler 1 heeft een hogere score, dan anderen dezelfde";
														$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $wedstrijd2a["lidnr"] . "'");
														echo "<br>UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $wedstrijd2a["lidnr"] . "'";
														echo "<br>plaats is " . $plaats . "<br>";
														$plaats = $plaats + 1;
														
													}
											}
										else
											{
												$resultaat = "false";
											}
										
									}
								elseif ($deelnemers2[0]["SUM(IC_wedstrijdlid.uitslag)"] == $deelnemers2[1]["SUM(IC_wedstrijdlid.uitslag)"] && $deelnemers2[1]["SUM(IC_wedstrijdlid.uitslag)"] != $deelnemers2[2]["SUM(IC_wedstrijdlid.uitslag)"])
									{
										// speler 1 en 2 hebben dezelfde socre, 3 heeft lager
										echo "speler 1 en 2 hebben dezelfde socre, 3 heeft lager";										
										$wedstrijd1 = $db->get_array("SELECT * FROM IC_wedstrijdlid
			 														  	LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_wedstrijdlid.wedstrijdnr)
																		WHERE (IC_wedstrijdlid.lidnr='" . $deelnemers2[0]["lidnr"] . "' OR IC_wedstrijdlid.lidnr='" . $deelnemers2[1]["lidnr"] . "')
																			AND IC_wedstrijden.poulenr='" . $poulenr . "'
																		GROUP BY IC_wedstrijden.wedstrijdnr
																		HAVING COUNT(IC_wedstrijdlid.wedstrijdlidnr)=2");
										if (count($wedstrijd1) == 1)
											{
												echo "1 onderlinge wedstrijd";
												$wedstrijd2 = $db->get_array("SELECT * FROM IC_wedstrijdlid WHERE wedstrijdnr='" . $wedstrijd1[0]["wedstrijdnr"]  ."' ORDER BY uitslag DESC");
												foreach ($wedstrijd2 as $wedstrijd2a)
													{
														echo "speler 1 heeft een hogere score, dan anderen dezelfde";
														$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $wedstrijd2a["lidnr"] . "'");
														$plaats = $plaats + 1;
														echo "<br>plaats is " . $plaats . "<br>";
													}
												echo "Laatste speler invullen";
												$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND lidnr='" . $deelnemers2[2]["lidnr"] . "'");
												$plaats = $plaats + 1;
												echo "<br>plaats is " . $plaats . "<br>";
											}
										else
											{
												$resultaat = "false";
											}
									}
								else
									{
										if ($plaats == 3)
											{
												// alle drie hebben dezelfde score, gelukkig zijn de 1ste en 2de plaats al vergeven.
												echo "alle drie hebben dezelfde score, gelukkig zijn de 1ste en 2de plaats al vergeven.";
											}
										else
											{
												$resultaat = "false";
											}
									}
							}
					}
			}
		$laatstespeler = $db->get_array("SELECT * FROM IC_pouleindeling WHERE poulenr='" . $poulenr . "' AND plaats='0'");
		if (count($laatstespeler) == 1)
			{
				echo "een speler over gebleven.";
				$db->run_query("UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND plaats='0'");
				echo "<br>UPDATE IC_pouleindeling SET plaats='" . $plaats . "' WHERE poulenr='" . $poulenr . "' AND plaats='0'";
			}
		if ($resultaat == "true")
			{
				$db->run_query("UPDATE IC_poules SET poulestatus='3' WHERE poulenr='" . $poulenr . "'");
			}

	}
else
	{
//		header ("location: index.php");
		echo $poulenr . "foutje";
	}

echo "<br><br>" . $resultaat;
destruct( 'db' );

// header ("location: index.php?target=poule&poulenr=" . $poulenr);


?>