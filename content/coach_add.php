<?php
$clubnr = check_clubnr ((int)$_GET["clubnr"]);
$club = $db->get_array("SELECT * FROM IC_clubs WHERE clubnr=" . $clubnr);
echo "	<h1>Coach toevoegen</h1>
		<h2>Coach toevoegen bij " . $club[0][clubnaam] . "</h2>
		<form name=\"coach_add\" method=\"post\" action=\"coach_add2.php\">
		<input type=\"hidden\" name=\"clubnr\" value=\"" . $clubnr . "\">
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