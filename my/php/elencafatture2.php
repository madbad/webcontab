<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Elenca fatture</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<style>
	body{
		font-family: Arial, Helvetica, sans-serif;
	}
	</style>
</head>
<body>
<?php
include ('./core/config.inc.php');
set_time_limit ( 0);


$params = 	array(
		'_type'=>'Fattura',
		'data'=>array('<>','01/07/2023','31/12/2023'),
		'cod_cliente'=>'PAROD',
	);
$paramsC= Array(
	'codice'=> $params['cod_cliente'],
);
//print_r($paramsC);
$cliente= new ClienteFornitore($paramsC);

//print_r($cliente);
//parametri
//print_r($params);
echo '<h1>Elenco fatture</h1><hr>';
echo '<table>';
echo '<tr><td>CLIENTE:</td><td><b>'.$params['cod_cliente'].'</b> => '.$cliente->ragionesociale->getVal().'</td>';
echo '<tr><td>DAL:</td><td><b>'.$params['data'][1].'</b>'.'</td>';
echo '<tr><td>AL:</td><td><b>'.$params['data'][2].'</b>'.'</td>';
echo '</table><hr>';
//ottengo la lista fatture
$test=new MyList($params);


	echo '<br><table class="borderTable spacedTable rimanenze" style="text-align:right !important; font-family: Arial, Helvetica, sans-serif;"><tr style="font-weight:bold;">';
	echo '<td>Tipo</td>';
	echo '<td>Numero</td>';
	echo '<td>Data</td>';
	echo '<td>Imponibile</td>';
	echo '<td>Iva</td>';
	echo '<td>Totale Fattura</td>';
	echo '</tr>';


//ottengo una lista dei codici clienti 
//(mi serve per creare una cache degli oggetti cliente in modo da non dover fare poi una query per ogni singolo oggetto cliente)
$fatturato = array();
$test->iterate(function($obj){
	//$obj->getRighe();
	//print_r($obj);
	global $fatturato;
	
	$codiceCliente=$obj->cod_cliente->getVal();
	//$imponibile_fattura = $obj->getTotaleImponibile();
	$importiFattura = $obj->calcolaTotaliImponibiliIva();
	$importiFattura = $importiFattura[4];
	
	
	if($obj->tipo->getVal()=="n"){
		$importiFattura['imponibile'] = abs($importiFattura['imponibile'])*-1;
		$importiFattura['importo_iva'] = abs($importiFattura['importo_iva'])*-1;
	}
	
	//print_r($importiFattura);
	$fatturato[$codiceCliente]['imponibile'] += $importiFattura['imponibile'];
	$fatturato[$codiceCliente]['importo_iva'] += $importiFattura['importo_iva'];
	$importiFattura['importo_tot'] = $importiFattura['imponibile']+$importiFattura['importo_iva'];
	$fatturato[$codiceCliente]['importo_tot'] += $importiFattura['importo_tot'];
	echo '<tr>';
	echo '<td>'.$obj->tipo->getVal().'</td>';
	echo '<td>'.$obj->numero->getVal().'</td>';
	echo '<td>'.$obj->data->getFormatted().'</td>';
	echo '<td style="text-align:right">'.number_format($importiFattura['imponibile'],$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.').'</td>';
	echo '<td style="text-align:right">'.number_format($importiFattura['importo_iva'],$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.').'</td>';
	echo '<td style="text-align:right">'.number_format($importiFattura['importo_tot'],$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.').'</td>';

	echo '</tr>';
	
});

echo '<tr>';
echo '<td colspan="3">Totale</td>';
echo '<td style="text-align:right">'.number_format($fatturato[$params['cod_cliente']]['imponibile'],$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.').'</td>';
echo '<td style="text-align:right">'.number_format($fatturato[$params['cod_cliente']]['importo_iva'],$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.').'</td>';
echo '<td style="text-align:right">'.number_format($fatturato[$params['cod_cliente']]['importo_tot'],$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.').'</td>';
echo '</tr>';


echo '</table>';



/*
$params=array(
	'data'=>'07/01/2016',
	'numero'=>'1',
);
$fattura = new Fattura($params);
echo $fattura->cod_cliente->getVal();

echo $fattura->getTotaleImponibile();
*/
?>

</body>
</html>
