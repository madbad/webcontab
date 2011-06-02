<?php
/* -------------------------------------------------------------------------------------------------------
Questo script prepara la stampa relativa 
alla programmazione delle manutenzioni


----------------------------------------------------------------------------------------------------------
*/

//----------------------------------------------------
//   Inizializzo la libreria per creare i pdf
//

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
		
		$GLOBALS['img_file']='./moduli_certificazione/mod.manutenzioni.jpg';		
	
		$this->Image($GLOBALS['img_file'], 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
	}
}


//----------------------------------------------------
//   qui pensiamo alla nostra pagina vera e propria
//

switch($_GET['mode']){
	case 'list':
		$files=getXmlFilesFromDir('./xml2');
		$files=riordinaFiles($files);

		 include 'default.tpl.php';

		echo '<br><table class="elenco" cellspacing="0" style="border:1px solid #000000"><thead><td>LINEA</td><td>N.ASSEGNATO</td><td>LOCALIZZAZIONE</td><td>MACCHINARIO</td></thead>';
		for($i = 0; $i < sizeof($files); ++$i)
		{
			$xml = simplexml_load_file($files[$i]);
			echo '<tr><td></td><td>'.$xml->id.'</td><td>'.$xml->localizzazione.'</td><td>'.$xml->nome. ' || marca: '.$xml->marca.' || matricola: '.$xml->matricola.'</td></tr>';
		}
		echo '</table></body>';
	break;

	case 'calendario':
		// create new PDF document
		$pdf = new MYPDF('LANDSCAPE', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		creaCalendario($pdf);
	break;

	default:
	   include 'default.tpl.php';
		echo '<h1>Manutenzioni attrezzature</h1>Seleziona una modalita:<ul><li><a href="?mode=list">Lista attrezzature</a></li><li><a href="?mode=calendario">Manutenzioni programmate</a></li>';
	break;
}

 error_reporting(0);
 

 
//----------------------------------------------------
//   funzioni vaire
//
//----------------------------------------------------
 function getXmlFilesFromDir($myDir){
	$files='';
	$dir = opendir ($myDir);
   while (false !== ($file = readdir($dir))) {
   	if (strpos($file, '.xml',1)) {
         $files[]=$myDir.'/'.$file;
      }
   }  	
	return $files; 
}
 
//---------------------------------------------------- 
function riordinaFiles($arrayFiles){
	$newArray=array();
	foreach ($arrayFiles as $file){
		$xml = simplexml_load_file($file);
		$index=$xml->id*1 - 1;
		$newArray[$index]=$file;
	}
	return $newArray;
} 

//----------------------------------------------------
function creaCalendario($pdf){
	$GLOBALS['anno']=2011;
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('La Favorita');
$pdf->SetTitle('Manutenzioni');
$pdf->SetSubject('Manutenzioni');
$pdf->SetKeywords('Manutenzioni');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetheaderMargin(0);
$pdf->SetFooterMargin(0);

// remove default footer
$pdf->setPrintFooter(false);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$l='IT';
//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------
$files=getXmlFilesFromDir('./xml2');
$files=riordinaFiles($files);

for($i = 0; $i < sizeof($files); ++$i){
		$file=$files[$i];
		$xml = simplexml_load_file($file);
		if(count($xml->manutenzioni_programmate)>0){

		//echo $manut
		$pdf->AddPage();

		$html = '<p fill="true" style="font-size:8pt;">'.$file.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='10', $y='10', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		//Nome
		$html = '<p fill="true" style="font-size:8pt;">'.$xml->nome.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y='20', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		//Marca
		$html = '<p fill="true" style="font-size:8pt;">'.$xml->marca.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y='23', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		//Modello
		$html = '<p fill="true" style="font-size:8pt;">'.$xml->modello.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y='26', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		//numero assegnato
		$html = '<p fill="true" style="font-size:8pt;">'.$xml->id.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y='29', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		
		//localizzazione
		$html = '<p fill="true" style="font-size:8pt;">'.$xml->localizzazione.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y='32', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		
		$righe=0;
		$mesi=Array( 'gennaio'=>1,
				'febbraio'=>2,
				'marzo'=>3,
				'aprile'=>4,
				'maggio'=>5,
				'giugno'=>6,
				'luglio'=>7,
				'agosto'=>8,
				'settembre'=>9,
				'ottobre'=>10,
				'novembre'=>11,
				'dicembre'=>12);
				
				//stampa solo file con manutenzioni
				//echo count($xml->manutenzioni_programmate).' '.$file.' <br>';
				
				
		foreach ($xml->manutenzioni_programmate as $manut){
				foreach ($xml->manutenzioni_programmate->manutenzione as $manut1){
					$manutenzione=$manut1;
					$modificatore=$righe*3.15;
					//oggetto
 					$html = '<p fill="true" style="font-size:6pt;">'.$manutenzione->oggetto.'</p>';
					$pdf->writeHTMLCell($w=0, $h=0, $x='7', $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

					//attivita
					$attivita=str_split($manutenzione->attivita, 37);
					foreach ($attivita as $attivitaTxt){
	 					$html = '<p fill="true" style="font-size:6pt;">'.$attivitaTxt.'</p>';
						$pdf->writeHTMLCell($w=0, $h=0, $x='37', $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
						$modificatore=$modificatore+3.15;
						$righe++;
					}
					$modificatore=$modificatore-3.15;
	
					//periodicita
 					$html = '<p fill="true" style="font-size:8pt;">'.strtolower($manutenzione->periodicita).'</p>';
					$pdf->writeHTMLCell($w=0, $h=0, $x='74', $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

					//segno sul calendario
					$test=explode(' ',$manutenzione->data);//il primo è il mese il secondo è l'anno

					//if($test[1]==2010 && $test[1]!='null'){
						$html = '<p fill="true" style="font-size:8pt;">X</p>';
							//$manutenzione->data;
							//fattore 3.75 per ogni casella
							//valore di base=108
							//(più ho aggiunto io una settimana in modo che scadano la seconda settimana del mese

						
					//}
					global $pdf, $modificatore, $true, $mese;
					switch(strtolower($manutenzione->periodicita)){
						
						
						case 'trimestrale':

							$meseNumerico=$mesi[strtolower($test[0])];

							$posx=108+(($meseNumerico-1)*4+1)*3.75;						
							$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

							$meseNumerico=$meseNumerico+3;
							$posx=108+(($meseNumerico-1)*4+1)*3.75;						
							$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

							$meseNumerico=$meseNumerico+3;
							$posx=108+(($meseNumerico-1)*4+1)*3.75;						
							$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

							$meseNumerico=$meseNumerico+3;
							$posx=108+(($meseNumerico-1)*4+1)*3.75;						
							$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
						break;
						
						case 'annuale':
						$posx=108+(($mesi[strtolower($test[0])]-1)*4+1)*3.75;
						$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
						break;

						case 'biennale':
						if($test[1]==$GLOBALS['anno'] || ((($GLOBALS['anno']-$test[1])%2)==0 && $GLOBALS['anno']>=$test[1])){
						$posx=108+(($mesi[strtolower($test[0])]-1)*4+1)*3.75;
						$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
							}else{
						$resto=floor(($GLOBALS['anno']-$test[1])/2);
						$proxScadenza=$test[1]+2*($resto+1);
 						$html = '<p fill="true" style="font-size:8pt;">sc.'.$proxScadenza.'</p>';
						$pdf->writeHTMLCell($w=0, $h=0, $x=90, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
								
						}
						break;

						case 'triennale':
						if($test[1]==$GLOBALS['anno'] || ((($GLOBALS['anno']-$test[1])%3)==0 && $GLOBALS['anno']>=$test[1])){

						$posx=108+(($mesi[strtolower($test[0])]-1)*4+1)*3.75;
						$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
							}else{
						$resto=floor(($GLOBALS['anno']-$test[1])/3);
						$proxScadenza=$test[1]+3*($resto+1);
 						$html = '<p fill="true" style="font-size:8pt;">sc.'.$proxScadenza.'</p>';
						$pdf->writeHTMLCell($w=0, $h=0, $x=90, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
								
						}
						break;

						case 'semestrale':
							$meseNumerico=$mesi[strtolower($test[0])];

							$posx=108+(($meseNumerico-1)*4+1)*3.75;						
							$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

							$meseNumerico=$meseNumerico+6;
							$posx=108+(($meseNumerico-1)*4+1)*3.75;						
							$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

						break;
						
						
						case 'quadriennale':
						if($test[1]==$GLOBALS['anno'] || ((($GLOBALS['anno']-$test[1])%4)==0 && $GLOBALS['anno']>=$test[1])){

						$posx=108+(($mesi[strtolower($test[0])]-1)*4+1)*3.75;
						$pdf->writeHTMLCell($w=0, $h=0, $x=$posx, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
							}else{
						$resto=floor(($GLOBALS['anno']-$test[1])/4);
						$proxScadenza=$test[1]+4*($resto+1);
 						$html = '<p fill="true" style="font-size:8pt;">sc.'.$proxScadenza.'</p>';
						$pdf->writeHTMLCell($w=0, $h=0, $x=90, $y=48+$modificatore, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
								
						}
						break;
						
						
						case '':
						break;


						default:
						//tutti gli altri casi non sono stati riconosciuti
						echo '<br>periodicita non riconosciuta: '.$file;
						break;
					}
				}
			}	
		}
		}


//Close and output PDF document
$pdf->Output('manutenzioni2010.pdf', 'I');
}
?>