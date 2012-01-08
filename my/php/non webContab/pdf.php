<?php
/* -------------------------------------------------------------------------------------------------------
Questo script prepara la stampa relativa 
al superamento del periodo di affiancamento


----------------------------------------------------------------------------------------------------------
*/


//============================================================+
// File name   : example_051.php
// Begin       : 2009-04-16
// Last Update : 2010-05-20
//
// Description : Example 051 for TCPDF class
//               Full page background
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Full page background
 * @author Nicola Asuni
 * @copyright 2004-2009 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2009-04-16
 */

require_once('./my/php/tcpdf/config/lang/eng.php');
require_once('./my/php/tcpdf/tcpdf.php');


// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// full background image
		// store current auto-page-break status
		$bMargin = $this->getBreakMargin();
		$auto_page_break = $this->AutoPageBreak;
		$this->SetAutoPageBreak(false, 0);
		//$img_file = K_PATH_IMAGES.'image_demo.jpg';
//		$img_file='./img.jpg';
		$img_file='./varie/formazione_personale2.jpg';		
		$this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 051');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

// remove default footer
$pdf->setPrintFooter(false);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
//$pdf->SetFont('times', '', 48);

// add a page
$pdf->AddPage();


//nome e cognome
$html = '<p fill="true" style="font-size:16pt;">Trif Ioana Maria</p>';
$pdf->writeHTMLCell($w=0, $h=0, $x='42', $y='19', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);



for ($i=0; $i<26; $i++){
	$topDistance=($i*7.8)+65;
	//data
	$html = '<p fill="true" style="font-size:12pt;">16/05/2010</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='10', $y=$topDistance, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//descrizione
	$html = '<p fill="true" style="font-size:12pt;">You can set a full page background.'.$i.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y=$topDistance, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

	//esito
	$html = '<p fill="true" style="font-size:12pt;">Esito positivo</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='150', $y=$topDistance, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

}




// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output('example_051.pdf', 'I');

//============================================================+
// END OF FILE                                                
//============================================================+
?>
