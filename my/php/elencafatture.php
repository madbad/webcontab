<style>
a:link, a:visited, a:active  {
	opacity:1;
	text-decoration:none;
}
a:hover   {
	opacity:1;
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
		'data'=>array('<>','01/01/12','31/12/12'),
		//'cod_cliente'=>'SEVEN'
	)
);

$elenco=array();
$test->iterate(function($obj){
	global $html;
	$tipo=$obj->tipo->getVal();
	$html.= "<tr class='$tipo'>";

	$html.= '<td>'.$obj->tipo->getVal().'</td>';
	$html.= '<td>'.$obj->numero->getVal().'</td>';
	$html.= '<td>'.$obj->data->getFormatted().'</td>';
	$html.= '<td>'.$obj->importo->getFormatted().'</td>';
	$html.= '<td>'.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
	
	$link= '<td><a href="./gestioneFatture.php?';
	$link.= 'numero='.$obj->numero->getVal();
	$link.= '&data='.$obj->data->getVal();
	$link.= '&tipo='.$obj->tipo->getVal();
	
	//mail
	$html.= $link.'&do=inviaPec">Invia Mail</a></td>';

	//visulizza
	$html.= $link.'&do=visualizza">Visualizza</a> '.$obj->cod_cliente->extend()->__pec->getVal().'</td>';
	
	$html.="</tr>\n";
//	$html.= '<td><a href=""><img src="./img/printer.svg" alt="Stampa" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/pdf.svg" alt="Visualizza PDF" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/email.svg" alt="Invia PEC" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/ok.svg" alt="Stato: OK" width="30px"></a></td>';

	
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