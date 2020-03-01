
<?php
include ('./core/config.inc.php');
$elenco=array();
$totali=array();

$stampaRighe= function ($obj){
	global $elenco;
	global $totali;

	$ifco=array('IFCO 4310',
				'IFCO 4314',
				'IFCO 6410',
				'IFCO 6413',
				'IFCO 6416',
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
		'ddt_data'=>array('<>','09/11/16','21/11/16'),
		'cod_cliente'=>array('SEVEN'),
	);


$test=new MyList($params);
$test->iterate($stampaRighe);
//$elenco;
echo '<pre>';
//stampo l'intestazione del file cvs
echo 'DIREZIONE;DATA DI INSERIMENTO;DATA CONSEGNA;BOLLA DI CONSEGNA;POOL;MATERIALE;QUANTITA;IFCO-NR;MIO IFCO-NR;ANNOTAZIONE;NUMERO ORDINE;CONTENUTO;TARGA CAMION;ORIGINE;ANNOTAZIONE CONSEGNA';
echo "\n";

foreach ($elenco as $ddt){

	//DIREZIONE: R = Entrata oder S = Uscita
	echo 'S;';  

	//DATA DI INSERIMENTO: GG.MM.AAAA
	echo str_replace('/','.','24/11/2016').';';

	//DATA CONSEGNA: GG.MM.AAAA
	echo str_replace('/','.',$ddt['ddt']['data']).';';

	//BOLLA DI CONSEGNA: 
	echo '00000'.str_replace(' ','',$ddt['ddt']['numero']).';';

	//TIPO CASSE:
	//01 = IFCO Green Plus 
	//02 = IFCO Yellow Plus
	//05 = IFCO Meat Intelligence 
	//06 = IFCO Fish Intelligence
	//10 = IFCO Dolly
	echo '01;';

	//MATERIALE
	echo str_replace('IFCO ','',$ddt['modelloCasse']).';';
	
	//QUANTITA
	echo $ddt['numeroCasse'].';';
	
	//COD.CLIENTE: DESTINATARIO
	echo '701147;';

	//COD.CLIENTE: MIO
	echo '615045;';
	
	//altri campi non usati
	echo ';;;;;';
	echo "\n";
	
}

page_end();
?>
</body>