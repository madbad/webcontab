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

$fatturato= array();

//ottengo la lista fatture
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>','02/04/2018','30/05/2018'),
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
/*
$html='<table class="borderTable spacedTable">';
$test->iterate(function($obj){
	global $html;
	global $dbClientiWithIndex;
	global $fatturato;
	
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
	$fatturato[$cliente->ragionesociale->getVal()]+=$obj->importo->getVal()*1;
});

$html.='</table>';
echo $html;
print_r($fatturato);
*/
/*
$date_start='2018-01-01';
$date_end='2018-03-31';
*/
$date_start='2017-01-01';
$date_end='2017-12-31';

$result = dbFrom('RIGHEFT', 'SELECT sum(F_IMPONI) AS IMPONIBILE, F_CODCLI', "WHERE F_DATFAT >= #".$date_start."# AND F_DATFAT <= #".$date_end."# GROUP BY F_CODCLI");
//PRINT_R($result);
function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
$sorted = array_orderby($result, 'IMPONIBILE', SORT_DESC);
echo $date_start ,' => ';
echo $date_end."<br>\n";
echo '<table>';
foreach ($sorted as $item){
	echo "<TR><td>".$item['F_CODCLI']."</td><td style='text-align:right;'>".str_replace(".",",",round($item['IMPONIBILE'],2))."</td></TR>";
}
echo '</table>';
?>

</body>
</html>
