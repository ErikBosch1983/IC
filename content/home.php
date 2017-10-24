<?php
echo "	<center><h1>Welkom bij de Interne Competitie Noord-Limburg</h1></center>
		
		<p>De interne competitie is een samenwerkingsverband tussen de volgende judoclubs:
		<ul>";

$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief=1 AND clubnr<>2 ORDER BY clubnaam");
$aantal = $db->num_rows("SELECT * FROM IC_clubs WHERE clubactief=1 AND clubnr<>2 ORDER BY clubnaam");

if ($aantal == 0)
	{
		echo "<li> enz enz</li>";
	}
else
	{
		foreach ($clubs as $club)
			{
				echo "<li>" . $club["clubnaam"] . "</li>";
			}
	}


echo "	</ul></p>
		<p>De doelstelling van de Interne Competitie is het aanbieden van laagdrempelige judotoernooitjes in de regio ter bevordering van wedstrijdjudo.</p>";
?>