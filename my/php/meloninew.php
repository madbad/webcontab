<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab - Meloni</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
			.tableFixHead          { overflow-y: auto; height: 100px; }
			.tableFixHead thead th { position: sticky; top: 0; }
			
			.tableFixHead td {white-space: nowrap; overflow: hidden; text-overflow:ellipsis;}

			/* Just common table stuff. Really. */
			table  { width: 100%;border-collapse: collapse;  }
			th, td { padding: 2px 4px; border:1px solid black;}
			th     { background:#eee; }
		
		</style>
	</head>

	<body>
<?php 

$startDateR="01/01/2020";
$endDateR=date("j/n/Y");

$dbCosti=
"cliente,	provvigionePerc,	provvigioneEur,	facchinaggi,	trasporto
AMAT2,		0,					0,				0,				44.5
BELFR,		13,					0,				0.01,			39.5
CALAN,		0,					0,				0,				42.5
CALIM,		13,					0,				0.01,			24.5
CAPAS,		0,					0,				0,				42.5
ORTOA,		0,					0,				0,				44.5
SEVEN,		4,					0.05,			0,				4.0
SOGEG,		0,					0.05,			0,				44.5";

$dbCasse =
"nome,				costo,		colliPerBancale,	searchString
Cartone30x50,		0.79,		112,				3050
Cartone30x40,		0.69,		170,				3040
Polymer4316,		0.53,		88,					6111-
Polymer6419,		0.72,		44,					6111
Bins,				0,			4,					nonematching
Plastica40x60,		070,		0,					4060";

$dbBancali =
"nome,				costo,
Industriale,		1.50,
EuroAPerdere,		1.50,
PlasticaSeven,		1.55";


function parseCVS ($txt){
	$righe = explode("\n",$txt);
	$out=array();
	
	$headerFields = explode(",",$righe[0]);
	
	for ($i = 1; $i < count($righe); $i++) {
		$properties=explode(",",$righe[$i]);
		for ($h = 0; $h < count($headerFields); $h++) {
			$out[trim($properties[0])][trim($headerFields[$h])] = trim($properties[$h]);
		}
	}
	return $out;
}

$dbCasse = parseCVS($dbCasse);
$dbCosti = parseCVS($dbCosti);
$dbBancali = parseCVS($dbBancali);

$stampaRighe= function ($obj){
	global $dbCosti;
	global $dbCasse;
	global $dbBancali;
	/*
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
	*/
//echo trim($obj->cod_cliente->getVal());
//print_r($dbCosti[trim($obj->cod_cliente->getVal())]);
//print_r($dbCosti);
$tipoCassa='';
$articolo = $obj->cod_articolo->getVal();
foreach ($dbCasse as $key => $modCassa){
	//echo strpos($articolo, $modCassa['searchString']);
	if(strpos($articolo, $modCassa['searchString']) !== FALSE){
		$tipoCassa = $modCassa;
		break;
	}
}

if($tipoCassa=='' && $obj->cod_cliente->getVal()=='CALAN'){
	$tipoCassa= $dbCasse['Bins'];
}
if($tipoCassa==''){
	$tipoCassa= $dbCasse['Cartone30x50'];
}

$pedaneTrasporto = $obj->colli->getVal()/$tipoCassa['colliPerBancale'];
$resto = $pedaneTrasporto-(int)$pedaneTrasporto;
//echo $resto;
if($resto > 0.5){
	$pedaneTrasporto-=$resto;
	$pedaneTrasporto+=1;
}else{
	$pedaneTrasporto-=$resto;
	if($resto>0){
		$pedaneTrasporto+=0.5;
	}
}

if($tipoBancale==''){
	$tipoBancale= $dbBancali['Industriale'];
}
if($obj->cod_cliente->getVal()=='SEVEN'){
	$tipoBancale= $dbBancali['PlasticaSeven'];
}

?>


<tr>
	<td><?php echo $obj->ddt_numero->getVal(); ?></td>
	<td><?php echo $obj->ddt_data->getVal(); ?></td>
	<td><?php echo $obj->cod_cliente->extend()->ragionesociale->getVal(); ?></td>
	<td><?php echo $obj->cod_articolo->getVal(); ?></td>
	<td><?php echo $obj->colli->getFormatted(0) ?></td>
	<td><?php echo $obj->peso_netto->getFormatted(0) ?></td>
	<td></td>
	
	<?php
	if($obj->prezzo->getVal() != '0.001'){
		echo '<td>'.$obj->peso_netto->getFormatted(0).'</td>';
		echo '<td>'.$obj->prezzo->getFormatted(3).'</td>';
	}else{
		echo'<td></td>';
		echo'<td></td>';		
	}
	?>
	<td><?php echo $dbCosti[trim($obj->cod_cliente->getVal())]['provvigionePerc']; ?></td>
	<td><?php echo $dbCosti[trim($obj->cod_cliente->getVal())]['provvigioneEur']; ?></td>
	<td><?php echo $tipoCassa['nome']; ?></td>
	<td><?php echo $tipoCassa['costo']; ?></td>	
	<td><?php echo $pedaneTrasporto; ?></td>	
	<td><?php echo $dbCosti[trim($obj->cod_cliente->getVal())]['trasporto']; ?></td>
	<td><?php echo $tipoBancale['nome']; ?></td>
	<td><?php echo $tipoBancale['costo']; ?></td>
	
	
	<?php
		//calcolo costi
		$costi =array();


		if($obj->prezzo->getVal() != '0.001'){
			$ciSonoRiscontri = true;
			$costi['cassa'] = $obj->colli->getVal() * $tipoCassa['costo'] / $obj->peso_netto->getVal();
			$costi['trasporto'] = $pedaneTrasporto * $dbCosti[trim($obj->cod_cliente->getVal())]['trasporto'] / $obj->peso_netto->getVal();
			$costi['bancale'] = $pedaneTrasporto * $tipoBancale['costo'] / $obj->peso_netto->getVal();
			$costi['provvigioneEur'] = $dbCosti[trim($obj->cod_cliente->getVal())]['provvigioneEur'];
			$costi['provvigionePerc']  = $dbCosti[trim($obj->cod_cliente->getVal())]['provvigionePerc'] * $obj->prezzo->getVal() / 100;
			$costi['facchinaggi']  = $dbCosti[trim($obj->cod_cliente->getVal())]['facchinaggi'];
			if($tipoCassa['nome']=='Bins'){
				$costi['manodopera']  = 0.03;
				$costi['angolare']  = 0.00;						
			}else{
				$costi['manodopera']  = 0.08;
				$costi['angolare']  = 0.01;		
			}
	
		}else{
			$ciSonoRiscontri = false;
		}

		$netto = $obj->prezzo->getVal() - array_sum($costi);
	?>
	
	
	<td><?php echo round($costi['cassa'],3); ?></td>
	<td><?php echo round($costi['angolare'],3); ?></td>
	<td><?php echo round($costi['trasporto'],3); ?></td>
	<td><?php echo round($costi['facchinaggi'],3); ?></td>
	<td><?php echo round($costi['provvigionePerc'],3); ?></td>
	<td><?php echo round($costi['provvigioneEur'],3); ?></td>
	<td><?php echo round($costi['manodopera'],3); ?></td>
	<td><?php echo round(array_sum($costi),3);?></td>

	<td><?php echo round($netto,3);?></td>
</tr>	
<?php
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

?>
<table class="spacedTable, borderTable, tableFixHead">

  <colgroup>
    <col span="7" style="background-color:orange">
    <col span="2" style="background-color:white">
    <col span="8" style="background-color:green">

    <col span="9" style="background-color:yellow">
  </colgroup>

  <thead>
	<tr>
		<th>DDT<BR>Numero</th>
		<th>DDT<BR>Data</th>
		<th>Cliente</th>
		<th>Articolo</th>
		<th>Colli</th>
		<th>Peso<br>Partenza</th>
		<th>Prezzo<br>Comunicato</th>
		
		<th>Peso<br>Fatturato</th>
		<th>Prezzo<br>Fatturato</th>	

		<th>Provv. %</th>
		<th>Provv. Eur</th>
		<th>Cassa<br>Tipo</th>
		<th>Cassa<br>Costo</th>	
		<th>Trasporto<br>Numero</th>		
		<th>Trasporto<br>Costo</th>		
		<th>Bancale<br>Tipo</th>
		<th>Bancale<br>Costo</th>		
		
		<th>Cassa</th>
		<th>Angolare</th>
		<th>Trasporto</th>
		<th>Facchinaggio</th>
		<th>Provv. %</th>
		<th>Provv. Eur</th>
		<th>Manodopera</th>
		<th>Totale</th>

		<th>Ricavo netto</th>
	</tr>
	</thead>
	<tbody>
<?php

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

//print_r($codicimeloni);	
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
		//'cod_cliente'=>array('=','SOGEG'),
		//'cod_cliente'=>array('=','SEVEN'),
		//'cod_cliente'=>array('=','CALAN'),
		//'cod_cliente'=>array('!=','DOROD'),
		//'cod_cliente'=>array('!=','DOROD'),
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

?>
</tbody>
</table>
</body>
