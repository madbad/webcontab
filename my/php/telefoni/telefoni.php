<?php
/* -------------------------------------------------------------------------------------------------------
	Questa libreria esegue la stampa 
	della rubrica telefonica a partire da un file ods
----------------------------------------------------------------------------------------------------------
*/
//include ('./../core/config.inc.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/tcpdf/config/lang/ita.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/tcpdf/tcpdf.php');
error_reporting(-1);
set_time_limit (0);
// Extend the TCPDF class to create custom Header and Footer

class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// full background image
		// store current auto-page-break status
		$bMargin = $this->getBreakMargin();
		$auto_page_break = $this->AutoPageBreak;
		$this->SetAutoPageBreak(false, 0);
//		$this->Image($GLOBALS['img_file'], 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
	}
}



//**********************************************************
function generaRubrica(){

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('La Favorita di Brun G. & G. Srl Unip.');
	$pdf->SetTitle('Rubrica');

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
	$pdf->SetAutoPageBreak(TRUE, 0);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	@$pdf->setLanguageArray($l);

	//aggiungo una pagina vuota
	$pdf->AddPage();
	
	//********************************************
	//estrazione dati dal file rubrica
	
	include "./../libs/easyODS.php";
	/**
	 * We unpacking the *.ods files ZIP arhive to find content.xml path
	 * 
	 * @var String File path of the *.ods file
	 * @var String Directory path where we choose to store the unpacked files must have write permissions
	 */
	$path = easy_ods_read::extract_content_xml("./rubrica.ods","./temp");

	/**
	 * We create the $easy_ods_read object
	 *  
	 * @param Integer 0 First spreadsheet
	 * @param String $path File path of the content.xml file
	 * 
	 * @return Object $easy_ods_read Object of the class
	 */
	$easy_ods_read = new easy_ods_read(0,$path);

	/**
	 * We take the needed data from the file
	 */
	$righe=$easy_ods_read->extract_data("0","100");

	//**
	//**********************************************
	//rimuovi la prima riga con le intestazioni e la memorizza in una nuova variabile
	$intestazione = array_shift($righe);
	$html = "<table><th><td>Nominativo</td><td>Note</td><td>Num.Tel</td></th>";
	
	foreach ($righe as $key => $value) {
	//print_r($value);
		@$html .= '<tr><td>'.$value['A'].'</td>'.'<td>'.$value['B'].'</td>'.'<td>'.$value['C'].') '.$value['D'].'</td></tr>';
	}
	$html.='</table>';
	
	$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', 10);
	$pdf->writeHTMLCell($w=0, $h=0, $x='5', $y='5', $html, $border=1, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	
	//salvo il file
	@$pdf->Output("./rubrica.pdf", 'F');
	//e ne invio una copia al browser per visualizzarlo
	//@$pdf->Output('Rubrica.pdf', 'I');
	
}
generaRubrica();
?>