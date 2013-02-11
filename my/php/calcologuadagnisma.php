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

function findIfcoModel($art){
	global $ifco;
	$models=array_keys($ifco);
	foreach ($models as $ifcoModel){
		$found=stripos($art, $ifcoModel);
		if ($found){
			return $ifcoModel;
			break;
		}
	}
	//se non trovo niente ritorno falso
	return FALSE;
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


include "./easyODS.php";
/**
 * We unpacking the *.ods files ZIP arhive to find content.xml path
 * 
 * @var String File path of the *.ods file
 * @var String Directory path where we choose to store the unpacked files must have write permissions
 */
$path = easy_ods_read::extract_content_xml("./COSTO_BASE.ods","./temp");

/**
 * We create the $easy_ods_read object
 *  
 * @param Integer 0 First spreadsheet
 * @param String $path File path of the content.xml file
 * 
 * @return Object $easy_ods_read Object of the class
 */
$easy_ods_read = new easy_ods_read(0,$path);

/**
 * We take the needed data from the file
 */
$fileODS=$easy_ods_read->extract_data("0","366");

//sconti
$sconti['fissi']=0;
$sconti['percentuali']=12.4/100;
$sconti['collo']=0.02;
$sconti['percentualiPeriodici']=0;




	$array='';
	$stampaRighe= function ($obj){
	if($obj->cod_articolo->getVal()!=''){
		global $array;
		
		$data=$obj->ddt_data->getFormatted();
		$dayNumer=1+date('z',strtotime(str_replace('/','-',$data)));
		
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['descrizione']=$obj->descrizione->getVal();
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['colli']=$obj->colli->getFormatted();
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['peso']=$obj->peso_netto->getFormatted(2);
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['prezzo']=$obj->prezzo->getFormatted(2);
		global $ifco;
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['costoCassa']= round($obj->colli->getVal() * $ifco[findIfcoModel($obj->descrizione->getVal())]['costo'] / $obj->peso_netto->getVal(), 3);
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['nGiorno']=$dayNumer;

		}
		return;
	};

	$colonne=11;
	$tabellaH='<table>';
	$tabellaH.='<tr><td></td><td colspan="'.$colonne.'">Riccia</td><td colspan="'.$colonne.'">Scarola</td><td colspan="'.$colonne.'">Chioggia</td><td colspan="'.$colonne.'">Treviso</td><td colspan="'.$colonne.'">PDZ</td><td colspan="'.$colonne.'">Verona</td></tr>';
	$tabellaH.='<tr><td>Data</td>';
	$riga='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td><td>Costo Base</td><td>Costo Cassa</td><td>Costro Trasporto</td><td>Costo Manodopera</td><td>Costi Sconti</td><td>Costo Totale</td><td>Ricavno</td>';
	$tabellaH.=$riga.$riga.$riga.$riga.$riga.$riga;
	$tabellaH.='</tr>';
	$tabellaF='</table>';

$start=$prevKey='01/01/12';
$end='05/02/12';
	
	
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$start,$end),
			'cod_cliente'=>'SMA',
		)
	);
	$test->iterate($stampaRighe);

$guadagnoComplessivo=0;
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

		$sommaPesi='';
		foreach ($articoli as $articolo){
			if (array_key_exists($articolo,$value)){$articolo=$articolo;}
			if (array_key_exists($articolo.'-',$value)){$articolo=$articolo.'-';}
			if (array_key_exists($articolo.'--',$value)){$articolo=$articolo.'--';}
				
			if(array_key_exists($articolo,$value) ){
				@$sommaPesi[$key]+=str_replace(',','.',$value[$articolo]['peso']);
			}
		}

		$guadagnoGiornata=0;
		
		foreach ($articoli as $articolo){
			//verifico in quale dei medi è impostato l'articolo
			if (array_key_exists($articolo,$value)){$articolo=$articolo;}
			if (array_key_exists($articolo.'-',$value)){$articolo=$articolo.'-';}
			if (array_key_exists($articolo.'--',$value)){$articolo=$articolo.'--';}
				
			if(array_key_exists($articolo,$value) ){
				$link=$value[$articolo];

				echo '<td>'.$articolo.'</td>';
			//	echo '<td>'.findIfcoModel($link['descrizione']).'</td>';
				echo '<td>'.$link['colli'].'</td>';
				
				$peso=str_replace(',','.',$link['peso']);
				echo '<td>'.$peso.'</td>';
				
				$prezzo=str_replace(',','.',$link['prezzo']);
				echo '<td>'.$prezzo.'</td>';

				$giorno=$link['nGiorno']+1;
				switch ($articolo){
					case '701':   $colonna='B';$manodopera=0.35;break;
					case '703':   $colonna='C';$manodopera=0.35;break;
					case '705':   $colonna='D';$manodopera=0.40;break;
					case '705-':  $colonna='D';$manodopera=0.40;break;
					case '705--': $colonna='D';$manodopera=0.40;break;
					case '708':   $colonna='F';$manodopera=0.25;break;
					case '708-':  $colonna='F';$manodopera=0.25;break;
					case '708--': $colonna='F';$manodopera=0.25;break;
					case '729':   $colonna='E';$manodopera=0.35;break;
					case '731':   $colonna='G';$manodopera=0.20;break;
					default:      $colonna='A';$manodopera=0.0;break;
				}
				global $fileODS;
				$costoBase=str_replace(',','.',$fileODS[$giorno][$colonna]);
				echo '<td>'.$costoBase.'</td>';
				echo '<td>'.$link['costoCassa'].'</td>';
				
				$numPedane=str_replace(',','.',$fileODS[$giorno]['H']);
				$costoTrasporto=round($numPedane*31/$sommaPesi[$key],3);
				//$costoTrasporto=round($numPedane,3);
				
				echo '<td>'.$costoTrasporto.'</td>';
				echo '<td>'.$manodopera.'</td>';
				
				global $sconti;
				//$sconto=($link['colli'] * $sconti['collo'] / $link['peso']) + ($link['prezzo']*$sconti['percentuali']);
				$sconto=($link['colli'] * $sconti['collo'] / $link['peso']) + (str_replace(',','.',$link['prezzo'])*$sconti['percentuali']);
				$sconto=round($sconto, 3);
				echo '<td>'.$sconto.'</td>';
				
				
				$costoTotale=$costoBase+$link['costoCassa']+$costoTrasporto+$manodopera+$sconto;
				echo '<td>'.$costoTotale.'</td>';
				
				$guadagnoArticolo=round(($prezzo-$costoTotale)*$peso,2);
				if($guadagnoArticolo<0){$colore='red';}else{$colore='green';}
				echo '<td style="color:'.$colore.'">'.$guadagnoArticolo.'</td>';
				
				$guadagnoGiornata+=$guadagnoArticolo;
			}else{
				global $colonne;
				echo str_repeat('<td></td>',$colonne);
			}
		}

		$guadagnoComplessivo+=$guadagnoGiornata;
		echo '</tr>';
		$prevKey=$key;
	}
	echo $tabellaF;

echo 'Guadagno complessivo: '.$guadagnoComplessivo;
?>
</body>