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
	//echo '<br>'.$query;
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
	//configurazioni
	$config='';
	$config['spedizione']['01']='Vettore';
	$config['spedizione']['02']='Destinatario';
	$config['spedizione']['03']='Mittente';

	$config['causaleddt']['V']='Vendita';
	$config['causaleddt']['D']='Reso da c/deposito(??? diversi)';
	
	$config['mittente']='';
	$config['mittente']['ragionesociale']='La Favorita di Brun G. & G. Srl Unip.';
	$config['mittente']['via']='Via Camgre, 38/B';
	$config['mittente']['paese']='Isola della Scala';
	$config['mittente']['cap']='37063';
	$config['mittente']['provincia']='VR';
	$config['mittente']['codicefiscale']='01588530236';
	$config['mittente']['partitaiva']='01588530236';
	
	while($row = odbc_fetch_array($result)){
		$test++;
		//myEcho($row);
		$ddt='';
		$ddt['data']=$row['F_DATBOL'];
		$ddt['numero']=$row['F_NUMBOL'];
		$ddt['cliente']='';
		$ddt['cliente']['codice']=$row['F_CODCLI'];
		$ddt['cliente']['destinazione']['codice']=$row['F_SUFFCLI'];
		$ddt['spedizione']='';
		$ddt['spedizione']['codice']=$row['F_SPEDIZ'];
		$ddt['spedizione']['descrizione']=$config['spedizione'][$row['F_SPEDIZ']]; //1=mittente 2=destinatario 3=vettore		
		$ddt['valuta']='';
		$ddt['valuta']['codice']=$row['F_CODVAL'];
		$ddt['totali']='';
		$ddt['totali']['quantita']=$row['F_QTATOT'];
		$ddt['totali']['colli']=$row['F_TOTCOLLI'];
		$ddt['totali']['pesolordo']=$row['F_PLORDO'];
		$ddt['totali']['pesonetto']=$row['F_PNETTO'];
		$ddt['totali']['colli']=$row['F_CODVAL'];
		$ddt['causale']='';
		$ddt['causale']['codice']=$row['F_TIPODOC'];
		$ddt['causale']['descrizione']=$config['causaleddt'][$row['F_TIPODOC']];
		$ddt['note']=$row['F_NOTE'];
		
		//DATI DELLE RIGHE DEL DDT
		$result2=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_NUMBOL='".$numeroDDT."' AND F_DATBOL = #".$dataDDT."#");
		while($row2 = odbc_fetch_array($result2)){
			$ddt['righe'][$row2['F_PROGRE']]='';
			$ddt['righe'][$row2['F_PROGRE']]['prodotto']='';
			$ddt['righe'][$row2['F_PROGRE']]['prodotto']['codice']=$row2['F_CODPRO'];
			$ddt['righe'][$row2['F_PROGRE']]['prodotto']['descrizione']=$row2['F_DESPRO']; //todo fissare le descrizioni multiriga
			$ddt['righe'][$row2['F_PROGRE']]['unitamisura']=$row2['F_UM'];
			$ddt['righe'][$row2['F_PROGRE']]['quantita']=$row2['F_QTA'];
			$ddt['righe'][$row2['F_PROGRE']]['prezzo']=$row2['F_PREUNI'];
			$ddt['righe'][$row2['F_PROGRE']]['unitamisura']=$row2['F_UM'];
			$ddt['righe'][$row2['F_PROGRE']]['iva']='';
			$ddt['righe'][$row2['F_PROGRE']]['iva']['codice']=$row2['F_CODIVA'];
			$ddt['righe'][$row2['F_PROGRE']]['colli']=$row2['F_NUMCOL'];
			$ddt['righe'][$row2['F_PROGRE']]['pesonetto']=$row2['F_PESNET'];
		}
		//DATI DEL CLIENTE
		$result3=dbFrom('ANAGRAFICACLIENTI', 'SELECT *', "WHERE F_CODCLI='".$ddt['cliente']['codice']."'");
		while($row3 = odbc_fetch_array($result3)){
			$ddt['cliente']['ragionesociale']=$row3['F_RAGSOC'];
			$ddt['cliente']['via']=$row3['F_INDIRI'];
			$ddt['cliente']['paese']=$row3['F_LOCALI'];
			$ddt['cliente']['cap']=$row3['F_CAP'];
			$ddt['cliente']['provincia']=$row3['F_PROV'];
			$ddt['cliente']['codicefiscale']=$row3['F_CODFIS'];
			$ddt['cliente']['partitaiva']=$row3['F_PIVA'];
			$ddt['cliente']['vettore']='';
			$ddt['cliente']['vettore']['codice']=$row3['F_VET'];
		}
		//DATI DELLA DESTINAZIONE
		if($ddt['cliente']['destinazione']['codice']!=''){
			$result4=dbFrom('DESTINAZIONICLIENTI', 'SELECT *', "WHERE F_CODCLI='".$ddt['cliente']['codice']."' AND F_SUFFCLI='".$ddt['cliente']['destinazione']['codice']."'");
			while($row4 = odbc_fetch_array($result4)){
				$ddt['cliente']['destinazione']['ragionesociale']=$row4['F_RAGSOC'];
				$ddt['cliente']['destinazione']['via']=$row4['F_INDIRI'];
				$ddt['cliente']['destinazione']['paese']=$row4['F_LOCALI'];
				$ddt['cliente']['destinazione']['cap']=$row4['F_CAP'];
				$ddt['cliente']['destinazione']['provincia']=$row4['F_PROV'];
			}	
		}
		//DATI DEL VETTORE
		if($ddt['cliente']['vettore']['codice']!=''){
			$result5=dbFrom('ANAGRAFICAVETTORI', 'SELECT *', "WHERE F_CODVET='".$ddt['cliente']['vettore']['codice']."'");
			while($row5 = odbc_fetch_array($result5)){
				$ddt['vettore']='';
				$ddt['vettore']['codice']=$ddt['cliente']['vettore']['codice'];
				$ddt['vettore']['ragionesociale']=$row5['F_DESVET'];
				$ddt['vettore']['via']=$row5['F_INDIRI'];
				$ddt['vettore']['paese']=$row5['F_LOCALI'];
				$ddt['vettore']['cap']=$row5['F_CAP'];
			}	
		}
	}
	//se la spedizione è con vettore
	if($ddt['spedizione']['codice']=='01'){
		$ddt['caricatore']=$config['mittente'];
		$ddt['proprietario']=$config['mittente'];
	}
	return $ddt;
}
//echo '==='+$test;
?>