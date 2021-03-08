		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab RISCONTRI SEVEN</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		body{
			color: black;
			font-size: 12px;
			font-family: tahoma,arial,verdana,sans-serif;
		}
		</style>
	</head>

	<body>
<?php


	$stampaRighe= function ($obj){
		$color='';
		if ($obj->prezzo->getVal()=='0.001'){
			$color=' style="background-color:red;color:white;" ';
		}

		echo '<tr '.$color.'> ';
		
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo '<td>'.$obj->cod_articolo->getVal().'</td>';
//		echo '<td>'.$obj->cod_cliente->getVal().' # '.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
		echo '<td>'.$obj->cod_cliente->getVal().'</td>';
		echo '<td>'.$obj->colli->getFormatted(0).'</td>';
		if($obj->peso_lordo->getVal() != $obj->peso_netto->getVal()){$warningPeso='<span style="color:orange"><b>*****</b></span>';}else{$warningPeso='';};
		echo '<td>'.$obj->peso_lordo->getFormatted(2).'</td>';
		echo '<td>'.$obj->peso_netto->getFormatted(2).$warningPeso.'</td>';

		echo '<td>'.$obj->prezzo->getFormatted(3).'</td>';
//		$number = number_format($obj->getPrezzoLordo(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
//		echo '<td>'.$number.'</td>';
//		$number = number_format($obj->getPrezzoNetto(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
//		echo '<td>'.$number.'</td>';
//		$number = number_format($obj->peso_netto->getVal()/$obj->colli->getVal(),2,$separatoreDecimali=',',$separatoreMigliaia='.');
//		echo '<td>'.$number.'</td>';
		$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
		$number = number_format($impNetto,2,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo '<td>'.$number.'</td>';
		$obj->_totImponibileNetto->setVal($impNetto);
		//echo '<td>'.$obj->imponibile->getVal().'</td>';
		echo '</tr>';
	};
	$stampaTotali= function ($obj){
		echo '<tr>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';		
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('colli').'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('peso_netto').'</td>';
//		echo '<td>'.'-'.'</td>';
//		echo '<td>'.'-'.'</td>';
		echo '<td>'.round($obj->sum('_totImponibileNetto')/$obj->sum('peso_netto'),4).'</td>'; //media del prezzo
//		echo '<td>'.round($obj->sum('peso_netto')/$obj->sum('colli'),2).'</td>';
		echo '<td>'.$obj->sum('_totImponibileNetto').'</td>';
		echo '</tr>';
	};
	$calcolaImponibileNetto= function ($obj){
		$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
		$obj->_totImponibileNetto->setVal($impNetto);
	};


	$tabellaH='<table class="spacedTable, borderTable">';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Art.</td><td>Cliente</td><td>Colli</td><td>Peso lordo<td>Peso netto</td><td>Prezzo</td><td>Imponibile Calc.</td></tr>'; //<td>Imponibile Memo.</td>
	$tabellaF='</table><br><br>';


/*prodotti normali*/

$parametriRicerca = array(
	'_type'=>'Riga',
	'ddt_data'=>array('<>','01/06/20','28/02/21'),
	'cod_cliente'=>array('=','SEVEN'),
	'cod_articolo'=>array('!=','631','631+','631FLOW','631FLOW6','631FLOW6+'),
	'prezzo'=>array('!=','0.001','0.000'),

);
echo '<pre>';
print_r($parametriRicerca['ddt_data']);
echo '</pre>';
echo '<h1>SFUSO</h1>';
echo $tabellaH;
$test=new MyList($parametriRicerca);
$test->iterate($stampaRighe);
$stampaTotali($test);
echo $tabellaF;			



/*flowpack*/
$parametriRicerca = array(
	'_type'=>'Riga',
	'ddt_data'=>array('<>','01/06/20','28/02/21'),
	'cod_cliente'=>array('=','SEVEN'),
	'cod_articolo'=>array('=','631','631+','631FLOW','631FLOW6','631FLOW6+'),
	'prezzo'=>array('!=','0.001','0.000'),

);
echo '<h1>CONFEZIONATO</h1>';
echo $tabellaH;
$test=new MyList($parametriRicerca);
$test->iterate($stampaRighe);
$stampaTotali($test);
echo $tabellaF;			

page_end();

?>
</body>
