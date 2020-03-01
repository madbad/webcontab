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

$prevLettera = $lettera ='A';
$riga=0;
$html='';
$pdf='';
// Extend the TCPDF class to create custom Header and Footer

class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		global $lettera;
		// restore auto-page-break status
		$this->SetAutoPageBreak(FALSE, 0);
        // Logo
        // Set font
        $this->SetFont('helvetica', 'B', 9);
        // Title
        $this->Cell(0, 4, 'Rubrica telefonica '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}
}

function endPage(){
	global $lettera, $prevLettera, $currLettera, $riga, $html, $pdf;
	

	$border = ' style="border: 0.5px solid black;" ';
	$col1 =   ' style="width: 370px;" ';
	$col2 =   ' style="width: 180px;" ';
	$col3 =   ' style="width: 150px;" ';
	
	$html.='</table>';	
	$pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
	$pdf->writeHTMLCell($w=0, $h=0, $x='5', $y='5', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);			

	$pdf->RoundedRect(190, -18, 40, 30, 3.50, '1111', 'DF', array('width' => 0.2, 'color' => array(0, 0, 0)), array(355,355,355));
	$pdf->SetFont('helvetica', 'B', 20);
	$pdf->writeHTMLCell($w=0, $h=0, $x=190, $y=0, $lettera, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);				
}

function newPage(){
	global $lettera, $prevLettera, $currLettera, $riga, $html, $pdf;

	$border = ' style="border: 0.5px solid black;" ';
	$col1 =   ' style="width: 370px;" ';
	$col2 =   ' style="width: 180px;" ';
	$col3 =   ' style="width: 150px;" ';

	$riga=0;
	$lettera=$currLettera;
	$prevLettera = $currLettera;

	$html  ="<table border=\"1\" cellpadding=\"1\" cellspacing=\"0\">";
	$html .="<tr>";
	$html .="<td $border $col1><b><i>Nominativo</i></b></td>";
	$html .="<td $border  $col2><b><i>Note</i></b></td>";
	$html .="<td $border  $col3><b><i>Num.Tel</i></b></td>";
	$html .="</tr>";
	$pdf->AddPage();
}
//**********************************************************
function generaRubrica(){
	global $lettera, $prevLettera, $currLettera, $riga, $html, $pdf;
	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('La Favorita di Brun G. & G. Srl Unip.');
	$pdf->SetTitle('Rubrica');

	// remove default footer
	$pdf->setPrintFooter(false);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	@$pdf->setLanguageArray($l);


	
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
	$righe=$easy_ods_read->extract_data("0","2000");

	//**
	//**********************************************
	//rimuovi la prima riga con le intestazioni e la memorizza in una nuova variabile
	$intestazione = array_shift($righe);
/*
	//riordino le righe in ordine alfabetico
	usort($righe, function($a, $b) {
	//	print_r($a);
		return $a['A'] - $b['A'];
	});	
*/
	//cerco duplicati
	searchDuplicatesNumbers($righe);

	$border = ' style="border: 0.5px solid black;" ';
	$col1 =   ' style="width: 370px;" ';
	$col2 =   ' style="width: 180px; font-size:20px;" ';
	$col3 =   ' style="width: 150px;" ';

	newPage();

	foreach ($righe as $key => $value) {
		//SE è UN'EMAIL O UN SITO INTERNET PASSO AL PROSSIMO
		if($value['C']=='MAIL'){ continue;}
		if($value['C']=='PEC'){ continue;}
		if($value['C']=='WEB'){ continue;}
		
		//GESTIONE LETTERE DELLE PAGINE
		$currLettera = $value['A'][0];
		if($currLettera==$prevLettera && $riga < 42){
			$riga++;
			//nothing else to do;
		}else{
			endPage();
			newPage();
		}
		

		
		@$html .= '<tr>';
		@$html .= "<td $border $col1>".$value['A'].'</td>';
		@$html .= "<td $border $col2>".$value['B'].'</td>';
		@$html .= "<td $border $col3>";
		if(@$value['C']==''){$value['C']='null';}
		@$html .= '<img src="./img/'.$value['C'].'.svg" alt="img" width="18" height="18" border="0" />';
		@$html .= '<img src="./img/spacer.svg" alt="img" width="12" border="0" />';
		@$html .= $value['D'].'</td>';
		@$html .='</tr>';
	}
	endPage();
	
	//salvo il file
	@$pdf->Output("./rubrica.pdf", 'F');
	//e ne invio una copia al browser per visualizzarlo
	//@$pdf->Output('Rubrica.pdf', 'I');

}
generaRubrica();


function searchDuplicatesNumbers($myArray){
	$result = Array();
	$duplicates = Array();
	foreach ($myArray as $key => $value) {
		if(!array_key_exists('C',$value)){$value['C']='';}
		if(!array_key_exists('D',$value)){$value['D']='';}
		
		if (array_key_exists($value['D'].$value['C'], $result)){
			array_push($result[$value['D'].$value['C']], $value);

			$duplicates[$value['D'].$value['C']] = $result[$value['D'].$value['C']]; 

		}else{
			$result[$value['D'].$value['C']] = Array();
			array_push($result[$value['D'].$value['C']], $value);
		}
	}
	echo '<pre>';
	print_r($duplicates);
	echo '</pre>';
}
?>