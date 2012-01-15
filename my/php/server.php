<?php
include('./classes.php');
//include('./ddt.pdf.php');


         //numero	//data
//printDdt('     794','11-03-2008')

//$myDdt=new Ddt();
//echo $myDdt->getNumber();
debugger('test');
function getCliente($codice){
	$result=dbFrom('ANAGRAFICACLIENTI', 'SELECT *', "WHERE F_CODCLI='".$codice."'");
	while($row = odbc_fetch_array($result)){
		$ragionesociale=$row['F_RAGSOC'];
	}	
	return $ragionesociale;
}

function getDdtList($params){
	debugger($params['numero'].' '.$params['data']);
	if($params['numero']!=null && $params['data']==null)
		$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_NUMBOL='".$params['numero']."'");			
	if($params['data']!=null && $params['numero']==null)
		$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_DATBOL > #".$params['data']."#");	
	if($params['data']!=null && $params['numero']!=null) 
		$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_NUMBOL='".$params['numero']."' AND F_DATBOL = #".$params['data']."#");	

	//debugger('ddt trovati');
	$myList= array();
	while($row = odbc_fetch_array($result)){
		$ddt['data']=$row['F_DATBOL'];
		$ddt['numero']=$row['F_NUMBOL'];

		
		$cliente=new ClienteFornitore(array('codice'=>$row['F_CODCLI']));
		$ddt['cliente']=$cliente->ragionesociale->getVal();
		array_push($myList,$ddt);
		//debugger($ddt['data'].' '.$ddt['numero']);
	}
	
	//riordina l'array per numero!
	foreach ($myList as $key => $row) {
		$numeri[$key]  = $row['numero']; 
	}
	array_multisort($numeri, SORT_ASC, $myList);

	//riordina l'array per data!
	foreach ($myList as $key => $row) {
		$date[$key]  = $row['data']; 
	}
	array_multisort($date, SORT_ASC, $myList);
	
	return $myList;
}


$data='07-20-2011';
$numero='     794';
//getDdtList(array("numero" => $numero, data => $data));
$myList=getDdtList(array(data => $data));
$reply=array('successo'=>true, 'ora'=>time(), 'ddtList'=>$myList);
echo json_encode($reply);
?>