		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">

<?php
include ('./core/config.inc.php');
set_time_limit ( 0);
$html='<table class="spacedTable">';

$test=new MyList(
	array(
		'_type'=>'Fattura',
//		'data'=>array('<>','01/08/12','31/12/12'),
		'data'=>array('<>','01/01/12','31/08/13'),
		'cod_cliente'=>'SMA'
	)
);

$elenco=array();
$test->iterate(function($obj){
	global $html;
	$tipo=$obj->tipo->getVal();
	$obj->getSqlDbData();
	$obj->_params['_autoExtend']='all';
	$obj->getDataFromDbCallBack();
	
	$html.= "<tr class='$tipo'>";

	$cliente=$obj->cod_cliente->extend();

	$html.= '<td>'.$cliente->ragionesociale->getVal().'</td>';	
	$html.= '<td>'.$obj->tipo->getVal().'</td>';
	$html.= '<td>'.$obj->numero->getVal().'</td>';
	$html.= '<td>'.$obj->data->getFormatted().'</td>';
	$html.= '<td>'.$obj->importo->getFormatted(2).'</td>';

	$html.="</tr>\n";


	
/****************************/

$params=array(
	'numero' => $obj->numero->getVal(),
	'data'   => $obj->data->getVal(),
	'tipo'  => $obj->tipo->getVal()
);

/*
	global $elenco;
	$cliente=$obj->cod_cliente->extend()->ragionesociale->getVal();
	$elenco[$cliente]='*'.$obj->cod_cliente->getVal().'*'.$obj->cod_cliente->extend()->p_iva->getVal().' <br>';
	//array_push($elenco,$obj->cod_cliente->extend()->ragionesociale->getVal());
*/
});
$html.='</table>';
echo $html;