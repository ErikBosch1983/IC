<?php
$errors = array ();

$uitslagen = $db->get_array("
				SELECT * FROM IC_pouleindeling
					LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
					LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
					LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
				WHERE IC_deelnemers.clubnr=" . $user[0][clubnr] . " AND IC_pouleindeling.plaats>0
				ORDER BY IC_competities.datum DESC, IC_pouleindeling.plaats ASC, IC_deelnemers.achternaam ASC");
$aantaluitslagen = $db->num_rows("
				SELECT * FROM IC_pouleindeling
					LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
					LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
					LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
				WHERE IC_deelnemers.clubnr=" . $user[0][clubnr] . " AND IC_pouleindeling.plaats>0
				ORDER BY IC_competities.datum DESC, IC_pouleindeling.plaats ASC, IC_deelnemers.achternaam ASC");
				
echo "	<center><h1>Uitslagen van " . $user[0][clubnaam] . "</h1></center>";
if ($aantaluitslagen == 0)
	{
		echo "Er zijn nog geen uitslagen bekend.";
	}
else
	{
		$competitienr = 0;
		foreach ($uitslagen as $uitslag)
			{
				if ($uitslag["competitienr"] != $competitienr)
					{
						$clubnaam = $db->get_array("SELECT * FROM IC_competities
												   	LEFT JOIN IC_clubs ON (IC_competities.clubnr = IC_clubs.clubnr)
													WHERE IC_competities.competitienr=" . $uitslag["competitienr"]);
						echo "<h3>Interne Competitie bij " . $clubnaam[0]["clubnaam"] . " op " . strftime("%d %B %Y", strtotime($uitslag["datum"])) . "</h3>";
						$competitienr = $uitslag["competitienr"];
					}
				if ($uitslag["plaats"] == 1)
					{
						echo "1<sup>ste</sup> ";
					}
				else
					{
						echo $uitslag["plaats"] . "<sup>de</sup> ";
					}
				echo naam($uitslag["lidnr"]) . "<br>";
			}
	}
?>
		