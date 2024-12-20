<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

$startDateFormatted = '';
$endDateFormatted = '';
$startDate='';
$endDate='';
include ('./selettoredate.inc.php');

//posti a terra

//mostro le ddt 
$test=new MyList(
	array(
		'_type'=>'Ddt',
		//'data'=>array('<>','01/12/2023', '31/12/2023'),
		'data'=>array('<>',$startDate, $endDate),
		'cod_destinatario'=>array('=','SEVE2'),
		'cod_causale' => array('!=','D')
	)
);
echo '<table class="bordertable">';
$somma = 0;
$test->iterate(function($obj){
	echo '<tr><td>'.$obj->data->getVal().'</td>';

	echo '<td>'.$obj->numero->getVal().'</td>';
	
	echo '<td>'.$obj->note->getVal().'</td>';
	
	$bancali = preg_replace('/[^0-9]/', '', $obj->note->getVal());  

	echo '<td>'.$bancali.'</td>';
	echo '</tr>';
	global $somma;
	$somma +=$bancali;
});

echo '</table>';
echo 'Totale: '.$somma;


/*
//mostro le ddt 
$test=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','01/12/2020', '31/12/2020'),
		'cod_cliente'=>array('=','SEVEN'),
		'cod_articolo' => array('=','BSEVEN')
	)
);
echo '<table class="bordertable">';
$somma = 0;
$test->iterate(function($obj){
	echo '<tr><td>'.$obj->ddt_data->getVal().'</td>';

	echo '<td>'.$obj->ddt_numero->getVal().'</td>';
	
	echo '<td>'.$obj->colli->getVal().'</td>';
	
	$bancali = $obj->colli->getVal();  

	echo '<td>'.$bancali.'</td>';
	echo '</tr>';
	global $somma;
	$somma +=$bancali;
});

echo '</table>';
echo 'Totale: '.$somma;
*/
