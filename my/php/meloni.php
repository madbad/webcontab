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
		echo '<td>'.$obj->cod_articolo->getVal().'</td>';
		echo '<td>'.$obj->colli->getFormatted(0).'</td>';
		echo '<td>'.$obj->peso_netto->getFormatted(2).'</td>';
		//if($obj->prezzo->getVal()*1>0.001){$prezzo=$obj->prezzo->getVal();}else{$prezzo='';}
		//echo '<td>'.$obj->imponibile->getVal().'</td>';
		echo '<td>'.$obj->prezzo->getFormatted(3).'</td>';
		echo '<td>'.$obj->imponibile->getVal().'</td>';
		echo '</tr>';
	};

	$stampaTotali= function ($obj){
		echo '<tr>';
		echo '<td>'.'Totali'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('colli').'</td>';
		echo '<td>'.$obj->sum('peso_netto').'</td>';
		echo '<td>'.'-'.'</td>';		
		echo '<td>'.$obj->sum('imponibile').'</td>';
		echo '</tr>';
	};

	$tabellaH='<table class="spacedTable, borderTable">';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Articolo</td><td>Colli</td><td>Peso Netto</td><td>___Prezzo___</td><td>IMPONIBILE</td>'; //<td>Imponibile Memo.</td>
	$tabellaF='</table><br><br>';

	
	
	
	
	$listaMeloni=new MyList(
		array(
			'_type'=>'Articolo',
			'descrizione'=>array('LIKE','%MELONI%'),
		)
	);
	
	$codicimeloni=array();
	$codicimeloni[]='=';
	$listaMeloni->iterate(function ($obj){
		global $codicimeloni;
		$codicimeloni[]=$obj->codice->getVal();
		//echo '<br>'.$obj->descrizione->getVal();
		//echo '<br>'.$obj->codice->getVal();
	});
	
	
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=> $codicimeloni,
			//'cod_causale'=> array('=','V'),
			//'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI'),
			//'cod_cliente'=>array('=','SALVA','MAROC','FERRN','PAROD'),
			//'cod_cliente'=>array('=','PAROD'),
			//'cod_cliente'=>array('=','PAROD'),
			//'cod_cliente'=>array('=','SEVEN'),
			//'cod_cliente'=>array('=','CALAN'),
			'cod_cliente'=>array('!=','DOROD'),
			//'prezzo'=>array('=','0.001'),
			
			
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