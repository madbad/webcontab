<?php
include ('./core/config.inc.php');
?>

<?php
//TIPI CASSE
$imballaggio = new stdClass();
$imb = $imballaggio->{"Cartone 30x40"} =  new stdClass();
$imb->costo=0.57;
$imb = $imballaggio->{"Cartone 30x50"} =  new stdClass();
$imb->costo=0.67;
$imb = $imballaggio->{"IFCO 6416"} =  new stdClass();
$imb->costo=0.70;
$imb = $imballaggio->{"Bins a rendere"} =  new stdClass();
$imb->costo=0.0;
$imb = $imballaggio->{"Minibins Cartone"} =  new stdClass();
$imb->costo=9.0;

//TIPI BANCALI
$bancale = new stdClass();
$banc =  $imballaggio->{"Bancale INDUSTRIALE"} =  new stdClass();
$banc->costo = 3.50;
$banc =  $imballaggio->{"Bancale EURO"} =  new stdClass();
$banc->costo = 3.50;
$banc =  $imballaggio->{"Bancale SEVEN"} =  new stdClass();
$banc->costo = 3.50;

//MANODOPERA
$manodopera = new stdClass();
$mano =  $manodopera->{"LAVORAZIONE CASSETTE"} =  new stdClass();
$mano->costo = 0.08;
$mano =  $manodopera->{"LAVORAZIONE BINS"} =  new stdClass();
$mano->costo = 0.08;
$mano =  $manodopera->{"TAL QUALE BINS"} =  new stdClass();
$mano->costo = 0.02;

//
$dbClienti=getDbClienti();


//calcolo dei costi
function calcolaCosti($riga){
	$cliente = $dbClienti[$riga->cod_cliente];
	//costotrasporto
	//provvigionemercato
	//provvigioneterzi
	$costo = new stdclass();
	$costo->imballaggio = $imballaggio->{$riga->imballaggio}->costo * $riga->colli / $riga->peso;
	$costo->trasporto = $riga->trasportopedanenumero * $cliente->costotrasporto / $riga->peso;
	$costo->manodopera = $mandodopera->{$riga->manodopera}->costo;
	$costo->bancale =  $riga->bancaletipo * $riga->bancalenumero / $riga->peso;
	$costo->provvigionemercato = $riga->prezzo * $riga->provvigionemercato;
	$costo->provvigioneterzi = $riga->prezzo * $riga->provvigionemercato;/*todo*/
}


//
function saveToDbMeloni($obj){
	//copy some data in the right place
	$obj->ddt_riga = $obj->numero;
	$obj->prezzopartenza = $obj->prezzo;
	
	//remove some non used fields
	$remove='numero,importo_iva,importo_totale,peso_lordo,cod_iva,stato,_dbName,_dbIndex,peso_netto,_totImponibileNetto,_params,unita_misura,descrizione,prezzo,imponibile';
	$fieldstoberemoved = explode(',', $remove);
	foreach ($fieldstoberemoved as $field){
		unset($obj->$field);
	}

	$fields=array();
//print_r($obj);
	//salva i dati nel database sqLite
	foreach ($obj as $field =>$value){
		$fields[]= $field;
	}
//print_r($fields);
	
	$sqlite=$GLOBALS['config']->sqlite;
	$table='uscite';
	$indexes=array();
	//$indexes[]='id';
	//elenco di tutti i campi da aggiornare
	$fields= array_merge ($fields, $indexes);
//print_r($fields);
	//creo l'elenco di tutti i valori da memorizzare
	$values=array();
	foreach ($fields as $field){
		$val=$obj->$field->valore;
		$values[]=(string) $obj->$field->valore;
		/*
		if($val=='' && in_array($field, $indexes)){
			//abortisco una delle chiavi primarie è nulla: non posso salvare nel database (e comunque non avrebbe senso farlo)
			return;
		}
		*/
	}
	//aggiungo le '' per evitare che il testo venga trattato numericamente
	$values=implode($values,"','");
	$values="'".$values."'";

	//apro il database
	$db = new SQLite3($sqlite->dir.'/meloni.sqlite3');
	//creo la tabella
	//to fix : letto su internet che se vado ad aggiornare una riga com esempio solo 3 campi su quattro il campo che non vado ad aggiornare in questo momento con i nuovi valori viene resettato al valore di default o messo a null
	$query="INSERT OR REPLACE INTO $table (".implode($fields,',').") VALUES ($values)";

	//echo $query.'<br>';
	$db->exec($query) or die($query);
	return;
	}



//ricerca righe meloni
$righevenditecontab=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','01/01/10','14/05/13'),
		'cod_articolo'=>array('=','11','111','112','113',
								  '8111','8112','8111-',
								  '911', '9111', '9112'),
		'_autoextend'=>-1,
		'_select'=>'numero,ddt_data,ddt_numero,peso_netto,cod_articolo,cod_cliente,colli,prezzo',
	)
);

$elenca = function($riga, $righe){
//print_r($riga);
	global $righe;
	$fields = explode(',', 'numero,ddt_data,ddt_numero,peso_netto,cod_articolo,cod_cliente,colli,prezzo');
	if($righe==''){
		$righe .='<tr>';
		foreach ($riga as $name =>$value){
			if(in_array($name, $fields)){
				$righe .='<td>'.$name.'</td>';
			}
		}
		$righe .='</tr>';
	}

	$righe .='<tr>';
	foreach ($riga as $name =>$value){
		if(in_array($name, $fields)){
			$righe .='<td>'.$riga->$name->valore.'</td>';
		}
	}
	$righe .='</tr>';
//saveToDbMeloni($riga);
	//echo $riga->toJson();
};

//$righevenditecontab->iterate($stampaRighe);
$righe='';
$righevenditecontab->iterate($elenca, $righe);
page_end();
?>

<!DOCTYPE HTML>
<html lang="IT">
    <head>
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
		
		<!--ExtJs-->
		<script src="./../js/ext.js/ext-all.js" type="text/javascript"></script>

		<link href="./../js/ext.js/resources/css/ext-all.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
		Ext.require(['*']);
		</script>
 		<script src="./../js/ext.js/locale/ext-lang-it.js" type="text/javascript"></script>

		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
    </head>
     <body>

<table class="borderTable">

<?php 
echo $righe; 
?>

</table>
</body>