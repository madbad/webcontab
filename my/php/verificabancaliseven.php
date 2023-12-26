<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style_print.css" media="print">

<?php
$startDateFormatted = '';
$endDateFormatted = '';

if(!array_key_exists('startDate', $_POST)){
	/*todo da sistemare*/
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];
	
}else{
	$giorno = date('d', time());
	$mese = date('m', time());
	$anno = date('Y', time());
	$giorniDelMese = cal_days_in_month(CAL_GREGORIAN,$mese,$anno); 
	
	$startDate = '01'.'/'.$mese.'/'.$anno;
	$endDate = $giorniDelMese.'/'.$mese.'/'.$anno;
	
	$startDateFormatted = $anno.'-'.$mese.'-'.'01';
	$endDateFormatted = $anno.'-'.$mese.'-'.$giorniDelMese;

}


?>

<form action="./verificabancaliseven.php" class="dateform hideOnPrint" method="post"> 
	<span class="dateformtitle">Selezione parametri</span>
	<br> <span class="dateselectordescription">From:</span>
	<input class="dateselector" type="date" id="startDate" name="startDate" value="<?php echo $startDateFormatted ?>">
	<br> <span class="dateselectordescription">to:</span>
	<input class="dateselector" type="date" id="endDate" name="endDate" value="<?php echo $endDateFormatted ?>">
	<input type="submit" value="Submit" style="padding:1em;width:20em;">
</form>



<?php
include ('./core/config.inc.php');
set_time_limit ( 0);


//posti a terra

//mostro le ddt 
$test=new MyList(
	array(
		'_type'=>'Ddt',
		//'data'=>array('<>','01/12/2023', '31/12/2023'),
		'data'=>array('<>',$startDate, $endDate),
		'cod_destinatario'=>array('=','SEVEN'),
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
