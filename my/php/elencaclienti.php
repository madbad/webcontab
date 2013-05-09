<style>
a:link, a:visited, a:active  {
	opacity:1;
	text-decoration:none;
}
a:hover   {
	opacity:1;
	color:#cd6f00;
}
.n {
	color:red;
}
table,tr,td {
margin: 0px;
padding:0.0em;
border: 1px #e1e1e1 solid;
}
</style>

<?php
include ('./config.inc.php');

set_time_limit ( 0);
$out='<table>';

$test=new MyList(
	array(
		'_type'=>'ClienteFornitore',
		'tipo'=>array('<>','')
	)
);

echo '<table style="font-size: x-small;">';
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