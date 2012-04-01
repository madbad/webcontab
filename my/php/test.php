<?php
include ('./config.inc.php');
require_once('./classes.php');
page_start();

$ddtList=new MyList();
$ddtList->createFromQuery();
//$ddtList->sum('numero');

$ddtList->iterate(
	function($obj){
		echo $obj->data->getVal().' ** ';
		echo $obj->numero->getVal().' ** ';		
		echo $obj->cod_destinatario->extend()->ragionesociale->getVal().'<br>';
	}
);
echo $out;
page_end();

//$wc=new WebContab();

//eseguo il setup/instllazione iniziale  di webContab sul database
//$wc->setup();

/*
$test=new Fattura();
$test->cod_cliente->setVal('GRUPP');
//$test->cod_cliente->extend();
//echo $test->cod_cliente->extend()->ragionesociale->getVal();
echo '<pre>';
print_r($test->cod_cliente->extend()->ragionesociale->getVal());
echo '</pre>';
*/

/*
$mioArticolo= new Articolo(array('codice'=>'ALBOTRANS'));
//echo $mioArticolo->descrizione->getVal();
//echo "<pre>".$mioArticolo->descrizionelunga->getVal()."</pre>";

//echo $mioArticolo->cod_iva->extend()->descrizione->getVal();
$mioArticolo->cod_iva->getDataType();
$mioArticolo->cod_iva->extend()->descrizione->getDataType();
*/

//$mio= new Ddt(array('numero'=>'908','data'=>'11-19-2008'));

//$mio= new Ddt(array('numero'=>'908','data'=>'19/11/2008'));

//echo $mioArticolo->descrizione->getVal();
//echo "<pre>".$mioArticolo->descrizionelunga->getVal()."</pre>";

//echo $mioArticolo->cod_iva->extend()->descrizione->getVal();
//$mio->data->getDataType();
//header('Content-type: application/json');
?>