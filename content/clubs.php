<?php
echo "	<center><h1>Club Overzicht</h1><center>
		<p>De deelnemende clubs zijn:</p>";

$clubs = $db->get_array("SELECT * FROM IC_clubs WHERE clubactief=1 AND clubnr<>2 ORDER BY clubnaam");
$aantal = $db->num_rows("SELECT * FROM IC_clubs WHERE clubactief=1 AND clubnr<>2 ORDER BY clubnaam");

foreach ($clubs as $club)
	{
		echo "<p>" . $club[clubnaam];
		$coaches 	= $db->get_array("SELECT * FROM IC_coaches WHERE clubnr=" . $club[clubnr] . " ORDER BY actief DESC, achternaam ASC, voornaam ASC");
		$coachaantal = $db->num_rows("SELECT * FROM IC_coaches WHERE clubnr=" . $club[clubnr] . " ORDER BY actief DESC, achternaam ASC, voornaam ASC");
		if ($coachaantal != 0)
			{
				foreach ($coaches as $coach)
					{
						echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . naam_coach ($coach[coachnr]) . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(" . $coach["email"] . ")" ;
					}
			}
		echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?target=coach_add&clubnr=" . $club[clubnr] . "\">Coach toevoegen</a></p>";
	}
echo "	<h2>Club toevoegen</h2>
		
			<form method=\"post\" name=\"club_toevoegen\" action=\"club_add.php\">
		<p>		Clubnaam: <input type=\"text\" name=\"clubnaam\" value=\"\"><input type=\"submit\" value=\"Club toevoegen\"></p>
			</form>
		";


?>