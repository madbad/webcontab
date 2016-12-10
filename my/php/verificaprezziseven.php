
<?php
include ('./core/config.inc.php');

?>

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

//==============================================================================================================================

//==============================================================================================================================
$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>','01/07/16','31/08/16'),
			//'cod_articolo'=>array('=','847'),
			'cod_cliente'=>array('=','SOGEG'),
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
});

?>
</body>
