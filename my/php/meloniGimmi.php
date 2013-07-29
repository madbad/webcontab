<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
	</head>

	<body>
<?php 
$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
<form name="input" action="./meloniGimmi.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<button type="submit">Search</button>
</form> 

<?php

if (@$_GET['mode']=='print'){
	$stampaRighe= function ($obj){
		$dbClienti=getDbClienti();
		$codCliente=$obj->cod_cliente->getVal();
		$provvigione=$dbClienti["$codCliente"]['__provvigione'];
	
	
		echo '<tr>';
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo '<td>'.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
		echo '<td>'.$obj->colli->getVal().'</td>';				
		echo '<td>'.$obj->peso_netto->getVal().'</td>';
		echo '<td></td>'; //Costo meloni
		echo '<td></td>'; //Manodopera (&euro; 0,04)
		echo '<td></td>'; //Imballaggio (&euro; 0,13)
		echo '<td></td>'; //bancale angolari etc (&euro; 0,13)
		echo '<td></td>'; //Trasporto (&euro; 0,05)
		echo '<td valign="top" style=" padding: 0px;"><span style="font-size:0.8em">('.$provvigione.'%)</span></td>'; //Provvigione
		echo '<td></td>'; //Facchinaggio (&euro; 0,015)
		echo '<td></td>'; //Totale

		if($obj->prezzo->getVal()*1>0.001){$prezzo=$obj->prezzo->getVal();}else{$prezzo='';}
		echo '<td>'.$prezzo.'</td>'; //Ricavo
		
		//echo '<td>'.$obj->imponibile->getVal().'</td>';			
		echo '</tr>';
	};
	$stampaTotali= function ($obj){
		echo '<tr>';
		echo '<td>'.'Totali'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('colli').'</td>';				
		echo '<td>'.$obj->sum('peso_netto').'</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '<td>-</td>';
		echo '</tr>';
	};

	$tabellaH='<table>';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso Netto</td><td>Costo Meloni</td><td>Manod. <br>(&euro; 0,04)</td><td>Imballo <br>(&euro; 0,13)</td><td>Bancale angolari etc</td><td>Trasporto <br>(&euro; 0,05)</td><td>Provvigione</td><td>Facchinaggi (&euro; 0,02)</td><td>__Tot.Costo__</td><td>___Ricavo___</td>';
	$tabellaF='</table><br><br>';
/*
//martinelli
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'05',
			'cod_cliente'=>'MARTI',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

//mercato
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'05',
			'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA')
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;
	
//ortom
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'805',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;		
//sma
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'705',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;	
//II
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'905',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;
*/
//mercato //capucci
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','11','111','112','113','8111','8112','8113'),
			//'cod_articolo'=>array('=','11','111'),
			//'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI')
		)
	);
	echo '<h1>PARTENZE DAL '.$startDateR;
	echo 'AL '.$endDateR.'</h1>';
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;
	
	page_end();
}
?>
</body>