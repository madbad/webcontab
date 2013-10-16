<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style_print.css" media="print">

<?php
include ('./core/config.inc.php');

set_time_limit ( 0);
$out='<table>';

$test=new MyList(
	array(
		'_type'=>'ClienteFornitore',
		//'tipo'=>array('<>',''),
		'cod_banca'=>array('!=','01','02','09','10'),/*ELENCA TUTTI I CLIENTI CHE HANNO UN CODICE BANCA CHE NON è TRA LE NOSTRE CORRENTI*/
	)
);

echo '<table style="font-size: x-small;" class="borderTable">';
//echo '<table>';


$out.=$test->iterate(function($obj){
	$out='<tr>';
	$out.='<td>'.$obj->codice->getVal().'</td>';
	$out.='<td>'.$obj->ragionesociale->getVal().'==>'.$obj->sigla_paese->getVal().'</td>';
	if($obj->cod_pagamento->getVal()!=''){
		$out.='<td>'.$obj->cod_pagamento->getVal().'</td>';
		$out.='<td>'.$obj->cod_pagamento->extend()->descrizione->getVal().'</td>';
	}else{
		$out.='<td></td>';
		$out.='<td></td>';
	
	}
	if($obj->cod_banca->getVal()!=''){
		$out.='<td>'.$obj->cod_banca->getVal().'</td>';
		$out.='<td>'.$obj->cod_banca->extend()->ragionesociale->getVal().'</td>';
	}else{
		$out.='<td></td>';
		$out.='<td></td>';
	
	}
	
	$out.='</tr>';
	echo $out;
//	return $out;
});
echo '</table>';


//echo $out;