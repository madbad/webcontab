		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');


/////////////////////////////////////////////////////////////////////////////////////////////
/*
SUL CAMBIO ANNO
MODIFICARE 			A RIGA 23 L'ANNO
E DECOMMENTARE 		SU RIGA 40-41 
MODIFICANDO CON I DATI RELATIVI AL NUOVO ANNO
*///ESEGUIR UNA VOLTA LA COPIA DEI DATI E POI RICOMMENTARE LE RIGHE 40-41
/////////////////////////////////////////////////////////////////////////////////////////////


//////////
//  1   //
//quale è l'ultimo ddt (data e numero) che ho salvato nel mio database?
$table="BACKUPRIGHEDDT";
echo $GLOBALS['config']->sqlite->dir.'/myDb.sqlite3'; 
//select the max date from this year = 2018
$query="SELECT MAX( ddt_data ) as DataUltimoDdt  FROM '".$table."' WHERE ddt_data LIKE '%-2023'"; 
$db = new SQLite3($GLOBALS['config']->sqlite->dir.'/myDb.sqlite3');
$result = $db->query($query);
$ultimoDdtData = '';
$ultimoDdtNumero = '';
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
	$ultimoDdtData = $row['DataUltimoDdt'];
}

//select the max ddt
$query="SELECT MAX( ddt_numero ) as NumeroUltimoDdt  FROM '".$table."' WHERE ddt_data ='".$ultimoDdtData."'" ;
$db = new SQLite3($GLOBALS['config']->sqlite->dir.'/myDb.sqlite3');
$result = $db->query($query);
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
	$ultimoDdtNumero =  $row['NumeroUltimoDdt'];
	}

//$ultimoDdtData= "01/10/2023";
//$ultimoDdtNumero= "0";

echo "\n<br>Ultimo ddt memorizzato è il";
echo "\n<br>N. ".$ultimoDdtNumero." del (mm-gg-aaa) ".$ultimoDdtData;
//////////
//  2   //
//ottieni le righe di tutti i ddt successivi al mio
$nuoveRighe=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('>=',$ultimoDdtData),
		'ddt_numero'=>array('>',$ultimoDdtNumero),
	)
);

//////////
//  3   //
//salva le righe ottenute nel mio database
$func = function ($obj){
	$keys=array();
	$values=array();
	//escape string for sqlite query
	$obj->cod_cliente->setVal(SQLite3::escapeString($obj->cod_cliente->getVal()));
	
	foreach($obj as $key => $value) {
		if($key[0]=='_') continue;
		//echo '**';
		/*
		echo '<br>';
		echo $key.':';
		echo $value->getVal();
		*/
		$keys[]="'".$key."'";
		$values[]="'".$value->getVal()."'";
	}
	echo "\n<br>".'<br>Sto salvando il ddt ';
	echo "\n<br>"."N. ".$obj->ddt_numero->getVal()." del (mm-gg-aaa) ".$obj->ddt_data->getVal()." riga ". $obj->numero->getVal();

	$table="'BACKUPRIGHEDDT'";
	$query ='INSERT INTO '.$table.' ('.implode(",", $keys).')';
	$query .=' VALUES ('.implode(",", $values).');';
	//ECHO $query;
	$sqlite=$GLOBALS['config']->sqlite;
	$myDbArray=array();
	$table='BACKUPRIGHEDDT';
	$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
	$db->query($query);

};
echo "\n<br>Sono state selezionate righe: ". (count($nuoveRighe)-1);
$nuoveRighe->iterate($func);

?>
</body>
