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

$fornitori = "
KULDIP
MASIH KULDIP
VIA LONGHENA N.25
37038 VERONA (VR)
04219630235
KSHKDP83R07Z222N
----
TUNG
TUNG ARIS
VIA TIZIANO N.1
37138 VERONA (VR)
04699510238
TNGRSA79M45Z222N
----
BUONA TERRA
BUONA TERRA DI SINGH HARJAN
VIA PIOMBAZZO
37060 BUTTAPIETRA (VR)
04802870230
SNGRJN73A26Z222K
----
GURU NANAK
GURU NANAK DI SINGH GURDEEP
VIA CARLO ALBERTO 19 I.4
37057 SAN GIOVANNI LUPATOTO (VR)
04734170238
SNGGDP89C07Z222Z
----
ASHA
AZ.AGR.RANI ASHA
VIA VITRUVIO N.9
37138 VERONA (VR)
04321800239
RNASHA82M42Z222M
----
PARMINDER
AZ.AGR.SINGH PARMINDER
VIA VALMALA N.8
37060 ERBE (VR)
11249540961
SNGPMN88T28Z222D
----
ZILOCCHI
AZ.AGR.ZILOCCHI DARIO E ALDERINO
VIA FOSSA CREAR N.23
37050 RALDON DI SAN GIOVANNI LUPATOTO (VR)
00948640230
00948640230
----
NICOLIS
AZ.AGR.NICOLIS DANIELE
VIA CA NOVA TORO 24A
37135 CADIDAVID (VR)
01407510237
NCLDNL52B07L781Y
----
ORTO DI KAUR
ORTO DI KAUR SARBJIT
VIA CANOVA N.4
37053 CEREA (VR)
03793950985
KRASBJ73E45Z222A
----
GURDEEP
AZ.AGR.GURU NANAK DI SINGH GURDEEP
VIA CARLO ALBERTO 19 I4
37057 SAN GIOVANNI LUPATOTO (VR)
04734170238
SNGGDP89C07Z222Z
----
SHANGARA
SINGH SHANGARA
VIA VALMARA N.8
37060 ERBE (VR)
04155560982
----
VEER
SINGH VEER



SNGURE85R132222U
----
VEER
SINGH VEER
VIA ROMA N.6
37063 ISOLA DELLA SCALA (VR)
04804160234
SNGVRE85R13Z222U
----
FACCINI
AZ.AGR.FACCINI GIANNI
VIA ISOLANA N722
37056 SALIZZOLE (VR)
02441080237
FCCGNN55L10F918Z
----
SANDEEP
AZ.AGR.SINGH SANDEEP
VIA PALAZZINA N.467
37056 SALIZZOLE (VR)
04722300235
SNGSDP92C02Z222U
----
SCHIAVO
AZ.AGR.DA GIULIO DI SCHIAVO PAOLO
VIA NOGAROLE N.3
37064 POVEGLIANO VERONESE (VR)
04830200236
SCHPLA81T04E349Z
----
HARBHINDER
SINGH HARBHINDER
VIA BRESCIANI N.40
46040 GAZOLDO DEGLI IPPOLITI (MN)
02582520207
SNGHBH68E19Z222P
----
ZOHRA
ZOHRA GHULAM
VIA BARABO' N.2
37054 NOGARA (VR)
04025140163
ZHRGLM76S667Z236I
----
MAAN
SINGH MAAN DARSHAN
VIA MEZZAVILLA N.21
37060 SORGA' (VR)
04612830234
SNGDSH61R10Z222V
----
NDBK
N.DBK LOGISTICA TRASPORTI SRLS
VIA VALENTINA ZAMBRA II
38121 TRENTO (TN)
02636420222
02636420222 
----
ROSSIGNOLI
AZ.AGR.ROSSIGNOLI OMAR
VIA ONI N.2
37060 ERBE' (VR)
02663070239
RSSMRO77E11F918W 
----
SIDHU
NS SIDHU AZ.AGR. DI SINGH NARINDER
VIA ABETONE N.12
37063 ISOLA DELLA SCALA (VR)
04605750233
SNGNND75E04Z222X 
----
AMNINDER
ANSH FARMS DI SINGH AMNINDER
VIA AZZANON.91/D
37064 POVEGLIANO VERONESE (VR)
02627590207
SNGMND94P13Z222S 
----
ZAPPOLA
AZ.AGR.ZAPPOLA MARCO E CLEMENTE
VIA COMUNI N.11
37046 MINERBE (VR)
00941860231
00941860231 
----
HARJIT
SINGH HARJIT
VIA ADRIA N.2 INT.9
37134 VERONA (VR)
04708930237
SNGHJT88A16Z222M 
----
MUHAMMAD
MUHAMMAD ZAKA ULLAH
VIA COLONNA 50
46032 CASTELBELFORTE (MN)
04920390236
ZKLMMM92T10Z236K
----
SCOLARI
AZ.AGR.SCOLARI MARIO E FRANCESCO E LUCA
VIA PINZON N.12
37057 RALDON DI S.GIOVANNI LUPATOTO (VR)
02734610237
02734610237
----
HARPAL
SHRI GURU RAM DAS SINGH HARPAL E SINGH SARBJEET S.S. SOC.AGR.
VIA MONZAMBANO 23
37100 VERONA (VR)
04292460237
04292460237
----
TIZIANI
AZ.AGR.TIZIANI SIMONE
CA' VECCHIA, 3
37060 ERBE' (VR)
02489660239
TZNSMN66A17E349C
----
BRUNO
SBIZZERA BRUNO
VIA BORIACO, 2
37060 GAZZO VERONESE (VR)
01307630234
SBZBRN58L06D957O 
----
MIGLIORINI
AZ.AGR.MIGLIORINI GIOVANNI
VIA CAPITELLO N.8
37060 FAGNANO DI TREVENZUOLO (VR)
02094090236
MGLGNN61H07L396D
----
TORRESANI
AZ.AGR.TORRESANI CESARINA
VIA CAPITELLO N.237
37056 SALIZZOLE (VR)
02741130237
TRRCRN62B44H714W
----
GIANELLO
GIANELLO GABRIELE
VIA SAN CARLO
37060 ERBE' (VR)
02336420233
GNLGRL59P27E349H
----
SUKHWINDER
AZ.AGR.SINGH SUKHWINDER
VIA BIONDE N.352
37056 SALIZZOLE (VR)
04743340236
SNGSHW94E05Z222Z
----
SARTAJ
KHAISA FRESS VEGETABLES DI SINGH SARTAJ
VIA NUVOLE N.14
37054 NOGARA (VR)
04429170238
SNGSTJ76H11Z222D
----
BALJINDER
AZ.AGR.DI SANDHU BALJINDER SINGH
VIA BASSA N.114
46039 VILLIMPENTA (MN)
04682480233
SNDBJN73R17Z222U
----
MANJIT
SINGH MANJIT
VIA MATTEOTTI N.13
46033 CASTEL D'ARIO (MN)
02354650208
SNGMJT87H09Z222L
----
NANAK
SHRIGURU NANAK DI SINGH GURDEV E SANDU NIRMALJEET KAUR SS SOC.
VIA SANSOVINO N.16
37138 VEROONA (VR)
04062180239
04062180239
----
DOROMICHELE
AZ.AGR.DORO MICHELE
VIA BOSCHI N.
37060 ERBE' (VR)
03216380232
DROMHL79R14E349R
----
LOVEPREET
AZ.AGR. GREWAL DI SINGH LOVEPREET
VIA DEGLI ALPINI N.16
37054 NOGARA (VR)
04936200239
SNGLPR02P01Z222K
----
DHANOA
SOC.AGR.DHANOA S.S.
VIA FABBRICHE N.6
37054 BOVOLONE (VR)
04936250234
04936250234
----
MARCULESCU
MARCULESCU MAGDALENA CRISTINA
VIA TRINITA' N.9
37060 BOVOLONE (VR)
04564940139
MRCMDL91R53Z129E
----
SAINI
AZIENDA SAINI AGRICOLTURA DI SINGH JASKARAN
VIA FABBRICHE N.6
37054 BOVOLONE (VR)
02694450202
SNGJKR03R21Z222P
----
THIND
AZ.AGR.THIND HARPREET KAUR
VIA PALMIRO STERZI N.9
37054 NOGARA (VR)
04538850233
THNHPR99R62Z222Y
----
ZAPPOLA
AZ.AGR.ZAPPOLA MARCO - CLEMENTE
VIA COMUNI N.11
37046 MINERBE (VR)
00941860231
00941860231
----
SATWINDER
AZ.AGR.KAUR SATWINDER
VIA MARTIN LUTER KING N.1
46033 CASTEL D'ARIO (MN)
02418430209
KRASWN85C47Z222E
----
AMRITPAL
AZ.AGR.AMRITMALHI DI SINGH AMRITPAL
VIA ABETONE N.12
37063 ISOLA DELLA SCALA (VR)
04942200231
SNGMTP93C25Z222G
";
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
<form action="./fattura.ods.php" class="dateform" method="post"> 
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

function estraiDatiFattura(){
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
	
	$datainiziale = str_replace('-','',$inizio);
	$datafinale = str_replace('-','',$fine);
	$nomefoglio = 2020;
	$fornitore = $codiceFornitore;
		
	//output
	$fattura = new stdClass();
	$fattura->righe = array();
	$fattura->righeOriginali = array();
	$fattura->ddt = array();
	$fattura->imponibile = 0;

	//estraggo il file ods
	$dataFile = "C:/Documenti/Favorita/Fornitori/vari non abituali - riscontri.ods";
	$tempExtractionDir='C:/Programmi/EasyPHP-5.3.9/www/webcontab/my/php/tmp/ods/'.time();
	//echo '<br>Making dir:'.$tempExtractionDir;
	mkdir($tempExtractionDir.'/');
	//$dataFile = "./vari non abituali - riscontri.ods";
	extractODS($dataFile, $tempExtractionDir);

	//estrapolo i dati fattura
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
		//print_r($riga);
		//se è una riga imballaggio passo alla prossima (non la considero)
		if( strpos($riga->childNodes->item($ARTICOLO)->nodeValue,"IMBALLAGGI NS USCITA")) {continue;}
		if( strpos($riga->childNodes->item($ARTICOLO)->nodeValue,"IMBALLAGGI NS ENTRATA")) {continue;}
		if( strpos($riga->childNodes->item($ARTICOLO)->nodeValue,"IMBALLAGGI SALDO")) {continue;}

		//suddivido i totali per articolo
		$suddivisore = $riga->childNodes->item($ARTICOLO)->nodeValue.$riga->childNodes->item($PREZZO)->nodeValue;
		$suddivisoreDDT = $riga->childNodes->item($NUMERO)->nodeValue.' - '.$riga->childNodes->item($DATA)->nodeValue;

		if (!array_key_exists($suddivisore, $fattura->righe)){
			$fattura->righe[$suddivisore] = array();
			$fattura->righe[$suddivisore]['PESO']=0;
		}
		$fattura->righe[$suddivisore]['ARTICOLO']= $riga->childNodes->item($ARTICOLO)->nodeValue;
		$fattura->righe[$suddivisore]['PESO']= $fattura->righe[$suddivisore]['PESO']+importNumber($riga->childNodes->item($PESO)->nodeValue)*1;
		$fattura->righe[$suddivisore]['PREZZO'] = importNumber($riga->childNodes->item($PREZZO)->nodeValue);
		$fattura->righe[$suddivisore]['UM'] = 'KG';

		//$fattura->righe[$suddivisore]['RIFDDT'] = $suddivisoreDDT;
		
	//	echo "\n<br>".substr($fattura->righe[$suddivisore]['ARTICOLO'],0,4);
		if(substr($fattura->righe[$suddivisore]['ARTICOLO'],0,4)=="MENO"){
			//echo 'detected!!!!!!!!!!!!!!!';
			//echo $fattura->righe[$suddivisore]['PREZZO'];
			$fattura->righe[$suddivisore]['PREZZO'] = -1*$fattura->righe[$suddivisore]['PREZZO'];
			$fattura->righe[$suddivisore]['UM'] = 'NR';
		}
		
		if (!array_key_exists($suddivisore, $fattura->ddt)){
			$fattura->ddt[$suddivisoreDDT]=array();
			$fattura->ddt[$suddivisoreDDT]['numero'] = $riga->childNodes->item($NUMERO)->nodeValue;
			$fattura->ddt[$suddivisoreDDT]['data'] = $riga->childNodes->item($DATA)->nodeValue;
		}
		
		$fattura->imponibile +=importNumber($riga->childNodes->item($PESO)->nodeValue)*$fattura->righe[$suddivisore]['PREZZO'];
		
		$nuovaRiga=count ($fattura->righeOriginali);
		$fattura->righeOriginali[$nuovaRiga]['ddt']  = $riga->childNodes->item($NUMERO)->nodeValue;
		$fattura->righeOriginali[$nuovaRiga]['data']= $riga->childNodes->item($DATA)->nodeValue;
		$fattura->righeOriginali[$nuovaRiga]['fornitore']= $riga->childNodes->item($FORNITORE)->nodeValue;
		$fattura->righeOriginali[$nuovaRiga]['articolo']= $riga->childNodes->item($ARTICOLO)->nodeValue;
		$fattura->righeOriginali[$nuovaRiga]['colli'] = $riga->childNodes->item($COLLI)->nodeValue;
		$fattura->righeOriginali[$nuovaRiga]['peso'] = $riga->childNodes->item($PESO)->nodeValue;
		$fattura->righeOriginali[$nuovaRiga]['prezzo'] = $riga->childNodes->item($PREZZO)->nodeValue;
	}
	return $fattura;
}

//estrapolo i dati fattura dal file ODS
$fattura = estraiDatiFattura();

//riordino le righe in modo da unire gli articoli
krsort($fattura->righe);


//arrotondamenti dei totali
$fattura->imponibile = round($fattura->imponibile,2);
$fattura->iva = round($fattura->imponibile *4/100,2);
$fattura->totale = $fattura->iva+ $fattura->imponibile;

//imposto numero e data se necessario
$fattura->numero ='';
$fattura->data ='';

//dati del fornitore
$fornitore = $dbFornitori[$codiceFornitore];

//print_r($fattura);
//procedo a creare e mostrare la fattura
include "./fattura.html.php";
?>
