<?php
include ('./config.inc.php');
include ('./stampe/ft.php');
$params=array(
	'numero' => '248',
	'data'   => '2012-04-14'
);
$myFt= new Fattura($params);
//var_dump($myFt);
//var_dump($myFt->calcolaTotaliImponibiliIva());
//var_dump($myFt->cod_cliente->getVal);
printFt($myFt);
?>