<?php
function naam ($lidnr)
	{
		global $db;
		$lid = $db->get_array("SELECT * FROM IC_deelnemers WHERE lidnr=" . $lidnr);
		if ($lid[0][tussenvoegsels] == "")
			{
				$naam = $lid[0][voornaam] . " " . $lid[0][achternaam];
			}
		else
			{
				$naam = $lid[0][voornaam] . " " . $lid[0][tussenvoegsels] . " " . $lid[0][achternaam];
			}
		return $naam;
	}

function naam2 ($lidnr)
	{
		global $db;
		$lid = $db->get_array("SELECT * FROM IC_deelnemers WHERE lidnr=" . $lidnr);
		if ($lid[0][tussenvoegsels] == "")
			{
				$naam = $lid[0]["achternaam"] . ", " . $lid[0]["voornaam"];
			}
		else
			{
				$naam = $lid[0]["achternaam"] . " " . $lid[0]["tussenvoegsels"] . ", " . $lid[0]["voornaam"];
			}
		return $naam;
	}
	
function naam_coach ($coachnr)
	{
		global $db;
		$lid = $db->get_array("SELECT * FROM IC_coaches WHERE coachnr=" . $coachnr);
		if ($lid[0][tussenvoegsels] == "")
			{
				$naam = $lid[0][voornaam] . " " . $lid[0][achternaam];
			}
		else
			{
				$naam = $lid[0][voornaam] . " " . $lid[0][tussenvoegsels] . " " . $lid[0][achternaam];
			}
		return $naam;
	}
	
function check_redirect ($string)
	{
		$dir = "content";
		$dh = opendir($dir);
		$exclude = array (".", "..");
		while (false !== ($filename = readdir($dh)))
			{
				if (!in_array ($filename, $exclude))
					{
						$bestand = explode ('.', $filename);
						$files[] = $bestand["0"];
					}
			}
		if (!in_array ($string, $files))
			{
				return "home";
			}
		else
			{
				return $string;
			}
	}
function clean($string)
	{
		$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '-'=>'&#45;', '/'=>'&#47;', '\\'=>'&#92;', '.'=>'&#46;',
							','=>'&#44;', ';'=>'&#59;', ':'=>'&#58;', '>'=>'&#62;', '<'=>'&#60;', '='=>'&#61;', '\''=>'&#39;', '!'=>'&#33;', '_'=>'&#95;', '['=>'&#91;', ']'=>'&#93;');
		$newstring = strtr( $string, $unwanted_array );
   		return ($newstring);
	}

function pdfready($string)
	{
		$unwanted_array = array('&#45;'=>'-');
		$newstring = strtr( $string, $unwanted_array );
   		return ($newstring);
	}

function check_clubnr ($clubnr)
	{
		if(preg_match('/^\d+$/',$clubnr))
			{
				global $db;
				$clubnrs = $db->get_array("SELECT clubnr FROM IC_clubs");
				$clubnrs2 = array();
				foreach ($clubnrs as $club)
					{
						array_push($clubnrs2, $club["clubnr"]);
					}
		  		if (in_array ($clubnr, $clubnrs2))
					{
						return $clubnr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}

function check_seizoennr ($seizoennr)
	{
		if(preg_match('/^\d+$/',$seizoennr))
			{
				global $db;
				$seizoennrs = $db->get_array("SELECT seizoennr FROM IC_seizoenen");
				$seizoennrs2 = array ();
				foreach ($seizoennrs as $seizoen)
					{
						array_push($seizoennrs2, $seizoen["seizoennr"]);
					}
		  		if (in_array ($seizoennr, $seizoennrs2))
					{
						return $seizoennr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}

function check_gewichtsklassenr ($gewichtsklassenr)
	{
		if(preg_match('/^\d+$/',$gewichtsklassenr))
			{
				global $db;
				$gewichtsklassenrs = $db->get_array("SELECT gewichtsklassenr FROM IC_gewichtsklassen");
				$gewichtsklassenrs2 = array ();
				foreach ($gewichtsklassenrs as $gewichtsklasse)
					{
						array_push($gewichtsklassenrs2, $gewichtsklasse["gewichtsklassenr"]);
					}
		  		if (in_array ($gewichtsklassenr, $gewichtsklassenrs2))
					{
						return $gewichtsklassenr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}
	
function check_wedstrijdnr ($wedstrijdnr)
	{
		if(preg_match('/^\d+$/',$wedstrijdnr))
			{
				global $db;
				$wedstrijdnrs = $db->get_array("SELECT wedstrijdnr FROM IC_wedstrijden");
				$wedstrijdnrs2 = array ();
				foreach ($wedstrijdnrs as $wedstrijd)
					{
						array_push($wedstrijdnrs2, $wedstrijd["wedstrijdnr"]);
					}
		  		if (in_array ($wedstrijdnr, $wedstrijdnrs2))
					{
						return $wedstrijdnr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}

function check_coachnr ($coachnr)
	{
		if(preg_match('/^\d+$/',$gewichtsklassenr))
			{
				global $db;
				$coachnrs = $db->get_array("SELECT coachnr FROM IC_coaches");
				$coachnrs2 = array ();
				foreach ($coachnrs as $coach)
					{
						array_push($coachnrs2, $coach["coachnr"]);
					}
		  		if (in_array ($coachnr, $coachnrs2))
					{
						return $coachnr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}
	
function check_competitiedagnr ($competitiedagnr)
	{
		if(preg_match('/^\d+$/',$competitiedagnr))
			{
				global $db;
				$competitiedagnrs = $db->get_array("SELECT competitienr FROM IC_competities");
				$competitiedagnrs2 = array ();
				foreach ($competitiedagnrs as $competitiedag)
					{
						array_push($competitiedagnrs2, $competitiedag["competitienr"]);
					}
		  		if (in_array ($competitiedagnr, $competitiedagnrs2))
					{
						return $competitiedagnr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}
	
function check_deelnemer ($lidnr)
	{
		if(preg_match('/^\d+$/',$lidnr))
			{
				global $db;
				$deelnemers = $db->get_array("SELECT lidnr FROM IC_deelnemers");
				$deelnemers2 = array ();
				foreach ($deelnemers as $deelnemer)
					{
						array_push($deelnemers2, $deelnemer["lidnr"]);
					}
		  		if (in_array ($lidnr, $deelnemers2))
					{
						return $lidnr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}
	
function check_poulenr ($poulenr)
	{
		if(preg_match('/^\d+$/', $poulenr))
			{
				global $db;
				$poules = $db->get_array("SELECT poulenr FROM IC_poules");
				$poules2 = array ();
				foreach ($poules as $poule)
					{
						array_push($poules2, $poule["poulenr"]);
					}
		  		if (in_array ($poulenr, $poules2))
					{
						return $poulenr;
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}

function check_geslacht ($geslacht)
	{
		if(preg_match('/^\d+$/',$geslacht))
			{
		  		if ($geslacht == 1)
					{
						return 1;
					}
				else
					{
						return 0;
					}
			}
		else
			{
				return 0;
			}
	}

function check_graduatie ($graduatie)
	{
		if(preg_match('/^\d+$/',$graduatie))
			{
		  		if ($graduatie == 4)
					{
						return 4;
					}
				elseif ($graduatie == 5)
					{
						return 5;
					}
				else
					{
						return 6;
					}
			}
		else
			{
				return 6;
			}
	}

function check_gewicht ($gewicht)
	{
		$stukken = explode (".", $gewicht);
		if (count($stukken) == 1 || count($stukken) == 2)
			{
				$gewicht = (int)$stukken[0] . "." . (int)$stukken[1];
				
				return $gewicht;
			}
		else
			{
				return "99.9";
			}
	}

function check_rating ($rating)
	{
		if (is_int($rating))
			{
				return $rating;
			}
		else
			{
				return 1000;
			}
	}

function bepaal_rating ($lidnr, $datum)
	{
		global $db;
		$mutaties = $db->get_array("
						SELECT * FROM IC_ratings 
							LEFT JOIN IC_wedstrijden ON (IC_wedstrijden.wedstrijdnr = IC_ratings.wedstrijdnr)
							LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_wedstrijden.poulenr)
							LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
						WHERE (IC_ratings.wedstrijdnr=0 AND IC_ratings.lidnr=" . $lidnr . ")
							OR (IC_ratings.lidnr=" . $lidnr . " AND IC_competities.datum<'" . $datum . "')");
		if (empty($mutaties))
			{
				return 1000;
			}
		else
			{
				$rating = 0;
				foreach ($mutaties as $mutatie)
					{
						$rating = $rating + $mutatie["change"];
					}
				return $rating;
			}
	}
function wachtwoordmaken ($lengte)
	{
		$Chars = array('0'=>0,
		   '1'=>"1",
		   '2'=>"2",
		   '3'=>"3",
		   '4'=>"4",
		   '5'=>"5",
		   '6'=>"6",
		   '7'=>"7",
		   '8'=>"8",
		   '9'=>"9",
		   '10'=>"A",
		   '11'=>"B",
		   '12'=>"C",
		   '13'=>"D",
		   '14'=>"E",
		   '15'=>"F",
		   '16'=>"G",
		   '17'=>"H",
		   '18'=>"I",
		   '19'=>"J",
		   '20'=>"K",
		   '21'=>"L",
		   '22'=>"M",
		   '23'=>"N",
		   '24'=>"O",
		   '25'=>"P",
		   '26'=>"Q",
		   '27'=>"R",
		   '28'=>"S",
		   '29'=>"T",
		   '30'=>"U",
		   '31'=>"V",
		   '32'=>"W",
		   '33'=>"X",
		   '34'=>"Y",
		   '35'=>"Z");
	
			for ($n=0; $n<=$lengte; $n++)
				{
					$random = rand (0, 35);
					$codepart .= $Chars[$random];
				}
			return $codepart;
		
	}
function datumkiezer ($day, $month, $year, $datumold)
	{
		$maandnamen = array(
			1 => "Januari",
			2 => "Februari",
			3 => "Maart",
			4 => "April",
			5 => "Mei",
			6 => "Juni",
			7 => "Juli",
			8 => "Augustus",
			9 => "September",
			10 => "Oktober",
			11 => "November",
			12 => "December"
			);
		if ($datumold == "")
			{
				$jaar = date('Y');
				$maand = date ('n');
				$dag = date ('j');
			}
		else
			{
				$jaar = strftime ("%Y", strtotime ($datumold));
				$maand = strftime ("%-m", strtotime ($datumold));
				$dag = strftime ("%e", strtotime ($datumold));
			}
		$jaarr = date('Y');
		
		$datumkiezer = "<select name=\"" . $day . "\">";

		for ($i=1 ; $i<=31 ; $i++)
			{
				if ($i == $dag)
					{
						$datumkiezer .= "<option value=\"" . $i . "\" selected>" . $i . "</option>\n";
					}
				else
					{
						$datumkiezer .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
					}
					}
		$datumkiezer .= "</select><select name=\"" . $month . "\">";
		for ($i=1 ; $i<=12 ; $i++)
			{
				if ($i == $maand)
					{
						$datumkiezer .= "<option value=\"" . $i . "\" selected>" . $maandnamen["$i"] . "</option>\n";
					}
				else
					{
						$datumkiezer .= "			<option value=\"" . $i . "\">" . $maandnamen["$i"] . "</option>\n";
					}
			}
		$datumkiezer .= "</select><select name=\"" . $year . "\">";
		for ($i=$jaarr+2 ; $i>=$jaarr-80 ; $i--)
			{
				if ($i == $jaar)
					{
						$datumkiezer .= "<option value=\"" . $i . "\" selected>" . $i . "</option>\n";
					}
				else
					{
						$datumkiezer .= "<option value=\"" . $i . "\">" . $i . "</option>\n";
					}
			}
		$datumkiezer .= "</select>";
		return $datumkiezer;
	}

function datumbouwer ($dag, $maand, $jaar)
	{
		if (ctype_digit($dag) && ctype_digit($maand) && ctype_digit($jaar))
			{
				if ($dag >= 1 && $dag <= 31)
					{
						if ($maand >= 1 && $maand <= 12)
							{
								if ($jaar >= 1900 && $jaar <= 2200)
									{
										$datum = $jaar . "-";
										if ($maand < 10)
											{
												$datum .= "0";
											}
										$datum .= $maand . "-";
										if ($dag < 10)
											{
												$datum .= "0"; 
											}
										$datum .= $dag;
										return $datum;
									}
								else
									{
										return "false";
									}
							}
						else
							{
								return "false";
							}
					}
				else
					{
						return "false";
					}
			}
		else
			{
				return "false";
			}
	}

function zoek_judoka ($competitienr, $gewichtsklassenr, $nieuwpoule, $poulenr, $knip, $kniprating, $gradreq, $graduatie, $clubreq, $club, $drop)
	{
		// $competitienr : het nummer van de competitiedag
		// $gewichtsklassenr : de gewichtsklasse waarmee gewerkt wordt
		// $nieuwpoule : 0 => geen nieuwe poule aanmaken, 1 => nieuwe poule aanmaken, 2 => nieuwe poule aanmaken en vorige sluiten
		// $poulenr : poulenr waaraan judoka meot worden toegevoegd
		// $knip : 0 => Geen knip toepassen, 1 => Onder knip, 2 => Boven knip
		// $kniprating : rating waarop de knip moet worden toegepast
		// $gradreq : 0 => Geen eisen, 1 => persee toevoegen, 2 => uitsluiten
		// $graduatie : De uit te sluiten of af te dwingen graduatie
		// $clubreq : 0 => Geen eisen, 1 => persee toevoegen, 2 => uitsluiten
		// $club : lijstje met clubs om toe te voegen of uit te sluiten
		// $drop : Als er geen judoka voldoet aan de eis, 1 => vervalt de graduatie eis, 2 => vervalt de clubeis
		
		global $db;
		
		if ($knip == 0)
			{
				$knipquery = " ";
			}
		elseif ($knip == 1)
			{
				$knipquery = " IC_deelnemers.rating<'" . $kniprating . "' ";
			}
		elseif ($knip == 2)
			{
				$knipquery = " IC_deelnemers.rating>='" . $kniprating . "' ";
			}
		
		if ($gradreq == 0)
			{
				$gradquery = " ";
			}
		elseif ($gradreq == 1)
			{
				$gradquery = " IC_deelnemers.graduatie='" . $graduatie . "' ";
			}
		elseif ($gradreq == 2)
			{
				$gradquery = " IC_deelnemers.graduatie<>'" . $graduatie . "' ";
			}
		
		if ($clubreq == 0)
			{
				$clubquery = " ";
			}
		elseif ($clubreq == 1)
			{
				$clubs = explode ("en", $club);
				$aantalclubs = count ($clubs);
				if ($aantalclubs == 1)
					{
						$clubquery = " IC_deelnemers.clubnr='" . $clubs[0] . "' ";
					}
				elseif ($aantalclubs>1)
					{
						$controle = $aantalclubs - 1;
						$clubquery = " (";
						for ($i = 0; $i < $aantalclubs; $i++)
							{
								$clubquery .= " IC_deelnemers.clubnr='" . $clubs[$i] . "' ";
								if ($i < $controle)
									{
										$clubquery .=  " OR ";
									}
							}
						$clubquery .= ") ";
					}
			}
		elseif ($clubreq == 2)
			{
				$clubs = explode ("en", $club);
				$aantalclubs = count ($clubs);
				foreach ($clubs as $clubss)
					{
						$clubquery .= " AND IC_deelnemers.clubnr<>'" . $clubss . "' ";
					}
			}
			
		$query = "SELECT * FROM IC_pouleindeling
					LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
					WHERE IC_pouleindeling.poulenr='0'
						AND IC_pouleindeling.gewichtsklassenr='" . $gewichtsklassenr . "'
						AND IC_pouleindeling.competitienr='" . $competitienr . "' ";
		$query2 = $query;
		if ($knip > 0)
			{
				$query .= " AND " . $knipquery;
				$query2 .= " AND " . $knipquery;
			}
		if ($gradreq > 0)
			{
				$query .= " AND " . $gradquery;
				if ($drop != 1)
					{
						$query2 .= " AND " . $gradquery;
					}
			}
		if ($clubreq == 1)
			{
				$query .= " AND " . $clubquery;
				if ($drop != 2)
					{
						$query2 .= " AND " . $clubquery;
					}
			}
		if ($clubreq == 2)
			{
				$query .= $clubquery;
				if ($drop != 2)
					{
						$query2 .= $clubquery;
					}
			}
		$query .= " ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC LIMIT 1";
		$query2 .= " ORDER BY IC_deelnemers.rating ASC, IC_deelnemers.gewicht ASC LIMIT 1";
		
		if ($nieuwpoule == 1)
			{
				$poulenr = $db->insert_id("INSERT INTO IC_poules (competitienr, gewichtsklassenr, poulestatus, mat) VALUES ('$competitienr', '$gewichtsklassenr', '0', '0')");
			}
		elseif ($nieuwpoule == 2)
			{
				$db->run_query("UPDATE IC_poules set poulestatus='1' WHERE poulenr=" . $poulenr);
				$poulenr = $db->insert_id("INSERT INTO IC_poules (competitienr, gewichtsklassenr, poulestatus, mat) VALUES ('$competitienr', '$gewichtsklassenr', '0', '0')");
			}
		$lid = $db->get_array($query);
		$lidaantal = $db->num_rows($query);
		$lid2 = $db->get_array($query2);
		$lidaantal2 = $db->num_rows($query2);

		if ($lidaantal == 1)
			{
				$volgorde = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE poulenr=" . $poulenr) + 1;
				$db->run_query("UPDATE IC_pouleindeling SET poulenr='" . $poulenr . "', volgorde='" . $volgorde . "'  WHERE competitienr='" . $competitienr . "' AND lidnr=" . $lid[0]["lidnr"]);
			}
		elseif ($lidaantal2 == 1)
			{
				$volgorde = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE poulenr=" . $poulenr) + 1;
				$db->run_query("UPDATE IC_pouleindeling SET poulenr='" . $poulenr . "', volgorde='" . $volgorde . "'  WHERE competitienr='" . $competitienr . "' AND lidnr=" . $lid2[0]["lidnr"]);
			}
		
		return $query;
	}

function bepaal_aantal_van_graduatie ($competitienr, $gewichtsklassenr, $graduatie, $soort, $poulenr, $knip, $kniprating)
	{
		global $db;
		if ($knip == 0) // Geen knip toepassen
			{
				$query = "SELECT * FROM IC_pouleindeling
							LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
							LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
							WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
								AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
								AND IC_deelnemers.graduatie='" . $graduatie . "' ";
				if ($soort == "inclusief")
					{
						$query .= "AND (IC_pouleindeling.poulenr='0' OR (IC_pouleindeling.poulenr<>'0' AND IC_poules.poulestatus='0'))";
					}
				elseif ($soort == "exclusief")
					{
						$query .= "AND IC_pouleindeling.poulenr='0'";
					}
				elseif ($soort == "inpoule")
					{
						$query .= "AND IC_pouleindeling.poulenr='" . $poulenr . "'";
					}
				else
					{
						return 0;
					}
			}
		elseif ($knip == 1) // Onder de knip
			{
				$query = "SELECT * FROM IC_pouleindeling
							LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
							LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
							WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
								AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
								AND IC_deelnemers.graduatie='" . $graduatie . "'
								AND IC_deelnemers.rating<'" . $kniprating . "' ";
				if ($soort == "inclusief")
					{
						$query .= "AND (IC_pouleindeling.poulenr='0' OR (IC_pouleindeling.poulenr<>'0' AND IC_poules.poulestatus='0'))";
					}
				elseif ($soort == "exclusief")
					{
						$query .= "AND IC_pouleindeling.poulenr='0'";
					}
				elseif ($soort == "inpoule")
					{
						$query .= "AND IC_pouleindeling.poulenr='" . $poulenr . "'";
					}
				else
					{
						return 0;
					}
			}
		elseif ($knip == 2) // Boven de knip
			{
				$query = "SELECT * FROM IC_pouleindeling
							LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
							LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
							WHERE IC_pouleindeling.competitienr='" . $competitienr . "' 
								AND IC_pouleindeling.gewichtsklassenr=" . $gewichtsklassenr . " 
								AND IC_deelnemers.graduatie='" . $graduatie . "'
								AND IC_deelnemers.rating>='" . $kniprating . "' ";
				if ($soort == "inclusief")
					{
						$query .= "AND (IC_pouleindeling.poulenr='0' OR (IC_pouleindeling.poulenr<>'0' AND IC_poules.poulestatus='0'))";
					}
				elseif ($soort == "exclusief")
					{
						$query .= "AND IC_pouleindeling.poulenr='0'";
					}
				elseif ($soort == "inpoule")
					{
						$query .= "AND IC_pouleindeling.poulenr='" . $poulenr . "'";
					}
				else
					{
						return 0;
					}
			}
		else
			{
				return 0;
			}
		$aantal = $db->num_rows($query);
		return $aantal;										
	}
?>