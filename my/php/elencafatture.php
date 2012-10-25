<style>
a:link    {color:green;}
a:visited {color:green;}
a:hover   {color:red;}
a:active  {color:yellow;} 
</style>

<?php
include ('./config.inc.php');
require_once ('./stampe/ft.php');
set_time_limit ( 0);

$test=new MyList(
	array(
		'_type'=>'Fattura',

		'data'=>array('<>','01/01/12','31/12/12'),
		//'cod_cliente'=>'SEVEN'
	)
);

echo '<pre>';
$elenco=array();
$test->iterate(function($obj){
	
	$tipo=$obj->tipo->getVal();
	if ($tipo=='N'){
		echo '<b style="color:red;">';
	}
	//link per le fatture
	echo '<a href="./stampafattura.php?';
	echo 'numero='.$obj->numero->getVal();
	echo '&data='.$obj->data->getVal();
	echo '&tipo='.$obj->tipo->getVal();
	echo '">';
	
	echo $obj->tipo->getVal().' ';
	echo $obj->numero->getVal().' :: ';
	echo $obj->data->getFormatted().' :: ';
	echo $obj->importo->getFormatted().' :: ';
	echo $obj->cod_cliente->getVal().' :: <br>';
	if ($tipo=='N'){
		echo '</b>';
	}
	echo '</a>';
	
/****************************/

$params=array(
	'numero' => $obj->numero->getVal(),
	'data'   => $obj->data->getVal(),
	'tipo'  => $obj->tipo->getVal()
);
$myFt= new Fattura($params);
printFt($myFt);	
/***************************/



/*
	global $elenco;
	$cliente=$obj->cod_cliente->extend()->ragionesociale->getVal();
	$elenco[$cliente]='*'.$obj->cod_cliente->getVal().'*'.$obj->cod_cliente->extend()->p_iva->getVal().' <br>';
	//array_push($elenco,$obj->cod_cliente->extend()->ragionesociale->getVal());
*/
});
echo '</pre>';
print_r($elenco);