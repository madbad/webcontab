<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

$today = date('d/m/Y');

$ddtodierni=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('=',$today),
		'cod_destinatario'=>array('=','SOGEG'),
		'cod_causale' => array('!=','D')
	)
);

//ottengo una lista dei codici clienti 
//(mi serve per creare una cache degli oggetti cliente in modo da non dover fare poi una query per ogni singolo oggetto cliente)

$ddtodierni->iterate(function($obj){
	global $html;
	global $dbClientiWithIndex;
/*
	$html.= $obj->numero->getVal();
	$html.= $obj->data->getFormatted();
	$html.= $obj->cod_destinazione->getVal();
	$html.= $obj->cod_destinatario->getVal();
*/
	$html= 'http://localhost/webContab/my/php/core/gestioneDdt.php?';
	$html.= 'numero='.$obj->numero->getVal();
	$html.= '&data='.$obj->data->getVal();
	$html.= '&cod_causale='.$obj->cod_causale->getVal();
	$html.= '&do=mail&force_nascondiprezzo=true';

});
Echo "<h1>Redirecting to: " . $html . "</h1>";
header("Refresh:5; $html");
?>