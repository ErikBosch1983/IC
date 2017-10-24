<?php
# Class includen
include 'classes.php';
include 'formules.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$clubnaam = clean ($_POST["clubnaam"]);

$db->run_query("INSERT INTO IC_clubs (clubnaam) VALUES ('$clubnaam')");

destruct( 'db' );

header ("location: index.php?target=clubs");


?>