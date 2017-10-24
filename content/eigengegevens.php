<?php
$errorbericht = (int)$_GET["err"];
$errorberichten = array (1 => "Geen mailadres ingegeven", 2 => "Geen correct mailadres ingegevoerd", 3 => "Het huidge wachtwoord is onjuist ingevoerd.", 4 => "Het nieuwe wachtwoord is niet twee keer hetzelfde ingevoerd.", 5 => "Dit mailadres is al in gebruik door iemand anders");

echo "<h1>Eigen gegevens</h1>
		<h2>Naam aanpassen</h2>
		<form name=\"coach_update1\" method=\"post\" action=\"coach_update1.php\">
		<input type=\"hidden\" name=\"coachnr\" value=\"" . $user[0][coachnr] . "\">
			<table cellpadding=\"3\" cellspacing=\"0\" border=\"0\">
				<tr>
					<td>Voornaam:</td>
					<td><input type=\"text\" name=\"voornaam\" value=\"" . $user[0][voornaam] . "\"></td>
				</tr>
				<tr>
					<td>Tussenvoegsels:</td>
					<td><input type=\"text\" name=\"tussenvoegsels\" value=\"" . $user[0][tussenvoegsels] . "\"></td>
				</tr>
				<tr>
					<td>Achternaam:</td>
					<td><input type=\"text\" name=\"achternaam\" value=\"" . $user[0][achternaam] . "\"></td>
				</tr>
				<tr>
					<td><input type=\"submit\" value=\"Gegevens aanpassen\"></td>
					<td></td>
				</tr>
			</table>
		</form><br>
		<h2>Email-adres veranderen</h2>";
		if ($errorbericht == 1 || $errorbericht == 2 || $errorbericht == 5)
			{
				echo $errorberichten[$errorbericht] . "<br><br>";
			}
echo "	<form name=\"coach_update2\" method=\"post\" action=\"coach_update2.php\">
		<input type=\"hidden\" name=\"coachnr\" value=\"" . $user[0][coachnr] . "\">
			<table cellpadding=\"3\" cellspacing=\"0\" border=\"0\">
				<tr>
					<td>Email:</td>
					<td><input type=\"text\" name=\"email\" value=\"" . $user[0][email] . "\"></td>
				</tr>
				<tr>
					<td><input type=\"submit\" value=\"Email aanpassen\"></td>
					<td></td>
				</tr>
			</table>
		</form><br>
		<h2>Wachtwoord veranderen</h2>";
		if ($errorbericht == 3 || $errorbericht == 4)
			{
				echo $errorberichten[$errorbericht] . "<br><br>";
			}
echo "	<form name=\"coach_update3\" method=\"post\" action=\"coach_update3.php\">
		<input type=\"hidden\" name=\"coachnr\" value=\"" . $user[0][coachnr] . "\">
			<table cellpadding=\"3\" cellspacing=\"0\" border=\"0\">
				<tr>
					<td>Huidige wachtwoord:</td>
					<td><input type=\"password\" name=\"wachtwoord1\" value=\"\"></td>
				</tr>
				<tr>
					<td>Nieuwe wachtwoord:</td>
					<td><input type=\"password\" name=\"wachtwoord2\" value=\"\"></td>
				</tr>
				<tr>
					<td>Nieuwe wachtwoord:</td>
					<td><input type=\"password\" name=\"wachtwoord3\" value=\"\"></td>
				</tr>
				<tr>
					<td><input type=\"submit\" value=\"Wachtwoord aanpassen\"></td>
					<td></td>
				</tr>
			</table>
		</form>";


?>