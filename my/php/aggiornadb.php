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

//////////
//  3   //
//salva le righe ottenute nel mio database
$func = function ($obj){
	echo '<br>';
	foreach($obj as $key => $value) {
		if($key[0]=='_') continue;
		echo '<br>';
		echo $key.':';
		echo $value->getVal();
	}
};
$nuoveRighe->iterate($func);



$sqlite=$GLOBALS['config']->sqlite;
$myDbArray=array();
$table='ANAGRAFICACLIENTI';
$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
//faccio una query e stampo i risultati
$results = $db->query('SELECT * FROM '.$table);
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
	//global $myArray;
	$myDbArray[$row['codice']]=$row;
	//var_dump($row);
}




?>
</body>