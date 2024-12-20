<?php
include ('./core/config.inc.php');

$fatturaIntegrativa;
$fatturaEstera;
$fornitore;
$righe;
$contarighe = 0;

function getProgressivoInvioSDI(){
	//conto i file ufficiali
	$fi = new FilesystemIterator($GLOBALS['config']->xmlDir, FilesystemIterator::SKIP_DOTS);
	$fileCount = iterator_count($fi);
	
	//conto i file temporanei
	if($GLOBALS['isTempFile']){
		$fi = new FilesystemIterator($GLOBALS['config']->xmlDir.'/../ftXml_prove', FilesystemIterator::SKIP_DOTS);
		$fileCount += iterator_count($fi);
	}
	
	$progressivoInvio = str_pad ( $fileCount+1 , $pad_length=5 , $pad_string="0" , $pad_type=STR_PAD_LEFT );
	return $progressivoInvio + 2283;
}

$isTempFile=false;

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




/*--------------------------------------------------------
	ESTRAZIONE DEI DATI DAL FILE ODS
//--------------------------------------------------------*/
//estraggo il file ods
$dataFile = "C:/Programmi/EasyPHP-5.3.9/www/webcontab/my/php/dati/FATTURE_INTRA_INTEGRATIVE.ods";
$tempExtractionDir='C:/Programmi/EasyPHP-5.3.9/www/webcontab/my/php/tmp/ods/'.time();
//echo '<br>Making dir:'.$tempExtractionDir;
mkdir($tempExtractionDir.'/');
//$dataFile = "./vari non abituali - riscontri.ods";
extractODS($dataFile, $tempExtractionDir);

/*--------------------------------------------------------
	PARSING DEI DATI DAL FILE ODS
//--------------------------------------------------------*/
$nomefoglio='2024';

//estrapolo i dati fattura
$doc = new DOMDocument;
// We don't want to bother with white spaces
$doc->preserveWhiteSpace = false;
$doc->load($tempExtractionDir.'/content.xml');
$xpath = new DOMXPath($doc);

//intestazione colonne
$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="'.$nomefoglio.'"]/table:table-row[1]';
$rigaIntestazione = $xpath->query($query);

$query = '//office:document-content/office:body/office:spreadsheet/table:table[@table:name="'.$nomefoglio.'"]/table:table-row';
$righe = $xpath->query($query);

$contaColonne = 0;
$intestazioniColonne = Array();

//PASSA IL NOME DELLE VALORI DELLE COLONNE NELLE MIE VARIABILI
foreach ($rigaIntestazione->item(0)->childNodes as $cella) {
	$colonnaNumero = $contaColonne;
	$colonnaNome = $cella->nodeValue;
	$contaColonne++;

	$colonnaNome = str_replace(" ", "_", $colonnaNome);
	echo 		"\n<br>".$colonnaNome;
	
	$intestazioniColonne[$colonnaNome] = $colonnaNumero;
	global $$colonnaNome;
	$$colonnaNome = $colonnaNumero;
}


//PARSO OGNI RIGA DEL FOGLIO => OGNI RIGA È UNA FATTURA
foreach ($righe as $riga) {
	global $fatturaIntegrativa;
	global $fatturaEstera;
	global $fornitore;
	global $righe;
	global $contarighe;
	$contarighe++;
	if($contarighe==1){continue;}//non processare la prima riga, è intestazione /*todo*/
	if($riga->childNodes->item($DATA_INTEGRAZIONE )->nodeValue==""){continue;}//non processare la riga se non ha dati
if (!($contarighe > 4+1 && $contarighe <=16)){continue;}

	//echo $contarighe;
	
	//DATI DI FATTURAZIONE
	$fatturaIntegrativa = new stdClass();
	$fatturaIntegrativa->data = $riga->childNodes->item($DATA_INTEGRAZIONE )->nodeValue;  //la data di ricezione
	$fatturaIntegrativa->numero = $riga->childNodes->item($NUMERO_INTEGRAZIONE )->nodeValue; //il progressivo di protocollo reverse charge
	$fatturaIntegrativa->progressivoSDI = getProgressivoInvioSDI(); //il progressivo di protocollo reverse charge /*TODO*/

	$fatturaEstera = new stdClass();
	$fatturaEstera->data = $riga->childNodes->item($DATA_FATTURA_ORIGINARIA )->nodeValue;
	$fatturaEstera->numero = $riga->childNodes->item($NUMERO_FATTURA_ORIGINARIA )->nodeValue;

	$fornitore = new stdClass();
	$fornitore->ragione_sociale = "PROMAPRIM SAS";
	$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
	$fornitore->cap =             "30400";
	$fornitore->paese =           "VILLENEUVE LES AVIGNON";
	$fornitore->nazione =         "FR";
	$fornitore->piva =            "57428690739";

	$righe = array();
	//DESCRIZIONE, PESO, UM, PREZZO, IVA
	//$righe[] = "INS.SCAROLA, 974.00, KG, 2.00, 4.0";
	//$righe[] = "INS.RICCIA, 346.00, KG, 2.00, 4.0";
	/*todo*/
	//todo
	//per far funzionare correttamente 
	//  $riga->childNodes->item($RIGA1DESCRIZIONE )->nodeValue
	// dobbiamo contare le celle ripeture nella riga prima della cella desiderata, come succede in questo esempio
	// <table:table-cell table:number-columns-repeated="2" office:value-type="float" office:value="0" calcext:value-type="float">
	
	if($riga->childNodes->item($RIGA1DESCRIZIONE )->nodeValue !='-' && $riga->childNodes->item($RIGA1DESCRIZIONE )->nodeValue !='0'){
		getCellaDaRiga($riga, $RIGA1DESCRIZIONE);
		$righe[] = getCellaDaRiga($riga, $RIGA1DESCRIZIONE)->nodeValue."# ".str_replace(",", ".",getCellaDaRiga($riga, $RIGA1QUANTITA )->nodeValue)."# KG#".str_replace(",", ".",getCellaDaRiga($riga, $RIGA1PREZZO )->nodeValue)."# 4.00";
	}


	if($riga->childNodes->item($RIGA2DESCRIZIONE )->nodeValue !='-' && $riga->childNodes->item($RIGA2DESCRIZIONE )->nodeValue !='0'){
		echo '-->'.$riga->childNodes->item($RIGA2DESCRIZIONE )->nodeValue.'<--';
		$righe[] = getCellaDaRiga($riga, $RIGA2DESCRIZIONE)->nodeValue."# ".str_replace(",", ".",getCellaDaRiga($riga, $RIGA2QUANTITA )->nodeValue)."# KG#".str_replace(",", ".",getCellaDaRiga($riga, $RIGA2PREZZO )->nodeValue)."# 4.00";

	}
	
	if($riga->childNodes->item($RIGA3DESCRIZIONE )->nodeValue !='-' && $riga->childNodes->item($RIGA3DESCRIZIONE )->nodeValue !='0'){
		echo '-->'.$riga->childNodes->item($RIGA3DESCRIZIONE )->nodeValue.'<--';
		$righe[] = getCellaDaRiga($riga, $RIGA3DESCRIZIONE)->nodeValue."# ".str_replace(",", ".",getCellaDaRiga($riga, $RIGA3QUANTITA )->nodeValue)."# KG#".str_replace(",", ".",getCellaDaRiga($riga, $RIGA3PREZZO )->nodeValue)."# 4.00";
	}

	$nomeFile = creaFileXml();
	sleep(0.5);//give it time to finisch
	inviaSDI($nomeFile, $fatturaIntegrativa);
	sleep(0.5);//give it time to finisch
}



/*
$fatturaIntegrativa = new stdClass();
$fatturaIntegrativa->data = "2024-10-21";  //la data di ricezione
$fatturaIntegrativa->numero = "2"; //il progressivo di protocollo reverse charge
$fatturaIntegrativa->progressivoSDI = "2790"; //il progressivo di protocollo reverse charge

$fatturaEstera = new stdClass();
$fatturaEstera->data = "2024-10-04";
$fatturaEstera->numero = "259";

$fornitore = new stdClass();
$fornitore->ragione_sociale = "PROMAPRIM SAS";
$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
$fornitore->cap =             "30400";
$fornitore->paese =           "VILLENEUVE LES AVIGNON";
$fornitore->nazione =         "FR";
$fornitore->piva =            "57428690739";

$righe = array();
//DESCRIZIONE, PESO, UM, PREZZO, IVA
$righe[] = "INS.SCAROLA, 1338.00, KG, 2.00, 4.0";
$righe[] = "INS.RICCIA, 659.00, KG, 2.00, 4.0";
*/

/*
$fatturaIntegrativa = new stdClass();
$fatturaIntegrativa->data = "2024-10-21";  //la data di ricezione
$fatturaIntegrativa->numero = "3"; //il progressivo di protocollo reverse charge
$fatturaIntegrativa->progressivoSDI = "2791"; //il progressivo di protocollo reverse charge

$fatturaEstera = new stdClass();
$fatturaEstera->data = "2024-10-07";
$fatturaEstera->numero = "261
";

$fornitore = new stdClass();
$fornitore->ragione_sociale = "PROMAPRIM SAS";
$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
$fornitore->cap =             "30400";
$fornitore->paese =           "VILLENEUVE LES AVIGNON";
$fornitore->nazione =         "FR";
$fornitore->piva =            "57428690739";

$righe = array();
//DESCRIZIONE, PESO, UM, PREZZO, IVA
$righe[] = "INS.SCAROLA, 817.00, KG, 1.80, 4.0";
$righe[] = "INS.RICCIA, 1600.00, KG, 1.80, 4.0";
*/



/*
$fatturaIntegrativa = new stdClass();
$fatturaIntegrativa->data = "2024-10-21";  //la data di ricezione
$fatturaIntegrativa->numero = "4"; //il progressivo di protocollo reverse charge
$fatturaIntegrativa->progressivoSDI = "2792"; //il progressivo di protocollo reverse charge

$fatturaEstera = new stdClass();
$fatturaEstera->data = "2024-10-10";
$fatturaEstera->numero = "266";

$fornitore = new stdClass();
$fornitore->ragione_sociale = "PROMAPRIM SAS";
$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
$fornitore->cap =             "30400";
$fornitore->paese =           "VILLENEUVE LES AVIGNON";
$fornitore->nazione =         "FR";
$fornitore->piva =            "57428690739";

$righe = array();
//DESCRIZIONE, PESO, UM, PREZZO, IVA
$righe[] = "INS.RICCIA, 691.00, KG, 1.80, 4.0";

*/


function calcolaTotale(){
	global $righe;
	$totaleFattura = 0;
	foreach($righe as $riga){
		list($descrizione, $peso, $um, $prezzo, $iva) = explode("#", $riga);
		$totaleFattura += $peso*$prezzo * ($iva+100)/100;
	}
	return $totaleFattura;
}

function creaFileXml(){
	
	global $fatturaIntegrativa;
	global $fatturaEstera;
	global $fornitore;
	global $righe;

	//echo "****".$fatturaIntegrativa->numero."****";


	//salvo loutput in una varibile
	ob_start();
	?>
<?xml version="1.0"?>
<p:FatturaElettronica xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" versione="FPR12" xsi:schemaLocation=" http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 https://www.agenziaentrate.gov.it/portale/documents/20143/2931841/Schema_VFPR12.xsd">
  <FatturaElettronicaHeader>
	<DatiTrasmissione>
	  <IdTrasmittente>
		<IdPaese>IT</IdPaese>
		<IdCodice>01588530236</IdCodice>
	  </IdTrasmittente>
	  <ProgressivoInvio><?php echo $fatturaIntegrativa->progressivoSDI; ?></ProgressivoInvio>
	  <FormatoTrasmissione>FPR12</FormatoTrasmissione>
	  <CodiceDestinatario>0000000</CodiceDestinatario>
	</DatiTrasmissione>
	<CedentePrestatore>
	  <DatiAnagrafici>
		<IdFiscaleIVA>
		  <IdPaese><?php echo $fornitore->nazione; ?></IdPaese>
		  <IdCodice><?php echo $fornitore->piva; ?></IdCodice>
		</IdFiscaleIVA>
		<Anagrafica>
		  <Denominazione><?php echo $fornitore->ragione_sociale; ?></Denominazione>
		</Anagrafica>
		<RegimeFiscale>RF01</RegimeFiscale>
	  </DatiAnagrafici>
	  <Sede>
		<Indirizzo><?php echo $fornitore->indirizzo; ?></Indirizzo>
		<CAP><?php echo $fornitore->cap; ?></CAP>
		<Comune><?php echo $fornitore->paese; ?></Comune>
		<Nazione><?php echo $fornitore->nazione; ?></Nazione>
	  </Sede>
	</CedentePrestatore>
	<CessionarioCommittente>
	  <DatiAnagrafici>
		<IdFiscaleIVA>
		  <IdPaese>IT</IdPaese>
		  <IdCodice>01588530236</IdCodice>
		</IdFiscaleIVA>
		<CodiceFiscale>01588530236</CodiceFiscale>
		<Anagrafica>
			<Denominazione>LA FAVORITA DI BRUN G. &amp; G. SRL UNIP.</Denominazione>
		</Anagrafica>
	  </DatiAnagrafici>
	  <Sede>
		<Indirizzo>Camagre 38/B</Indirizzo>
		<CAP>37063</CAP>
		<Comune>Isola della Scala</Comune>
		<Provincia>VR</Provincia>
		<Nazione>IT</Nazione>
	  </Sede>
	</CessionarioCommittente>
  </FatturaElettronicaHeader>
  <FatturaElettronicaBody>
	<DatiGenerali>
	  <DatiGeneraliDocumento>
		<TipoDocumento>TD18</TipoDocumento>
		<Divisa>EUR</Divisa>
		<Data><?php echo $fatturaIntegrativa->data;?></Data>
		<Numero><?php echo $fatturaIntegrativa->numero;?>/2024/RCE</Numero>
		<ImportoTotaleDocumento><?php echo number_format(calcolaTotale(), 2,".",""); ?></ImportoTotaleDocumento>
		<Causale>INTEGRAZIONE FATTURA NUMERO <?php echo $fatturaEstera->numero;?> DEL <?php echo $fatturaEstera->data; ?></Causale>
	  </DatiGeneraliDocumento>
	  <DatiFattureCollegate>
		<IdDocumento><?php echo $fatturaEstera->numero;?></IdDocumento>
		<Data><?php echo $fatturaEstera->data;?></Data>
	  </DatiFattureCollegate>
	</DatiGenerali>
	<DatiBeniServizi>
<?php
$contariga = 0;
$aliquote = array();
foreach($righe as $riga){
	list($descrizione, $peso, $um, $prezzo, $iva) = explode("#", $riga);
	$contariga++; 
	$importo = $peso*$prezzo;
echo"
	<DettaglioLinee>
		<NumeroLinea>$contariga</NumeroLinea>
		<Descrizione>".trim($descrizione)."</Descrizione>
		<Quantita>".trim($peso)."</Quantita>
		<UnitaMisura>".trim($um)."</UnitaMisura>
		<PrezzoUnitario>".trim($prezzo)."</PrezzoUnitario>
		<PrezzoTotale>".number_format($importo, 2,".","")."</PrezzoTotale>
		<AliquotaIVA>".trim($iva)."</AliquotaIVA>
	 </DettaglioLinee>
";
	@$aliquote[trim($iva)]  += $importo;
}
?>
	  <DatiRiepilogo>
<?php
foreach($aliquote as $aliquotaKey => $aliquotaValue ){
	echo "
		<AliquotaIVA>$aliquotaKey</AliquotaIVA>
		<ImponibileImporto>".number_format($aliquotaValue,2,".","")."</ImponibileImporto>
		<Imposta>".number_format($aliquotaKey * $aliquotaValue/100,2,".","")."</Imposta>
		<EsigibilitaIVA>I</EsigibilitaIVA>
";
}
?>
	  </DatiRiepilogo>
	</DatiBeniServizi>
  </FatturaElettronicaBody>
</p:FatturaElettronica>
	<?php
	$nomeFile = 'IT01588530236_'.$fatturaIntegrativa->progressivoSDI.'.xml';

	//ottengo l'output dal buffer
	$xml = ob_get_contents();
	ob_end_clean();


	#$myfile = fopen("./integrazioneFtIntrastat/".$nomeFile, "w") or die("Unable to open file!");
	$myfile = fopen($GLOBALS['config']->xmlDir.'/'.$nomeFile, "w") or die("Unable to open file!");
	fwrite($myfile, $xml);
	fclose($myfile);
	return $nomeFile;
}

function getCellaDaRiga($riga, $cella){
echo "<br>\n-------------------------------";
	//per arrivare alla cella corretta
	//devo partendo dalla prima ciclare
	//contanto le celle ripetute
	//fino ad arrivare alla mia
	//ad esempio se ho cella 1,2,3,4,5  ma 3 e 4 sono ripetute libre office le mette in un unica "cella"
	//quindi se circo la cella 4 devo ritornare il valore della 3
	$cellaRichiesta =  $cella;
	$contaCella = 0;
	echo 'cella richiesta: '.$cellaRichiesta;
	//~ //new DOMDocument
	foreach ($riga->childNodes as $nodoCella) {
		$contaCella++;
		if ($nodoCella->hasAttribute('table:number-columns-repeated')) {
			// Do something with the element
			//~ $tagName = $element->tagName;
			//~ $repeatedValue = $element->getAttribute('repeated');
			//echo "<$tagName repeated=\"$repeatedValue\">" . $element->textContent . "</$tagName>\n";
			echo "<br>\n ".($contaCella-1)." repeated";
			echo $nodoCella->getAttribute('table:number-columns-repeated');
			$ripetizioni = $nodoCella->getAttribute('table:number-columns-repeated');
			
			if($contaCella-1 < $cellaRichiesta && ($contaCella-1 + $ripetizioni) >= $cellaRichiesta){
				print_r($nodoCella);
				return $nodoCella;
			}
		}else{
			echo "<br>\n ".($contaCella-1)." not repeated";
			if($contaCella-1 == $cellaRichiesta){
				print_r($nodoCella);
				return $nodoCella;
			}
		}
	}
	echo "<br>\n ".($contaCella-1)." nothing found";
	
}



function inviaSDI($nomeFile, $fatturaIntegrativa){

	$urlFile = $GLOBALS['config']->xmlDir.'/'.$nomeFile;

	//importo i dati di configurazione della pec
	$pec=$GLOBALS['config']->pec;
	//$cliente=$this->cod_cliente->extend();
	//var_dump($cliente);
	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

	$mail->IsSMTP(); // telling the class to use SMTP

	try {
		$mail->Host       = $pec->Host;
		$mail->SMTPDebug  = $pec->SMTPDebug;
		$mail->SMTPAuth   = $pec->SMTPAuth;
		$mail->Port       = $pec->Port;
		$mail->Username   = $pec->Username;
		$mail->Password   = $pec->Password;

		//indirizzo mail PEC
		$mail->AddAddress($GLOBALS['config']->SDIpec, 'SDI'); //destinatario
//		$mail->AddAddress('favoritasrl@gmail.com', 'SDI'); //destinatario
		
		//mi faccio mandare la ricevuta di lettura
		$mail->ConfirmReadingTo=$pec->ReplyTo->Mail;
		$mail->SetFrom($pec->From->Mail, $pec->From->Name);
		$mail->AddReplyTo($pec->ReplyTo->Mail, $pec->ReplyTo->Name);
		//Invio a SDI - 20190115_F00000001 - File IT01588530236_00001.xml - FT n.1 del 15-01-2019 - Primo Invio
		$mail->Subject = 'Invio a SDI - '.substr($nomeFile, 0, -4).' - File '.$nomeFile.' - Fattura Integrativa INTRA'.'. Nr. '.$fatturaIntegrativa->numero.' del '.$fatturaIntegrativa->data.' - Primo Invio'; //oggetto
		
		/*
		Si invia in allegato file relativo alla Fattura Elettronica
		FT n.1 del 15-01-2019
		File IT01588530236_00001.xml
		Primo invio 
		*/
		//$message="[Messaggio automatizzato] <br><br>\n\n Si invia in allegato file relativo alla Fattura Elettronica";
		$message="<br>\n Fattura Integrativa".'. Nr. '.$fatturaIntegrativa->numero.' del '.$fatturaIntegrativa->data;
		$message.="<br>\n"."File ".$nomeFile;
		$message.="<br>\n"."Primo invio";


		$mail->MsgHTML($message);
		//$mail->Body($message); 

		//allego l'xml della fattura
		$mail->AddAttachment($urlFile); 
		
		//var_dump($mail);
		
		if($mail->Send()){
			//$this->__nomefilexml->setVal($this->getXmlFileName());
			return true;
		}
	} catch (phpmailerException $e) {
		echo $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
		echo $e->getMessage(); //Boring error messages from anything else!
	}
	return false;
}

?>
