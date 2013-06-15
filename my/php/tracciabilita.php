<style>
table, td {
border:1px solid #000000;
padding: 0.3em;
font-size: 0.9em;

}

</style>

<?php
include ('./config.inc.php');
set_time_limit ( 0);

//mostro le fatture 
$test=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','11/06/2013','14/06/2013'),
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
//$html='';
$output = '';

echo '<table>';
	$test->iterate(function($obj){
/*	
			//stampa tutte le righe
			echo riga(
				cella($obj->ddt_numero->getVal()).
				cella($obj->ddt_data->getVal()).
				cella($obj->numero->getVal()).
				cella($obj->cod_articolo->getVal()).
				cella($obj->descrizione->getVal())
			);
*/
		//ho trovato un lotto
		//presumo che si riferisca alla riga precedente
		if (
			$obj->cod_articolo->getVal() == ''
			&& strpos($obj->descrizione->getVal(),'/P') !== false
			){
				global $prevObj;
//				global $html;
				global $output;
/*				
				$html.= riga(
				cella("Trovato un lotto: ").
				cella($obj->descrizione->getVal()).
				cella($prevObj->cod_cliente->getVal()).
				cella($prevObj->ddt_numero->getVal()).
				cella($prevObj->ddt_data->getVal()).
				cella($prevObj->descrizione->getVal())
				);
*/
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
/*
echo '</table>';
echo '<table>'.$html.'</table>';
*/

foreach ($output as $key => $ddtFornitore){
	echo '<b>Ddt: '.$key.'</b>';
	echo '<table>';
	foreach ($ddtFornitore as $key2 => $partitaMerce){
		echo riga(cella($key2));
		foreach ($partitaMerce as $uscita){
			echo stampaUscite($uscita);
		}
	}
	echo '</table>';
}
