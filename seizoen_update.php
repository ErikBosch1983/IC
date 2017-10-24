<?php
# Class includen
include 'classes.php';
include 'functions.php';

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$seizoennr 	= check_seizoennr ($_POST["seizoennr"]);

echo $seizoennr;

if ($seizoennr != "false")
	{
		$db->run_query("UPDATE IC_seizoenen SET status='1' WHERE seizoennr=" . $seizoennr);
	}

destruct( 'db' );

header ("location: index.php?target=seizoenen");


?>