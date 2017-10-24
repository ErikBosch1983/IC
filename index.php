<?php
include "functions.php";
include "classes.php";
include "header.php";
setlocale(LC_ALL, 'nl_NL');

$errorberichten = array (1 => "Mail adres is onbekend", 2 => "Het ingegeven wachtwoord is onjuist." ,3 => "Uw inlog rechten zijn door iemand van uw club op non-actief gezet.");
$errorbericht = (int)$_GET["er"];
							
$target = "content/" . check_redirect ($_GET["target"]) . ".php";

session_start();

$uitslagen 			= $db->get_array("SELECT * FROM IC_competities 
										LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
										LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
										WHERE IC_seizoenen.status=1 AND IC_competities.dagstatus=2
										ORDER BY IC_competities.datum DESC");
$aantaluitslagen 	= $db->num_rows ("SELECT * FROM IC_competities 
										LEFT JOIN IC_seizoenen ON (IC_seizoenen.seizoennr = IC_competities.seizoennr)
										LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_competities.clubnr)
										WHERE IC_seizoenen.status=1 AND IC_competities.dagstatus=2
										ORDER BY IC_competities.datum DESC");

if (@$_SESSION['IClogin'] == "true")
	{
		if ($_SESSION['ICusernr'] == 27 || $_SESSION['ICusernr'] == 28 || $_SESSION['ICusernr'] == 29 || $_SESSION['ICusernr'] == 30 || $_SESSION['ICusernr'] == 31)
			{
				header ("location: index2.php");
			}
		$inlog = $_SESSION['ICusernr'];
		
		$user = $db->get_array("SELECT * FROM IC_coaches LEFT JOIN IC_clubs ON (IC_coaches.clubnr = IC_clubs.clubnr) WHERE IC_coaches.coachnr=" . $inlog);
		if ($user[0][tussenvoegsels] == "")
			{
				$usernaam = $user[0][voornaam] . " " .$user[0][achternaam];
			}
		else
			{
				$usernaam = $user[0][voornaam] . " " .$user[0][tussenvoegsels] . " " .$user[0][achternaam];
			}
		
		echo "
		<html>
		<head>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
			<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />
			<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />
			<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
			<script type=\"text/javascript\" src=\"menu_jquery.js\"></script>
			<title>Interne competitie Noord-Limburg</title>
			<script>
            	$(document).ready(function (){
                	$(\"#selecter\").change(function() {
                    	if ($(this).val() != \"0\") {
                        	$(\"#extra1\").hide();
							$(\"#extra2\").hide();
							$(\"#extra3\").hide();
							$(\"#extra4\").hide();
							$(\"#extra5\").hide();
							$(\"#extra6\").hide();
							$(\"#extra7\").hide();
							$(\"#extra8\").hide();
                        }else{
							$(\"#extra1\").show();
							$(\"#extra2\").show();
							$(\"#extra3\").show();
							$(\"#extra4\").show();
							$(\"#extra5\").show();
							$(\"#extra6\").show();
							$(\"#extra7\").show();
							$(\"#extra8\").show();
						} 
					});
				});
			</script>
		</head>
		<body>
			<div id=\"container\">
				<div id=\"header\">
					<img src=\"afbeeldingen/logo-ic.png\" style=\"float: left; padding-right: 10px; display: block; position: absolute; left: -50px;\"  />
					<center><font size=\"40\">Interne Competitie<br>Noord-Limburg</font></center>
				</div>
				<div id=\"cssmenu\">
					<ul>
						<li class=\"active\"><a href=\"index.php?target=home\"><span>Home</span></a></li>
						<li class=\"active\"><a href=\"index.php?target=bestuur\"><span>Bestuur</span></a></li>
						<li class=\"has-sub\"><a href=\"index.php?target=clubgegevens\"><span>Club gegevens</span></a>
							<ul>
								<li class=\"active\"><a href=\"index.php?target=clubgegevens\"><span>Club gegevens</span></a></li>
								<li class=\"active\"><a href=\"index.php?target=eigengegevens\"><span>Eigen gegevens</span></a></li>
								<li class=\"active\"><a href=\"index.php?target=clubdeelnemers\"><span>Deelnemers</span></a></li>
								<li class=\"last\"><a href=\"index.php?target=clubuitslagen\"><span>Resultaten</span></a></li>
							</ul>
						</li>";
		if ($user[0][admin] == 1)
			{
				echo "<li class=\"has-sub\"><a href=\"index.php?target=competitie\"><span>Competitie</span></a>
						<ul>
							<li class=\"active\"><a href=\"index.php?target=clubs\"><span>Clubs</span></a></li>
							<li class=\"active\"><a href=\"index.php?target=seizoenen\"><span>Seizoenen</span></a></li>
							<li class=\"active\"><a href=\"index.php?target=deelnemers\"><span>Deelnemers</span></a></li>
							<li class=\"last\"><a href=\"index.php?target=statistiek\"><span>Statistiek</span></a></li>
						</ul>
					</li>";
			}
		echo "			<li class=\"has-sub\"><a href=\"index.php?target=uitslagen\"><span>Uitslagen</span></a>
							<ul>";
		if ($aantaluitslagen != 0)
			{
				$i = 0;
				foreach ($uitslagen as $uitslag)
					{
						$i = $i + 1;
						if ($i != $aantaluitslagen)
							{
								echo "<li class=\"active\"><a href=\"index.php?target=uitslag&dagnr=" . $uitslag["competitienr"] . "\"><span>" . strftime("%e %B %Y", strtotime($uitslag["datum"])) . "<br>" . $uitslag[clubnaam] . "</span></a></li>";
							}
						else
							{
								echo "<li class=\"last\"><a href=\"index.php?target=uitslag&dagnr=" . $uitslag["competitienr"] . "\"><span>" . strftime("%e %B %Y", strtotime($uitslag["datum"])) . "<br>" . $uitslag[clubnaam] . "</span></a></li>";
							}
					}
			}
		else
			{
				echo "<li class=\"last\"><a href=\"index.php?target=uitslagen\"><span>Uitslagen</span></a></li>";
			}
		echo "				</ul>
						<li class=\"last\"><a href=\"index.php?target=uitloggen\"><span>Uitloggen</span></a></li>
					</ul>
				</div>
				<div id=\"content\" valign=\"top\">";
		if ($errorbericht == 1 || $errorbericht == 2 || $errorbericht == 3 )
			{
				echo $errorberichten[$errorbericht];
			}
		include $target;
		
		echo "	</div>
				<div id=\"footer\">
					<p><center>Ingelogd als " . $usernaam . " van " . $user[0][clubnaam] . " <a href=\"uitloggen.php\">uitloggen</a><center></p>
				</div>
			</div>
		</body>
		</html>";
		
	}
elseif (@$_SESSION['IClogin'] == "false")
	{
		echo "
		<html>
		<head>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
			<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />
			<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />
			<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
			<script type=\"text/javascript\" src=\"menu_jquery.js\"></script>
			<title>Interne competitie Noord-Limburg</title>
		</head>
		<body>
			<div id=\"container\">
				<div id=\"header\">
					<img src=\"afbeeldingen/logo-ic.png\" style=\"float: left; padding-right: 10px; display: block; position: absolute; left: -50px;\"  />
					<center><font size=\"40\">Interne Competitie<br>Noord-Limburg</font></center>
				</div>
				<div id=\"cssmenu\">
					<ul>
						<li class=\"active\"><a href=\"index.php?target=home\"><span>Home</span></a></li>
						<li class=\"active\"><a href=\"index.php?target=bestuur\"><span>Bestuur</span></a></li>
						<li class=\"active\"><a href=\"index.php?target=meedoen\"><span>Mee doen?</span></a></li>
						<li class=\"has-sub\"><a href=\"index.php?target=uitslagen\"><span>Uitslagen</span></a>
							<ul>";
		if ($aantaluitslagen != 0)
			{
				$i = 0;
				foreach ($uitslagen as $uitslag)
					{
						$i = $i + 1;
						if ($i != $aantaluitslagen)
							{
								echo "<li class=\"active\"><a href=\"index.php?target=uitslag&dagnr=" . $uitslag["competitienr"] . "\"><span>" . strftime("%e %B %Y", strtotime($uitslag["datum"])) . "<br>" . $uitslag[clubnaam] . "</span></a></li>";
							}
						else
							{
								echo "<li class=\"last\"><a href=\"index.php?target=uitslag&dagnr=" . $uitslag["competitienr"] . "\"><span>" . strftime("%e %B %Y", strtotime($uitslag["datum"])) . "<br>" . $uitslag[clubnaam] . "</span></a></li>";
							}
					}
			}
		else
			{
				echo "<li class=\"last\"><a href=\"index.php?target=uitslagen\"><span>Uitslagen</span></a></li>";
			}
		echo "				</ul>
						<li class=\"last\"><a href=\"index.php?target=inloggen\"><span>inloggen</span></a></li>
					</ul>
				</div>
				<div id=\"content\" valign=\"top\">";
		if ($errorbericht == 1 || $errorbericht == 2 || $errorbericht == 3 )
			{
				echo $errorberichten[$errorbericht];
			}			
		include "content/inloggen.php";
		
		echo "	</div>
				<div id=\"footer\">
					<p><center><center></p>
				</div>
			</div>
		</body>
		</html>";		
	}
else
	{

		echo "
		<html>
		<head>
			<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
			<meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge,chrome=1\" />
			<link rel=\"stylesheet\" type=\"text/css\" href=\"style.css\" />
			<script src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js\"></script>
			<script type=\"text/javascript\" src=\"menu_jquery.js\"></script>
			<title>Interne competitie Noord-Limburg</title>
		</head>
		<body>
			<div id=\"container\">
				<div id=\"header\">
					<img src=\"afbeeldingen/logo-ic.png\" style=\"float: left; padding-right: 10px; display: block; position: absolute; left: -50px;\"  />
					<center><font size=\"40\">Interne Competitie<br>Noord-Limburg</font></center>
				</div>
				<div id=\"cssmenu\">
					<ul>
						<li class=\"active\"><a href=\"index.php?target=home\"><span>Home</span></a></li>
						<li class=\"active\"><a href=\"index.php?target=bestuur\"><span>Bestuur</span></a></li>
						<li class=\"active\"><a href=\"index.php?target=meedoen\"><span>Mee doen?</span></a></li>
						<li class=\"has-sub\"><a href=\"index.php?target=uitslagen\"><span>Uitslagen</span></a>
							<ul>";
		if ($aantaluitslagen != 0)
			{
				$i = 0;
				foreach ($uitslagen as $uitslag)
					{
						$i = $i + 1;
						if ($i != $aantaluitslagen)
							{
								echo "<li class=\"active\"><a href=\"index.php?target=uitslag&dagnr=" . $uitslag["competitienr"] . "\"><span>" . strftime("%e %B %Y", strtotime($uitslag["datum"])) . "<br>" . $uitslag[clubnaam] . "</span></a></li>";
							}
						else
							{
								echo "<li class=\"last\"><a href=\"index.php?target=uitslag&dagnr=" . $uitslag["competitienr"] . "\"><span>" . strftime("%e %B %Y", strtotime($uitslag["datum"])) . "<br>" . $uitslag[clubnaam] . "</span></a></li>";
							}
					}
			}
		else
			{
				echo "<li class=\"last\"><a href=\"index.php?target=uitslagen\"><span>Uitslagen</span></a></li>";
			}
		echo "				</ul>
						<li class=\"last\"><a href=\"index.php?target=inloggen\"><span>Inloggen</span></a></li>
					</ul>
				</div>
				<div id=\"content\" valign=\"top\">";
					
		include $target;
		
		echo "	</div>
				<div id=\"footer\">
					<p><center><center></p>
				</div>
			</div>
		</body>
		</html>";
	}
?>