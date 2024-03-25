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
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<link rel="stylesheet" type="text/css" href="style.css" media="print">
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
		.movimentoEntrata{
			background-color:#b0ffc5;//green
		}
		.movimentoUscita{
			background-color:#ffb4b0;//red
		}
		.movimentoSaldo{
			background-color:#b0f4ff;//blue
		}
		</style>
    </head>
     <body>

<div id="selettoreDati" class="hideOnPrint">
<form action="./imballaggi.php" class="dateform" method="post"> 
	<span class="dateformtitle">Selezione parametri</span>
	<br> <span class="dateselectordescription" style="width:5em;display:inline-block;">From:</span>
	<input class="dateselector" type="date" name="inizio" value="<?php echo $inizio ?>">
	<br> <span class="dateselectordescription" style="width:5em;display:inline-block;">to:</span>
	<input class="dateselector" type="date" name="fine" value="<?php echo $fine ?>">
	<br><input list="fornitori" type="text" name="fornitore" value="<?php echo $codiceFornitore ?>" autofocus>
	<?PHP
		/*---------------------------------------------------*/
		//suggerimento fornitori per l'input
/*
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
*/
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

$saldiImballi = array();


function estraiDati(){
	global $inizio;
	global $fine;
	global $codiceFornitore;
	global $saldiImballi;
	
	$datainiziale = str_replace('-','',$inizio);
	$datafinale = str_replace('-','',$fine);
	$nomefoglio = 2020;
	$fornitore = $codiceFornitore;
	
	$out=array();
		
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

	/*filtra per data e fornitore*/
	$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="'.$nomefoglio.'"]/table:table-row[./table:table-cell[translate(@office:date-value, "-", "") >= '.$datainiziale.'] and ./table:table-cell[translate(@office:date-value, "-", "") <= '.$datafinale.'] and ./table:table-cell/text:p[contains(.,\''.$fornitore.'\')] ]';
	$righe = $xpath->query($query);

	/*todo ordina l'array per data*/

	$class='';


	//somma per articolo
	foreach ($righe as $riga) {
		switch($riga->childNodes->item($ARTICOLO)->nodeValue){
			case 'IMBALLAGGI SALDO':
				//rimpiazza il saldo alla data corrente
				$saldiImballi[$riga->childNodes->item($TIPOIMB)->nodeValue] = 0;
				$class='movimentoSaldo';
				break;
			case 'IMBALLAGGI NS ENTRATA':
				//assicurati che sia a segno positivo>
				$riga->childNodes->item($COLLI)->nodeValue = +1 *abs($riga->childNodes->item($COLLI)->nodeValue);
				$class='movimentoEntrata';
				break;
			case 'IMBALLAGGI NS USCITA':
				//assicurati che sia a segno negativo
				$riga->childNodes->item($COLLI)->nodeValue = -1 * abs($riga->childNodes->item($COLLI)->nodeValue);
				$class='movimentoUscita';
				break;
			default:
				//è un ddt di entrata segno positivo
				//assicurati che sia a segno positivo>
				$riga->childNodes->item($COLLI)->nodeValue = +1 *abs($riga->childNodes->item($COLLI)->nodeValue);
				$class='movimentoEntrata';
				break;
		}
		
		if(!array_key_exists($riga->childNodes->item($TIPOIMB)->nodeValue, $saldiImballi)){
			$saldiImballi[$riga->childNodes->item($TIPOIMB)->nodeValue] = 0;
		}
		//aggiorno il saldo imballi
		$saldiImballi[$riga->childNodes->item($TIPOIMB)->nodeValue] += $riga->childNodes->item($COLLI)->nodeValue;
		
		
		//fix the date
		
		echo '<tr class="'.$class.'">';
		echo '<td>'.$riga->childNodes->item($DOC)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($NUMERO)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($DATA)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($FORNITORE)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($ARTICOLO)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($COLLI)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($TIPOIMB)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($NOTE)->nodeValue.'</td>';
		echo '<td>'.$riga->childNodes->item($TRASPORTO)->nodeValue.'</td>';
		echo '</tr>';
	}
	return;
}
echo '<table class="spacedTable borderTable">';
//estrapolo i dati Ddt dal file ODS
$movimenti = estraiDati();
echo '</table>';
echo '<pre>';
print_r($saldiImballi);
echo '</pre>';
?>
