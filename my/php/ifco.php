
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
include ('./core/config.inc.php');
$elenco=array();
$totali=array();

echo '<div class="columns">';

$stampaRighe= function ($obj){
	global $elenco;
	global $totali;

	$ifco=array('IFCO 4310',
				'IFCO 4314',
				'IFCO 6410',
				'IFCO 6413',
				'IFCO 6416',
				//'EURO CHEP',
	);
	$art= new Articolo(array('codice'=> $obj->cod_articolo->getVal()));
	$descrizione= $art->descrizione->getVal();
	//echo '<br>'.$descrizione;
	//$descrizione=$obj->descrizione->getVal();

	foreach ($ifco as $ifcoModel){
		//$found=stripos($obj->descrizione->getVal(), $ifcoModel);
		//echo '<br>'.$obj->cod_articolo->extend()->descrizione->getVal();
		//$art = $obj->cod_articolo->extend();
		$found=stripos($descrizione, $ifcoModel);
		if ($found){
			$name=$obj->ddt_data->getFormatted().' :: ddt '.$obj->ddt_numero->getFormatted();
@			$elenco[$name][$ifcoModel]+=$obj->colli->getVal();
			//exit the foreach cicle
@			$totali["\nsommaTotale\n------------"]+=$obj->colli->getVal();
@			$totali[$ifcoModel]+=$obj->colli->getVal();
			break;
		}
	}
	//se al termine non ho ancora trovato il modello IFCO e c'è una quantità di colli maggiore di zero (ovvero non si tratta di solo testo ma di un articolo)
	//allora stampo la stringa di descrizione dell'articolo e la relativa data
	if (!$found & $obj->colli->getVal()>0){
		echo 'IFCO model not found <br>:: ';
		echo $obj->ddt_data->getFormatted().' :: ';
		echo $descrizione;
		echo ' :: colli ';
		echo $obj->colli->getFormatted().'<br>';
	}
};
$params=array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','16/01/15','31/01/15'),
		'cod_cliente'=>array('SEVEN'),
	);

$test=new MyList($params);
$test->iterate($stampaRighe);

echo '============';
echo '============';
echo '============';
var_dump($params);
echo '============';
echo '============';
echo '============';
var_dump($elenco);
echo '============';
echo '============';
echo '============';
var_dump($totali);
echo '</div>';
page_end();
?>
</body>