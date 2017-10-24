<?php
$errors = array ();
echo "	<center><h1>Gegevens van " . $user[0][clubnaam] . "</h1></center>
		<h2>Aanpassen van de clubnaam</h2>
		<form name=\"club_update\" method=\"post\" action=\"club_update.php\">
		<input type=\"hidden\" name=\"clubnr\" value=\"" . $user[0][clubnr] . "\">
		Naam: <input size=\"50\" type=\"text\" name=\"clubnaam\" value=\"" . $user[0][clubnaam] . "\">
		<input type=\"submit\" value=\"Aanpassen\">
		</form>
		<br>
		<h2>Coaches / Begeleiders</h2>
			<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">";
		
		$coaches 	= $db->get_array("SELECT * FROM IC_coaches WHERE clubnr=" . $user[0][clubnr] . " AND actief=1");
		$aantal		= $db->num_rows ("SELECT * FROM IC_coaches WHERE clubnr=" . $user[0][clubnr] . " AND actief=1");
		
		if ($aantal == 0)
			{
				echo "<p>Geen actieve coaches / begeleiders</p>";
			}
		else
			{
				foreach ($coaches as $coach)
					{
						echo "	<tr>
									<td>" . $coach[voornaam] . " " . $coach[tussenvoegsels] . " " . $coach[achternaam] . "</td>
									<td>" . $coach[email] . "</td>";
						if ($coach[coachnr] != $user[0][coachnr])
							{
								echo "	<td><a href=\"coach_activeren.php?coachnr=" . $coach[coachnr] . "\"><font color=\"green\">Actief</font></a></td>";
							}
						echo "	</tr>";
					}
					
			}
		$coaches 	= $db->get_array("SELECT * FROM IC_coaches WHERE clubnr=" . $user[0][clubnr] . " AND actief=0");
		$aantal		= $db->num_rows ("SELECT * FROM IC_coaches WHERE clubnr=" . $user[0][clubnr] . " AND actief=0");
		
		if ($aantal != 0)
			{
				foreach ($coaches as $coach)
					{
						echo "	<tr>
									<td>" . $coach[voornaam] . " " . $coach[tussenvoegsels] . " " . $coach[achternaam] . "</td>
									<td>" . $coach[email] . "</td>
									<td><a href=\"coach_activeren.php?coachnr=" . $coach[coachnr] . "\"><font color=\"red\">Inactief</font></a></td>
								</tr>";
					}
					
			}
		echo "	</table><br>
				<h2>Nieuwe coach/begeleider</h2>
				<form name=\"coach_add\" method=\"post\" action=\"coach_add.php\">
				<input type=\"hidden\" name=\"clubnr\" value=\"" . $user[0][clubnr] . "\">
					<table cellpadding=\"3\" cellspacing=\"0\" border=\"0\">
						<tr>
							<td>Voornaam</td>
							<td><input type=\"text\" name=\"voornaam\" value=\"\"></td>
						</tr>
						<tr>
							<td>Tussenvoegsels</td>
							<td><input type=\"text\" name=\"tussenvoegsels\" value=\"\"></td>
						</tr>
						<tr>
							<td>Achternaam</td>
							<td><input type=\"text\" name=\"achternaam\" value=\"\"></td>
						</tr>
						<tr>
							<td>Email</td>
							<td><input type=\"text\" name=\"email\" value=\"\"></td>
						</tr>
						<tr>
							<td><input type=\"submit\" value=\"Toevoegen\"></td>
							<td></td>
						</tr>
					</table>
				</form>";
						?>
		