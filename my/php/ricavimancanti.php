<?php
include ('./core/config.inc.php');
include ('./richiestaricavi.php');
?>
<?php 

if (array_key_exists('mese',$_GET)){
/*
	$anno = $_GET['anno'];
	$mese = $_GET['mese'];
	$giorni = cal_days_in_month(CAL_GREGORIAN, $mese, $anno); // 31
*/
	$today = date("j/n/Y"); 
	$startDate='01/01/2021';
	$endDate='31/01/2021';

}else{
	
	$anno = date("Y");
	$mese = date("n")-1;

	if($mese == 00){
		$anno = date("Y")*1-1;
		$mese = 12;
		
	}
	//echo $mese;
	//echo $anno;
	$giorni = cal_days_in_month(CAL_GREGORIAN, $mese, $anno); // 31

	date("Y");
	$startDate='01/'.$mese.'/'.$anno;
	$endDate=$giorni.'/'.$mese.'/'.$anno;
}


$mancanti = array();

$stampaRighe= function ($obj){
	global $mancanti;
	//echo '<br>'.$obj->cod_cliente->extend()->ragionesociale->getVal();
	//echo '<br>'.$obj->ddt_numero->getVal();
	$ddt = '<br>- n.'.$obj->ddt_numero->getVal().' del '.$obj->ddt_data->getFormatted();
	if (!in_array($ddt,$mancanti)){
		@$mancanti[$obj->cod_cliente->extend()->ragionesociale->getVal()];
	}
	$mancanti[$obj->cod_cliente->extend()->ragionesociale->getVal()]['codcliente']=$obj->cod_cliente->getVal();
	$mancanti[$obj->cod_cliente->extend()->ragionesociale->getVal()]['mail']=$obj->cod_cliente->extend()->__mail->getVal();
	$mancanti[$obj->cod_cliente->extend()->ragionesociale->getVal()]['ddt'][$ddt]='*';
};

$test=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>',$startDate,$endDate),
		'cod_cliente'=>array('!=','VIOLA','FACCG'),		
		'cod_articolo'=>array('!=','ASSOLVE','BSEVEN',''),
		'prezzo'=>array('=','0.001'),
		//'cod_causale' =>array('=','V'), //SOLO LE BOLLE DI VENDITA
	)
);
$test->iterate($stampaRighe);

foreach ($mancanti as $clienteKey => $clienteValue){
	
	$matchCliente = (substr($clienteKey,0,10) == substr($_GET['mailacliente'],0,10));
	
	if (!isset($_GET['mailacliente']) or $matchCliente){
		echo '<hr><br><br>'.$clienteKey;
		echo '<br> <a href="?mailacliente='.$clienteKey.'&mese='.$mese.'">Invia mail a: '.$clienteValue['mail'].'</a>';
		echo '<br>';
	}
	$elencoDdt='';
	foreach ($clienteValue['ddt'] as $ddtKey => $ddtValue){
		if(strlen($ddtKey)>2){
			if (!isset($_GET['mailacliente']) or $matchCliente){
				echo $ddtKey.'';
			}
			$elencoDdt.= "\n".$ddtKey;
		}
	}
	
	//echo "\n <br>".$clienteKey;
	//echo "\n <br>".$_GET['mailacliente'];
	if($matchCliente){
		echo '<hr><br><br>'.$clienteKey;		
		echo '<br><b>Inviata mail!</b>';
		inviaMailRichiestaRicavi($clienteKey,$clienteValue['mail'],$elencoDdt);
		inviaMailRichiestaRicavi($clienteKey,'amministrazione@lafavoritasrl.it',$elencoDdt);

	}
	

}
//print_r($mancanti);
page_end();

?>
