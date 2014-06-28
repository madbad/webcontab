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
<form name="input" action="./melonimancanti.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<button type="submit">Search</button>
</form> 

<?php
$mancanti = array();
if (@$_GET['mode']=='print'){

	$stampaRighe= function ($obj){
		global $mancanti;
	//echo '<br>'.$obj->cod_cliente->extend()->ragionesociale->getVal();
	//echo '<br>'.$obj->ddt_numero->getVal();
	$ddt = '<br>-'.$obj->ddt_numero->getVal().' '.$obj->ddt_data->getFormatted();
	if (!in_array($ddt,$mancanti)){
		$mancanti['<br><br>'.$obj->cod_cliente->extend()->ragionesociale->getVal()];
	}
	$mancanti['<br><br>'.$obj->cod_cliente->extend()->ragionesociale->getVal()][$ddt]='*';
	
		/*
		echo '<tr>';
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo '<td>'.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
		echo '<td>'.$obj->cod_articolo->getVal().'</td>';
		echo '<td>'.$obj->colli->getVal().'</td>';
		echo '<td>'.$obj->peso_netto->getVal().'</td>';
		//if($obj->prezzo->getVal()*1>0.001){$prezzo=$obj->prezzo->getVal();}else{$prezzo='';}
		//echo '<td>'.$obj->imponibile->getVal().'</td>';
		echo '<td>'.$obj->prezzo->getVal().'</td>';
		//echo '<td>'.$obj->imponibile->getVal().'</td>';
		echo '</tr>';
		*/
	};

	
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','11','911','113','111', '8111','8112','112','9112', '8111-', '9111'),
			'prezzo'=>array('=','0.001'),
		)
	);
	$test->iterate($stampaRighe);
	print_r($mancanti);
	page_end();
}
?>
</body>