<style>
a:link, a:visited, a:active  {
	opacity:1;
	text-decoration:none;
}
a:hover   {
	opacity:1;
	color:#cd6f00;
}
.n {
	color:red;
}
table,tr,td {
margin: 0px;
padding:0.4em;
border: 1px #e1e1e1 solid;
}
</style>

<?php
include ('./config.inc.php');
require_once ('./stampe/ft.php');
set_time_limit ( 0);
$html='<table>';

$test=new MyList(
	array(
		'_type'=>'Fattura',
//		'data'=>array('<>','01/08/12','31/12/12'),
		'data'=>array('<>','01/01/13','31/01/13'),
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