<?php
# Class includen
include 'classes.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$beginjaar = $_POST["beginjaar"];
$eindjaar = $_POST["eindjaar"];

$db->run_query("INSERT INTO IC_seizoenen (beginjaar, eindjaar) VALUES ('$beginjaar', '$eindjaar')");

destruct( 'db' );

header ("location: index.php?target=seizoenen");


?>