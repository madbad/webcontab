		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		body{
		font-family: "Arial", sans-serif;
		}
		
		td{
		font-family: "Arial", sans-serif;
		padding:0.15em;
		font-size: 0.9em;
		}
		</style>
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
//==============================================================================================================================
/*
19 CAPPUCCI
36 SEDANO
20 VERZE
21 CAVOLFIORI
40 GENTILE
42 PORRI
43 CIPOLLOTTI
45 BIANCO
47 ZUCCHINE
49 MELANZANE
50 ZUCCA
*/
$dataIniziale='17/01/2015';
$dataFinale='28/01/2015';
$cliente="CAPAS";
$imponibile=0;

$oCliente = new ClienteFornitore(
			array(
			'codice'=>$cliente,
			)
			);
echo '<b><big>'.$oCliente->ragionesociale->getVal().'</big></b>';
echo '<br>'.$oCliente->via->getVal();
echo ' '.$oCliente->paese->getVal();
echo ' ('.$oCliente->citta->getVal().')';
echo '<br>Partita IVA: '.$oCliente->p_iva->getVal();
echo '<br>Codice Fiscale: '.$oCliente->cod_fiscale->getVal();
echo '<br>Telefono: '.$oCliente->telefono->getVal();
echo '<br>Fax: '.$oCliente->fax->getVal();
echo '<br><br>';

	$ddtlist=new MyList(
		array(
			'_type'=>'Ddt',
			'data'=>array('<>',$dataIniziale,$dataFinale),
			'cod_destinatario'=>array('=',$cliente),
		)
	);

	echo '<table class="spacedTable, borderTable">';
	echo "<tr>";
		echo "<td>Articolo</td>";
		echo "<td>Colli</td>";
		echo "<td>Peso netto</td>";
		echo "<td>Prezzo netto</td>";
		echo "<td>Prezzo lordo</td>";
	echo "</tr>";
	$ddtlist->iterate(function($obj){
		
		$righe = new MyList(
			array(
			'_type'=>'Riga',
			'ddt_data'=>$obj->data->getVal(),
			'ddt_numero'=>$obj->numero->getVal()
			)
		);
		echo "<tr><td colspan='5'><b>DDT ".$obj->numero->getVal()." DEL ".$obj->data->getFormatted()."</b></td></tr>";
		$righe->iterate(function($obj){
			global $imponibile;
			$imponibile += $obj->imponibile->getVal();
			//evito le righe vuote
			if($obj->peso_netto->getVal()==0){return;}
			//se manca l'articolo
			if ($obj->cod_articolo->getVal()!=''){
				$descrizione = $obj->cod_articolo->extend()->descrizione->getVal();
			}else{
				$descrizione ='****';
			}

			$cssRight=" style='text-align:right;' ";
			echo "<tr>";
				echo "<td>".$descrizione."</td>";
				echo "<td $cssRight>".number_format($obj->colli->getVal(),0)."</td>";
				echo "<td $cssRight>".number_format($obj->peso_netto->getVal(),1)."</td>";
				//echo "<td $cssRight>".round($obj->getPrezzoNetto(),3)."</td>";
				echo "<td $cssRight>"."</td>";
				echo "<td $cssRight>".number_format($obj->getPrezzoLordo(),3)."</td>";
			//	echo "<td $cssRight>".number_format($obj->getPrezzoLordo()*$obj->peso_netto->getVal(),3)."</td>";
			echo "</tr>";
		});
		echo "<tr><td colspan='5'></tr>";
	});
	echo "</table>";
	echo '<b>Totale imponibile: '.number_format($imponibile,2).'</b>';
	page_end();

?>
</body>