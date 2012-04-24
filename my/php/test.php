<?php
include ('./config.inc.php');
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

/*
$mioDDT= new Ddt(array('numero'=>'908','data'=>'11-19-2008'));
header('Content-type: application/json');
echo $mioDDT->toJson();
*/
//mercato

/*
	$test=new MyList(
		array(
			'_type'=>'Ddt',
			'data'=>'05/01/2011',
		)
	);
	$test->iterate(function($obj){
		echo $obj->numero->getVal().' : ';
		echo $obj->data->getVal().'<br>';
	});
*/

$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']['pathToDbFiles'].";Exclusive=NO;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=false;"; //DELETTED=1??
$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_IF_NEEDED ) or die('Non riesco a connettermi al database:<br>'.$GLOBALS['config']['pathToDbFiles'].'<br><br>Dns:<br>'.$dns);
//$query='SELECT * FROM 03BOTESD.DBF WHERE F_DATBOL=#01-05-2011# ORDER BY F_NUMBOL,F_DATBOL';
//$query='SELECT * FROM 03BOTESD.DBF';
$query='SELECT TOP 2 * FROM 03BOTESD.DBF WHERE F_NUMBOL=\'      794\'';
$result = odbc_exec($odbc, $query) or die (odbc_errormsg().'<br><br>La query da eseguire era:<br>'.$query);
		while($record = odbc_fetch_array($result)){
			print_r($record);
		}



page_end();
?>