<?php
include ('./config.inc.php');
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>','01/08/12','31/12/12'),
		'cod_cliente'=>'SMA'
	)
);

echo '<table>';
$test->iterate(function($obj){
	echo '<tr>';
	echo '<td>'.$obj->cod_cliente->getVal().'</td>';
	echo '<td>'.$obj->numero->getVal().'</td>';
	echo '<td>'.$obj->data->getVal().'</td>';
	echo '<td>'.$obj->imponibile->getVal().'</td>';
	echo '</tr>';
});
echo '</table>';