<?php
include ('./config.inc.php');
$test=new MyList(
	array(
		'_type'=>'Fattura',

		'data'=>array('<>','01/01/12','31/12/12'),
		'cod_cliente'=>'SEVEN'
	)
);

echo '<table>';
$test->iterate(function($obj){
	$tipo=$obj->tipo->getVal();
	if ($tipo=='N'){
		echo '<b style="color:red;">';
	}
	echo $obj->cod_cliente->getVal().' :: ';
	echo $obj->tipo->getVal().' ';
	echo $obj->numero->getVal().' :: ';
	echo $obj->data->getFormatted().' :: ';
	echo $obj->importo->getFormatted().' :: <br>';
	if ($tipo=='N'){
		echo '</b>';
	}
});