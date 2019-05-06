<?php
include ('./core/config.inc.php');

?>
<?php 
$today = date("j/n/Y"); 
$startDate='01/04/2019';
$endDate='30/04/2019';

$mancanti = array();

$stampaRighe= function ($obj){
	global $mancanti;
//echo '<br>'.$obj->cod_cliente->extend()->ragionesociale->getVal();
//echo '<br>'.$obj->ddt_numero->getVal();
$ddt = '<br>- n.'.$obj->ddt_numero->getVal().' del '.$obj->ddt_data->getFormatted();
if (!in_array($ddt,$mancanti)){
	$mancanti['<br><br>'.$obj->cod_cliente->extend()->ragionesociale->getVal()];
}
$mancanti['<br><br>'.$obj->cod_cliente->extend()->ragionesociale->getVal()][$ddt]='*';

	/*
	echo '<tr>';
	echo '<td>'.$obj->ddt_numero->getVal().'</td>';
	echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
	echo '<td>'.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
	echo '<td>'.$obj->cod_articolo->getVal().'</td>';
	echo '<td>'.$obj->colli->getVal().'</td>';
	echo '<td>'.$obj->peso_netto->getVal().'</td>';
	//if($obj->prezzo->getVal()*1>0.001){$prezzo=$obj->prezzo->getVal();}else{$prezzo='';}
	//echo '<td>'.$obj->imponibile->getVal().'</td>';
	echo '<td>'.$obj->prezzo->getVal().'</td>';
	//echo '<td>'.$obj->imponibile->getVal().'</td>';
	echo '</tr>';
	*/
};

$test=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>',$startDate,$endDate),
		'cod_articolo'=>array('=','01','03','05','31', '08'),
		'prezzo'=>array('=','0.001'),
	)
);
$test->iterate($stampaRighe);


foreach ($mancanti as $clienteKey => $clienteValue){
	echo '<br><br>'.$clienteKey;
	foreach ($clienteValue as $ddtKey => $ddtValue){
		if(strlen($ddtKey)>2){
			echo $ddtKey.'';
		}
	}
}
//print_r($mancanti);
page_end();

?>
