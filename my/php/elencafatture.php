<?php
include ('./config.inc.php');
$test=new MyList(
	array(
		'_type'=>'Fattura',
<<<<<<< HEAD
		'data'=>array('<>','01/08/12','31/12/12'),
		'cod_cliente'=>'SMA'
=======
		'data'=>array('<>','01/01/12','31/12/12'),
		//'cod_cliente'=>'SMA'
>>>>>>> f402c76fddaa9f70a0bfef937c73b23a42012614
	)
);

echo '<table>';
$test->iterate(function($obj){
<<<<<<< HEAD
	echo '<tr>';
	echo '<td>'.$obj->cod_cliente->getVal().'</td>';
	echo '<td>'.$obj->numero->getVal().'</td>';
	echo '<td>'.$obj->data->getVal().'</td>';
	echo '<td>'.$obj->imponibile->getVal().'</td>';
	echo '</tr>';
});
echo '</table>';
=======
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
>>>>>>> f402c76fddaa9f70a0bfef937c73b23a42012614
