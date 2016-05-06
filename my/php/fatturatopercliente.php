<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Elenca fatture</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

//ottengo la lista fatture
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>','01/12/2015','30/12/2015'),
		'_autoExtend'=>'1',
		//'_select'=>'numero,data,cod_cliente,tipo' //this was a try to optimize the select statement but gives no performance increase... It is even a little bit slower
		//'cod_cliente'=>'SEVEN'
	)
);

//ottengo una lista dei codici clienti 
//(mi serve per creare una cache degli oggetti cliente in modo da non dover fare poi una query per ogni singolo oggetto cliente)
$codiciCliente =array('=');
$test->iterate(function($obj){
	global $codiciCliente;
	$codiceCliente=$obj->cod_cliente->getVal();
	$codiciCliente[$codiceCliente]= $codiceCliente;
});

//ricavo dalla lista precedente gli "oggetti" cliente
$dbClienti=new MyList(
	array(
		'_type'=>'ClienteFornitore',
		'codice'=>$codiciCliente,
	)
);

function td($txt){
	global $html;
	$html.= '<td>'.$txt.'</td>';

}
//creo un nuovo array che ha per "indice" il codice cliente in modo da rendere più semplice ritrovare "l'oggetto" cliente
$dbClientiWithIndex = array();
$dbClienti->iterate(function($myCliente){
	global $dbClientiWithIndex;
	$codcliente = $myCliente->codice->getVal();
	$dbClientiWithIndex[$codcliente]= $myCliente;
});

//stampo la lista delle fatture

$html='<table class="borderTable spacedTable">';
$test->iterate(function($obj){
	global $html;
	global $dbClientiWithIndex;
	$dataInvioPec=$obj->__datainviopec->getVal();
	
	$tipo=$obj->tipo->getVal();
	$cliente = $dbClientiWithIndex[$obj->cod_cliente->getVal()];

	$html.= '<tr>';
	td($tipo);
	td($obj->numero->getVal());
	td($obj->data->getFormatted());
	td($obj->importo->getVal());
	td($cliente->codice->getVal());
	td($cliente->ragionesociale->getVal());
	$html.= '</tr>';
});

$html.='</table>';
echo $html;
?>

</body>
</html>
