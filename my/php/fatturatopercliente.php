<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Elenca fatture</title>
	<link rel="stylesheet" type="text/css" href="style.css">

	<style>
	.dateform{
		border: 1px solid #00a3f5;
	padding: 0.2em;
		
	}
	.dateformtitle{
		background-color: #00a3f5;
		width:100%;
		padding:0.5em;	
		display:inline-block;
		font-size:1.5em;
	}
	.dateselector{
		padding:0.3em;	
		font-size:1.2em;	
		
	}
	.dateselectordescription{
		width:10em;
		display:inline-block;
		padding:0.8em;	
	}
	dateselectorcheckbox{
		font-size:2em;
		padding:0.3em;
		transform: scale(2);
		
	}
	</style>


</head>
<body>

<form action="./fatturatopercliente.php" class="dateform hideOnPrint" method="post"> 
	<span class="dateformtitle">Selezione parametri</span>

	<br> <span class="dateselectordescription">From:</span>
	<input class="dateselector" type="date" id="startDate" name="startDate" value="<?php echo $_POST['startDate']; ?>">

	<br> <span class="dateselectordescription">to:</span>
	<input class="dateselector" type="date" id="endDate" name="endDate" value="<?php echo $_POST['endDate']; ?>">

	<input type="submit" value="Submit" style="padding:1em;width:20em;">
</form>


<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

$startDate= $_POST['startDate'];
$endDate= $_POST['endDate'];
/*
$startDate= '01/01/2020';
$endDate= '31/08/2020';
*/

//ricavo dalla lista precedente gli "oggetti" cliente
$dbClienti=new MyList(
	array(
		'_type'=>'ClienteFornitore',
		'codice'=>array('!=',''),
	)
);

//creo un nuovo array che ha per "indice" il codice cliente in modo da rendere pi� semplice ritrovare "l'oggetto" cliente
$dbClientiWithIndex = array();
$dbClienti->iterate(function($myCliente){
	global $dbClientiWithIndex;
	$codcliente = $myCliente->codice->getVal();
	$dbClientiWithIndex[$codcliente]= $myCliente;
});
//print_r($dbClientiWithIndex);



$fatturato= array();

//ottengo la lista fatture
$test=new MyList(
	array(
		'_type'=>'Fattura',
//		'data'=>array('<>','01/01/2018','30/06/2018'),
		'data'=>array('<>',$startDate,$endDate),
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
/*
//creo un nuovo array che ha per "indice" il codice cliente in modo da rendere pi� semplice ritrovare "l'oggetto" cliente
$dbClientiWithIndex = array();
$dbClienti->iterate(function($myCliente){
	global $dbClientiWithIndex;
	$codcliente = $myCliente->codice->getVal();
	$dbClientiWithIndex[$codcliente]= $myCliente;
});
*/
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
/*
$date_start='2018-01-01';
$date_end='2018-06-30';
*/
$date_start=$startDate;
$date_end=$endDate;


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
echo '<table class="borderTable">';
$totaleFatturatoPeriodo = 0;
foreach ($sorted as $item){
	global $dbClientiWithIndex;
	global $totaleFatturatoPeriodo;
$cli=$dbClientiWithIndex[$item['F_CODCLI']];

//print_r($cli);
	$totaleFatturatoPeriodo+= $item['IMPONIBILE'];
//	echo "<TR><td>".$item['F_CODCLI']."</td><td style='text-align:right;'>".str_replace(".",",",round($item['IMPONIBILE'],2))."</td></TR>";
	$formatted = number_format(round($item['IMPONIBILE'],2),$decimali,$separatoreDecimali=',',$separatoreMigliaia='.');
	echo "<TR><td>".$item['F_CODCLI']."</td><td style='text-align:right;'>".$formatted."</td>";
	echo "<td>".$cli->ragionesociale->getVal()."</td>";
	echo "<td>".$cli->cod_iva->getVal()."</td>";
	echo "<td>".$cli->via->getVal()."</td>";	
	echo "<td>".$cli->paese->getVal()."</td>";	
	echo "<td>".$cli->citta->getVal()."</td>";	
	echo "<td>".$cli->p_iva->getVal()."</td>";
//	echo "<td>".$cli->cod_fiscale->getVal()."</td>";
//	echo "<td>".$cli->sigla_paese->getVal()."**</td>";
	echo "</tr>";
}
echo '</table>';
$totaleFatturatoPeriodo = number_format(round($totaleFatturatoPeriodo,2),$decimali,$separatoreDecimali=',',$separatoreMigliaia='.');

echo 'Totale:'.$totaleFatturatoPeriodo;
?>

</body>
</html>
