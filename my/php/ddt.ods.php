<?php
function isWindows(){
	if ( strtoupper(PHP_OS) == 'LINUX' ){
		//echo '<br>This is a server using Linux!';
		return false;
	}else{
		//echo '<br>This is a server using Windows!';
		return true;
	}
}
function extractODS($fileUrl, $destination){
	//copio il file in modo da non avere problemi se è in uso da altri
	//source, target
	$fileCopyUrl = $destination.'/'.basename($fileUrl);

	//echo '<br>Original File here:'.$fileUrl;
	//echo '<br>File copy will be here :'.$fileCopyUrl;
	copy($fileUrl, $fileCopyUrl);
	
	
	if (isWindows()){
		$command = 'C:\Programmi\EasyPHP-5.3.9\www\webContab\my\php\utils\7za.exe x "'.$fileCopyUrl.'" -o"'.$destination.'"';
	}else{//linux
		$command = 'unzip -o '.$fileCopyUrl.' -d '.$destination;
	}
	//echo '<br>'.$command;
	shell_exec($command);
}

/*-------------------------------------------------------------------------------------------------------------*/

set_time_limit (0); //0=nessun limite di tempo
error_reporting(-1); //0=spento || -1=acceso
include "./anagrafiche.inc.php"; 
$fornitori = $anagrafiche;
$fornitori = explode("----",$fornitori);
//print_r($fornitori);
$dbFornitori = array();
foreach ($fornitori as $key => $fornitore) {
	$temp = explode("\n",$fornitore);
	//echo '--';
	//print_r($fornitore);
	//echo $fornitore[0].'<<';
	//print_r($fornitore[0]);
	$code = trim($temp[1]);
	@$dbFornitori[$code]['codice'] = $temp[1];
	@$dbFornitori[$code]['ragsoc'] = $temp[2];
	@$dbFornitori[$code]['via'] = $temp[3];
	@$dbFornitori[$code]['citta'] = $temp[4];
	@$dbFornitori[$code]['piva'] = $temp[5];
	@$dbFornitori[$code]['codfisc'] = $temp[6];
}

?>


<?php
if (array_key_exists("inizio", $_POST)){
	//echo 'ce li ho ';
	$inizio=$_POST["inizio"];
	$fine=$_POST["fine"];
	$codiceFornitore = $_POST["fornitore"];
}else{
	//echo 'mi mancano ';
	$anno = date("Y");
	$mese = date("n")-1;

	if($mese == 00){
		$anno = date("Y")*1-1;
		$mese = 12;
		
	}
	$giorni = cal_days_in_month(CAL_GREGORIAN, $mese, $anno); // 31

	if ($mese < 10){$mese ='0'.$mese;}
	date("Y");
	$inizio=$anno.'-'.$mese.'-01';
	$fine=$anno.'-'.$mese.'-'.$giorni;
	$codiceFornitore = "";
}
?>
<!DOCTYPE HTML>
<html lang="IT">
    <head>
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
        <!--
		<link rel="stylesheet" type="text/css" href="style.css">
		-->

		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		#selettoreDati{
			padding:1em;
			position:absolute; 
			top:0px; 
			left:0px; 
			z-index:100;
			opacity:0.1;
			background-color:green;
			font-size:1.5em;
		}
		#selettoreDati:hover{
			opacity:0.9
		}
		#selettoreDati input {
			font-size:1.0em;
		}
		</style>
    </head>
     <body>

<div id="selettoreDati" class="hideOnPrint">
<form action="./ddt.ods.php" class="dateform" method="post"> 
	<span class="dateformtitle">Selezione parametri</span>
	<br> <span class="dateselectordescription" style="width:5em;display:inline-block;">From:</span>
	<input class="dateselector" type="date" name="inizio" value="<?php echo $inizio ?>">
	<br> <span class="dateselectordescription" style="width:5em;display:inline-block;">to:</span>
	<input class="dateselector" type="date" name="fine" value="<?php echo $fine ?>">
	<br><input list="fornitori" type="text" name="fornitore" value="<?php echo $codiceFornitore ?>" autofocus>
	<?PHP
		/*---------------------------------------------------*/
		//suggerimento fornitori per l'input

		echo '<datalist id="fornitori">';
		foreach ($fornitori as $key => $fornitore) {
			$temp = explode("\n",$fornitore);
			//echo '--';
			//print_r($fornitore);
			//echo $fornitore[0].'<<';
			//print_r($fornitore[0]);
			$code = trim($temp[1]);
			echo   "\n".'<option value="'.$temp[1].'">';
		}
		echo '</datalist>';
		/*---------------------------------------------------*/

	?>
	<BR><input type="submit" value="Submit" style="padding:1em;width:20em;">
</form>
</div>

<?php
function importNumber($number){
	$number = str_replace('.', '',$number);
	$number = str_replace(',', '.',$number);
	return $number;
}

function exportNumber($number,$precision=2){
	return number_format($number,$precision,',','.');
}

$totali= array();

function estraiDatiDdt(){
	//criteri di filtraggio
	/*
	$datainiziale = 20230901;
	$datafinale = 20230930;
	$nomefoglio = 2020;
	$fornitore ='KULDIP';
	*/
	global $inizio;
	global $fine;
	global $codiceFornitore;
	global $dbFornitori;
	
	$datainiziale = str_replace('-','',$inizio);
	$datafinale = str_replace('-','',$fine);
	$nomefoglio = 2020;
	$fornitore = $codiceFornitore;
		
	//output
	$elencoDdt = array();
	$ddt = new stdClass();
	$ddt->righe = array();
	$ddt->imponibile = 0;

	//estraggo il file ods
	$dataFile = "C:/Documenti/Favorita/Fornitori/vari non abituali - riscontri.ods";
	$tempExtractionDir='C:/Programmi/EasyPHP-5.3.9/www/webcontab/my/php/tmp/ods/'.time();
	//echo '<br>Making dir:'.$tempExtractionDir;
	mkdir($tempExtractionDir.'/');
	//$dataFile = "./vari non abituali - riscontri.ods";
	extractODS($dataFile, $tempExtractionDir);

	//estrapolo i dati Ddt
	$doc = new DOMDocument;
	// We don't want to bother with white spaces
	$doc->preserveWhiteSpace = false;
	$doc->load($tempExtractionDir.'/content.xml');
	$xpath = new DOMXPath($doc);

	//intestazione colonne
	$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="'.$nomefoglio.'"]/table:table-row[1]';
	$rigaIntestazione = $xpath->query($query);
	$contaColonne = 0;
	$intestazioniColonne = Array();
	foreach ($rigaIntestazione->item(0)->childNodes as $cella) {
		$colonnaNumero = $contaColonne;
		$colonnaNome = $cella->nodeValue;
		$contaColonne++;
		
		$intestazioniColonne[$colonnaNome] = $colonnaNumero;
		global $$colonnaNome;
		$$colonnaNome = $colonnaNumero;
	}
	//print_r($intestazioniColonne);

	//how to for xpath queryes
	//https://docs.oracle.com/communications/E79201_01/doc.735/e79208/dev_xpath_functions.htm#OSMDR780
	//               yyyymmdd

	//il foglio
	//$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="2023"]';
	//le righe del foglio
	//$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="2023"]/table:table-row';

	//le righe filtrate per sola datadel foglio comprese in data                                   //foglio                                                                                       //data iniziale                                                                     //data finale
	//$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="'.$nomefoglio.'"]/table:table-row[./table:table-cell[translate(@office:date-value, "-", "") >= '.$datainiziale.'] and ./table:table-cell[translate(@office:date-value, "-", "") <= '.$datafinale.']]';

	/*filtra per data e fornitore*/
	$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="'.$nomefoglio.'"]/table:table-row[./table:table-cell[translate(@office:date-value, "-", "") >= '.$datainiziale.'] and ./table:table-cell[translate(@office:date-value, "-", "") <= '.$datafinale.'] and ./table:table-cell/text:p[contains(.,\''.$fornitore.'\')] ]';
	$righe = $xpath->query($query);

	//somma per articolo
	foreach ($righe as $riga) {

		$suddivisoreDDT = $riga->childNodes->item($NUMERO)->nodeValue.' - '.$riga->childNodes->item($DATA)->nodeValue;	
		if (!array_key_exists($suddivisoreDDT, $elencoDdt)){
			//se non esiste creo il ddt
			$elencoDdt[$suddivisoreDDT] = new stdClass();
			$myDDT = $elencoDdt[$suddivisoreDDT];
			$myDDT->righe = array();
			$myDDT->numero = $riga->childNodes->item($NUMERO)->nodeValue;
			$myDDT->data = $riga->childNodes->item($DATA)->nodeValue;
			$myDDT->codFornitore = $riga->childNodes->item($FORNITORE)->nodeValue;
			//$myDDT->fornitore = $dbFornitori[$myDDT->codFornitore];
			$myDDT->totColli = 0;
			$myDDT->totPeso = 0;	
		}else{
			$myDDT = $elencoDdt[$suddivisoreDDT];
		}

		//inserisco la riga nel ddt esistente
		$nuovaRiga=count ($myDDT->righe);
		$myDDT->righe[$nuovaRiga]['colli']= $riga->childNodes->item($COLLI)->nodeValue;
		$myDDT->righe[$nuovaRiga]['articolo'] = $riga->childNodes->item($ARTICOLO)->nodeValue;
		$myDDT->righe[$nuovaRiga]['pesoLordo'] = round(importNumber($riga->childNodes->item($PESO)->nodeValue)*1 + $riga->childNodes->item($COLLI)->nodeValue *30);
		$myDDT->righe[$nuovaRiga]['pesoNetto'] = importNumber($riga->childNodes->item($PESO)->nodeValue)*1;
		$myDDT->righe[$nuovaRiga]['prezzo'] =  importNumber($riga->childNodes->item($PREZZO)->nodeValue);
		$myDDT->righe[$nuovaRiga]['um'] =  'KG';
		
		$myDDT->totColli += $myDDT->righe[$nuovaRiga]['colli'];
		$myDDT->totPeso += $myDDT->righe[$nuovaRiga]['pesoLordo'];
	}
	return $elencoDdt;
}

//estrapolo i dati Ddt dal file ODS
$elencoDdt = estraiDatiDdt();

//arrotondamenti dei totali
//$Ddt->imponibile = round($Ddt->imponibile,2);
//$Ddt->iva = round($Ddt->imponibile *4/100,2);
//$Ddt->totale = $Ddt->iva+ $Ddt->imponibile;

//imposto numero e data se necessario
//$Ddt->numero ='';
//$Ddt->data ='';

//dati del fornitore
$fornitore = $dbFornitori[$codiceFornitore];
//print_r($elencoDdt);
//print_r($Ddt);
//procedo a creare e mostrare la Ddt
include "./ddt.html.php";
?>
