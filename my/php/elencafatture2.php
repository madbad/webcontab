<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Elenca fatture</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

//ottengo la lista fatture
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>','01/01/2016','30/11/2016'),
	)
);

//ottengo una lista dei codici clienti 
//(mi serve per creare una cache degli oggetti cliente in modo da non dover fare poi una query per ogni singolo oggetto cliente)
$fatturato = array();
$test->iterate(function($obj){
	global $fatturato;
	$codiceCliente=$obj->cod_cliente->getVal();
	$fatturato[$codiceCliente] += $obj->getTotaleImponibile();
});
print_r($fatturato);


/*
$params=array(
	'data'=>'07/01/2016',
	'numero'=>'1',
);
$fattura = new Fattura($params);
echo $fattura->cod_cliente->getVal();

echo $fattura->getTotaleImponibile();
*/
?>

</body>
</html>
