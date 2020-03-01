		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab - Uscite imballaggi</title>
		<meta charset="utf-8">
	</head>

	<body>

<?php 
$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
	<h1>Uscite imballaggi || 
	<?php echo $startDateR; ?> =>  <?php echo $endDateR; ?>
	</h1>
<form name="input" action="./bins.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<button type="submit">Search</button>
</form> 

<?php

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
		echo '<td><a href="./core/gestioneDdt.php?numero='.$obj->ddt_numero->getVal().'&data='.$obj->ddt_data->getVal().'&cod_causale=V&do=visualizza">vedi</a></td>';
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
		echo '<td>-</td>';
		echo '</tr>';
	};



//==============================================================================================================================
//selezioni i ddt emessi nel periodo selezionato
$elencoddt = new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('<>',$startDateR,$endDateR),
		'cod_causale'=>array('=','V'),
		'_select'=>'cod_destinatario'
	)
);
$clientiSelezionati = array();
$clientiScartati;
$dbClienti=getDbClienti();

//ricavo dall'elenco dei ddt i codici clienti (escludendo i mercati e supermercati)
$elencoddt->iterate(function($ddt){
	global $clientiSelezionati;
	global $clientiScartati;
	global $dbClienti;
	$codcliente=$ddt->cod_destinatario->getVal();
	
	$clientedb = $dbClienti[$codcliente];
	
	if ($clientedb['__classificazione']!='mercato' && $clientedb['__classificazione']!='supermercato'){
		//se non è ne mercato ne supermercato lo seleziono
		//echo '<BR>ok: '.$clientedb['__classificazione'].'<BR>'; 
			$clientiSelezionati[$codcliente]=$codcliente;
	}else{
		//altrimenti no
		//echo '<BR>DO NOT DO IT: '.$clientedb['__classificazione'].'<BR>'; 
		$clientiScartati.='<br>'.$codcliente.' ('.$clientedb['__classificazione'].')';
		return;
	}
	
	//global dbClienti;


});
//echo '<br>Clienti Scartati:<br>'.$clientiScartati;
//echo '<br>Clienti slezionati:';
//echo print_r($clientiSelezionati);


$clienti = new MyList(
	array(
		'_type'=>'ClienteFornitore',
		//'codice'=>array('<>',''),
		'codice'=>$clientiSelezionati,
		//'codice'=>'FACC2',
		//'_select'=>'codice'
		//'tipo'=>array('<>',''),
		//'cod_banca'=>array('!=','01','02','09','10'),//ELENCA TUTTI I CLIENTI CHE HANNO UN CODICE BANCA CHE NON è TRA LE NOSTRE CORRENTI
	)
);

//print_r($clienti);


/*
$clientiSelez[]='=';
foreach ($dbClienti as $cliente){
	if ($cliente['__classificazione']!='mercato' && $cliente['__classificazione']!='supermercato'){
		$clientiSelez[]= $cliente['codice'];
	}
}
*/

/*
$clienti=new MyList(
	array(
		'_type'=>'ClienteFornitore',
		'codice'=>$clientiSelez
	)
);
*/

$clienti->iterate(function($cliente){
global $dbClienti;
/*
	$clientedb = $dbClienti[$cliente->codice->getVal()];
	if ($clientedb['__classificazione']!='mercato' && $clientedb['__classificazione']!='supermercato'){
		//do nothing
		echo '<BR>ok: '.$clientedb['__classificazione'].'<BR>'; 
	}else{
		echo '<BR>DO NOT DO IT: '.$clientedb['__classificazione'].'<BR>'; 
		return;
	}
*/

	global $startDateR;
	global $endDateR;
	global $stampaRighe;
	global $stampaTotali;
	global $sommaBins;
	global $sommaCasse;
	global $sommaBins;
	$sommaBins=0;
	global $sommaCasse;
	$sommaCasse=0;
	$tabellaH='<table class="spacedTable, borderTable">';
	$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso Netto</td><td>Prezzo</td><td>media collo</td><td>Bins</td><td>Casse</td><td>+</td></tr>';
	$tabellaF='</table><br>';

	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_cliente'=>array('=', $cliente->codice->getVal()),
//			'cod_cliente'=>array('=', 'VOLOR'),
			'colli'=>array('!=', '0')
		)
	);

	if (count($test->arr)<1){return;}
	
	echo '<table><tr><td><b>'.$cliente->ragionesociale->getVal();
	//echo '</td><td>';
	echo '</b><br>Tel: '.$cliente->telefono->getVal();
	echo ' ### Fax: '.$cliente->fax->getVal();
	echo ' ### Cell: '.$cliente->cellulare->getVal();
	echo '</td></tr></table>';

	
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test,$sommaBins,$sommaCasse);
	echo $tabellaF;

});

	page_end();
}
?>
</body>
