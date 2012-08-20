<?php
include ('./config.inc.php');

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
				font-size:2em;
				padding-left:4px;
				padding-right:4px;
				padding:0.5em;
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
$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
<form name="input" action="./meloni.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<button type="submit">Search</button>
</form> 

<?php

if (@$_GET['mode']=='print'){
	$stampaRighe= function ($obj){
		echo '<tr>';
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo '<td>'.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
		echo '<td>'.$obj->colli->getVal().'</td>';				
		echo '<td>'.$obj->peso_netto->getVal().'</td>';
		if($obj->prezzo->getVal()*1>0.001){$prezzo=$obj->prezzo->getVal();}else{$prezzo='';}
		echo '<td>'.$prezzo.'</td>';
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
		echo '</tr>';
	};

	$tabellaH='<table>';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso Netto</td><td>___Prezzo___</td>'; //<td>Imponibile Memo.</td>
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

//mercato //capucci
*/
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
//			'cod_articolo'=>array('=','11','111','112','113',
//								      '911','9111','9112','9113',
//			),
			'cod_articolo'=>array('=','501','500'),

			//'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI'),
			//'cod_cliente'=>array('=','SALVA','MAROC','FERRN','PAROD'),
			//'cod_cliente'=>array('=','MORIN'),

		//	'prezzo'=>array('=','0.001'),
			
			
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