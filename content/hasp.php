<?php

$wachtwoord = $_GET["ww"];
echo $wachtwoord . "<br>" . password_hash("$wachtwoord", PASSWORD_DEFAULT); 

?>