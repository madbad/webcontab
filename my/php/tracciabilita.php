		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
/*
TODO: 
- RIMUOVERE IL PUNTO NEL CODICE FORNITORE
- SISTEMARE IL CASO IN CUI VIENE INDICATO IL LOTTO SOLO UNA VOLTA IN QUANTO UGUALE PER PIù ARTICOLI

*/
include ('./core/config.inc.php');
set_time_limit ( 0);

//mostro le fatture 
$test=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','21/08/2013','23/08/2013'),
	)
);
function cella ($txt){
	return "\n\t\t".'<td>'.$txt.'</td>';
}
function riga ($txt){
	return "\n\t".'<tr>'.$txt.'</tr>';
}
function tabella ($txt){
	return "\n".'<table>'.$txt.'</table>';
}

function stampaUscite ($obj){
	return riga(
		cella($obj->cod_cliente->getVal()).
		cella(' ddt '.$obj->ddt_numero->getVal()).
		cella(' del '.$obj->ddt_data->getFormatted()).
		cella($obj->descrizione->getVal()).
		cella(' colli: '.round($obj->colli->getVal())).
		cella(' kg: '.round($obj->peso_netto->getVal()))
		
	);
}

function fixDate ($dateString){
	$dateString = strtr($dateString, '.', '/');
	$mydate = explode('/', $dateString);
	
	//fix day
	if(strlen($mydate[0])==1){
		$mydate[0] = '0'.$mydate[0];
	}
	//fix month
	if(strlen($mydate[1])==1){
		$mydate[1] = '0'.$mydate[1];
	}
	//fix years
	if(strlen($mydate[2])==2){
		$mydate[2] = '20'.$mydate[2];
	}
	
	return implode($mydate, '/');
}

$prevObj = '';
$output = '';

echo '<table class="spacedTable">';
	$test->iterate(function($obj){
		//ho trovato un lotto
		//presumo che si riferisca alla riga precedente
		if (
			$obj->cod_articolo->getVal() == ''
			&& strpos($obj->descrizione->getVal(),'/P') !== false
			){
				global $prevObj;
				global $output;

				/*fix lotto description*/
				$lotto = explode('-', $obj->descrizione->getVal());
				
				$codiceFornitore = $lotto[0];
				$dataFornitura = fixDate($lotto[1]);
@				$lottoFornitura = $lotto[2];
				$articoloFornito = $prevObj->cod_articolo->getVal();
				
				$chiaveDdt = $codiceFornitore.'-'.$dataFornitura;
				$chiaveLottoProdotto = 'Prodotto: ('.$articoloFornito.') Lotto: ('. $lottoFornitura.')';
				
				$output [$chiaveDdt][$chiaveLottoProdotto][] = $prevObj;
			
			}
		global $prevObj;
		$prevObj = $obj;
	});

	
foreach ($output as $key => $ddtFornitore){
	echo '<b>Ddt: '.$key.'</b>';
	echo '<table  class="spacedTable, borderTable">';
	foreach ($ddtFornitore as $key2 => $partitaMerce){
		echo riga(cella($key2));
		foreach ($partitaMerce as $uscita){
			echo stampaUscite($uscita);
		}
	}
	echo '</table>';
}
