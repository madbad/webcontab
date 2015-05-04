		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">
	</head>

	<body>
<?php
$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
<form name="input" action="./riscontri.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<button type="submit">Search</button>
</form>

<?php

if (@$_GET['mode']=='print'){
	$stampaRighe= function ($obj){
	$color='';
	if ($obj->prezzo->getVal()=='0.001'){
		$color=' style="background-color:red;color:white;" ';
	}

		echo '<tr '.$color.'> ';
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
//		echo '<td>'.$obj->cod_cliente->getVal().' # '.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
		echo '<td>'.$obj->cod_cliente->getVal().'</td>';
		echo '<td>'.$obj->colli->getFormatted(0).'</td>';
		if($obj->peso_lordo->getVal() != $obj->peso_netto->getVal()){$warningPeso='<span style="color:orange"><b>*****</b></span>';}else{$warningPeso='';};
		echo '<td>'.$obj->peso_lordo->getFormatted(2).'</td>';
		echo '<td>'.$obj->peso_netto->getFormatted(2).$warningPeso.'</td>';

		echo '<td>'.$obj->prezzo->getFormatted(3).'</td>';
		$number = number_format($obj->getPrezzoLordo(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo '<td>'.$number.'</td>';
		$number = number_format($obj->getPrezzoNetto(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo '<td>'.$number.'</td>';
		$number = number_format($obj->peso_netto->getVal()/$obj->colli->getVal(),2,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo '<td>'.$number.'</td>';
		$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
		$number = number_format($impNetto,2,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo '<td>'.$number.'</td>';
		$obj->_totImponibileNetto->setVal($impNetto);
		//echo '<td>'.$obj->imponibile->getVal().'</td>';
		echo '</tr>';
	};
	$stampaTotali= function ($obj){
		echo '<tr>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('colli').'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('peso_netto').'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.round($obj->sum('peso_netto')/$obj->sum('colli'),2).'</td>';
		echo '<td>'.$obj->sum('_totImponibileNetto').'</td>';
		echo '</tr>';
	};

	$tabellaH='<table class="spacedTable, borderTable">';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso lordo<td>Peso netto</td><td>Prezzo</td><td>Prezzo L.</td><td>Prezzo N.</td><td>Media peso</td><td>Imponibile Calc.</td></tr>'; //<td>Imponibile Memo.</td>
	$tabellaF='</table><br><br>';

//==============================================================================================================================
/*
echo '<h1>'.$startDateR.'</h1><hr>';

//martinelli
	echo '<h1>Martinelli</h1>';
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

	
//ortom
	echo '<h1>Ortomercato</h1>';
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
	
//sisa
	echo '<h1>Sisa</h1>';
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'05',
			'cod_cliente'=>'SISA',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

//mercato
	echo '<h1>Mercato</h1>';
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','05'),
			'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SISA'),
			//'prezzo'=>array('!=','0.001')
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

//II
	echo '<h1>Mercato II</h1>';
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

//==============================================================================================================================
/*
19 CAPPUCCI
36 SEDANO
37 CATALOGNA
20 VERZE
21 CAVOLFIORI
39 LATTUGA
40 GENTILE
41 GENTILE ROSSA
42 PORRI
43 CIPOLLOTTI
45 BIANCO
47 ZUCCHINE
49 MELANZANE
56 MELANZANE VIOLA
50 ZUCCA
52 CETRIOLI
60 PEPERONE
*/

	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			//'cod_articolo'=>array('!=','BSEVEN'),
			'cod_articolo'=>array('=','05'),
			//'cod_cliente'=>array('!=','VIOLA','MARTI'),
			//'cod_cliente'=>array('!=','MARTI','VIOLA'),
			//'cod_cliente'=>array('!=','MARTI','LAME2','MORAN','TESI'),
			'cod_cliente'=>array('=','FACCG'),
			//'cod_destinatario'=>array('=','RAVEN'),
			//'colli'=>array('!=','0'),
			//'prezzo'=>array('!=','0.001')
		)
	);
	var_dump($test->_params['cod_articolo']);
	var_dump($test->_params['ddt_data']);
	echo '<table><tr><td style="background-color:red;color:white;" >------</td><td>= manca ricavo</td></tr></table>';
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

//==============================================================================================================================
/*
//SOLO TUTTI I MERCATI
$dbClienti=getDbClienti();
$mercati[]='=';
foreach ($dbClienti as $cliente){
//print_r($cliente);
	if ($cliente['__classificazione']=='mercato'){
		$mercati[]= $cliente['codice'];
	}
}

	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','01','03'),
			//'cod_cliente'=>array('=','MORAN','TESI','ZANAR','GIAC1','NERIO'),
			'cod_cliente'=>$mercati,
			//'cod_cliente'=>array('=','MARTI'),
			//'cod_cliente'=>array('!=','MARTI','LAME2','MORAN','TESI'),
			'prezzo'=>array('!=','0.001')
		)
	);
	var_dump($test->_params['cod_articolo']);
	var_dump($test->_params['ddt_data']);
	echo '<table><tr><td style="background-color:red;color:white;" >------</td><td>= manca ricavo</td></tr></table>';
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;
*/
//==============================================================================================================================
/*
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
		//	'cod_articolo'=>array('=','11','111','112','113',
		//						      '911','9111','9112','9113','05'
		//	),
			'cod_articolo'=>array('=','18','19'),
			'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI'),
			//'prezzo'=>array('!=','0.001'),
			
		//	'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI')
			//'cod_articolo'=>array('=','11','111'),
			//'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI')
		)
	);	
*/
//==============================================================================================================================

/*
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','20'),
			'cod_cliente'=>array('!=','VIOLA'),
			//'prezzo'=>array('!=','0.001'),
		)
	);
	echo '<pre style="font-size:x-small;">';
	print_r($test->_params);
	echo '</pre>';
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;
*/
	page_end();
}
?>
<table>
<tr>
<td>
<table style="font-size:1.2em;" class="borderTable spacedTable">
	<tr>
		<td></td>
		<td>Colli</td>
		<td>_Peso_</td>
	</tr>
	<tr>
		<td>Rim.Iniziale</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Entrate</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Uscite</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Rim.Finale</td>
		<td></td>
		<td></td>
	</tr>
</table>
</td>
<td>
<table style="font-size:1.2em;" class="borderTable spacedTable">
		<tr><td>Ricavo Lordo</td><td width="100px"></td></tr>
		<tr><td>cassa</td><td></td></tr>
		<tr><td>trasporto</td><td></td></tr>
		<tr><td>provvigione</td><td></td></tr>
		<tr><td>provvigione</td><td></td></tr>
		<tr><td>manodopera</td><td></td></tr>
		<tr><td><b>netto<b></td><td></td></tr>
		<tr><td>prezzo pagabile</td><td></td></tr>
</table>
</td>
</tr>
</table>
</body>