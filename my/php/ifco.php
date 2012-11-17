
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">
		<style type="text/css" >	
		html,body,div{
			height:99%;
		}
		body{
			font-size:1em;
		}
		div{
			-moz-column-count:2; /* Firefox */
			column-count:2;
		}
		</style>
	</head>
	<body>

<?php
include ('./config.inc.php');
$elenco=array();
$totali=array();

$stampaRighe= function ($obj){
	global $elenco;
	global $totali;

	$ifco=array('IFCO 4310',
				'IFCO 4314',
				'IFCO 6410',
				'IFCO 6413',
				'IFCO 6416',
	);
	$descrizione=$obj->descrizione->getVal();

	foreach ($ifco as $ifcoModel){
		$found=stripos($obj->descrizione->getVal(), $ifcoModel);
		if ($found){
@			$elenco[$obj->ddt_data->getFormatted()][$ifcoModel]+=$obj->colli->getVal();
			//exit the foreach cicle
@			$totali['=====================sommaTotale']+=$obj->colli->getVal();
@			$totali[$ifcoModel]+=$obj->colli->getVal();
			break;
		}
	}
	//se al termine non ho ancora trovato il modello IFCO e c'è una quantità di colli maggiore di zero (ovvero non si tratta di solo testo ma di un articolo)
	//allora stampo la stringa di descrizione dell'articolo e la relativa data
	if (!$found & $obj->colli->getVal()>0){
		echo 'IFCO model not found :: '.$obj->ddt_data->getFormatted().' :: '.$obj->descrizione->getVal().'<br>';
	}
};
$params=array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','01/11/12','15/11/12'),
		'cod_cliente'=>'SMA'
	);

$test=new MyList($params);
$test->iterate($stampaRighe);

echo '<div>';
var_dump($params);
echo '===============';
echo '===============';
echo '===============';
var_dump($elenco);
echo '===============';
echo '===============';
echo '===============';
var_dump($totali);
echo '</div>';
page_end();
?>
</body>