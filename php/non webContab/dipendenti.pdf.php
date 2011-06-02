<?php
/* -------------------------------------------------------------------------------------------------------
Questo script prepara alcune stampe necessarie per la certificazione relative al personale
- scheda di assunzione
- questionario sulla salute
----------------------------------------------------------------------------------------------------------
*/

//$MODE può essere:
//1=FORMAZIONE DEL PERSONALE';
//2=ASSUNZIONE DEL PERSONALE
//3=QUESTIONARIO SULLA SALUTE
$mode=2;

/*
switch($_GET['mode']){
	case 'list':
		$files=getXmlFilesFromDir('./xml');
		$files=riordinaFiles($files);
		 include 'default.tpl.php';
		echo '<h1>Elenco attrezzature</h1>';

		echo '<table cellspacing="0" style="border:1px solid #000000"><thead><td>Id</td><td>Nome</td><td>marca</td><td>Matricola</td><td>Localizzazione</td></thead>';
		for($i = 0; $i < sizeof($files); ++$i)
		{
			$xml = simplexml_load_file($files[$i]);
			echo '<tr><td>'.$xml->id.'</td><td>'.$xml->nome.'</td><td>'.$xml->marca.'</td><td>'.$xml->matricola.'</td><td>'.$xml->localizzazione.'</td></tr>';
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
*/



 function isInArray($val, $array)
{
	foreach ($array as $key => $arrVal) {
		if($val==$arrVal) return true;
	}
 return false;
}
 class Dipendente
 {
	function __construct($id, $nome, $cognome, $dnascita, $lnascita, $via, $civico, $paese, $provincia, $qualifica, $mansione, $assunzione, $dipendente, $cooperativa, $sostituisceChi) {
   	$this->id=$id;
   	$this->nome=$nome;
   	$this->cognome=$cognome;
   	$this->dnascita=$dnascita;
   	$this->lnascita=$lnascita;
   	$this->via=$via;
   	$this->civico=$civico;
   	$this->paese=$paese;
   	$this->provincia=$provincia;
   	$this->qualifica=$qualifica;
   	$this->mansione=$mansione;
   	$this->assunzione=$assunzione;
   	$this->dipendente=$dipendente;
   	$this->cooperativa=$cooperativa;
   	$this->sostituisceChi=$sostituisceChi;
	}	
	public function getName() {
		return $this->cognome.' '.$this->nome;
	}
	public function getIndirizzo() {
		return $this->via.' '.$this->paese.' '.$this->provincia;
	}
	public function getCorsi($corsi) {
		$out=array();
		foreach ($corsi as $key => $val) {
			$corso=$corsi[$key];
			$test= explode(',',$corso->idPartecipanti);
			if(isInArray($this->id,$test))
			 {
				$out[]=$corso;
			}
		}
		return $out;
	}
}
  class Corso{
	function __construct($data, $ente, $durata, $validita, $scadenza, $idPartecipanti,$descrizione) {
   	$this->data=$data;
   	$this->ente=$ente;
   	$this->durata=$durata;
   	$this->validita=$validita;
   	$this->scadenza=$scadenza;
   	$this->descrizione=$descrizione;
	$this->idPartecipanti=$idPartecipanti;
 }
}	
//function __construct(      $id,   $nome,   $cognome, $dnascita, $lnascita,           $via,        $civico, $paese,          $provincia, $qualifica, $mansione, $assunzione, $dipendente, $cooperativa, $sostituisceChi) {
$dipendenti[]=new Dipendente( '1', 'Giorgio', 'Brun', '20/08/54', 'Isola della Scala', 'Via San Rocco', '14', 'Isola della Scala', 'VR', 'socio lavoratore', 'resp.ordini e produzione', '21/06/83', 0,1,''	);		
$dipendenti[]=new Dipendente( '2', 'Gionni', 'Brun', '03/09/82', 'Isola della Scala', 'Via San Rocco', '14', 'Isola della Scala', 'VR', 'amministratore', 'dirigente', '15/12/01', 0,1, 'Zambotto Giuseppe + Brun Debora'	);		
$dipendenti[]=new Dipendente( '3', 'Igor', 'Ciumacenco', '03/10/74', 'Moldavia', 'Corso Milano', '71', 'Verona', 'VR', 'autista', 'Autista', '09/08/04', 1,0, 'Brun Gimmi'	);		
$dipendenti[]=new Dipendente( '4', 'Giuseppe', 'Zambotto', '11/08/56', 'Trevenzuolo', 'Via Roma', '', 'Isola della Scala', 'VR', 'impiegato', 'Agronomo', '12/02/08', 1,0,''	);		
$dipendenti[]=new Dipendente( '5', 'Debora', 'Brum', '13/02/79', '', 'Via Abetone', '', 'Isola della Scala', 'VR', 'impiegata', 'Impiegata', '31/12/08', 1,0, 'Brun Gionni'	);		
$dipendenti[]=new Dipendente( '6', 'Marisa', 'Brun', '25/10/65', 'Isola della Scala', 'Via Doltra', '43/D', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '04/03/08', 1,0	, 'Brun Lisa');		
$dipendenti[]=new Dipendente( '7', 'Gimmi', 'Brun', '01/04/79', 'Isola della Scala', 'Via San Rocco', '14', 'Isola della Scala', 'VR', 'autista', 'Autista', '02/04/10', 1,0,''	);		
$dipendenti[]=new Dipendente( '8', 'Genni', 'Brun', '08/07/80', 'Isola della Scala', 'Via San Rocco', '14/A', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '01/06/09', 1,0,''	);		
$dipendenti[]=new Dipendente( '9', 'Lisa', 'Brun', '20/04/86', 'Isola della Scala', 'Via San Rocco', '14', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '01/06/09', 1,0,'Brun Tiziana');		
$dipendenti[]=new Dipendente('10', 'Ioana Maria', 'Trif', '15/09/84', 'Romania', 'Via Motta', '12', 'Nogara', 'VR', 'cernitrice', 'Cernita verdura', '26/10/04', 1,0,''	);		
$dipendenti[]=new Dipendente('11', 'Tiziana', 'Brun', '12/07/64', 'Isola della Scala', 'Via Doltra', '3', 'Verona', 'VR', 'cernitrice', 'Cernita verdura', '06/10/09', 1,0,''	);		
$dipendenti[]=new Dipendente('12', 'Franco', 'Formigari', '02/09/54', 'Isola della Scala', 'Via Bovo', '24', 'Verona', 'VR', 'collaboratore', 'Commerciale', '01/04/10', 1,0,''	);		
$dipendenti[]=new Dipendente('13', 'Simonetta', 'Calmero', '24/12/66', 'Isola della Scala', 'Via Gen. Dalla Chiesa', '2', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('14', 'Rodica', 'Sandu', '18/05/75', 'Romania', 'Via Abetone', '5/A', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('15', 'Imane', 'Badoui', '18/02/88', 'Marocco', 'Via Forno Bianco', '21', 'Gazzo Veronese', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('16', 'Elkebira', 'Ettaoufi', '28/09/57', 'Marocco', 'Via Forno Bianco', '21', 'Gazzo Veronese', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('17', 'Fatima', 'El Haouzy', '01/01/66', 'Marocco', 'Via Trieste', '35', 'Bovolone', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('18', 'Najia', 'Ikiker', '02/08/64', 'Marocco', 'Via Ghisiolo', '11', 'San Giorgio di Mantova', 'MN', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('19', 'Samira', 'El Gorsi', '30/04/84', 'Marocco', 'Via Rangona', '7', 'Sanguinetto', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('20', 'Aicha', 'Taktour', '04/11/58', 'Marocco', 'Via Roma', '29', 'Trevenzuolo', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('21', 'Fatima', 'Taktour', '19/05/60', 'Marocco', 'Via B. Avesani', '15', 'Verona', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('22', 'Laura', 'Murari', '09/11/76', 'Isola della Scala', 'Via Sabbionara', '15/A', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('23', 'Rudy', 'Foggini', '31/08/69', 'Isola della Scala', 'Via Abetone', '47', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '31/10/08', 0,1,''	);		
$dipendenti[]=new Dipendente('24', 'Nadia', 'El Gorsi', '21/03/88', 'Marocco', 'Via Rangona', '7', 'Sanguinetto', 'VR', 'cernitrice', 'Cernita verdura', '02/04/10', 0,1,''	);		
$dipendenti[]=new Dipendente('25', 'Viorica', 'Miron', '13/02/69', 'Romania', 'Via XX Settembre', '27', 'Erbè', 'VR', 'cernitrice', 'Cernita verdura', '12/12/08', 0,1,''	);		
$dipendenti[]=new Dipendente('26', 'Hanan', 'Belkhadir', '18/04/83', 'Marocco', 'Via Sterzi', '9', 'Nogara', 'VR', 'cernitrice', 'Cernita verdura', '03/06/09', 0,1,''	);		
$dipendenti[]=new Dipendente('27', 'Anna Maria', 'Bucata', '12/08/83', 'Romania', 'Via Garribaldi Giuseppe', '7', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '16/03/10', 0,1,''	);		
$dipendenti[]=new Dipendente('28', 'Oneda', 'Nezha', '27/08/84', 'Albania', 'Via Monte Bianco', '3', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '07/04/10', 0,1,''	);		
$dipendenti[]=new Dipendente('29', 'Siham', 'Benslama', '22/08/88', 'Marocco', 'Via Luigi Verrimi', '4', 'Isola della Scala', 'VR', 'cernitrice', 'Cernita verdura', '27/04/10', 0,1,''	);		
$dipendenti[]=new Dipendente('30', 'Giampaolo', 'Brun', '23/07/68', 'Isola della Scala', 'Via Motta', '12', 'Nogara', 'VR', 'collaboratore', '-', '-', 0,0,''	);		

 
//nostri corsi
$corsi[]=new Corso('24/03/1999', 'Saute e Sicurezza Srl', '8 ore', '???', '???', '4,23', 'Corso teorico pratico di Antincendio ed Evacuazione per addetti alla Squadra di Emergenza');
$corsi[]=new Corso('gennaio 2002', 'TRIMEC', '-', '-', '-', '22', 'Conduzione ed uso del carrello elevatore in sicurezza');

$corsi[]=new Corso('27/11/2006', 'ULSS22', '-', '3 anni', '27/11/2009', '8,9,10', 'Corso per addetti alla manipolazione delle sostanze alimentari');
$corsi[]=new Corso('19-21-5-28/06/2007', 'Saute e Sicurezza Srl e Studio Medico dottori Caneva Ayyad', '16 ore', '???', '???', '2', 'Corso di informazione e formazione per il  Responsabile del Servizio di Prevenzione e Protezione');
$corsi[]=new Corso('13-15-20-22-06/2007', 'Saute e Sicurezza Srl e Studio Medico dottori Caneva Ayyad', '16 ore', '???', '???', '9', 'Corso teorico pratico di Primo Soccorso per addetti alla squadra di primo pntervento in azienda');
$corsi[]=new Corso('13/02/2009', 'La Favorita Srl', '2 ore', '-', '-', '1,2,3,4,5,6,7,8,9,10', 'I concetti generali della prevenzione e i DPI');
$corsi[]=new Corso('13/02/2009', 'Porto 626 - Andromeda Scarl', '2', '-', '-', '13,14,15,16,17,18,19,20,21,22,23,25', 'I concetti generali della prevenzine e i DPI');
$corsi[]=new Corso('3-12-19/06/2009 e 10/07/2009', 'Organismo paritetico provinciale - Artec Progetti Sas', '32 ore', '???', '???', '5', 'Corso di formazione "Rappreentante dei lavoratori per la Sicurezza - R.L.S.');
$corsi[]=new Corso('19/06/2009', 'Porto 626 - Andromeda Scarl', '2', '-', '-', '13,14,15,16,17,18,19,20,21,22,23,25,26', 'Procedure di lavoro e la movimentazion dei carichi');
$corsi[]=new Corso('19/06/2009', 'Porto 626 - Andromeda Scarl', '-', '-', '-', '22,23', 'Corso per addetti alluso del carrello elevatore');
$corsi[]=new Corso('21/12/2009', 'ULSS22', '-', '3 anni', '21/12/2012', '8,9,10', 'Corso per addetti alla manipolazione delle sostanze alimentari');
$corsi[]=new Corso('24/05/2010', 'ULSS22', '-', '3 anni', '24/05/2013', '6,11', 'Corso per addetti alla manipolazione delle sostanze alimentari');
//$data, $ente, $durata, $validita, $scadenza, $idPartecipanti,$descrizione)
$corsi[]=new Corso('27/06/2010', '4COMPANY - DONATELLA MOLON', '1,5 ore', '-', '-', '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29', 'Cenni standard IFS, Politica della qualita, Norme di comportamento piano delle pulizie');
$corsi[]=new Corso('27/06/2010', '4COMPANY - DONATELLA MOLON', '1 ora', '-', '-', '1,2,7,9', 'Manutenzioni e contaminazioni crociate');
$corsi[]=new Corso('27/06/2010', '4COMPANY - DONATELLA MOLON', '1 ora', '-', '-', '1,2,4,9', 'Buone pratiche di lavorazione ed introduzione ai criteri per la predisposizione del piano di autocontrollo');
 
 
 
 /*-----------------------------------------------------*/
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
		

switch($GLOBALS['mode']):
case 1:
	$GLOBALS['img_file']='./moduli_certificazione/formazione_personale2.jpg';		
	$GLOBALS['nomeFile']='modFormazioneDipendenti';
	break;
case 2:
	$GLOBALS['img_file']='./moduli_certificazione/assunzione_personale2.jpg';	
	$GLOBALS['nomeFile']='modAssunzioneDipendenti';	
	break;
case 3:
	$GLOBALS['img_file']='./moduli_certificazione/rientro_malattia2.jpg';	
	$GLOBALS['nomeFile']='modRientroMalattiaDipendenti';	
	break;

endswitch;
	
		$this->Image($GLOBALS['img_file'], 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('La Favorita');
$pdf->SetTitle('Corsi dipendenti');
$pdf->SetSubject('Corsi dipendenti');
$pdf->SetKeywords('Corsi dipendenti');

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
$nomeFile='';
foreach ($dipendenti as $key => $val) {
	$dipendente=$dipendenti[$key];

	// add a page
	$pdf->AddPage();

switch($mode):
case 1:
//-----------------------------------------------------------------------
//   MODELLO SCHEDA DEL PERSONALE R00
//-----------------------------------------------------------------------
	//CORSI
	$myCorsi=$dipendente->getCorsi($corsi);

	//nome e cognome
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->getName().'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='42', $y='19', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

	$i=0;
	foreach ($myCorsi as $key => $val) {
		$topDistance=($i*7.8)+65;
		$i++;

		//data
		$html = '<p fill="true" style="font-size:6pt;">'.$val->data.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='10', $y=$topDistance, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
		//descrizione
		$html = '<p fill="true" style="font-size:6.5;">'.$val->descrizione.'</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y=$topDistance, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

		//esito
		$html = '<p fill="true" style="font-size:12pt;">Esito positivo</p>';
		$pdf->writeHTMLCell($w=0, $h=0, $x='150', $y=$topDistance, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	}
	break;
case 2:
//-----------------------------------------------------------------------
//   MODELLO ASSUNZIONE DEL PERSONALE R00
//-----------------------------------------------------------------------
	//nome e cognome
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->getName().'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='42', $y='19', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//data di nascita
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->dnascita.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='42', $y='26', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//luogo di nascita
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->lnascita.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='132', $y='26', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//via
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->via.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='52', $y='33', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//civico
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->civico.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='177', $y='33', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//paese
	$html = '<p fill="true" style="font-size:14pt;">'.$dipendente->paese.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='142', $y='40', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//provincia
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->provincia.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='52', $y='40', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//qualifica
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->qualifica.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='52', $y='47', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//mansione
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->mansione.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='52', $y='61', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//esito positivo
	$html = '<p fill="true" style="font-size:18pt;">X</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='40', $y='160', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//periodo di affiancamento
	$html = '<p fill="true" style="font-size:16pt;">15 giorni</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='80', $y='147', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//sostituisce chi
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->sostituisceChi.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='52', $y='107', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);


	break;
//-----------------------------------------------------------------------
//   MODELLO QUESTIONARIO SULLA SALUTE DEI DIPENDENTI R00
//-----------------------------------------------------------------------
case 3:
	//nome e cognome
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->getName().'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='52', $y='55', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
	//nome e cognome
	$html = '<p fill="true" style="font-size:16pt;">'.$dipendente->assunzione.'</p>';
	$pdf->writeHTMLCell($w=0, $h=0, $x='57', $y='63', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

	break;
endswitch;

// ---------------------------------------------------------
	}
	//echo $GLOBALS['nomeFile'];
//Close and output PDF document
//$GLOBALS['nomeFile']
$nome=$GLOBALS['nomeFile'];
$pdf->Output($nome, 'I');

//============================================================+
// END OF FILE                                                
//============================================================+

?>