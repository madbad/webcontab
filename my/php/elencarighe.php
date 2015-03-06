		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Elenca Righe</title>
		<meta charset="utf-8">
	</head>

	<body>
<?php
$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
<form name="input" action="./elencarighe.php?mode=print" method="get">
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
		echo '<td>'.$obj->cod_cliente->getVal().'</td>';
		echo '<td>'.$obj->cod_articolo->getVal().'</td>';
		echo '<td>'.$obj->colli->getFormatted(0).'</td>';
		echo '<td>'.$obj->peso_netto->getFormatted(2).$warningPeso.'</td>';
		echo '<td>'.$obj->prezzo->getFormatted(3).'</td>';
		echo '</tr>';
	};


	$tabellaH='<table class="spacedTable, borderTable">';
	$tabellaH.= 
	'<tr>
	<td>Numero</td>
	<td>Data</td>
	<td>Cliente</td>
	<td>Articolo</td>
	<td>Colli</td>
	<td>Peso </td>
	<td>Prezzo</td>
	</tr>';

	
	$tabellaF='</table><br><br>';

//==============================================================================================================================

	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			//'cod_articolo'=>array('=','842'),
			'cod_cliente'=>array('=','SISA'),
			//'prezzo'=>array('!=','0.001')
		)
	);
	var_dump($test->_params['cod_articolo']);
	var_dump($test->_params['ddt_data']);
	echo '<table><tr><td style="background-color:red;color:white;" >------</td><td>= manca ricavo</td></tr></table>';
	echo $tabellaH;
	$test->iterate($stampaRighe);
	echo $tabellaF;

	page_end();
}
?>
</body>