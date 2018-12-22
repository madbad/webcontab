<?php
include ('./core/config.inc.php');
//require_once('./classes.php');
//page_start();

$dbClienti=getDbClienti();
$date_start = '2017-10-01';
$date_end = '2017-10-20';
//print_r($dbClienti);

$getClienti = dbFrom('RIGHEDDT', 'SELECT F_CODCLI', "WHERE F_DATBOL >= #".$date_start."# AND F_DATBOL <= #".$date_end."# GROUP BY F_CODCLI");
$getArticoli = dbFrom('RIGHEDDT', 'SELECT F_CODPRO', "WHERE F_DATBOL >= #".$date_start."# AND F_DATBOL <= #".$date_end."# GROUP BY F_CODPRO");
/*
print_r($getClienti);
print_r($getArticoli);
*/
$myDb = array();
/*
foreach ($getClienti as $idCliente => $cliente){
	echo "\N".'<br>';
	$result = dbFrom('RIGHEDDT', 'SELECT sum(F_PESNET) AS PESO, F_CODPRO', "WHERE F_DATBOL >= #".$date_start."# AND F_DATBOL <= #".$date_end."# AND F_CODCLI = '".$cliente['F_CODCLI']."' GROUP BY F_CODPRO");
	echo "\n<br>".$cliente['F_CODCLI']."\n<br>";
	
	foreach ($result as $id => $row){
		global $myDb;
		$cliente['F_CODCLI']= $row;
	}
}
print_r($myDb);
*/
function fixArticolo($in){
	$out = str_replace('-','',$in);
	$out = substr($out, -2);
	//echo "<br>\n".$in." became ".$out;
	return $out; 
}

$result = dbFrom('RIGHEDDT', 'SELECT sum(F_PESNET) AS PESO, F_CODPRO, F_CODCLI', "WHERE F_DATBOL >= #".$date_start."# AND F_DATBOL <= #".$date_end."# GROUP BY F_CODPRO, F_CODCLI");
//print_r($result);

$articolo='';
foreach($result as $row){
	if($dbClienti[$row['F_CODCLI']]['__classificazione']=='supermercato' OR $dbClienti[$row['F_CODCLI']]['__classificazione']=='mercato'){
		//ok
		//vogliamo solo mercati e supermercati
	}else{
		//il resto lo saltiamo
		//echo "SKIPPPED";
		continue;
	
	}

	global $articolo;
	if($articolo==''){
		$articolo = fixArticolo($row['F_CODPRO']);
		//echo '<h1>'.$articolo.'</h1>';
	}else if ($articolo != fixArticolo($row['F_CODPRO'])){
		$articolo = fixArticolo($row['F_CODPRO']);
	//echo '<h1>'.$articolo.'</h1>';
		}
	//echo "\n<BR>".$row['F_CODCLI'] .' => '.$row['PESO'];
	
	$classificazione = $dbClienti[$row['F_CODCLI']]['__classificazione']; 
	//echo $classificazione; 
	$myDb[$articolo][$classificazione] += $row['PESO'];

}

foreach($myDb as $row => $index){

	if (in_array($index, array('01','03','08','29','31','05'))){
		echo "Supermercati".$row['supermercato']['PESO'];
		echo "Mercati".$row['mercato']['PESO'];
	}else{
		echo 'not found';
	}
}

//print_r($myDb);


page_end();
?>
</span>
</body>