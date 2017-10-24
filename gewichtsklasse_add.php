<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$upperbound = (int)$_POST["upperbound"];
$seizoennr 	= check_seizoennr ($_POST["seizoennr"]);

$gewichtsklassenr = $db->insert_id("INSERT INTO IC_gewichtsklassen (upperbound) VALUES ('$upperbound')");
$db->run_query("INSERT INTO IC_gewichtseizoen (seizoennr, gewichtsklassenr) VALUES ('$seizoennr', '$gewichtsklassenr')");

destruct( 'db' );

header ("location: index.php?target=seizoenen");


?>