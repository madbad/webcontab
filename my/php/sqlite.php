<?php
include ('./config.inc.php');
$sqlite=$GLOBALS['config']->sqlite;

/*#############################################################
	ATTENZIONE QUESTO FILE MI E' SERVITO SOLO PER POPOLARE IL DATABASE SQLITE
	DA ALCUNI FILE DI TESTO CHE AVEVO IO
	PROBABILMENTE NON VERRA PIU UTILIZZATO
*/
function leggiFile(){
	$db=array();
	
	//leggo il file 
	$news=fopen("./dbClienti.txt","r");  //apre il file
	while (!feof($news)) {
		$buffer = fgets($news, 4096);
		$arr=explode(', ',$buffer);
		$codCliente= sqlite_escape_string(trim($arr[0]));
		$tipoCliente=trim($arr[2]);
		$provvigione=trim($arr[3]);
		$db["$codCliente"]['codice']=$codCliente;
		$db["$codCliente"]['__classificazione']=$tipoCliente;
		$db["$codCliente"]['__provvigione']=$provvigione;
	}
	//chiude il file
	fclose ($news);
	
	//leggo il file 	
	$news=fopen("./pec_clienti.txt","r");  //apre il file
	while (!feof($news)) {
		$buffer = fgets($news, 4096);
		$arr=explode(',',$buffer);
		$arr[0]=$arr[0].str_repeat(' ',5-strlen($arr[0]));
		//echo strlen($arr[0]);
		$codCliente= sqlite_escape_string(trim($arr[0]));
		$pec=trim($arr[1]);
		$db["$codCliente"]['codice']=$codCliente;
		$db["$codCliente"]['__pec']=$pec;
		//echo "$codCliente=$tipoCliente<br>"; //riga letta
	}
	return $db;
}
$dati=leggiFile();
print_r($dati);

/*#############################################################

*/
$fakeClient=new ClienteFornitore(array('_autoExtend'=>-1));

$table=$fakeClient->_dbName->getVal();
$key=$fakeClient->_dbIndex->getVal();
$key=$key[0];

//creo/apro il file database
$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');

//creo la tabella
$query="CREATE TABLE if not exists $table($key TEXT PRIMARY KEY NOT NULL,
											__classificazione TEXT,
											__provvigione TEXT,
											__pec TEXT)";
$db->exec($query) or die($query);


/*#############################################################

*/
foreach ($dati as $cliente){
	//inserisco un po di dati a caso
	$query="INSERT INTO $table (".implode(",", array_keys($cliente)).") VALUES ('".implode("','", array_values($cliente))."')";
	$db->exec($query) or die($query);

}



//faccio una query e stampo i risultati
$results = $db->query('SELECT * FROM '.$table);
while ($row = $results->fetchArray()) {
    var_dump($row);
}
?>