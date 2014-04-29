<link rel="stylesheet" type="text/css" href="style.css">
<?php
include ('./core/config.inc.php');


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
	</head>

	<body>

<?php


include "./libs/easyODS.php";
/**
 * We unpacking the *.ods files ZIP arhive to find content.xml path
 * 
 * @var String File path of the *.ods file
 * @var String Directory path where we choose to store the unpacked files must have write permissions
 */
$path = easy_ods_read::extract_content_xml("./dati/COSTO_BASE.ods","./temp");

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
$sconti['percentuali']=12.9/100;
$sconti['collo']=0.022;
$sconti['percentualiPeriodici']=0;
$costo_trasporto = 31;




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
	$tabellaH='<table class="borderTable">';
	$tabellaH.='<tr><td></td><td colspan="'.$colonne.'">Riccia</td><td colspan="'.$colonne.'">Scarola</td><td colspan="'.$colonne.'">Chioggia</td><td colspan="'.$colonne.'">Treviso</td><td colspan="'.$colonne.'">PDZ</td><td colspan="'.$colonne.'">Verona</td></tr>';
	$tabellaH.='<tr><td>Data</td>';
	$riga='<td>Art.</td><td>Colli</td><td>Peso</td><td>Prezzo</td><td>Costo Base</td><td>Costo Cassa</td><td>Costro Trasporto</td><td>Costo Manodopera</td><td>Costi Sconti</td><td>Costo Totale</td><td>Ricavno</td>';
	$tabellaH.=$riga.$riga.$riga.$riga.$riga.$riga;
	$tabellaH.='</tr>';
	$tabellaF='</table>';

$start=$prevKey='01/01/14';
$end='31/12/14';
	
	
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
					case '701':   $colonna='B';$cmanodopera='J';break;
					case '703':   $colonna='C';$cmanodopera='K';break;
					case '705':   $colonna='D';$cmanodopera='L';break;
					case '705-':  $colonna='D';$cmanodopera='L';break;
					case '705--': $colonna='D';$cmanodopera='L';break;
					case '708':   $colonna='F';$cmanodopera='N';break;
					case '708-':  $colonna='F';$cmanodopera='N';break;
					case '708--': $colonna='F';$cmanodopera='N';break;
					case '729':   $colonna='E';$cmanodopera='M';break;
					case '729-':   $colonna='E';$cmanodopera='M';break;
					case '731':   $colonna='G';$cmanodopera='O';break;
					default:      $colonna='A';$cmanodopera=0.000001;break;
				}
				
				
				global $fileODS;
				$costoBase=str_replace(',','.',$fileODS[$giorno][$colonna]);
				echo '<td>'.$costoBase.'</td>';
				echo '<td>'.$link['costoCassa'].'</td>';
				
				$manodopera = str_replace(',','.',$fileODS[$giorno][$cmanodopera]);
				
				$numPedane=str_replace(',','.',$fileODS[$giorno]['H']);
				$costoTrasporto=round($numPedane*$costo_trasporto/$sommaPesi[$key],3);
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