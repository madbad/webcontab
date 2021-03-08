		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		body{
		font-family: "Arial", sans-serif;
		}
		
		td{
		font-family: "Arial", sans-serif;
		padding:0.15em;
		font-size: 0.9em;
		}
		</style>
<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">
	</head>
	<div class="fixedTopRightBar">
		<form action="./lordi2.php" method="post">
			<label>Cliente</label><input name="cliente" type="text" value="<?php echo $_POST['cliente']; ?>" autofocus>
			<br><label>dal</label><input name="dal" type="text" value="<?php echo $_POST['dal']; ?>">
			<br><label>al</label><input name="al" type="text" value="<?php echo $_POST['al']; ?>">
			<br><label> DebugPrezzi  </label> <input class="dateselectorcheckbox" type="checkbox" name="debugPrezzi" <?php if(@$_POST['debugPrezzi']){echo 'checked';}?>>
			<br><input type="submit" value="invia">
		</form>
	</div>

	<body>
<?php
//==============================================================================================================================
/*
19 CAPPUCCI
36 SEDANO
20 VERZE
21 CAVOLFIORI
40 GENTILE
42 PORRI
43 CIPOLLOTTI
45 BIANCO
47 ZUCCHINE
49 MELANZANE
50 ZUCCA
*/
//OVERRIDE DEFAULT CONFIG
//$config->pathToDbFiles= 'F:/CONTAB';

$prodotti=array();


//print_r($_POST);
$dataIniziale=$_POST['dal'];
$dataFinale=$_POST['al'];
//$cliente=str_replace("'", "\'",$_POST['cliente']);
$cliente=$_POST['cliente'];


//$dataIniziale='28/04/2015';
//$dataFinale='28/04/2015';
//$cliente="FTESI";

$imponibile=0;
$colliddt=0;
$pesoddtRiscontrato=0;
$pesoddtPartenza=0;
$pesoddtPartenzaTot=0;
$colliricavo=0;
$pesoricavo=0;

$oCliente = new ClienteFornitore(
			array(
			'codice'=>$cliente,
			)
			);
echo '<b><big>'.$oCliente->ragionesociale->getVal().'</big></b>';
echo '<br>'.$oCliente->via->getVal();
echo ' '.$oCliente->paese->getVal();
echo ' ('.$oCliente->citta->getVal().')';
echo '<br>Partita IVA: '.$oCliente->p_iva->getVal();
echo '<br>Codice Fiscale: '.$oCliente->cod_fiscale->getVal();
echo '<br>Telefono: '.$oCliente->telefono->getVal();
echo '<br>Fax: '.$oCliente->fax->getVal();
echo '<br><br>';

	//select from dbf
	$ddtlist=new MyList(
		array(
			'_type'=>'Ddt',
			'data'=>array('<>',$dataIniziale,$dataFinale),
			'cod_destinatario'=>array('=',$cliente),
			//'data'=>array('=',$dataIniziale,$dataFinale),
			)
	);
	function filterArrayByPartialKeyMatch ($myarray, $partialKey){
		$array = $myarray;
		foreach (array_keys($array) as $key) {
			if (!preg_match('/'.$partialKey.'/', $key)) {
				unset($array[$key]);
			}
		}
		return $array;
	}
	
	
	function fixDateForSql($date){
		$data = explode("/",$date);
		$giorno = $data[0];
		$mese = $data[1];
		$anno = $data[2];
		if(strlen($anno)){$anno = '20'.$anno;}
		return "$mese-$giorno-$anno";
	}
	//filter an array based n partial string match
	function filterArray($array, $string){
		$array = array_filter_key($array, function($key) {
			return strpos($key, $string) === 0;
		});
		return $array;
	}
	
	//select from sqlite
	$table="";
	$query ="SELECT * FROM 'BACKUPRIGHEDDT'
			WHERE ddt_data >= '".fixDateForSql($dataIniziale)."'
			and ddt_data <= '".fixDateForSql($dataFinale)."'
			and cod_cliente = '".SQLite3::escapeString($cliente)."';";
	
	//echo $query;
	$sqlite=$GLOBALS['config']->sqlite;
	$table='BACKUPRIGHEDDT';
	$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
	//echo $query;
	$result = $db->query($query);
	$sqlResult = array();
	//echo "***";
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
	//echo "***|";
		$key =trim($row['ddt_numero'])."#".trim($row['ddt_data'])."#".trim($row['numero'])."#".trim($row['cod_articolo']);
		//echo '<br> ('.$key.')';
		$sqlResult[$key] =  $row;
	}

	//print_r($sqlResult);
//print_r(filterArrayByPartialKeyMatch($sqlResult, '3080#11-08-2014#15.0#820'));

	echo '<table class="spacedTable, borderTable">';
	echo "<tr>";
		echo "<td>Articolo</td>";
		echo "<td>Colli</td>";
		echo "<td>Peso netto<br>Partenza</td>";
		echo "<td>Peso netto<br>Riscontrato</td>";
		echo "<td>Differenza</td>";
		echo "<td>Prezzo netto</td>";
		echo "<td>Prezzo lordo</td>";
	echo "</tr>";
	$ddtlist->iterate(function($obj){
		global $colliddt;
		global $pesoddtRiscontrato;
		global $colliricavo;
		global $pesoricavo;
		global $sqlResult;
		global $pesoddtPartenza;
		global $pesoddtPartenzaTot;

		
		$pesoddtPartenza=0;
		
		
		$righe = new MyList(
			array(
			'_type'=>'Riga',
			'ddt_data'=>$obj->data->getVal(),
			//'cod_articolo'=>array('!=','100'),
			'ddt_numero'=>$obj->numero->getVal()
			)
		);
		echo "\n<tr><td colspan='5'><b>DDT ".$obj->numero->getVal()." DEL ".$obj->data->getFormatted()."</b></td></tr>";
		$colliddt=0;
		$pesoddtRiscontrato=0;
		$righe->iterate(function($obj){
			global $imponibile;
			global $colliddt;
			global $pesoddtRiscontrato;
			global $sqlResult;
			global $pesoddtPartenza;
			global $prodotti;

			$css ='';

			$key =trim($obj->ddt_numero->getVal())."#".$obj->ddt_data->getVal()."#".$obj->numero->getVal()."#".$obj->cod_articolo->getVal();
			//print_r($sqlResult);
			if(array_key_exists($key, $sqlResult)){
				//echo "<br>found (".$key.")";
				$partenza = $sqlResult[$key];
				
				//remove this element from the array
				unset($sqlResult[$key]);
				$pesoddtPartenza += $partenza['peso_netto'];
				$differenzaPesoKg = $obj->peso_netto->getVal()-$partenza['peso_netto'];
				$differenzaPesoPercentuale = $differenzaPesoKg * 100 /$partenza['peso_netto'];
				
			}else{
				//echo "<br>NOT found (".$key.")";
				$partenza = array();
				$differenzaPesoKg = "";
				$differenzaPesoPercentuale = "";
				
				$css = "background-color:lightgreen";
			}
			//print_r($partenza);
			
			
			$imponibile += $obj->imponibile->getVal();
			
			//colcolo totale peso e colli del ddt //escludendo le righe senza colli (albotrans assolve)
			if($obj->colli->getVal()>0){
				$colliddt+= $obj->colli->getVal();
				$pesoddtRiscontrato+= $obj->peso_netto->getVal();
			}
			
			//evito le righe vuote
			if($obj->peso_netto->getVal()==0){return;}
			//se manca l'articolo
			if ($obj->cod_articolo->getVal()!=''){
				$descrizione = $obj->cod_articolo->extend()->descrizione->getVal();
			}else{
				$descrizione ='****';
			}

			$cssRight=" style='text-align:right;$css' ";
			$css = " style='$css' ";

			echo "<tr>";
				echo "<td $css>";
				/*todo add more like piccolo etc*/
				$outDescr = str_replace(' CAT.II','<span style="background-color:black;color:white"> CAT.II </span>',$descrizione); 
				$outDescr = str_replace(' PICCOLO ','<span style="background-color:black;color:white"> PICCOLO </span>',$outDescr); 
				$outDescr = str_replace(' 12 E 15 PZ ','<span style="background-color:black;color:white"> 12 E 15 PZ </span>',$outDescr); 
				$outDescr = str_replace(' 15 PZ ','<span style="background-color:black;color:white"> 15 PZ </span>',$outDescr); 
				$outDescr = str_replace(' 18 PZ','<span style="background-color:black;color:white"> 18 PZ</span>',$outDescr); 
				$outDescr = str_replace(' APERTA ','<span style="background-color:black;color:white"> APERTA </span>',$outDescr); 
				$outDescr = str_replace(' GROSSO ','<span style="background-color:black;color:white"> GROSSO </span>',$outDescr); 
				$outDescr = str_replace(' FLOWPACK ','<span style="background-color:black;color:white"> FLOWPACK </span>',$outDescr); 

				echo  $outDescr;
				echo '</td>';
				//echo $descrizione."</td>";
				echo "<td $cssRight $css>".number_format($obj->colli->getVal(),0,',','')."</td>";
				echo "<td $cssRight $css>".number_format($partenza['peso_netto'],1,',','')."</td>";
				echo "<td $cssRight $css>".number_format($obj->peso_netto->getVal(),1,',','')."</td>";
				echo "<td $cssRight $css>".number_format($differenzaPesoKg,1,',','')." (".round($differenzaPesoPercentuale,0)."%)</td>";
				if (TRUE){
					echo "<td $cssRight>".number_format($obj->getPrezzoNetto(),3,',','')."</td>";
					//echo "<td $cssRight>".round($obj->getPrezzoNetto()*$obj->peso_netto->getVal(),3)."</td>";
				}else{
					echo "<td $cssRight $css>"."</td>";
				}
				echo "<td $cssRight $css>".number_format($obj->getPrezzoLordo(),3,',','')."</td>";
				//echo "<td $cssRight>".number_format($obj->getPrezzoNetto(),3,',','')."</td>";
				if ($_POST['debugPrezzi']){
					echo "<td $cssRight>".number_format($obj->getPrezzoNetto()*$obj->peso_netto->getVal(),3,',','')."</td>";
				}				

				
				//echo "<td $cssRight>".number_format($obj->getPrezzoLordo()*$obj->peso_netto->getVal(),3)."</td>";
				//echo "<td $cssRight>".number_format($obj->getPrezzoNetto()*$obj->peso_netto->getVal(),3)."</td>";
				$codArt = $obj->cod_articolo->getVal();
				if(!array_key_exists($codArt,$prodotti)){
					$prodotti[$codArt]['descrizione']= $descrizione;
					$prodotti[$codArt]['peso']= 0;
					$prodotti[$codArt]['importo']= 0;
				}
				$prodotti[$codArt]['peso'] += $obj->peso_netto->getVal();
				$prodotti[$codArt]['importo'] +=	$obj->getPrezzoLordo() * $obj->peso_netto->getVal();	
//echo $codArt.'->'.$prodotti[$codArt]['peso']."<br>";
//echo $codArt.'->'.$prodotti[$codArt]['importo']."<br>";
//echo $codArt = $prodotti[$codArt]['peso']."<br>";$obj->peso_netto->getVal()
			echo "</tr>";
		});
		echo "<tr style='color:grey'><td colspan='7'>Colli Riscontrati: $colliddt";
		echo " ## Peso: $pesoddtPartenza => ";
		echo " $pesoddtRiscontrato ##";
		echo " (".($pesoddtPartenza -$pesoddtRiscontrato).") ##";
		echo " (".round(($pesoddtRiscontrato - $pesoddtPartenza)/$colliddt,2)."/kg-collo) ";
		echo " (".round(($pesoddtRiscontrato - $pesoddtPartenza)*100 / $pesoddtPartenza,0)."%) ";
		
		
		$partialkey = trim($obj->numero->getVal())."#".$obj->data->getVal();
		$delettedRows = filterArrayByPartialKeyMatch($sqlResult, $partialkey);
		//echo count($delettedRows)-1;
		
		echo "</td></tr>";
		$colliricavo+=$colliddt;
		$pesoricavo+=$pesoddtRiscontrato;
		$pesoddtPartenzaTot += $pesoddtPartenza;
		
	});
	echo "</table>";
	echo '<table><tr><td>';
	echo "\n<b>Totale imponibile: ".number_format($imponibile,2).'</b>';
	echo "\n<br>Totale colli: $colliricavo";
	echo "\n<br>Totale peso Partenza: ";
	echo "\n<br>Totale peso Riscontrato: $pesoricavo";
	echo "\n<br>------------------------";
	echo "\n<br>Totale peso mancante: ".($pesoddtPartenzaTot-$pesoricavo);
	echo "\n<br>Percentuale: ".round(($pesoddtPartenzaTot-$pesoricavo)*100/$pesoddtPartenzaTot,0)."%";
	echo "\n<br>Al collo: ".round(($pesoddtPartenzaTot-$pesoricavo)/$colliricavo,2)."kg.";
	
	echo '</td><td>';
	echo '<BR><BR><CENTER><B>- - - M E D I E - - -</B></CENTER>';
	echo '<table class="borderTable" WIDTH="100%">';
	echo	'
		<tr>
		<td>Articolo</td>
		<td>Peso</td>
		<td>Media Prezzo (lordo)</td>
		</tr>
	';
	foreach($prodotti as $prodotto){
		if($prodotto['descrizione']=='****'){continue;}

		
		echo '<tr><td>';
		echo "\n".$prodotto['descrizione'];
		echo '</td><td STYLE="text-align:right">';
		echo $prodotto['peso'].' KG';
		echo '</td><td STYLE="text-align:right">';
		echo number_format($prodotto['importo'] / $prodotto['peso'],2,',','').' EUR';
		//echo ": ".$prodotto['importo'];
		//echo " :::  ".$prodotto['peso'];
		echo '</td></tr>';

	}
	echo '</table>';


	echo '</tr></table>';
	page_end();

?>
</body>
