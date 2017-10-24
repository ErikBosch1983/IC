<?php
# Class includen
include 'classes.php';
include 'functions.php';
require('fpdf/fpdf.php');

header('Content-type: application/pdf');

# Object aanmaken
$db = new database( 'judokanblerick_', 'blackheart1', 'judokanblerick_' );

$poulenr = check_poulenr ($_GET["poulenr"]);

if ($poulenr != "false")
	{
		$poule = $db->get_array("SELECT * FROM IC_poules 
									LEFT JOIN IC_competities ON (IC_poules.competitienr = IC_competities.competitienr)
									LEFT JOIN IC_clubs ON (IC_competities.clubnr = IC_clubs.clubnr)
									WHERE IC_poules.poulenr='" . $poulenr . "'");
		
		$deelnemers = $db->get_array("SELECT * FROM IC_pouleindeling
									 	LEFT JOIN IC_deelnemers ON (IC_deelnemers.lidnr = IC_pouleindeling.lidnr)
										LEFT JOIN IC_clubs ON (IC_clubs.clubnr = IC_deelnemers.clubnr)
										WHERE IC_pouleindeling.poulenr='" . $poulenr . "'
										ORDER BY IC_pouleindeling.volgorde");
		$gewichtsklasse = $db->get_array("SELECT * FROM IC_poules
										 	LEFT JOIN IC_gewichtsklassen ON (IC_gewichtsklassen.gewichtsklassenr = IC_poules.gewichtsklassenr)
											WHERE IC_poules.poulenr='" . $poulenr . "'");
		$aantal = count($deelnemers);
																					  
		$pdf = new FPDF('L','mm','A4');
		$pdf->SetFont('Arial','B',25);
		$pdf->SetFillColor(200,200,200);
		
		$pdf->SetXY (10, 10);

		$pdf->Cell(10, 200, '', 0);
		$pdf->Cell(254, 11, 'Wedstrijdformulier', 1, 2, 'C');
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(254, 5, '', 0, 2);
		$pdf->Cell(27, 10, 'Organisatie', 1, 0);
		$pdf->Cell(68, 10, pdfready($poule[0]["clubnaam"]), 1, 0);
		$pdf->Cell(27, 10, '', 0, 0);
		$pdf->Cell(35, 10, 'Poulenr', 1, 0);
		$pdf->Cell(35, 10, $poulenr, 1, 1, 'R');
		
		$pdf->Cell(10, 10);
		$pdf->Cell(27, 10, 'Datum', 1, 0);
		$pdf->Cell(68, 10, strftime("%e %B %Y", strtotime($poule[0]["datum"])), 1, 0);
		$pdf->Cell(27, 10, '', 0, 0);
		$pdf->Cell(35, 10, 'Gewichtsklasse', 1, 0);
		$pdf->Cell(35, 10, '-' . $gewichtsklasse[0]["upperbound"], 1, 1, 'R');
		
		$pdf->Cell(5, 5, '', 0, 1);
		$pdf->Cell(10, 5);
		$pdf->Cell(65, 5, '', 0, 0, 'C');
		
		if ($aantal == 5)
			{
				for ($i = 1; $i <= 10; $i++)
					{
						$pdf->Cell(14, 5, "$i", 0, 0, 'C');
					}
			}
		else
			{
				for ($i = 1; $i <= 6; $i++)
					{
						$pdf->Cell(14, 5, "$i", 0, 0, 'C');
					}
			}
		$pdf->Cell(14, 5, 'Gew.', 0, 0, 'C');
		$pdf->Cell(14, 5, 'Punt', 0, 0, 'C');
		$pdf->Cell(14, 5, 'Plaats', 0, 1, 'C');

		$volgorde = 0;
		foreach ($deelnemers as $deelnemer)
			{
				$volgorde = $volgorde + 1;
				$rating = bepaal_rating ($deelnemer["lidnr"], $poule[0]["datum"]);
				
				$pdf->Cell(10, 10);
				$pdf->SetFont('Arial','B',13);
				$pdf->Cell(65, 10, pdfready(naam($deelnemer["lidnr"])) . "  (" . $rating . ")", 'LTR');
				
				if ($aantal == 5)
					{
						for ($i = 1; $i <= 10 ; $i++)
							{
								$wedstrijd = $db->get_array("SELECT * FROM IC_matches WHERE volgorde='" . $i . "' AND size='" . $aantal . "' AND  (spelerrood='" . $volgorde . "' OR spelerwit='" . $volgorde . "')");
								if (empty($wedstrijd))
									{
										$pdf->Cell(14, 10, '', 'TRL', 0, 'L', '1');
									}
								else
									{
										$pdf->Cell(14, 10, '', 'LTR', 0, 'L');
									}
							}
					}
				else
					{
						for ($i = 1; $i <= 6; $i++)
							{
								$wedstrijd = $db->get_array("SELECT * FROM IC_matches WHERE volgorde='" . $i . "' AND size='" . $aantal . "' AND  (spelerrood='" . $volgorde . "' OR spelerwit='" . $volgorde . "')");
								if (empty($wedstrijd))
									{
										$pdf->Cell(14, 10, '', 'LTR', 0, 'L', '1');
									}
								else
									{
										$pdf->Cell(14, 10, '', 'LTR', 0, 'L');
									}
							}
					}

				
				$pdf->Cell(14, 10, '', 'LTR', 0, 'L');
				$pdf->Cell(14, 10, '', 'LTR', 0, 'L');
				$pdf->Cell(14, 10, '', 'LTR', 1, 'L');
				
				$pdf->Cell(10, 7);
				
				$pdf->SetFont('Arial','',11);
				$pdf->Cell(65, 7, pdfready($deelnemer["clubnaam"]), 'LBR');
				
				if ($aantal == 5)
					{
						for ($i = 1; $i <= 10 ; $i++)
							{
								$wedstrijd = $db->get_array("SELECT * FROM IC_matches WHERE volgorde='" . $i . "' AND size='" . $aantal . "' AND  (spelerrood='" . $volgorde . "' OR spelerwit='" . $volgorde . "')");
								if (empty($wedstrijd))
									{
										$pdf->Cell(14, 7, '', 'LBR', 0, 'L', '1');
									}
								else
									{
										$pdf->Cell(14, 7, '', 'LBR', 0, 'L');
									}
							}
					}
				else
					{
						for ($i = 1; $i <= 6; $i++)
							{
								$wedstrijd = $db->get_array("SELECT * FROM IC_matches WHERE volgorde='" . $i . "' AND size='" . $aantal . "' AND  (spelerrood='" . $volgorde . "' OR spelerwit='" . $volgorde . "')");
								if (empty($wedstrijd))
									{
										$pdf->Cell(14, 7, '', 'LBR', 0, 'L', '1');
									}
								else
									{
										$pdf->Cell(14, 7, '', 'LBR', 0, 'L');
									}
							}
					}

				
				$pdf->Cell(14, 7, '', 'LBR', 0, 'L');
				$pdf->Cell(14, 7, '', 'LBR', 0, 'L');
				$pdf->Cell(14, 7, '', 'LBR', 1, 'L');
				
				
			}
			
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(8, 8, '', 0, 1);
		$pdf->Cell(10, 10);
		$pdf->Cell(65, 10, 'Hoogste verschilpunt', 1);	
		$pdf->Cell(98, 10);	
		$pdf->Cell(84, 10, 'Tijd houdgrepen in seconden', 1, 1);
		$pdf->Cell(10, 10);
		$pdf->Cell(30, 10, 'Ippon:', 'LT');
		$pdf->Cell(35, 10, '10 punten', 'TR');	
		$pdf->Cell(98, 10);	
		$pdf->Cell(30, 10, '10 t/m 19:', 'LT');
		$pdf->Cell(54, 10, 'Waza-Ari:', 'TR', 1);
		$pdf->Cell(10, 10);
		$pdf->Cell(30, 10, 'Waza-Ari:', 'L');
		$pdf->Cell(35, 10, '7 punten', 'R');	
		$pdf->Cell(98, 10);	
		$pdf->Cell(30, 10, '20:', 'LB');
		$pdf->Cell(54, 10, 'Ippon', 'RB', 1);
		$pdf->Cell(10, 10);
		$pdf->Cell(30, 10, 'Beslissing:', 'BL');
		$pdf->Cell(35, 10, '1 punt', 'BR');	
		$pdf->Cell(98, 10);	
		$pdf->Cell(84, 10, 'Wedstrijdtijd 2 minuten', 'LBR');


		$pdf->Output('I', "poule.pdf");	
	}
else
	{
		header ("location: index.php");
	}

?>