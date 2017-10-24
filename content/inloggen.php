<?php
$error = (int)$_GET["er"];
$errorberichten = array (
	1 => "Er is geen mail-adres ingevoerd.",
	2 => "Het ingegeven mail-adres is geen geldig mail-adres.",
	3 => "",
	4 => "Om onbekende redenen kon geem email worden verstuurd.",
	5 => "Het opgegeven mail-adres staat niet geregistreerd bij een coach.");
echo "
<center><h1>Inloggen</h1></center>
<br><br><font color=\"red\">" . $errorberichten[$error] . "</font>
<form name=\"inloggen\" method=\"post\" action=\"checkuser.php\">
	<center>
	<table border=\"0\" cellpadding=\"3\" cellspacing=\"0\">
		<tr>
			<td>Email</td><td><input type=\"text\" name=\"email\" value=\"\"></td>
		</tr>
		<tr>
			<td>Wachtwoord</td><td><input type=\"password\" name=\"wachtwoord\" value=\"\"></td>
		</tr>
		<tr>
			<td><input type=\"submit\" value=\"Inloggen\"></td><td><a href=\"index.php?target=wachtwoordvergeten\">Wachtwoord vergeten?</a></td>
		</tr>
	</table>
	</center>
</form>";


?>