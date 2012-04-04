<?php
include ('./config.inc.php');
require_once('./classes.php');
page_start();

/*
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
*/



//$wc=new WebContab();

//eseguo il setup/instllazione iniziale  di webContab sul database
//$wc->setup();

/*
$params = array("_autoExtend" => -1);

$test=new Ddt($params);
$test->cod_destinatario->setVal('GRUPP');
echo $test->cod_destinatario->extend()->ragionesociale->getVal();
*/


$mioDDT= new Ddt(array('numero'=>'908','data'=>'11-19-2008'));

header('Content-type: application/json');
echo $mioDDT->toJson();
page_end();
?>