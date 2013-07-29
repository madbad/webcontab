<?php
include ('./core/config.inc.php');
?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Chep</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>

	<body>
<?php 
$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
<form name="input" action="./chep.php?mode=print" method="get">
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
		$color=' style="color:black;" ';
	}
	
		echo '<tr '.$color.'> ';
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo '<td>'.$obj->cod_cliente->getVal().'</td>';
		echo '<td>'.$obj->colli->getVal().'</td>';				
		echo '<td>'.$obj->peso_netto->getVal().'</td>';
		echo '<td>'.$obj->prezzo->getVal().'</td>';
		echo '<td>'.round($obj->getPrezzoLordo(),3).'</td>';
		echo '<td>'.round($obj->getPrezzoNetto(),3).'</td>';
		echo '<td>'.round($obj->peso_netto->getVal()/$obj->colli->getVal(),2).'</td>';
		$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
		echo '<td>'.round($impNetto,2).'</td>';
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
		echo '<td>'.$obj->sum('peso_netto').'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.round($obj->sum('peso_netto')/$obj->sum('colli'),2).'</td>';
		echo '<td>'.$obj->sum('_totImponibileNetto').'</td>';			
		echo '</tr>';
	};

	$tabellaH='<table>';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso Netto</td><td>Prezzo</td><td>Prezzo L.</td><td>Prezzo N.</td><td>Media peso</td><td>Imponibile Calc.</td></tr>'; //<td>Imponibile Memo.</td>
	$tabellaF='</table><br><br>';

	//CONTROLLO BANCALI CHEP
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','BCHEP'),
			'cod_cliente'=>array('=','SMA'),
		)
	);

	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

	page_end();
}
?>
</body>