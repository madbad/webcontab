<?php
include ('./config.inc.php');
include ('./stampe/ft.php');
$params=array(
	'numero' => '1',
	'data'   => '2012-01-09'
);
$myFt= new Fattura($params);
//var_dump($myFt);

//var_dump($myFt->cod_cliente->getVal);
printFt($myFt);
?>