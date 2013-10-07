		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		.customTable {
			font-size: 0.8em;
		}
		
		.customTable td:first-child { 
		/* column 1*/
			width: 4em;
		}

		.customTable td:first-child + td { 
		/* column 2*/
			width: 5em;
		
		}
		
		.customTable td:first-child + td + td { 
		/* column 3*/
			width: 8em;
		
		}
		
		.customTable td:first-child + td + td + td{ 
		/* column 4*/
			width: 18em;
		
		}
		.customTable td:first-child + td + td + td + td{ 
		/* column 5*/
			width: 6.5em;
		
		}
		.customTable td:first-child + td + td + td + td + td{ 
		/* column 6*/
			width: 5em;
		
		}
		
		.lotto {
			font-weight:bold;
			font-size:1.5em;
			padding-top:1.5em;
			
		}
		</style>
<?php
/*
TODO: 
- SISTEMARE IL CASO IN CUI VIENE INDICATO IL LOTTO SOLO UNA VOLTA IN QUANTO UGUALE PER PIù ARTICOLI
potrei ipotizzare che se trovo un lotto si riferisca a tutte le righe precedenti che non ne avevano uno nello stesso ddt (dal'ultimo trovato)

- controllare che tutte le vendite di grezzo abbiano un lotto
	potrei verificare con una query il tipo di cliente del ddt se è mercato/supermercato lascio perdere...
	altrimenti ci deve essere un lotto e ritorno un errore (potrebbe essere lento)
- sistemare il caso in cui ci siano altre annotazioni nella riga del lotto (tipo: "26 bins")
*/
include ('./core/config.inc.php');
set_time_limit ( 0 );

$test=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','04/10/2013','04/10/2013'),
	)
);
function cella ($txt, $colspan=0){
	return "\n\t\t".'<td colspan="'.$colspan.'">'.$txt.'</td>';
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

function fixArticolo ($articolo){
	$articolo = str_replace('-', '',$articolo);//remove the "-" "--"

	if (@$articolo[0]== 8 || @$articolo[0]== 7){
		$articolo = substr($articolo, 1);
	}

	return $articolo;
}

function fixDate ($dateString){
	$dateString = trim(strtr($dateString, '.', '/'));//remove spaces and replace the . with a /
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

function sortableDate ($dateString){
	$mydate = explode('/', $dateString);
	
	return $mydate[2].'-'.$mydate[1].'-'.$mydate[0];
}


$dbClienti = getDbClienti();
$prevObj = array();
$output = '';

echo '<table class="spacedTable">';
	$test->iterate(function($obj){
		global $prevObj;
		global $output;
		//ho trovato un lotto
		//presumo che si riferisca alla riga precedente
		if (
			$obj->cod_articolo->getVal() == ''
			&& strpos($obj->descrizione->getVal(),'/P') !== false
			){

				/*fix lotto description*/
				$lotto = explode('-', $obj->descrizione->getVal());
				$codiceFornitore = str_replace('.', '', $lotto[0]);
				$dataFornitura = fixDate($lotto[1]);
@				$lottoFornitura = $lotto[2];
				
				/*assegnazione del lotto alle righe in sospeso*/
				foreach ($prevObj as $curObj){
					$articoloFornito = fixArticolo($curObj->cod_articolo->getVal());
					$chiaveDdt = sortableDate($dataFornitura).'-'.$codiceFornitore;
					$chiaveLottoProdotto = 'Prodotto: ('.$articoloFornito.') Lotto: ('. $lottoFornitura.')';
					$output[$chiaveDdt][$chiaveLottoProdotto][] = $curObj;
				}
				
				//se sono arrivato qui significa che ho trovato un lotto e l'ho assegnato a tutte le righe che avenvo in sospeso... posso ripulire la coda
				$prevObj  = array();
			}else{
			//non ho trovato niente
			//se ho altre righe in sospeso e questa riga fa parte dello stesso ddt di quella che ho in sospeso
			if (count($prevObj)>0
				&& $obj->ddt_numero->getVal() == $prevObj[count($prevObj)-1]->ddt_numero->getVal()
				&& $obj->ddt_data->getVal() == $prevObj[count($prevObj)-1]->ddt_data->getVal()
				){
					//aggiugno questo alla mia coda in attesa di lotto
					//assieme agli altri
					$prevObj [] = $obj;
			}else{
				//o non avevo altre righe in sospeso : non faccio niente
				//o non erano dello stesso ddt quindi 
				//mando un avvertimento se si tratta di grezzo e non ci sono lotti per le righe precedenti
				//:: righe scartate ::
				/*todo*/
				
				//quindi procedo a ripulire la coda dal pregresso
				//aggiugno questo alla mia coda in attesa di lotto
				$prevObj  = array();
				$prevObj [] = $obj;
			}
		}
	});

//ordino l'array per data
ksort($output);

foreach ($output as $key => $ddtFornitore){
	echo '<div class="lotto">Ddt: '.$key.'</div>';
	echo '<table  class="spacedTable borderTable customTable">';
	foreach ($ddtFornitore as $key2 => $partitaMerce){
		echo riga(cella('<b>#############'.$key2.'</b>',$colspan=6));
		foreach ($partitaMerce as $uscita){
			echo stampaUscite($uscita);
		}
	}
	echo '</table>';
}
