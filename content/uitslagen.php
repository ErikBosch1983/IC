<?php
echo "<h1>Competitie uitslagen</h1>";

$uitslagen = $db->get_array("
				SELECT * FROM IC_pouleindeling
					LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
					LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
					LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
					LEFT JOIN IC_clubs ON (IC_deelnemers.clubnr = IC_clubs.clubnr)
				WHERE IC_pouleindeling.plaats>0
				ORDER BY IC_competities.datum DESC, IC_pouleindeling.plaats ASC, IC_deelnemers.achternaam ASC");
$aantaluitslagen = $db->num_rows("
				SELECT * FROM IC_pouleindeling
					LEFT JOIN IC_poules ON (IC_poules.poulenr = IC_pouleindeling.poulenr)
					LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_poules.competitienr)
					LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
					LEFT JOIN IC_clubs ON (IC_deelnemers.clubnr = IC_clubs.clubnr)
				WHERE IC_pouleindeling.plaats>0
				ORDER BY IC_competities.datum DESC, IC_pouleindeling.plaats ASC, IC_deelnemers.achternaam ASC");
				
if ($aantaluitslagen == 0)
	{
		echo "Er zijn nog geen uitslagen bekend.";
	}
else
	{
		echo "<center><table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
		$competitienr = 0;
		foreach ($uitslagen as $uitslag)
			{
				if ($uitslag["competitienr"] != $competitienr)
					{
						$competitie = $db->get_array("SELECT * FROM IC_competities LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr) WHERE IC_competities.competitienr=" . $uitslag["competitienr"]);
						echo "	<tr><td colspan=\"4\">&nbsp;</td></tr>
								<tr><td colspan=\"4\"><h3>" . strftime("%e %B %Y", strtotime($competitie[0]["datum"])) . " bij " . $competitie[0][clubnaam] . "<h3></td></tr>";
						$competitienr = $uitslag["competitienr"];
					}
				if ($uitslag["plaats"] == 1)
					{
						echo "<tr><td width=\"25\"></td><td>1<sup>ste</sup></td>";
					}
				else
					{
						echo "<tr><td width=\"25\"></td><td>" . $uitslag["plaats"] . "<sup>de</sup></td>";
					}
				echo "<td>" . naam($uitslag["lidnr"]) . "</td><td>" . $uitslag["clubnaam"] . "</td></tr>";
			}
		echo "</table></center>";
	}

?>