<?php
include ('./config.inc.php');
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>','01/01/12','31/12/12'),
		'cod_cliente'=>'SMA'
	)
);
$test->iterate(function($obj){
	echo $obj->numero->getVal().' : ';
	echo $obj->data->getVal().'<br>';
	echo $obj->imponibile->getVal().'<br>';
	
});