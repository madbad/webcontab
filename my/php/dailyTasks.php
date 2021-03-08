<?php
/*
QUESTO FILE VERRA AUTOMATICAMENT EESEGUITO TUTTI I GIORNI ALLE 19.00
*/

include ('./core/config.inc.php');
set_time_limit ( 0);
include ('./updateContab.php');

$today = date('d/m/Y');

$ddtodierni=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('=',$today),
		'cod_destinatario'=>array('=','SOGEG','VALEN','SEVEN'),
		'cod_causale' => array('!=','D')
	)
);

//ottengo una lista dei codici clienti 
//(mi serve per creare una cache degli oggetti cliente in modo da non dover fare poi una query per ogni singolo oggetto cliente)

$ddtodierni->iterate(function($obj){
	global $html;
	global $dbClientiWithIndex;
/*
	$html.= $obj->numero->getVal();
	$html.= $obj->data->getFormatted();
	$html.= $obj->cod_destinazione->getVal();
	$html.= $obj->cod_destinatario->getVal();
	* 
	* note
	* note1
	* note2
	* inviaMail
	* 
*/
	//mi preparo i parametri di ricerca della fattura
	$params=array(
		'numero' => $obj->numero->getVal(),
		'data'   => $obj->data->getVal(),
		'cod_causale'  => $obj->cod_causale->getVal()
	);
	//genero il mio oggetto ddt
	$ddt= new Ddt($params);

	if($ddt->cod_destinatario->getVal()=='SOGEG'){
		$error='';
		
		$ddt->getRighe();
		foreach ($ddt->righe as $key => $value) {
			$riga=$ddt->righe[$key];
		
			if( strpos('POLYM',$riga->descrizione->getVal()) && $riga->cod_articolo->getVal()!='ASSOLVE'){
				$error='Riscontrato un articolo non polimer: '.$riga->descrizione->getVal();
			}
		}
		if($error==''){
			echo "\n<BR>".'SOGEGROSS: Inviata mail';
			$ddt->inviaMail();
		}else{
			echo "\n<BR>".'SOGEGROSS: '.$error;
		}
	}
	if($ddt->cod_destinatario->getVal()=='SEVEN'){
		$error='';
		
		
		// check for polymer articles
		$ddt->getRighe();
		foreach ($ddt->righe as $key => $value) {
			$riga=$ddt->righe[$key];
		
			if( strpos('POLYM',$riga->descrizione->getVal()) && $riga->cod_articolo->getVal()!='ASSOLVE'){
				$error='Riscontrato un articolo non polimer: '.$riga->descrizione->getVal();
			}
		}
		
		//check presenza bancali
		$bancali = (int) filter_var($ddt->note->getVal(), FILTER_SANITIZE_NUMBER_INT);
		echo "\n<BR>".'SEVEN: Trovati bancali: '.$bancali;
		if($bancali<1){
			$error.="\n<BR>".'SEVEN: non trovato il numero di bancali.';
		}
		
		if($error==''){
			try{
				$ddt->inviaMail();
				echo "\n<BR>".'SEVEN: Inviata mail';
			}catch (Exception $e) {
				echo "\n<BR>".'SEVEN: Errore invio mail!!!';
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}else{
			echo "\n<BR>".'SEVEN: '.$error;
		}
	}

	if($ddt->cod_destinatario->getVal()=='VALEN'){
		try{
			$ddt->inviaMail();
			echo "\n<BR>".'VALENTINO TOSI: Inviata mail';
		}catch (Exception $e) {
			echo "\n<BR>".'VALENTINO TOSI: Errore invio mail!!!';
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

	}
	//wait 5 seconds
	//sleep (5);
	//echo "Wait 5 seconds";
});

//wait 5 seconds
//sleep (5);

include ('./polymerMailer.php');
?>
