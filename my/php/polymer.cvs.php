<?php
include ('./core/config.inc.php');
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
		'ddt_data'=>array('<>','08/02/19','08/02/19'),
		'cod_cliente'=>array('SEVEN'),
	);

	
$cliente = new ClienteFornitore(array('codice'=>'SEVEN'));

$test=new MyList($params);
$test->iterate($stampaRighe);

foreach ($elenco as $ddt){

	//1 CODICE INFORMATORE: COD.CLIENTE: MIO
	echo '6578'.'|';

	//2 DIREZIONE FLUSSO O = USCITE I= INGRESSI
	echo 'O'.'|';

	//3 DATA EVENTO (DEDL DDT): da GG.MM.AAAA a AAAAMMGG
 	$data=explode('/',$ddt['ddt']['data']);
	
	$newData = $data[2];
	$newData .= $data[1];
	$newData .= $data[0];
	
	echo $newData.'|';
	
	//4 CODICE IMBALLAGGIO
	echo $ddt['modelloCasse'].'|';
	
	//5 QUANTITA
	echo $ddt['numeroCasse'].'|';
	
	//6 DDT (NUMERO): 
	echo str_replace(' ','',$ddt['ddt']['numero']).'|';

	//7 CODICE CONTROPARTE
	echo 'SEVEN'.'|';

	// 8 CONTROPARTE: RAGIONE SOCIALE
	echo $cliente->ragionesociale->getVal().'|';
	
	// 9 CONTROPARTE: INDIRIZZO
	echo $cliente->via->getVal().'|';

	// 10 CONTROPARTE: CITTA
	echo $cliente->paese->getVal().'|';

	// 11 CONTROPARTE: CAP
	echo $cliente->cap->getVal().'|';

	// 12 CONTROPARTE: PROVINCIA
	echo $cliente->citta->getVal().'|';

	// 13 FINE RECORD
	echo "< \n";
}

header('Content-Disposition: attachment;filename="P2R06578.001";');
header('Content-Type: application/csv; charset=UTF-8');
?>
