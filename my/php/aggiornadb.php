		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

echo 'test';
//////////
//  1   //
//quale è l'ultimo ddt (data e numero) che ho salvato nel mio database?
$ultimoDdtData= "02/03/2016";
$ultimoDdtNumero= "430";
echo 'test2';

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
	$table="'BACKUPRIGHEDDT'";
	$query="SELECT MAX( ddt_numero )  FROM '".$table."'";
	ECHO $query;
	$sqlite=$GLOBALS['config']->sqlite;
	$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
	print_r($db->query($query));

//////////
//  3   //
//salva le righe ottenute nel mio database
$func = function ($obj){
	$keys=array();
	$values=array();
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
	$table="'BACKUPRIGHEDDT'";
	$query ='INSERT INTO '.$table.' ('.implode(",", $keys).')';
	$query .=' VALUES ('.implode(",", $values).');';
	ECHO $query;
	$sqlite=$GLOBALS['config']->sqlite;
	$myDbArray=array();
	$table='BACKUPRIGHEDDT';
	$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
	$db->query($query);
};

//$nuoveRighe->iterate($func);

?>
</body>