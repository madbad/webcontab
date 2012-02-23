<?php
include ('./config.inc.php');
function myEcho($myArray){
	echo '<pre style="font-size:12px;">';
	print_r(odbc_fetch_array($myArray));
	echo '</pre>';
}
function debugger ($txt){
	if ($GLOBALS['config']['debugger']) echo "\n".'<br><b style="color:red">debugger</b>:: '.time().' :: '.$txt;
}
function dbFrom($dbName, $toSelect, $conditions){
	switch ($dbName){
		case 'RIGHEDDT': 				$dbFile='03BORIGD.DBF' ;break;  //*
		case 'RIGHEFT': 				$dbFile='03FARIGD.DBF' ;break;  //* 
		case 'INTESTAZIONEDDT':			$dbFile='03BOTESD.DBF' ;break;  //* 
		case 'INTESTAIONEFT': 			$dbFile='03FATESD.DBF' ;break;  //* 
		case 'ANAGRAFICAFORNITORI': 	$dbFile='03ANFORD.DBF' ;break;  //* 
		case 'ANAGRAFICACLIENTI': 		$dbFile='03ANCLID.DBF' ;break;  //* 
		case 'ANAGRAFICAARTICOLI': 		$dbFile='03ANPROD.DBF' ;break;  //* 
		case 'DESTINAZIONICLIENTI': 	$dbFile='03TBDESD.DBF' ;break;  //*
		case 'ANAGRAFICAVETTORI': 		$dbFile='03TBVETD.DBF' ;break;  //*
		case 'CAUSALIPAGAMENTO': 		$dbFile='03TBPAGD.DBF' ;break;  //*
		//case 'CAUSALICONTABILITA': 		$dbFile='03TBCAUD.DBF' ;break;  // FATTURE
		case 'CAUSALIIVA': 				$dbFile='03TBALID.DBF' ;break;  //*
		case 'LISTINOPREZZI': 			$dbFile='03MALISD.DBF' ;break;  //*
		case 'ANAGRAFICABANCHE': 		$dbFile='03TBBAND.DBF' ;break;  //*
		case 'CAUSALIMAGAZZINO': 		$dbFile='03TBCMAD.DBF' ;break;  //*
		case 'CAUSALISPEDIZIONE': 		$dbFile='03TBSPED.DBF' ;break;  //*
		//case 'NUMERAZIONEDDT': 			$dbFile='03TBGRUD.DBF' ;break;  
		case 'ANNOTAZIONIDDT': 			$dbFile='03TBTESD.DBF' ;break;  		
		//case 'PAGAMENTI': 				$dbFile='03FASEFD.DBF' ;break; 
		//case 'MOVIMENTIMAGAZZINO': 		$dbFile='03MAMOVD.DBF' ;break; 
		//case 'SALDIMAGAZZINO': 			$dbFile='03MAMAGD.DBF' ;break; 
		//case 'PIANODEICONTI': 			$dbFile='03ANPIAD.DBF' ;break; 
		//A4PHONED.DBf                  //TELEFONI
		//03CONFID.DBF 					//CONFIGURAZIONE	
 	}

	//database connection string
	$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']['pathToDbFiles'].";Exclusive=YES;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=true;"; //DELETTED=1??
	//connect to database
	$odbc=odbc_connect($dsn," "," ") or die('Could Not Connect to ODBC Database!');
	//query string
	//	$query= "SELECT * FROM ".$dbFile." WHERE F_DATBOL >= #".$startDate."# AND F_DATBOL <= #".$endDate."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE ";
	$query= $toSelect." FROM ".$dbFile." $conditions";
	//echo '<br>'.$query;
	//query execution
	$result = odbc_exec($odbc, $query) or die ( debugger($query.odbc_errormsg()) );

	/* 
	//da informazioni in merito al tipo di campo che accetta il database
     $result = odbc_gettypeinfo($odbc);
     odbc_result_all($result,"border=1");
	*/
	/*
	//da informazioni in merito al tipo di campo che accetta il database
     $result = odbc_columns($odbc);
     odbc_result_all($result,"border=1");	
	*/
	return $result;
}
function getDDT ($numero,$data){
	$numeroDDT=$numero;
	$dataDDT=$data;
	debugger ('sto per cercare i dati di ddt n.'.$numeroDDT.' con data '.$dataDDT);
	//$result=dbFrom('ANAGRAFICAPRODOTTI', 'SELECT *', "WHERE F_CODPRO='ALBOTRANS'");
	debugger ('Eseguo la query su INTESTAZIONEDDT');
	$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_NUMBOL='".$numeroDDT."' AND F_DATBOL = #".$dataDDT."#");
	//$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_DATBOL > #08-30-2011#");
	//$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_DATBOL > #01-01-2001#");
	//$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_NOTE = '12 BINS VERDI BRUN                                                              '");
	//$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE F_NUMBOL='        '");

	//myEcho($result);

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
		debugger ('ho ottenuto un risultato valido proseguo con la ricerca di altri dati');
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
		debugger ('ora cerco i dati relativi alle righe del ddt precedente');
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
		debugger ('ora cerco i dati relativi al cliente');
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
		debugger ('ora cerco i dati relativi alla destinazione della merce');
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
function getArticleTable($params){
	/*
		$params = array("articles" => "31",
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 31,
						"colliPedana" => 104,
						"costoCassa" => 0.43);
	*/
	//$articlesCode=$params['articles'];
		$out=null;

		$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_DATBOL >= #".$params['startDate']."# AND F_DATBOL <= #".$params['endDate']."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE");
		
		$out.="<table class=\"righe\"><tr><th colspan='5'>cod:".join(",", $params['articles'])." <br>( ".$params['startDate']." > ".$params['endDate']." )</th></tr>";	
		$out.='<tr><th>Data</th><th>Cliente</th><th>Colli</th><th>p.Netto</th><th>md</th></tr>';
		//this will containt table totals
		$sum=array('NETTO'=>0,'F_NUMCOL'=>0);
		$dbClienti=getDbClienti();
		while($row = odbc_fetch_array($result))
		{
		$codCliente=$row['F_CODCLI'];
		$tipoCliente=$dbClienti["$codCliente"]['tipo'];
		if (in_array($row['F_CODPRO'],$params['articles']) && ($tipoCliente=='mercato' || $tipoCliente=='supermercato')){

			$calopeso=round(round($row['F_NUMCOL'])*$params['abbuonoPerCollo']);
			$netto=$row['F_PESNET']-$calopeso;
			$media=round($netto/$row['F_NUMCOL'],1);
			$out.="\n<tr><td>$row[F_DATBOL]</td><td>$row[F_CODCLI]</td><td>".round($row['F_NUMCOL'])."</td><td>$netto</td><td>$media</td></tr>";
			$sum['NETTO']+=$netto;
			$sum['F_NUMCOL']+=$row['F_NUMCOL'];
		}	
		}

		$out.="<tr><th>Totali</th><th>-</th><th class='totali'>".round($sum['F_NUMCOL'])."</th><th class='totali' colspan='2'>".$sum['NETTO']."</th></tr>";
		$out.='</table>';
		
		$out.=' Imballo: '.round($params['costoCassa']*$sum['F_NUMCOL']/$sum['NETTO'],3);
		$out.='<br> Trasporto: '.round($params['costoPedana']/(($sum['NETTO']/$sum['F_NUMCOL'])*$params['colliPedana']),3);
		$out.='<br>';

		//DISCONNECT FROM DATABASE
		odbc_close($odbc);
		return $out;
	}
function getArticleTable2($articlesCode, $startDate, $endDate, $type){
		$out='';
		$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_DATBOL >= #".$startDate."# AND F_DATBOL <= #".$endDate."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE");

		$out.=$type."<br>";
		$out.="<table><tr><th colspan='5'>cod:".join(",", $articlesCode)." ( $startDate > $endDate )</th></tr>";	
		$out.='<tr><th>Data</th><th>Cliente</th><th>Colli</th><th>p.Netto</th><th>md</th><th>prezzo</th><th>pr. lordo</th><th>provv.</th><th>pr. netto</th><th>imp. netto</th></tr>';
		//this will containt table totals
		$sum=array('NETTO'=>0,'F_NUMCOL'=>0);
		$dbClienti=getDbClienti();
		$mediaPrezzo='';
		while($row = odbc_fetch_array($result)){
			$codCliente=$row['F_CODCLI'];
			$tipoCliente=$dbClienti["$codCliente"]['tipo'];
			$provvigione=$dbClienti["$codCliente"]['provvigione']*1;

			$condition=false;
			if ($type=='martinelli') {
				if (in_array($row['F_CODPRO'],$articlesCode) && ($codCliente=='MARTI')){
					$condition=true;
				}
			}
			if ($type=='supermercato') {
				if (in_array($row['F_CODPRO'],$articlesCode) && ($tipoCliente=='supermercato')){
					$condition=true;
				}
			}
			if ($type=='mercato') {
				if (in_array($row['F_CODPRO'],$articlesCode) && ($tipoCliente=='mercato')){
					$condition=true;
				}
			}

			if ($condition){
				$netto=$row['F_PESNET'];
				$mediaPeso=round($netto/$row['F_NUMCOL'],1);
				//if($provvigione==0){
				//	$row['F_PREUNI']=round($row['F_PREUNI']*100/87,2);
				//}
				
				$data=$row['F_DATBOL'];
@				$mediaPrezzo[$data];
@				$mediaPrezzo[$data]['data']=$data;
@				$mediaPrezzo[$data]['valore']+=+$netto*$row['F_PREUNI'];
@				$mediaPrezzo[$data]['peso']+=+$netto;

@				$prezzo=$row['F_PREUNI'];
@				$prezzoNetto=round($row['F_PREUNI']*((100-$provvigione)/100),2);
@				$prezzoLordo=round($row['F_PREUNI']*(100/88),2);
@				$importoNetto=round($row['F_IMPONI']*((100-$provvigione)/100),2);

				$out.="\n<tr><td>$row[F_DATBOL]</td><td>$row[F_CODCLI]</td><td>".round($row['F_NUMCOL'])."</td><td>$netto</td><td>$mediaPeso</td><td>$prezzo</td><td>$prezzoLordo</td><td>$provvigione%</td><td>$prezzoNetto</td><td>$importoNetto</td></tr>";
				//$sum['NETTO']+=$netto;
				//$sum['F_NUMCOL']+=$row['F_NUMCOL'];
@				$sum['importoNetto']+=$importoNetto;
@				$sum['pesoNetto']+=$netto;
@				$sum['colli']+=$row['F_NUMCOL'];
			}

		}
		$out.="\n".'<tr><th>-</th><th>-</th><th>'.$sum['colli'].'</th><th>'.$sum['pesoNetto'].'</th><th>'.round($sum['pesoNetto']/$sum['colli'],3).'</th><th>-</th><th>-</th><th>-</th><th>'.round($sum['importoNetto']/$sum['pesoNetto'],3).'</th><th>'.$sum['importoNetto'].'</th></tr></table>';

		//$out.="<tr><th>Totali</th><th>-</th><th class='totali'>".round($sum['F_NUMCOL'])."</th><th class='totali' colspan='2'>".$sum['NETTO']."</th></tr>";
		//$out.='</table><BR>';
		//foreach ($mediaPrezzo as $value) {
			//$out.= $value['data'].': '.round($value['valore']/$value['peso'],2).'<br>';
			//$out.=$value.'<br>';
		//}
		//DISCONNECT FROM DATABASE
		odbc_close($odbc);
		return $out;
	}
function getDbClienti(){
		$db=array();
		$news=fopen("./dbClienti.txt","r");  //apre il file
		while (!feof($news)) {
			$buffer = fgets($news, 4096);
			$arr=explode(', ',$buffer);
			$codCliente=trim($arr[0]);
			$tipoCliente=trim($arr[2]);
			$provvigione=trim($arr[3]);
			$db["$codCliente"]['tipo']=$tipoCliente;
			$db["$codCliente"]['provvigione']=$provvigione;
			//echo "$codCliente=$tipoCliente<br>"; //riga letta
		}
		fclose ($news); #chiude il file
		return $db;
	}
function odbc_access_escape_str($str) {
 $out="";
 for($a=0; $a<strlen($str); $a++) {
  if($str[$a]=="'") {
   $out.="''";
  } else
  if($str[$a]!=chr(10)) {
   $out.=$str[$a];
  }
 }
 return $out;
}


/* -------------------------------------------------------------------------------------------------------
Questa libreria cotiene alcune classi/funzioni relativa a 
- fatture
- ddt
con validazione dei dati inseriti
----------------------------------------------------------------------------------------------------------
*/
/*########################################################################################*/
function Validate($obj, $params){
/*
$params['notEmpty']=true; // il campo non deve essere vuoto
$params['isNumeric']=true; //il campo deve essere numerico
$params['maxLength']=number //lunghezza massima del campo
$params['minLength']=number //lunghezza minima del campo
$params['existIn'] Table,Row
$params['rexExp']=regular expression
*/
//
	//imposto i parametri di default per i controlli
	$def['notEmpty']=true;
	$def['isNumeric']=false;
	$def['maxLength']=$obj->dbLunghezza;
	$def['minLength']=0;
	$def['existIn']=false;
	$def['rexExp']=false;
	
	$value=$obj->getVal();

	//creo l'array con i prametri definitivi su cui fare i controlli
	//sovrascrivendo, ove necessariom, i parametri di default con quelli specifici dell'oggetto da validare
	if(is_array($params)){
		$params=array_merge($def, $params);		
	}else{
		$params=$def;			
	}
	//inizio i controlli
	if($params['notEmpty'] && empty($value)) 												$error[]='Null value';
	if($params['isNumeric'] && !is_numeric($value))							                $error[]='Not numerica value: '.$value;
	if($params['maxLength'] && strlen($value)>$params['maxLength']) 						$error[]='Too long value: '.$value;
	if($params['minLength'] && $params['notEmpty'] && strlen($value)<$params['minLength']) 	$error[]='Too short value: '.$value;
	if($params['existIn'])
	if($params['rexExp'] && !preg_match()) 													$error[]='No regExp match: malformed value??: '.$value;

	//return validation results
	$result=new stdClass();
	if(empty($error)){
		$result->result=true;
		$result->errors=$error;				
	}else{
		$result->result=false;
		$result->errors=$error;
	}
	
	foreach ($params as $key => $value){
		if($value) $checks.=' * '.$key.'('.$value.')';
	}
	$result->checks=$checks;

	return $result;
}

class DefaultClass {
    public function __call($method, $args)     {
        if (isset($this->$method)) {
        	$func = $this->$method;
        	if(is_callable($func)){
				//$func($this);   
				return $func($this);
            }
        }
    }
}

class MyClass extends DefaultClass{
//la mia classe di base con proprietà e metodi aggiuntivi
   public function addProp($nome, $campoDbf, $validatore='') {
      $this->$nome=new Proprietà($nome, $campoDbf, $validatore);
		return $this;
  	}
  	public function getName(){
  		return get_class($this);	
	}
	public function getDataFromDb(){
		$result=dbFrom($this->dbName->getVal(), 'SELECT *', "WHERE ".$this->codice->campoDbf."='".odbc_access_escape_str($this->codice->getVal())."'");
		while($row = odbc_fetch_array($result)){
			//todo:better fix! devo escludere manualmente la key dbName in quanto non è presente nel database e genera uno warning
		    foreach($this as $key => $value) {
				if($key!='dbName'){
					$val=$code=$row[$value->campoDbf];
					$this->$key->setVal($val);
				}
			}
		}
		echo "<table style='border:1px solid #000000'>";
    for($i = 1;$i <= odbc_num_fields($result);$i++)
    {
		echo "<tr>";
        echo "<td style='border:1px solid #000000'>".odbc_field_name($result,$i);//nome del campo
        echo "</td><td style='border:1px solid #000000'>".odbc_field_num($result,$i);
        echo "</td><td style='border:1px solid #000000'>".odbc_field_scale($result,$i);
		echo "</td><td style='border:1px solid #000000'>".odbc_field_len($result,$i);//lunghezza
		echo "</td><td style='border:1px solid #000000'>".odbc_field_type($result,$i);//tipo
		echo "</td></tr>";
    }
	echo "</table>";
    unset($i);
	}	
	
	
	/*
   public function getFromDbByID ($id){
		$tableName=$this->getName();
	   $query = "SELECT * FROM $tableName WHERE id='$id' ORDER BY id DESC";
  		$result=$GLOBALS['wc']->db->query($query);

   	while ($row = mysql_fetch_array($result)){
			$totCampi =mysql_num_fields($result);           //CONTO IL NUMERO DI CAMPI NEL DB
			for ($i=0; $i < $totCampi; $i++){
					$varName=mysql_field_name($result, $i);   //ASSEGNO IL NOME DEL CAMPO NEL DB ALLA VARIABILE
					$varValue=$row[$varName];
					$this->$varName->setVal($varValue);      //E INSERISCO NELLA VARIABILE COSI' CREATA IL RELATIVO VALORE
			}
		}
		if(is_callable($this->getChildObjects())) $this->getChildObjects();
		return $this;
   }
   public function saveToDb (){
   	if($this->validate()){
   		if($this->isNew()){
      	   $this->saveToDbAsNew();
      	}else{
      		$this->saveToDbAsUpdate();
   	  }   		
  		}else{
  			$GLOBALS['log']->log("Impossibile salvare la fattura: problemi di validazione");		
  		}

   }
   public function saveToDbAsNew (){
   	$tableName=$this->getName();
   	foreach ($this as $prop => $propVal) {
  			$fieldName=$prop;
  			$fieldVal=$this->$prop->getVal();

			$fieldsNames[]=$fieldName;
			$fieldsVals[]="'$fieldVal'";
     	}

  		$fieldsNames = implode(",", $fieldsNames);
     	$fieldsVals = implode(",", $fieldsVals);
  		$query = "INSERT INTO $tableName (".$fieldsNames.") VALUES(".$fieldsVals.")";
		$GLOBALS['wc']->db->query($query);
		//echo "$fieldsNames <br> $fieldsVals <br>$query";
		$GLOBALS['log']->log("Memorizzata la Ft. n.[".$this->numero->getVal()."]  con id [".$this->id->getVal()."]");
   }
   public function saveToDbAsUpdate (){
   	$tableName=$this->getName();
  		$query = "UPDATE $tableName SET ";
     	foreach ($this as $prop => $propVal) {
  			$fieldName=$prop;
  			$fieldVal=$this->$prop->getVal();

			$updates[]="$fieldName='$fieldVal'";
     	}
     	$query.= implode(",", $updates);
     	$query.=" WHERE id='".$this->id->getVal()."'";

		$GLOBALS['wc']->db->query($query);
		//echo "$fieldsNames <br> $fieldsVals <br>$query";
		$GLOBALS['log']->log("Aggiornata la Ft. n.[".$this->numero->getVal()."]  con id [".$this->id->getVal()."]");
   }
   public function isNew (){
   	if($this->id->getVal()=='' && $this->numero->getVal()==''){
			return true;   		
  		}
  		return false;
   }
   */
}

class Proprietà extends DefaultClass {
	function __construct($nome, $campoDbf, $validatore=''){
	 	$this->nome=$nome;
		$this->campoDbf=$campoDbf;
	 	$this->validatore=$validatore;
	 	$this->valore='';
	}
	
	public function setVal($newVal){
		return $this->valore=$newVal;
	}

	public function getVal(){
		return $this->valore;
	}
	public function extend(){
		switch ($this->nome){
			case 'cod_mezzo':			$params=Array('codice'=>$this->valore); return new CausaliSpedizione($params);
			case 'cod_vettore':			$params=Array('codice'=>$this->valore); return new Vettore($params);
			case 'cod_pagamento':		$params=Array('codice'=>$this->valore); return new CausalePagamento($params);
			case 'cod_banca':			$params=Array('codice'=>$this->valore); return new Banca($params);
			case 'cod_iva':				$params=Array('codice'=>$this->valore); return new CausaleIva($params);
			case 'cod_cliente':			$params=Array('codice'=>$this->valore); return new ClienteFornitore($params);
			case 'cod_destinatario':	$params=Array('codice'=>$this->valore); return new ClienteFornitore($params);
			case 'cod_destinazione':	$params=Array('codice'=>$this->valore); return new DestinazioneCliente($params);
			case 'cod_articolo':		$params=Array('codice'=>$this->valore); return new Articolo($params);
		}
		/*
		$this->nome 
		//provo a estendere la corrente proprietà in un oggetto completo (esempio: da un codice pagamento creo un oggetto pagamento)
		$params=Array(codice=>$this->valore);
		return new ClienteFornitore($params);
		//     return $this->valore;
		*/
	}
  
	public function validate(){
		//echo ' sto validando valido '.$this->nome;
		return Validate($this,$this->ValidateParams);
	}
}
/*########################################################################################*/

class Fattura extends MyClass{
	function __construct() {
		$this->addProp('numero',		'F_NUMFAT');
  		$this->addProp('data',			'F_DATFAT');
		$this->addProp('cod_pagamento',	'F_CONPAG');
		$this->addProp('cod_mezzo',		'F_SPEDIZ');
		$this->addProp('cod_banca',		'F_BANCA');
		$this->addProp('cod_cliente',	'F_CODCLI');
		$this->addProp('stato',			'F_STATO'); // =fattura // N=nota credito
		$this->addProp('tipo',			'F_TIPODOC');// F=fattura // N=nota credito
		$this->addProp('valuta',		'F_CODVAL');
		$this->addProp('cod_iva',		'F_ESECLI');//codice iva esenzione cliente
		$this->addProp('peso_netto',	'F_PNETTO');
		$this->addProp('peso_lordo',	'F_PLORDO');
		$this->addProp('tot_colli',		'F_TOTCOLLI');
		$this->addProp('tot_peso',		'F_QTATOT');

		//configurazione database
		$this->addProp('dbName','');
		$this->dbName->setVal('INTESTAZIONEFT');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
		
		
/*
		$this->data->ValidateParams=array(
			'minLength'=>9,
			'isNumeric'=>true,
		);	
		$this->cod_destinazione->ValidateParams=array(
			'notEmpty'=>false,
		);
*/
  	}
  	public function validate(){
  		$GLOBALS['log']->log("Tento di validare la Fattura n.[".$this->numero->getVal()."]  con id [".$this->id->getVal()."]");
		foreach ($this as $prop => $propVal) {

			if($prop=='id' || $prop=='numero'){
				if($this->$prop->getVal()==''){
					$GLOBALS['log']->log("L'id o il numero della fattura sono nulli ma non mi interessa se ne sto inserendo una nuova");
					if (!$this->isNew()){
						$GLOBALS['log']->log("Non posso validare l'oggetto perchè la proprietà [".$prop."]  non ha un valore valido: ".$this->$prop->getVal());
						return false;						
					}
				}
			}else{
				//echo $prop.'*'.$this->$prop->validate()->result;
				if($this->$prop->validate()->result){
					$log2='<b style="color:green">valida</b>';
					$GLOBALS['log']->log('Verificata la proprietà <b style="color:darkblue">"'.$prop.'"</b> che è risultata '.$log2.$this->$prop->validate()->checks);					

				}else{
					$log2='<b  style="color:red">non valida</b>: '.join(",",$this->$prop->validate()->errors);					
					$GLOBALS['log']->log('Verificata la proprietà <b style="color:darkblue">"'.$prop.'"</b> che è risultata '.$log2.$this->$prop->validate()->checks);
					return false;
				}
			}
		}
			$GLOBALS['log']->log('La fattura è risultata <b style="color:green">valida</b>');					

  		return true;
  	}
  	public function getChildObjects(){
		//get DDT
  		$ddt=split(',', $this->ddt->getVal());
  		foreach ($ddt as $key => $value){
  			$this->oDdt[$key]=$GLOBALS['wc']->obj->ddt->getFromDbById($value);
  		}
  	}
}

class Ddt  extends MyClass {
	function __construct() {
		$this->addProp('numero',					'F_NUMBOL');
  		$this->addProp('data',						'F_DATBOL');
		$this->addProp('cod_destinatario',			'F_CODCLI');
		$this->addProp('cod_destinazione',			'F_SUFCLI');
		$this->addProp('cod_causale',				'F_TIPODOC');//V=VENDITA D=DEPOSITO
		$this->addProp('cod_mezzo',					'F_SPEDIZ');
		$this->addProp('cod_vettore',				'F_VET');
		$this->addProp('ora_inizio_trasporto',		'F_');
		$this->addProp('aspetto_beni',				'F_');
		$this->addProp('annotazioni',				'F_');
		$this->addProp('peso_lordo',				'F_PLORDO');
		$this->addProp('peso_netto',				'F_PNETTO');
		$this->addProp('tot_colli',					'F_TOTCOLLI');
		$this->addProp('tot_peso',					'F_QTATOT');
		$this->addProp('cod_pagamento',				'F_CONPAG');
		$this->addProp('cod_banca',					'F_BANCA');
		$this->addProp('stato',						'F_STATO'); //fatturato non fatturato
		$this->addProp('valuta',					'F_CODVAL');
		$this->addProp('fatturabile',				'F_SINOFATT');
		$this->addProp('tipocodiceclientefornitore','F_TIPOCF');
		$this->addProp('fattura_numero',			'F_NUMFATT');
		$this->addProp('fattura_data',				'F_DATFATT');
		$this->addProp('note',						'F_NOTE');
		$this->addProp('note1',						'F_NOTE1');
		$this->addProp('note2',						'F_NOTE2');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('INTESTAZIONEDDT');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class Riga extends MyClass {
	function __construct() {
		$this->addProp('numero',					'F_PROGRE');	
		$this->addProp('cod_articolo',				'F_CODPRO');
		$this->addProp('descrizione',				'F_DESPRO');
		$this->addProp('unita_misura',				'F_UM');
		$this->addProp('prezzo',					'F_PREUNI');
		$this->addProp('imponibile',				'F_IMPONI');
		$this->addProp('importo_iva',				'F_IMPIVA');
		$this->addProp('importo_totale',			'F_IMPORTO');		
		$this->addProp('colli',						'F_NUMCOL');
		$this->addProp('cod_imballo',				'F_');
		$this->addProp('peso_lordo',				'F_');
		$this->addProp('peso_netto',				'F_PESNET');
		$this->addProp('peso_netto',				'F_QTA');
		$this->addProp('tara',						'F_');
		$this->addProp('origine',					'F_');
		$this->addProp('categoria',					'F_');
		$this->addProp('lotto',						'F_');
		$this->addProp('ddt_data',					'F_DATBOL');
		$this->addProp('ddt_numero',				'F_NUMBOL');
		$this->addProp('cod_iva',					'F_CODIVA');
		$this->addProp('stato',						'F_STATO');
		$this->addProp('cod_cliente',				'F_CODCLI');

		/* TODO= FIX RIGHE FATTURE*/
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('RIGHEDDT');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class Articolo extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODPRO');
		$this->addProp('descrizione',				'F_DESPRO');
		$this->addProp('descrizione2',				'F_DESPR2');
		$this->addProp('descrizionelunga',			'F_MEMO');		
		$this->addProp('unitadimisura',				'F_UMACQ');
		$this->addProp('cod_iva',					'F_CODIVA');

		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('ANAGRAFICAARTICOLI');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();		
	}
}

class Imballaggio extends MyClass {
	function __construct() {
		$this->addProp('codice',					'F_');
		$this->addProp('descrizione',				'F_');
		$this->addProp('tara_acquisto',				'F_');
		$this->addProp('tara_vendita',				'F_');
	}
}

class ClienteFornitore extends MyClass {
	function __construct($params) {
		//$this->addProp('codice',					'F_CODFOR');	
		$this->addProp('codice',					'F_CODCLI');
		$this->addProp('ragionesociale',			'F_RAGSOC');
		$this->addProp('via',						'F_INDIRI');
		$this->addProp('paese',						'F_LOCALI');
		$this->addProp('citta',						'F_PROV');
		$this->addProp('cap',						'F_CAP');
		$this->addProp('cod_destinazione',			'F_DESTABI');
		$this->addProp('cod_pagamento',				'F_CONPAG');
		$this->addProp('cod_banca',					'F_BANCA');
		//F_AGENZI
		$this->addProp('cod_mezzo',					'F_SPEDIZ');
		$this->addProp('cod_vettore',				'F_VET');
		$this->addProp('p_iva',						'F_PIVA');
		$this->addProp('cod_fiscale',				'F_CODFIS');
		$this->addProp('cod_iva',					'F_CODIVA');
		$this->addProp('lettera_intento_num',		'F_NUMINTEN');
		$this->addProp('lettera_intento_data',		'F_DATINTEN');
		$this->addProp('lettera_intento_numinterno','F_NREGIS');
		$this->addProp('provvigione',				'F_CODPROVV');
		$this->addProp('tipo',						'F_GRCOCLI'); //15==CLIENTE //61==FORNITORE
		$this->addProp('telefono',					'F_TELEF');
		$this->addProp('cellulare',					'F_TELEX');
		$this->addProp('fax',						'F_TELEFAX');
		$this->addProp('email',						'F_EMAIL');
		$this->addProp('website',					'F_HOMEPAGE');
		$this->addProp('valuta',					'F_CODVAL');		

		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('ANAGRAFICACLIENTI');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
	/* == TOTO == FIX TIPO CLIENTE
	public function getDataFromDb(){
		$result=dbFrom('ANAGRAFICACLIENTI', 'SELECT *', "WHERE F_CODCLI='".odbc_access_escape_str($this->codice->getVal())."'");
		while($row = odbc_fetch_array($result)){
		    foreach($this as $key => $value) {
				$val=$code=$row[$value->campoDbf];
				$this->$key->setVal($val);
			}
		}
		//imposto il tipo cliente ricavandolo dal mio file db
		$dbClienti=getDbClienti();
		$codCliente=$this->codice->getVal();
		$this->tipo->setVal($dbClienti["$codCliente"]['tipo']);
	}
	*/
}

class DestinazioneCliente extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_SUFCLI');
		$this->addProp('cod_cliente',				'F_CODCLI');
		$this->addProp('ragionesociale',			'F_RAGSOC');	
		$this->addProp('via',						'F_INDIRI');
		$this->addProp('paese',						'F_LOCALI');
		$this->addProp('citta',						'F_PROV');
		$this->addProp('cap',						'F_CAP');
		$this->addProp('note',						'F_NOTE');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('DESTINAZIONICLIENTI');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();			
	}
}

class Vettore extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODVET');
		$this->addProp('ragionesociale',			'F_DESVET');	
		$this->addProp('via',						'F_INDIRI');
		$this->addProp('paese',						'F_LOCALI');
		$this->addProp('cap',						'F_CAP');
		$this->addProp('note',						'F_TEL');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('ANAGRAFICAVETTORI');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();			
	}
}

class CausalePagamento extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODPAG');
		$this->addProp('descrizione',				'F_DESPAG');	
		$this->addProp('tipo',						'F_TIPPAG'); //1=RIMESSA DIRETTA // 3=RICEVUTA BANCARIA // 5=BONIFICO	
		$this->addProp('scadenza',					'F_SCAD_1');
		$this->addProp('finemese',					'F_FIMESE');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('CAUSALIPAGAMENTO');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class CausaleIva extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODIVA');
		$this->addProp('descrizione',				'F_DESIVA');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('CAUSALIIVA');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class ListinoPrezzi extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODPRO');
		$this->addProp('prezzo',					'F_PREZZO');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('LISTINOPREZZI');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class Banca extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODBAN');
		$this->addProp('ragionesociale',			'F_DESBAN');
		$this->addProp('filiale',					'F_AGENZI');
		$this->addProp('abi',						'F_ABI');
		$this->addProp('cab',						'F_CAB');
		$this->addProp('contocorrente',				'F_CONTOCOR');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('ANAGRAFICABANCHE');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class CausaliMagazzino extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODCAU');
		$this->addProp('descrizione',				'F_DESCAU');
		$this->addProp('causale_trasporto',			'F_CAUTRA');
		$this->addProp('segno',						'F_SEGGIA');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('CAUSALIMAGAZZINO');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class CausaliSpedizione extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODSPE');
		$this->addProp('descrizione',				'F_DESSPE');
				
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('CAUSALISPEDIZIONE');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class AnnotazioniDdt extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODTES');
		$this->addProp('descrizione',				'F_DESTES1');
		$this->addProp('descrizione1',				'F_DESTES2');
		$this->addProp('descrizione2',				'F_DESTES3');
		$this->addProp('descrizione3',				'F_DESTES4');
		$this->addProp('descrizione4',				'F_DESTES5');
		
		//configurazione database
		$this->addProp('dbName','','','','','','');
		$this->dbName->setVal('ANNOTAZIONIDDT');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

/*########################################################################################*/
class Database {
	function __construct($host, $name, $user, $password) {
		$this->host=$host;
		$this->name=$name;
		$this->user=$user;
		$this->password=$password;   	
   	}

    function query($query) {
		$connection = mysql_connect($this->host, $this->user, $this->password)
		or die('Connessione al database non riuscita: ' . mysql_error());

		mysql_select_db($this->name, $connection)
		or die('Selezione del database non riuscita.');

		$result=mysql_query($query)
		or die("Query non valida: " . mysql_error(). "<br><br>LA QUERY DA ESEGUIRE ERA: <br>$query" );

		mysql_close($connection);
		return $result;
    }
    function createTable($tableName, $tableFields){

		$query = "CREATE TABLE $tableName (";
		foreach ($tableFields as $field => $fieldVal) {
			$fieldName=$tableFields->$field->nome;
			$filedLength=$tableFields->$field->dbLunghezza;
			$fieldType=$tableFields->$field->dbTipo;
			$fieldIndex=$tableFields->$field->dbIndice;
			if($fieldIndex){
				$query.="\n $fieldName TINYINT AUTO_INCREMENT";	
				$index=	$fieldName;
			}else{
				$query.=",\n $fieldName $fieldType($filedLength)";
			}
        }
		$query.=",\n index 							($index))";
  	    $this->query($query);
    }
    function addTableField($name, $type, $width, $isIndex){ 

    }
}

class WebContab {
	public $fattura='sssss';
	public $ddt;
	public $riga;
	public $articolo;
	public $imballaggio;
	public $clienteFornitore;

	function __construct() {
		$this->obj=new stdClass();   	
   	
		$this->obj->fattura=new Fattura();
		$this->obj->ddt=new Ddt();
		$this->obj->riga=new Riga();
		$this->obj->articolo=new Articolo();					
		$this->obj->imballaggio=new Imballaggio();
		$this->obj->clienteFornitore=new ClienteFornitore();
      
		$this->db=new Database($GLOBALS['conf']['db']['host'],
								$GLOBALS['conf']['db']['name'],
      							$GLOBALS['conf']['db']['user'],
      							$GLOBALS['conf']['db']['password']);
	}
	function setup(){
		foreach ($this->obj as $table => $tableVal) {
			$tableName=get_class($this->obj->$table);
			$tableFields=$this->obj->$table;
			echo '<br>'.$tableName;
			$this->db->createTable($tableName, $tableFields);
		}
	}
}


//$wc=new WebContab();

//eseguo il setup/instllazione iniziale  di webContab sul database
//$wc->setup();

/*
$test=new Fattura();
$test->cod_cliente->setVal('GRUPP');
//$test->cod_cliente->extend();
//echo $test->cod_cliente->extend()->ragionesociale->getVal();
echo '<pre>';
print_r($test->cod_cliente->extend()->ragionesociale->getVal());
echo '</pre>';
*/


$mioArticolo= new Articolo(array('codice'=>'ALBOTRANS'));
echo $mioArticolo->descrizione->getVal();
echo "<pre>".$mioArticolo->descrizionelunga->getVal()."</pre>";

echo $mioArticolo->cod_iva->extend()->descrizione->getVal();

?>