<?php
function myEcho($myArray){
	echo '<pre style="font-size:12px;">';
	print_r(odbc_fetch_array($myArray));
	echo '</pre>';
}
function dbFrom($dbName, $toSelect, $conditions){
	switch ($dbName){
		case 'RIGHEDDT': 				$dbFile='03BORIGD.DBF' ;break;
		case 'RIGHEFT': 				$dbFile='03FARIGD.DBF' ;break; 
		case 'INTESTAIONEDDT': 			$dbFile='03BOTESD.DBF' ;break; 
		case 'INTESTAIONEFT': 			$dbFile='03FATESD.DBF' ;break; 
		case 'ANAGRAFICAFORNITORI': 	$dbFile='03ANFORD.DBF' ;break; 
		case 'ANAGRAFICACLIENTI': 		$dbFile='03ANCLID.DBF' ;break; 
		case 'ANAGRAFICAPRODOTTI': 		$dbFile='03ANPROD.DBF' ;break; 
		case 'DESTINAZIONICLIENTI': 	$dbFile='03TBDESD.DBF' ;break; 
		case 'ANAGRAFICAVETTORI': 		$dbFile='03TBVETD.DBF' ;break; 
		case 'CONDIZIONIPAGAMENTO': 	$dbFile='03TBPAGD.DBF' ;break; 
		case 'CAUSALICONTABILITA': 		$dbFile='03TBCAUD.DBF' ;break;  // FATTURE
		case 'CODICIIVA': 				$dbFile='03TBALID.DBF' ;break; 
		case 'LISTINOPRODOTTI': 		$dbFile='03MALISD.DBF' ;break; 
		case 'PAGAMENTI': 				$dbFile='03FASEFD.DBF' ;break; 
		case 'MOVIMENTIMAGAZZINO': 		$dbFile='03MAMOVD.DBF' ;break; 
		case 'PIANODEICONTI': 			$dbFile='03ANPIAD.DBF' ;break; 
	}
	/*
	03CONFID.DBF == CONFIGURAZIONE
	03CODOCD.DBF
	03BAPROD.DBF
	03SOSPBL.DBF
	03INTERD.DBF
	03MOSTOD.DBF
	03FASCAD.DBF
	03ESTRAD.DBF
	*/
	$pathToDbFiles='C:\Programmi\EasyPHP-5.3.6.0\www\WebContab\calcoloCosti\FILEDBF\CONTAB';
	//$pathToDbFiles='C:\Program Files\EasyPHP-5.3.6.0\www\webContab\calcoloCosti\FILEDBF\CONTAB';
	//database connection string
	$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$pathToDbFiles.";Exclusive=YES;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=true;"; //DELETTED=1??
	//connect to database
	$odbc=odbc_connect($dsn," "," ") or die('Could Not Connect to ODBC Database!');
	//query string
	//	$query= "SELECT * FROM ".$dbFile." WHERE F_DATBOL >= #".$startDate."# AND F_DATBOL <= #".$endDate."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE ";
	$query= $toSelect." FROM ".$dbFile." $conditions";
	//echo $query;
	//query execution
	$result = odbc_exec($odbc, $query) or die (odbc_errormsg());
	return $result;
}

function getDDT (){
	$numeroDDT='    2593';
	$dataDDT='09-03-2011';
	//$result=dbFrom('ANAGRAFICAPRODOTTI', 'SELECT *', "WHERE F_CODPRO='ALBOTRANS'");
	$result=dbFrom('INTESTAIONEDDT', 'SELECT *', "WHERE F_NUMBOL='".$numeroDDT."' AND F_DATBOL = #".$dataDDT."#");
	//$result=dbFrom('INTESTAIONEDDT', 'SELECT *', "WHERE F_DATBOL > #08-30-2011#");
	//$result=dbFrom('INTESTAIONEDDT', 'SELECT *', "WHERE F_DATBOL > #01-01-2001#");
	//$result=dbFrom('INTESTAIONEDDT', 'SELECT *', "WHERE F_NOTE = '12 BINS VERDI BRUN                                                              '");
	//$result=dbFrom('INTESTAIONEDDT', 'SELECT *', "WHERE F_NUMBOL='        '");

//	myEcho($result);
	//ECHO odbc_num_fields($result);
	//echo odbc_num_rows($result);
	/*
	$arr = odbc_fetch_array($result);
		echo count($arr)['counter'];
	*/
	$test=0;
	$ddt='';
	while($row = odbc_fetch_array($result)){
		$test++;
		//myEcho($row);
		$ddt='';
		$ddt['data']=$row['F_DATBOL'];
		$ddt['numero']=$row['F_NUMBOL'];
		$ddt['cliente']='';
		$ddt['cliente']['codice']=$row['F_CODCLI'];
		$ddt['cliente']['destinazione']['codece']=$row['F_SUFFCLI'];
		$ddt['spedizione']=$row['F_SPEDIZ'];
		$ddt['valuta']='';
		$ddt['valuta']['codice']=$row['F_CODVAL'];
		$ddt['totali']='';
		$ddt['totali']['quantita']=$row['F_QTATOT'];
		$ddt['totali']['colli']=$row['F_TOTCOLLI'];
		$ddt['totali']['pesolordo']=$row['F_PLORDO'];
		$ddt['totali']['pesonetto']=$row['F_PNETTO'];
		$ddt['totali']['colli']=$row['F_CODVAL'];
		$ddt['tipo']='';
		$ddt['tipo']['codice']=$row['F_TIPODOC'];
		$ddt['note']=$row['F_NOTE'];
		
		$result2=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_NUMBOL='".$numeroDDT."' AND F_DATBOL = #".$dataDDT."#");
		while($row2 = odbc_fetch_array($result2)){
			$ddt['righe'][$row2['F_PROGRE']]='';
			$ddt['righe'][$row2['F_PROGRE']]['prodotto']='';
			$ddt['righe'][$row2['F_PROGRE']]['prodotto']['codice']=$row2['F_CODPRO'];
			$ddt['righe'][$row2['F_PROGRE']]['prodotto']['descrizione']=$row2['F_DESPRO'];
			$ddt['righe'][$row2['F_PROGRE']]['unitamisura']=$row2['F_UM'];
			$ddt['righe'][$row2['F_PROGRE']]['quantita']=$row2['F_QTA'];
			$ddt['righe'][$row2['F_PROGRE']]['prezzo']=$row2['F_PREUNI'];
			$ddt['righe'][$row2['F_PROGRE']]['unitamisura']=$row2['F_UM'];
			$ddt['righe'][$row2['F_PROGRE']]['iva']='';
			$ddt['righe'][$row2['F_PROGRE']]['iva']['codice']=$row2['F_CODIVA'];
			$ddt['righe'][$row2['F_PROGRE']]['colli']=$row2['F_NUMCOL'];
			$ddt['righe'][$row2['F_PROGRE']]['pesonetto']=$row2['F_PESNET'];
		}
	}
	return $ddt;
}
//echo '==='+$test;
?>