<?php
include ('./core/config.inc.php');
//echo $_GET['date'];
//echo array_key_exists("date",$_GET);

if(array_key_exists('date',$_GET)){
	if( preg_match('/^[0-9]{2}\/[0-9]{2}\/[0-9]{2}\z/', $_GET['date']) ) {
		$dataMovimenti = $_GET['date'];
	}else{
		exit('Formato data errato!');
	}

}else{
	$dataMovimenti = date("d/m/y");
}
//$dataMovimenti = date("22/02/19");

//echo $dataMovimenti;
//exit('Mi fermo qui stavo provando!');

$riferimentoDDT ='';

/*********************************
   ESTRAGGO I DATI DELLA DATA INDDICATA
**********************************/
$testoFile='';

$elenco=array();
$totali=array();

$stampaRighe= function ($obj){
	global $elenco;
	global $totali;

	$ifco=array('POLYM 4316',
				'POLYM 6411',
				'POLYM 6413',
				'POLYM 6416',
				'POLYM 6419',
				'POLYM 6422',
				//'EURO CHEP',
	);
	$art= new Articolo(array('codice'=> $obj->cod_articolo->getVal()));
	$descrizione= $art->descrizione->getVal();
	//echo '<br>'.$descrizione;
	//$descrizione=$obj->descrizione->getVal();

	foreach ($ifco as $ifcoModel){
		//$found=stripos($obj->descrizione->getVal(), $ifcoModel);
		//echo '<br>'.$obj->cod_articolo->extend()->descrizione->getVal();
		//$art = $obj->cod_articolo->extend();
		$found=stripos($descrizione, $ifcoModel);
		if ($found){
			$name=$obj->ddt_data->getFormatted().' :: ddt '.$obj->ddt_numero->getFormatted().$ifcoModel;
@			$elenco[$name][$ifcoModel]+=$obj->colli->getVal();
@			$elenco[$name]['ddt']['data'] = $obj->ddt_data->getFormatted();
@			$elenco[$name]['ddt']['numero'] = $obj->ddt_numero->getFormatted();
@			$elenco[$name]['modelloCasse'] = $ifcoModel; 
			$elenco[$name]['numeroCasse'] += $obj->colli->getVal();
			$elenco[$name]['cod_cliente'] = $obj->cod_cliente->getVal();
			//exit the foreach cicle
@			$totali["\nsommaTotale\n------------"]+=$obj->colli->getVal();
@			$totali[$ifcoModel]+=$obj->colli->getVal();
			break;
		}
	}
	//se al termine non ho ancora trovato il modello IFCO e c'è una quantità di colli maggiore di zero (ovvero non si tratta di solo testo ma di un articolo)
	//allora stampo la stringa di descrizione dell'articolo e la relativa data
/*
	if (!$found & $obj->colli->getVal()>0){
		//echo 'IFCO model not found <br>:: ';
		echo $obj->ddt_data->getFormatted().' :: ';
		echo $descrizione;
		echo ' :: colli ';
		echo $obj->colli->getFormatted().'<br>';
	}
*/
};
$params=array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>',$dataMovimenti,$dataMovimenti),
		'cod_cliente'=>array('SEVEN','SOGEG'),
	);

	
//$cliente = new ClienteFornitore(array('codice'=>'SEVEN'));

$test=new MyList($params);
$test->iterate($stampaRighe);


//print_r($test);

foreach ($elenco as $ddt){

	//1 CODICE INFORMATORE: COD.CLIENTE: MIO
	$testoFile.='6578'.'|';

	//2 DIREZIONE FLUSSO O = USCITE I= INGRESSI
	$testoFile.= 'O'.'|';

	//3 DATA EVENTO (DEDL DDT): da GG.MM.AAAA a AAAAMMGG
 	$data=explode('/',$ddt['ddt']['data']);
	
	$newData = $data[2];
	$newData .= $data[1];
	$newData .= $data[0];
	
	$testoFile.= $newData.'|';
	
	//4 CODICE IMBALLAGGIO
	$testoFile.= $ddt['modelloCasse'].'|';
	
	//5 QUANTITA
	$testoFile.= $ddt['numeroCasse'].'|';
	
	//6 DDT (NUMERO): 
	$testoFile.= str_replace(' ','',$ddt['ddt']['numero']).'|';

	//7 CODICE CONTROPARTE
	$testoFile.= $ddt['cod_cliente'].'|';

	// 8 CONTROPARTE: RAGIONE SOCIALE
	$cliente = new ClienteFornitore(array('codice'=>$ddt['cod_cliente']));
	$testoFile.= $cliente->ragionesociale->getVal().'|';
	
	// 9 CONTROPARTE: INDIRIZZO
	$testoFile.= $cliente->via->getVal().'|';

	// 10 CONTROPARTE: CITTA
	$testoFile.= $cliente->paese->getVal().'|';

	// 11 CONTROPARTE: CAP
	$testoFile.= $cliente->cap->getVal().'|';

	// 12 CONTROPARTE: PROVINCIA
	$testoFile.= $cliente->citta->getVal().'|';

	// 13 FINE RECORD
	$testoFile.= "< \n";
}
/*
header('Content-Disposition: attachment;filename="P2R06578.001";');
header('Content-Type: application/csv; charset=UTF-8');
*/
echo '<pre>'.$testoFile.'<pre>';

//se il testo e' vuoto vuol dire che non ho dati da inviare mi fermo qui ed evito di mandare la mail
if($testoFile!=''){
	/*********************************
	   SCRIVO IL FILE DA ALLEGARE
	**********************************/
	//get the file URL
	$anno = date('Y');
	$cartellaFile = realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/dati/MovimentiPolymer/'.$anno;


	$filecount = 0;
	$files = glob($cartellaFile."/*");
	if ($files){
	 $filecount = count($files);
	}
	$numeratoreFile = $filecount+1;
	$nomeFile = 'P2R06578.'.str_pad($numeratoreFile, 3, '0', STR_PAD_LEFT);
	$urlFile = $cartellaFile.'/'.$nomeFile;

	//actually write the file
	$myfile = fopen($urlFile, "w") or die("Unable to open file!");
	fwrite($myfile, $testoFile);
	fclose($myfile);


	/*********************************
	   MANDO LA MAIL
	**********************************/
	//invio la mail

	//importo i dati di configurazione della mail
	$gmail=$GLOBALS['config']->gmail;

	$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

	$mail->IsSMTP(); // telling the class to use SMTP

	try {
		$mail->Host       = $gmail->Host;
		$mail->SMTPDebug  = $gmail->SMTPDebug;
		$mail->SMTPAuth   = $gmail->SMTPAuth;
		$mail->Port       = $gmail->Port;
		$mail->Username   = $gmail->Username;
		$mail->Password   = $gmail->Password;
		
		$mail->SMTPSecure = "tls";

		//qui dovrei avere un elenco di indirizzi email separati da una virgola","
		//invio la mail ad ogni indirizzo
		//$mail->AddAddress('movements@polymerlogistics.com', 'Polymer Movements'); //destinatario
		$mail->AddAddress('movements@toscaltd.com', 'Tosca - Polymer Movements'); //destinatario
		 
		//ne invio una copia anche a me per conoscenza
		$mail->AddAddress('lafavorita_srl@libero.it', 'La Favorita Srl'); //mia copia per conoscenza

		//mi faccio mandare la ricevuta di lettura
		$mail->ConfirmReadingTo=$gmail->ReplyTo->Mail;
		$mail->SetFrom($gmail->From->Mail, $gmail->From->Name);
		$mail->AddReplyTo($gmail->ReplyTo->Mail, $gmail->ReplyTo->Name);
		
		//oggetto
		$mail->Subject = 'Invio movimenti casse file: '.$nomeFile.' data: '.$dataMovimenti;

		$message="[Messaggio automatizzato] <br><br>\n\n Si trasmettono in allegato movimenti<br>\n";
		$message.='relativi alla data '.$dataMovimenti;
		$message.='<br> con il file '.$nomeFile;
		
		$message.="<br><br>Distinti saluti<br>".$GLOBALS['config']->azienda->ragionesociale->getVal();;

		$mail->MsgHTML($message);
		//$mail->Body($message); 

		//allego il pdf della fattura
		$mail->AddAttachment($urlFile); 
		//var_dump($mail);

		if($mail->Send()){
			echo 'Messaggio Inviato!!';
			//	$html= '<h2 style="color:green">Messaggio Inviato</h2>';
			//	$html.= '<br>Il messaggio con oggetto: ';
			//	$html.= '<b>'.$mail->Subject.'</b>';
			//	$html.='<br>E\' stato inviato a: <b>'.$cliente->ragionesociale->getVal().'</b>';
			//	$html.='<br>all\'indirizzo: <b>'.$cliente->__pec->getVal().'</b>';
			//	$html.='<br>con allegato il file: <b>'.$this->getPdfFileUrl().'</b>';
				
				//memorizzo la data di invio
		//				$this->__datainviopec->setVal(date("d/m/Y"));
		//				$this->saveSqlDbData();
				//mostro il messaggio di avvenuto invio
			//	echo $html;
			//	var_dump($message);
				//all seems ok
			//return true;
		}

	} catch (phpmailerException $e) {
		echo $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
		echo $e->getMessage(); //Boring error messages from anything else!
	}
}else{
	echo "\n<br>Nessun dato da iniavre a Polymer!";
}
?>
