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
<form name="input" action="./bins.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<button type="submit">Search</button>
</form> 

<?php
$sommaBins=0;
$sommaCasse=0;
if (@$_GET['mode']=='print'){
	$stampaRighe= function ($obj){
		global $sommaBins, $sommaCasse;
		$mediaCollo = round($obj->peso_netto->getVal()/$obj->colli->getVal(),0);
		
		if($mediaCollo*1>30){
			$bins= round($obj->colli->getVal(),0);
			$casse= '';
			$sommaBins+=$bins;
		}else{
			$bins= '';
			$casse= round($obj->colli->getVal(),0);
			$sommaCasse+=$casse;
		}
		//escludo i colli troppo piccoli (casse a perdere);
		if($mediaCollo*1<100){
			//continue;
		}
	
		echo '<tr> ';
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo '<td>'.$obj->cod_cliente->getVal().'</td>';
		echo '<td>'.round($obj->colli->getVal(),0).'</td>';
		echo '<td>'.round($obj->peso_netto->getVal(),2).'</td>';
		echo '<td>'.$obj->prezzo->getVal().'</td>';
		echo '<td>'.$mediaCollo.'</td>';
		echo '<td>'.$bins.'</td>';
		echo '<td>'.$casse.'</td>';
		echo '</tr>';
	};
	$stampaTotali= function ($obj,$sommaBins,$sommaCasse){
		echo '<tr>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td><b>'.$obj->sum('colli').'</b></td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td><b>'.$sommaBins.'</b></td>';
		echo '<td><b>'.$sommaCasse.'</b></td>';
		echo '</tr>';
	};

	$tabellaH='<table class="spacedTable, borderTable">';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso Netto</td><td>Prezzo</td><td>media collo</td><td>Bins</td><td>Casse</td></tr>';
	$tabellaF='</table><br><br>';

//==============================================================================================================================

$clienti=new MyList(
	array(
		'_type'=>'ClienteFornitore',
		//'tipo'=>array('<>',''),
		'cod_banca'=>array('!=','00000'),/*ELENCA TUTTI I CLIENTI CHE HANNO UN CODICE BANCA CHE NON è TRA LE NOSTRE CORRENTI*/
	)
);

$dbClienti=getDbClienti();

$clienti->iterate(function($cliente){
	global $dbClienti;
	global $startDateR;
	global $endDateR;
	global $stampaRighe;
	global $stampaTotali;
	global $sommaBins;
	global $sommaCasse;
	global $$tabellaH;
	global $$tabellaF;

	$tipoCliente=$dbClienti[$cliente->codice->getVal()]['__classificazione'];
	if($tipoCliente=='mercato'){return;}

	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_cliente'=>array('=', $cliente->codice->getVal()),
			'colli'=>array('!=', '0')
		)
	);
	echo 'count:'.count($test); 
	if (count($test)<1){return;}
	
	echo '<b>USCITE IMBALLAGGI <br>';
	//$cliente= new ClienteFornitore(array('codice'=>$cliente));
	echo $cliente->ragionesociale->getVal();
	echo '</b>';
	echo '<br>Fax: '.$cliente->fax->getVal();
	echo '<br>Tel: '.$cliente->telefono->getVal();
	echo '<br>Cell: '.$cliente->cellulare->getVal();
	
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test,$sommaBins,$sommaCasse);
	echo $tabellaF;

});

	page_end();
}
?>
</body>