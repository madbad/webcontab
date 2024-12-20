<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		</head>

	<body>
<?php
include ('./core/config.inc.php');
$startDateFormatted = '';
$endDateFormatted = '';
$startDate='';
$endDate='';

include ('./selettoredate.inc.php');

//==============================================================================================================================
//fino al 31/05/17 ok
//==============================================================================================================================

function verifica($codCliente){
	global $startDate;
	global $endDate;
	echo "<h1>$codCliente</h1>";

	$test=new MyList(
			array(
				'_type'=>'Riga',
				//'data'=>array('<>',$startDateFormatted, $endDateFormatted),
				'ddt_data'=>array('<>',$startDate, $endDate),
				//'ddt_data'=>array('<>','01/10/23','31/10/23'),
				//'cod_articolo'=>array('=','847'),
				'cod_cliente'=>array('=',$codCliente),
				//'cod_cliente'=>array('=','SOGEG'),
				//'cod_cliente'=>array('=','MARTI'),
				//'cod_cliente'=>array('=','LAME2'),
				//'cod_cliente'=>array('=','BRUNF'),
			)
		);

	$prezzi=array();

	$test->iterate(function ($obj){
		global $prezzi;
		//$obj->cod_articolo->getVal();
		//$obj->prezzo->getVal();
		if ($prezzi[$obj->cod_articolo->getVal()] != $obj->prezzo->getVal()){
			echo '<br> prezzo per ';
			echo $obj->ddt_data->getFormatted();
			echo ' prezzo per '.$obj->cod_articolo->getVal().' era '.$prezzi[$obj->cod_articolo->getVal()].' diventa '.$obj->prezzo->getVal();
			$prezzi[$obj->cod_articolo->getVal()] = $obj->prezzo->getVal();
		}
		if($obj->peso_netto->getVal() != $obj->peso_lordo->getVal()){
			echo '<br> Manca Riscontro (?): '.$obj->ddt_data->getVal().' '.$obj->ddt_numero->getVal().' '.$obj->cod_articolo->getVal();
		}
	});	

}
verifica('SEVEN');
verifica('SEVE2');
verifica('SOGEG');
verifica('MARTI');
verifica('ABBA2');

verifica('LAME2');
verifica('GIAC3');
verifica('ORTO3');
verifica('PAVAN');
verifica('RADIC');
verifica('ROSSE');
verifica('PIOVA');
verifica('BOLLA');
verifica('HORTU');
?>
</body>
