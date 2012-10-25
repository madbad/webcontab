<?php
include ('./config.inc.php');
include ('./stampe/ft.php');
$params=array(
	'numero' => $_GET["numero"],
	'data'   => $_GET["data"],
	'tipo'  => $_GET["tipo"]
);
$myFt= new Fattura($params);
//var_dump($myFt);
//var_dump($myFt->calcolaTotaliImponibiliIva());
//var_dump($myFt->cod_cliente->getVal);
printFt($myFt);
?>