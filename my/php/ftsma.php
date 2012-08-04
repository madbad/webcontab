<?php
include ('./config.inc.php');
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>','01/01/09','01/01/11'),
		'cod_cliente'=>'SMA'
	)
);
$test->iterate(function($obj){
	echo $obj->numero->getVal().' : ';
	echo $obj->data->getVal().'<br>';
});