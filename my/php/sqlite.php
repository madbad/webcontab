<?php
include ('./config.inc.php');
$sqlite=$GLOBALS['config']->sqlite;

$fakeClient=new ClienteFornitore(array('_autoExtend'=>-1));

$table=$fakeClient->_dbName->getVal();
$key=$fakeClient->_dbIndex->getVal();
$key=$key[0];

//creo/apro il file database
$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');

//creo la tabella
$query="CREATE TABLE if not exists $table($key TEXT PRIMARY KEY NOT NULL, TIPO TEXT, PEC TEXT)";
$db->exec($query) or die($query);

//inserisco un po di dati a caso
$query="INSERT INTO $table ($key) VALUES ('***".rand()."')";
$db->exec($query) or die($query);

//faccio una query e stampo i risultati
$results = $db->query('SELECT * FROM '.$table);
while ($row = $results->fetchArray()) {
    var_dump($row);
}
?>