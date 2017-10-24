<?php
$datum = date("Y-m-d");

$competitiedagen = $db->get_array("SELECT * FROM IC_competities
								  	LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
									LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
									WHERE IC_seizoenen.status='1' AND IC_competities.datum<='" . $datum . "'
									ORDER BY IC_competities.datum ASC");
echo "	<h1>Statistiek</h1>
		<table border=\"1\" cellpadding=\"3\" cellspacing=\"0\">
			<tr>
				<td>Club</td>";

if (!empty($competitiedagen))
	{
		$competities = array();
		foreach($competitiedagen as $competitiedag)
			{
				echo "<td><center>" . $competitiedag["clubnaam"] . "<br>" . $competitiedag["datum"] . "</center></td>";
				array_push($competities, $competitiedag["competitienr"]);
				
			}
		echo "</tr>";
	}

$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief='1' AND clubnr<>'2'");
if (!empty($clubs))
	{
		foreach($clubs as $club)
			{
				echo "<tr><td>" . $club["clubnaam"] . "</td>";
				foreach ($competities as $competitie)
					{
						$deelnames = $db->num_rows("SELECT * FROM IC_pouleindeling 
												   		LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr) 
														WHERE IC_deelnemers.clubnr='" . $club["clubnr"] . "'  
															AND IC_pouleindeling.competitienr='" . $competitie . "'");
						echo "<td><center>" . $deelnames . "</center></td>";
					}
				echo "</tr>";
				
			}
	}
echo  "<tr><td>Totalen</td>";
foreach ($competities as $competitie)
	{
		$deelnames = $db->num_rows("SELECT * FROM IC_pouleindeling WHERE IC_pouleindeling.competitienr='" . $competitie . "'");
		$subtotaal = $subtotaal + $deelnames;
		echo "<td><center>" . $deelnames . "</center></td>";
	}
echo "</table>
		Totale deelname: " . $subtotaal . "<br><br>";	

$aantal1 = count($competities);
$vaakkomers1 = $db->num_rows("SELECT * FROM IC_pouleindeling
							 	LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_pouleindeling.competitienr)
							 	LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
								WHERE IC_seizoenen.status='1'
								GROUP BY IC_pouleindeling.lidnr
								HAVING COUNT(IC_pouleindeling.lidnr)=" . $aantal1);

$aantal2 = count($competities) - 1;
$vaakkomers2 = $db->num_rows("SELECT * FROM IC_pouleindeling
							 	LEFT JOIN IC_competities ON (IC_competities.competitienr = IC_pouleindeling.competitienr)
							 	LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
								WHERE IC_seizoenen.status='1'
								GROUP BY IC_pouleindeling.lidnr
								HAVING COUNT(IC_pouleindeling.lidnr)=" . $aantal2);		
echo "
		Aantal deelnemers met " . $aantal1 . " deelnames dit seizoen: " . $vaakkomers1 . "<br>
		Aantal deelnemers met " . $aantal2 . " deelnames dit seizoen: " . $vaakkomers2 . "<br>";
?>