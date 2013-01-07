<?php
include ('./config.inc.php');
set_time_limit ( 0);

//$separatore=";\t";
//$separatore=";";
$separatore="\t";

$startDate='01/12/2012';
$endDate='31/12/2012';
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>',$startDate,$endDate),
		'cod_cliente'=>'SMA'
	)
);

$titoli=array(
'COD.AMMINISTRATIVO'
,'SOCIETA'
,'TIPO DOC'
,'N.DOC'
,'DATA DOCUMENTO'
,'SCADENZA'
,'TOTALE FATTURA');

$html=implode($titoli, $separatore);

$elenco=array();
$test->iterate(function($obj){
	global $html;
	global $separatore;
	
	$tipo=$obj->tipo->getVal();
	$obj->getSqlDbData();
	$obj->_params['_autoExtend']='all';
	$obj->getDataFromDbCallBack();
	
	//calcolo la scadenza
	$data=$obj->data->getFormatted();
	$data = explode( '/', $data );
	$scadenza = mktime( 0, 0, 0, $data[1], $data[0] + 30, $data[2] );
	$scadenza=date ( 'd/m/Y' , $scadenza);

	
	//$html.= "<tr class='$tipo'>";

	$html.="\n".'808812';
	$html.=$separatore.'38';
	$html.=$separatore.'FATTURA'; //'NOTA CREDITO'
	$html.=$separatore.trim($obj->numero->getVal());
	$html.=$separatore.$obj->data->getFormatted();
	$html.=$separatore.$scadenza;//scadenza +30
	$html.=$separatore.$obj->importo->getFormatted(2);
});
//totali
	$totale=number_format($test->sum('importo')*1,$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.');
	$html.="\n".'TOTALE'.$separatore.$separatore.$separatore.$separatore.$separatore.$separatore.$totale;

	
$date=explode('/',$startDate);
$filename='38_808812_'.$date[2].'_'.$date[1].'.xls';
	
header("Content-Disposition: attachment;filename=$filename");
//header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Type: application/vnd.ms-excel");
//header("Content-type: application/csv"); 
echo $html;