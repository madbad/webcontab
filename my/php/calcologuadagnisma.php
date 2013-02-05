<?php
include ('./config.inc.php');

function datediff($tipo='G', $partenza, $fine)
{
	switch ($tipo)
	{
		case "A" : $tipo = 365;
		break;
		case "M" : $tipo = (365 / 12);
		break;
		case "S" : $tipo = (365 / 52);
		break;
		case "G" : $tipo = 1;
		break;
	}
	$arr_partenza = explode("/", $partenza);
	$partenza_gg = $arr_partenza[0];
	$partenza_mm = $arr_partenza[1];
	$partenza_aa = $arr_partenza[2];
	$arr_fine = explode("/", $fine);
	$fine_gg = $arr_fine[0];
	$fine_mm = $arr_fine[1];
	$fine_aa = $arr_fine[2];
	$date_diff = mktime(12, 0, 0, $fine_mm, $fine_gg, $fine_aa) - mktime(12, 0, 0, $partenza_mm, $partenza_gg, $partenza_aa);
	$date_diff  = floor(($date_diff / 60 / 60 / 24) / $tipo);
	return $date_diff;
}

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">
<style type="text/css" media="print" />		
 form{
	display:none;
 }
</style>
		
		<style type="text/css">
@PAGE landscape {size: landscape;}
TABLE {PAGE: landscape;} 
@page rotated { size : landscape }
			body{
			
			//	column-count: 3;
			//	-moz-column-count: 3;
			//	-webkit-column-count: 3;
			//	column-rule: 2px solid black;
			//	-moz-column-rule: 2px solid black;
			//	-webkit-column-rule: 2px solid black;
			//	font-size:x-small;
			}
			table, tr, td , th{
				font-size:small;
				padding:0px;
				margin:0;
				text-align:right;
				border:1px solid #000000;
				    border-collapse: collapse;
				margin-left:0.5em;
			}
			td, th{
				padding-left:4px;
				padding-right:4px;
			}
			th{
				font-weight:bold;
				text-align:left;
			}
			hr{
				margin-top:150px;
			}
			#rimanenze td{
				height:2em;
				width:9em;
				text-align:left;
			}
			div {
				float:left;
			}
			form label{
display:block;
font-weight:bold;
width:15 em;
			}
			.totali{
			 	 font-size:1.5em;
			}
tr:nth-child(odd) { background-color: #e1e1e1;}
		</style>
	</head>

	<body>

<?php

	$array='';
	$stampaRighe= function ($obj){
	if($obj->cod_articolo->getVal()!=''){
		global $array;

		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['colli']=$obj->colli->getFormatted();
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['peso']=$obj->peso_netto->getFormatted(2);
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['prezzo']=$obj->prezzo->getFormatted(2);

		}
		return;
	};


	$tabellaH='<table>';
	$tabellaH.='<tr><td></td><td colspan="4">Riccia</td><td colspan="4">Scarola</td><td colspan="4">Chioggia</td><td colspan="4">Treviso</td><td colspan="4">PDZ</td><td colspan="4">Verona</td></tr>';
	$tabellaH.='<tr><td>Data</td>';
	$tabellaH.='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td>';
	$tabellaH.='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td>';
	$tabellaH.='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td>';
	$tabellaH.='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td>';
	$tabellaH.='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td>';
	$tabellaH.='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td>';
	$tabellaH.='</tr>';
	$tabellaF='</table>';


	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>','01/01/13','05/02/13'),
			'cod_cliente'=>'SMA',
		)
	);
	$test->iterate($stampaRighe);

$prevKey='01/01/2013';
	echo $tabellaH;
	foreach ($array as $key => $value){
		$giorniSaltati=datediff('G',$prevKey,$key);
		while($giorniSaltati>1){
			$missingDay=date('d/m/Y',strtotime(str_replace('/','-',$key).' -'.($giorniSaltati-1).' days'));
			echo '<tr><td>'.$missingDay.'</td></tr>';
			$giorniSaltati--;
		}
	
		$articoli=array('701','703','708','729','731','705');
		echo "\n".'<tr> ';		
		echo '<td>'.$key.'</td>';

		foreach ($articoli as $articolo){
			//verifico in quale dei medi è impostato l'articolo
			if (array_key_exists($articolo,$value)){$articolo=$articolo;}
			if (array_key_exists($articolo.'-',$value)){$articolo=$articolo.'-';}
			if (array_key_exists($articolo.'--',$value)){$articolo=$articolo.'--';}
				
			if(array_key_exists($articolo,$value) ){
				$link=$value[$articolo];

				echo '<td>'.$articolo.'</td>';
				echo '<td>'.$link['colli'].'</td>';
				echo '<td>'.$link['peso'].'</td>';
				echo '<td>'.$link['prezzo'].'</td>';

			}else{
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
				echo '<td></td>';
			}
		}
		echo '</tr>';
		$prevKey=$key;
	}
	echo $tabellaF;


?>
</body>