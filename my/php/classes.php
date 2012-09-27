<?php
require_once('FirePHPCore/FirePHP.class.php');
include('./stampe/ddt.php');
$DataTypeInfo=array();//contiene i paramatri del database per ogni campo del database stesso
page_start();
/*
$log->log('Plain MessagePHP');     // or FB::
$log->info('Info MessagePHP');     // or FB::
$log->warn('Warn MessagePHP');     // or FB::
$log->error('Error MessagePHP');   // or FB::
 
$log->log('Message','Optional Label');
 
$log->fb('Message', FirePHP::*);
*/

class execStats {
	function __construct($name){
		$this->name=$name;
		$this->startTime=0;
	 	$this->stopTime=0;
	 	$this->totalExecTime=0;
	 	$this->numberOfCalls=0;
	}
    public function start()     {
	 	$this->numberOfCalls++;
		$this->startTime=microtime(true)*1000;		
    }
    public function stop()     {
		$this->stopTime=microtime(true)*1000;
	 	$this->totalExecTime+=$this->stopTime-$this->startTime;		
    }
    public function printStats()     {
		$out=$this->name.' / ';
		$out.='exec: '.round($this->totalExecTime).'ms / ';
		$out.='calls: '.$this->numberOfCalls."\n";
		return $out;
    }
}

function dbFrom($dbName, $toSelect, $operators){
	global $queryStats, $statsQrueyCached, $statsQrueyExecuted, $cache, $log,$DataTypeInfo;
	
	$thisqueryStats= new execStats('thisquery');
	$thisqueryStats->start();
		
	$queryStats->start();
	switch ($dbName){
		case 'RIGHEDDT': 				$dbFile='03BORIGD.DBF' ;break;  //*
		case 'RIGHEFT': 				$dbFile='03FARIGD.DBF' ;break;  //* 
		case 'INTESTAZIONEDDT':			$dbFile='03BOTESD.DBF' ;break;  //* 
		case 'INTESTAZIONEFT': 			$dbFile='03FATESD.DBF' ;break;  //* 
		case 'IVAFT': 					$dbFile='03LIBIVA.DBF' ;break;  //* 
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
	$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']['pathToDbFiles'].";Exclusive=NO;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=false;"; //DELETTED=1??
	//$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']['pathToDbFiles'].";collate=Machine;NULL=NO;DELETED=1;"; //DELETTED=1??

	//connect to database
	$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_IF_NEEDED ) or die('Non riesco a connettermi al database:<br>'.$GLOBALS['config']['pathToDbFiles'].'<br><br>Dns:<br>'.$dns);
	//$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_DRIVER ) or die('Could Not Connect to ODBC Database!');
	//$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_ODBC ) or die('Could Not Connect to ODBC Database!');
	//$odbc=odbc_connect($dsn," "," ") or die('Could Not Connect to ODBC Database!');

	//query string
	//	$query= "SELECT * FROM ".$dbFile." WHERE F_DATBOL >= #".$startDate."# AND F_DATBOL <= #".$endDate."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE ";
	$query= $toSelect." FROM ".$dbFile." $operators";

	//echo '<br>'.$query;
	$cacheEnabled=TRUE;
	$isCached=array_key_exists($query, $cache);
	
	if($isCached && $cacheEnabled){
		//uso il risultato della cache
		$records=$cache[$query];
		$statsQrueyCached++;
//$log->info($query.' ***** [CACHED]');
	}else{
		//query execution
		$result = odbc_exec($odbc, $query) or die (odbc_errormsg().'<br><br>La query da eseguire era:<br>'.$query);
		$statsQrueyExecuted++;
		
		//fileds info storing
		$tableName=$dbName;
		if(!array_key_exists($tableName,$DataTypeInfo)){
			$DataTypeInfo[$tableName]=Array();
			$columns=odbc_num_fields ($result);
			for ($i=1; $i<=$columns; $i++){
				$fieldName=odbc_field_name($result,$i);
				$DataTypeInfo[$tableName][$fieldName]=Array();
				$DataTypeInfo[$tableName][$fieldName]['name']=odbc_field_name($result,$i);//nome del campo
				$DataTypeInfo[$tableName][$fieldName]['len']=odbc_field_len($result,$i);//lunghezza
				$DataTypeInfo[$tableName][$fieldName]['type']=odbc_field_type($result,$i);//tipo=Date/Numeric/Char
				$DataTypeInfo[$tableName][$fieldName]['num']=odbc_field_num($result,$i); //bho - vuoto?
				$DataTypeInfo[$tableName][$fieldName]['scale']=odbc_field_scale($result,$i); //bho - vuoto?
				$DataTypeInfo[$tableName][$fieldName]['precision']=odbc_field_precision($result,$i); //bho - vuoto?
				//$DataTypeInfo['bho']=odbc_result_all(odbc_gettypeinfo($odbc));
				//$log->info($DataTypeInfo);
			}
			//print_r($DataTypeInfo);
		}

		
		//caching
		$records=array();
		while($record = odbc_fetch_array($result)){
			$records[] = $record;
		}
		$cache[$query]=$records;

		$thisqueryStats->stop();
		$log->info($query.' ***** '.$thisqueryStats->printStats());
	}

	
	//da informazioni in merito al tipo di campo che accetta il database
    //print_r(odbc_gettypeinfo($odbc));
    //odbc_result_all($result,"border=1");
	//$result=dbFrom($parentObj->_dbName->getVal(), 'SELECT '.$this->campoDbf, "");



	/*
	//da informazioni in merito al tipo di campo che accetta il database
     $result = odbc_columns($odbc);
     odbc_result_all($result,"border=1");	
	*/	
	
	
	
	//chiudo la connessione al databse
	//meglio di no... sembra che se la chiudo lo script rallenti di parecchio... 
	//io avrei pensato il contrario...Bho!
	//odbc_close($odbc);


	$queryStats->stop();
	return $records;
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
	//chiude il file
	fclose ($news);
	return $db;
}
function odbc_access_escape_str($str) {
	$out="";
	for($a=0; $a<strlen($str); $a++) {
		if($str[$a]=="'") {
			$out.="''";
		} else if($str[$a]!=chr(10)) {
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
				return $func($this);
            }
        }
    }
}

class MyClass extends DefaultClass{
//la mia classe di base con proprietà e metodi aggiuntivi
   public function addProp($nome, $campoDbf=null, $validatore=null) {
      $this->$nome=new Proprietà($nome, $campoDbf, $validatore, $this);
		return $this;
  	}
  	public function getName(){
  		return get_class($this);	
	}
	public function mergeParams($params){
		//importo eventuali valori delle proprietà che mi sono passato come $params nell'oggetto principale
		$this->_params=$params;
		foreach ($params as $key => $value){
			if($key[0]!='_'){
				$this->$key->setVal($value);
			}
		}
	}
	public function autoExtend(){
		global $log;
		if(!isset($this->_params['_autoExtend'])){$this->_params['_autoExtend']=1;}
		switch ($this->_params['_autoExtend']) {
			case -1://-1= non fare niente.
				$log->warn("Oggetto dichiarato NON estensibile!");
				break;
			case 'intestazione'://-1= recupera solo l'intestazione
				$this->getDataFromDb();
				break;
			case 0:// 0= recupera solo i dati dal database
				$this->getDataFromDb();
				break;
			case 1:// 1= estendi di un livello //default
				$this->getDataFromDb();
				break;
			case 2:// 2=estendi di 2 livelli
				echo "i equals 2";
				break;
			case 3:// 3=estendi di 3 livelli
				echo "i equals 3";
				break;
			case 10://10=estendi all'infinito	
				echo "i equals 10";
				break;
		}
	}
	public function getDataFromDb(){
		global $log;
		if (!isset($this->_params['_result'])){
			//imposto la clausola where a seconda delle chiavi di ricerca del DB per la classe corrente
			//imposto la clausola order a seconda delle chiavi di ricerca del DB per la classe corrente			
			$where='WHERE ';
			$order=' ORDER BY ';
			$indexes=$this->_dbIndex->getVal();
			foreach($indexes as $key => $property){
				//echo $key.'<br>';
				if($key>0){
					$where.=' AND ';
					$order.=',';
				}
				$info=$this->$property->getDataType();
				//echo $info['type'].'***************';
				switch($info['type']){
					case 'Date': $separatore="#";break;
					case 'Numeric': $separatore="";break;
					default: $separatore="'";break;
				
				}
				$where.=$this->$property->campoDbf."=".$separatore.odbc_access_escape_str($this->$property->getVal()).$separatore;
				$order.=$this->$property->campoDbf;
			}
			//eseguo la query
			$result=dbFrom($this->_dbName->getVal(), 'SELECT *', $where.$order);
			//
			foreach($result as $row){
				foreach($this as $key => $value) {
					//escludo le prorpietà che iniziano con "_" in quanto sono solo ad uso interno e non le devo ricavare dal database
					//escludo anche la proprietà 'righe' in quanto è una proprietà speciale che va trattata diersamente dalle altre
					//e cmq non proviene dal database (almeno non direttamente)
					if($key[0]!='_' && $key!='righe'){
						$val=$row[$value->campoDbf];
						$this->$key->setVal($val);
					}
				}
			}
		}else{
			$passedResult=$this->_params['_result'];
			foreach($this as $key => $value) {
				//escludo le prorpietà che iniziano con "_" in quanto sono solo ad uso interno e non le devo ricavare dal database
				//escludo anche la proprietà 'righe' in quanto è una proprietà speciale che va trattata diersamente dalle altre
				//e cmq non proviene dal database (almeno non direttamente)
				if($key[0]!='_' && $key!='righe'){
					$val=$this->_params['_result'][$value->campoDbf];
					$this->$key->setVal($val);
				}
			}
		}
		
		if (method_exists(get_class($this), 'getDataFromDbCallBack')){
			$this->getDataFromDbCallBack();
		}
	}
	public function toJson($subRun=0){
		//imposto il nome dell'oggetto
		if(!$subRun){
			$out='"'.strtolower(get_class($this)).'":{';
			//aggiungo una proprietà ad uso interno che descrive il tipo di oggetto
			$out.='"_type":"'.strtolower(get_class($this)).'",';
		}else{
			$out='';
		}

		foreach($this as $key => $value) {
			//se la proprietà è un oggetto (ovvero l'ho definita io come oggetto) provo ad estenderla
			if(is_object($this->$key)){
				$extendedObj=$this->$key->extend();
				if ($extendedObj){
					//se si stende chiamo il metodo json del suo oggetto
					//echo "estendo $key<br>";
					$out.='"'.$key.'":{';
					$out.='"_type":"'.strtolower(get_class($extendedObj)).'",';
					$out.=$extendedObj->toJson(1);
				}else{
					//altrimento si tratta di una semplice proprietà e la converto io in json
					if($key[0]!='_'){
						$val=$this->$key->getVal();
						$out.='"'.$key.'":"'.$val.'",';
					}
				}
			}

			//se invece è una proprietà
			if(is_array($this->$key) && $key[0]!='_'){
				$out.='"'.$key.'"'.': [';
				foreach ($this->$key as $subKey => $subValue){
					//se si stende chiamo il metodo json del suo oggetto
					//echo "estendo $key<br>";
					//$out.='"'.$subKey.'":{';
					$out.='{';

					$out.='"_type":"'.strtolower(get_class($subValue)).'",';
					$out.=$subValue->toJson(1);
				}
				//rimuovo la virgola dall'ultima proprietà dell'oggetto
				$out=substr($out, 0, -1);
				$out.='],';
			}

		}
		//rimuovo la virgola dall'ultima proprietà dell'oggetto
		$out=substr($out, 0, -1);
		//chiudo la definizione oggetto
		$out.='},';
		//se questa funzione è chiamata di prima istanza e non è una derivata in quanto chiamata come sotto oggetto
		if(!$subRun){
			//rimuovo l'ultima virgola
			$out=substr($out, 0, -1);
			//e aggiungo le graffea inizio e fine
			$out='{'.$out.'}';
		}
		return $out;
	}		
	
	
	/*
   public function getFromDbByID ($id){
		$tableName=$this->getName();
	   $query = "SELECT * FROM $tableName WHERE id='$id' ORDER BY id DESC";
  		$result=$GLOBALS['wc']->db->query($query);

		foreach($result as $row){
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
	function __construct($nome, $campoDbf, $validatore='', $parent){
	 	$this->nome=$nome;
		$this->campoDbf=$campoDbf;
	 	$this->validatore=$validatore;
	 	$this->valore='';
		$this->_parent=$parent;
	}
	
	public function setVal($newVal){
		//prima di impostare il valore eseguo dei controlli per verificare che sia corretto e delle trasformazioni se necessarie
		if($this->nome[0]!='_'){
			$type=$this->getDataType();
			
			//se il campo è un numero di bolla o di fattura
			//riempio di spazi da sinistra verso destra prima del numero fino ad arrivare 
			//al numero di caratteri richiesto dal campo del database
			if($type['name']=='F_NUMBOL' || $type['name']=='F_NUMFAT'){
				$newVal=str_pad($newVal, $type['len'], " ", STR_PAD_LEFT);  
			}
		
			//se la data ha il formato aaaa/mm/gg la trasformo in mm-gg-aaaa
			//come richiesto dal database
			if($type['type']=='Date' && preg_match('/....-..-../',$newVal)){
				$arr=explode("-", $newVal);
										//mese   //giorno //anno
				$newVal=mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]);
				$newVal=date ( 'm-d-Y' , $newVal);
			}
			
			//se la data ha il formato gg/mm/aaaa la trasformo in mm-gg-aaaa
			//come richiesto dal database
			if($type['type']=='Date' && preg_match('/.*\/.*\/.*/',$newVal)){

				$arr=explode("/", $newVal);
										//mese   //giorno //anno
				$newVal=mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]);
				$newVal=date ( 'm-d-Y' , $newVal);
			}
		}

		return $this->valore=$newVal;
	}

	public function getVal(){
		return $this->valore;
	}
	public function getFormatted(){
		$type=$this->getDataType();
		switch($type['type']){
			case 'Date':
				if (preg_match('/..-..-..../',$this->valore)){
					$arr=explode("-", $this->valore);
											//mese   //giorno //anno
					$newVal=mktime(0, 0, 0, $arr[0], $arr[1], $arr[2]);
					$newVal=date ( 'd/m/Y' , $newVal);
					$out=$newVal;
				}
				break;
			default:
				$out=$this->valore;
				break;
		}
		return $out;
	}

	public function extend(){
		//add some exception to prevent loop coddestinazion>cliente>codicedestinazione
		if(get_class($this->_parent)=='DestinazioneCliente' && $this->nome=='cod_cliente'){return false;}		
	
		//se la proprietà non ha un valore indipendentemente dal fatto che sia estendibile o meno non tento neanche di estenderla
		if($this->valore==''){return false;}
		
		//vedo se è estendibile ed in caso eseguo
		switch ($this->nome){
			case 'cod_mezzo':			$params=Array('codice'=>$this->valore); return new CausaliSpedizione($params);
			case 'cod_vettore':			$params=Array('codice'=>$this->valore); return new Vettore($params);
			case 'cod_pagamento':		$params=Array('codice'=>$this->valore); return new CausalePagamento($params);
			case 'cod_banca':			$params=Array('codice'=>$this->valore); return new Banca($params);
			case 'cod_iva':				$params=Array('codice'=>$this->valore); return new CausaleIva($params);
			case 'cod_articolo':		$params=Array('codice'=>$this->valore); return new Articolo($params);
			case 'cod_cliente':			$params=Array('codice'=>$this->valore); return new ClienteFornitore($params);
			case 'cod_destinatario':	$params=Array('codice'=>$this->valore); return new ClienteFornitore($params);
			case 'cod_destinazione':
				//ho bisogno di fare delle distinzioni a seconda che la funzione extend del parametro cod_destinatario sia stata fatta dall'oggetto "ddt" o dall'oggetto "anagraficacliente"
				if (property_exists($this->_parent,'codice')){
					//oggetto:"anagrafica cliente"
					$params=Array('codice'=>$this->valore, 'cod_cliente'=>$this->_parent->codice->getVal());
				}else{
					//oggetto:"ddt"
					$params=Array('codice'=>$this->valore, 'cod_cliente'=>$this->_parent->cod_destinatario->getVal());
				}
				return new DestinazioneCliente($params);

		}
		//se ha un valore ma non è nessuno dei precedenti allora la proprietà non è estendibile
		return false;
	}
  
	public function validate(){
		//echo ' sto validando valido '.$this->nome;
		return Validate($this,$this->ValidateParams);
	}
	
	public function getDataType(){
		global $DataTypeInfo;
		$parentObj=$this->_parent;
		$tableName=$parentObj->_dbName->getVal();
		$fieldName=$this->campoDbf;
		
		
		if(!array_key_exists($tableName,$DataTypeInfo)){
			//$result=dbFrom($tableName, 'SELECT *', "");
			$result=dbFrom($tableName, 'SELECT TOP 1, *', ""); 
		}

/*
		$out=Array();
		$out['name']=odbc_field_name($result,1);//nome del campo
		$out['len']=odbc_field_len($result,1);//lunghezza
		$out['type']=odbc_field_type($result,1);//tipo=Date/Numeric/Char
		$out['num']=odbc_field_num($result,1); //bho - vuoto?
		$out['scale']=odbc_field_scale($result,1); //bho - vuoto?
		$out['precision']=odbc_field_precision($result,1); //bho - vuoto?
*/	

		
		return $DataTypeInfo[$tableName][$fieldName];
	}
}
/*########################################################################################*/

class Fattura extends MyClass{
	function __construct($params) {
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

		$this->addProp('imponibile','FLCAMBIO');
	//	$this->addProp('iva',		'');
	//	$this->addProp('importo',		'');

	//	$this->addProp('pagato',		'F_PAGATO');
		
		

		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('INTESTAZIONEFT');
		$this->addProp('_dbName2');
		$this->_dbName2->setVal('IVAFT');
		
		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('numero','data'));
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
  	}
	public function getDataFromDbCallBack(){
	//echo 'test';
		//if ($this->_params['_autoExtend']!='intestazione'){
			//recupero le righe del ddt
//			$result=dbFrom($this->_dbName2->getVal(), 'SELECT *', "WHERE ".'F_NUMDOC'."='".odbc_access_escape_str($this->numero->getVal())."' AND ".'F_DATDOC'."=#".odbc_access_escape_str($this->data->getVal()."#"));
			$result=dbFrom($this->_dbName2->getVal(), 'SELECT *', "WHERE F_IMPONI <'0' ");

			//echo $result;
			foreach($result as $row){
	
					//array_push($this->righe, new Riga(array('ddt_numero'=>$this->numero->getVal(),'ddt_data'=>$this->data->getVal(),'numero'=>$row['F_PROGRE'])));
					/*todo fix righe*/
					//echo 'test';
					ECHO 'test';
					$this->imponibile->setVal($row['F_T_IMPONI']);
					$this->iva->setVal($row['F_T_IMPIVA']);
					$this->importo->setVal($row['F_T_IMPONI']+$row['F_T_IMPIVA'].'***');
			}
		//}

	}
}

class Ddt  extends MyClass {
	function __construct($params) {
		$this->addProp('numero',					'F_NUMBOL');
  		$this->addProp('data',						'F_DATBOL');
		$this->addProp('cod_destinatario',			'F_CODCLI');
		$this->addProp('cod_destinazione',			'F_SUFFCLI');
		$this->addProp('cod_causale',				'F_TIPODOC');//V=VENDITA D=DEPOSITO
		$this->addProp('cod_mezzo',					'F_SPEDIZ');
//		$this->addProp('cod_vettore',				'F_VET');
//		$this->addProp('ora_inizio_trasporto',		'F_');
//		$this->addProp('aspetto_beni',				'F_');
//		$this->addProp('annotazioni',				'F_');
		$this->addProp('peso_lordo',				'F_PLORDO'); //si aggiorna solo quando si stampa? sembra che non coincida con la somma dei pesi netti delle righe (dopo i riscontri)
		$this->addProp('peso_netto',				'F_PNETTO'); //si aggiorna solo quando si stampa? sembra che non coincida con la somma dei pesi netti delle righe (dopo i riscontri)
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
		
		$this->righe=array();
		
		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('INTESTAZIONEDDT');
		$this->addProp('_dbName2');
		$this->_dbName2->setVal('RIGHEDDT');
		
		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('numero','data'));
//print_r($params);
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
//print_r($this->_params);		
		//print_r($params['_result']);
		//avvio il recupero dei dati
		$this->autoExtend();
	}
	
	public function getDataFromDbCallBack(){
		if ($this->_params['_autoExtend']!='intestazione'){
			//recupero le righe del ddt
			$result=dbFrom($this->_dbName2->getVal(), 'SELECT *', "WHERE ".$this->numero->campoDbf."='".odbc_access_escape_str($this->numero->getVal())."' AND ".$this->data->campoDbf."=#".odbc_access_escape_str($this->data->getVal()."#"));
				foreach($result as $row){
					//array_push($this->righe, new Riga(array('ddt_numero'=>$this->numero->getVal(),'ddt_data'=>$this->data->getVal(),'numero'=>$row['F_PROGRE'])));
					/*todo fix righe*/
					//echo 'test';
					$params=array(
						'ddt_numero'=>$this->numero->getVal(),
						'ddt_data'=>$this->data->getVal(),
						'numero'=>$row['F_PROGRE'],
						'_result'=>$row,
					);
					array_push($this->righe, new Riga($params));

			}
		}

	}
	public function doPrint(){
		printDdt($this);
	}
	public function jsonList(){
		$out= "{\n\"numero\": \"".$this->numero->getFormatted()."\",";
		$out.= "\n\"data\": \"".$this->data->getFormatted()."\",";
		$out.= "\n\"cliente\": \"".$this->cod_destinatario->extend()->ragionesociale->getFormatted()."\"";
		$out.= "\n},";
		return $out;
	}	
}

class Riga extends MyClass {
	function __construct($params) {
		$this->addProp('numero',					'F_PROGRE');
		$this->addProp('ddt_data',					'F_DATBOL');
		$this->addProp('ddt_numero',				'F_NUMBOL');
		
		$this->addProp('cod_articolo',				'F_CODPRO');
		$this->addProp('descrizione',				'F_DESPRO');
		$this->addProp('unita_misura',				'F_UM');
		$this->addProp('prezzo',					'F_PREUNI');
		$this->addProp('imponibile',				'F_IMPONI');
		$this->addProp('importo_iva',				'F_IMPIVA');
		$this->addProp('importo_totale',			'F_IMPORTO');		
		$this->addProp('colli',						'F_NUMCOL');
//		$this->addProp('cod_imballo',				'F_');
//		$this->addProp('peso_lordo',				'F_');
		$this->addProp('peso_netto',				'F_PESNET');
		$this->addProp('peso_lordo',				'F_QTA');
//		$this->addProp('tara',						'F_');
//		$this->addProp('origine',					'F_');
//		$this->addProp('categoria',					'F_');
//		$this->addProp('lotto',						'F_');
		$this->addProp('cod_iva',					'F_CODIVA');
		$this->addProp('stato',						'F_STATO');
		$this->addProp('cod_cliente',				'F_CODCLI');

		/* TODO= FIX RIGHE FATTURE*/
		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('RIGHEDDT');

		$this->addProp('_totImponibileNetto');
		$this->_totImponibileNetto->setVal(0);			
		
		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('ddt_numero','ddt_data','numero'));
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
	}
	
	public function getPrezzoLordo(){
		$dbClienti=getDbClienti();
		$codCliente=$this->cod_cliente->getVal();
		$provvigione=$dbClienti["$codCliente"]['provvigione'];
		if ($provvigione*1>0){
			return $this->prezzo->getVal();
		}else{
			$provvigione=12;
			return $this->prezzo->getVal()*(100)/(100-$provvigione);
		}
	}
	public function getPrezzoNetto(){
		$dbClienti=getDbClienti();
		$codCliente=$this->cod_cliente->getVal();
		$provvigione=$dbClienti["$codCliente"]['provvigione'];
		if ($provvigione*1>0){
			return $this->prezzo->getVal()*(100-$provvigione)/100;
		}else{
			return $this->prezzo->getVal();
		}
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
		$this->addProp('_dbName');
		$this->_dbName->setVal('ANAGRAFICAARTICOLI');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();	
	}
}

class Imballaggio extends MyClass {
	function __construct($params) {
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
		$this->addProp('_classificazione',			'');
		
		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('ANAGRAFICACLIENTI');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
	}

	public function getDataFromDbCallBack(){
		if($this->_params['_autoExtend']!=-1){
			//imposto il tipo cliente ricavandolo dal mio file db
			$dbClienti=getDbClienti();
			$codCliente=$this->codice->getVal();
			$this->_classificazione->setVal($dbClienti["$codCliente"]['tipo']);
		}
	}
	
	
}

class DestinazioneCliente extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_SUFFCLI');
		$this->addProp('cod_cliente',				'F_CODCLI');
		$this->addProp('ragionesociale',			'F_RAGSOC');	
		$this->addProp('via',						'F_INDIRI');
		$this->addProp('paese',						'F_LOCALI');
		$this->addProp('citta',						'F_PROV');
		$this->addProp('cap',						'F_CAP');
		$this->addProp('note',						'F_NOTE');
		
		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('DESTINAZIONICLIENTI');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice','cod_cliente'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();		
	}
	/*
	public function getDataFromDb(){
		//questa rimpiazza la funzione con stesso nome ereditata dalla classe MyClass
		$result=dbFrom($this->_dbName->getVal(), 'SELECT *', "WHERE ".$this->codice->campoDbf."='".odbc_access_escape_str($this->codice->getVal())."' AND ".$this->cod_cliente->campoDbf."='".odbc_access_escape_str($this->cod_cliente->getVal())."'");
		foreach($result as $row){
			foreach($this as $key => $value) {
				//escludo le prorpietà che iniziano con "_" in quanto sono solo ad uso interno e non le devo ricavare dal database
				if($key[0]!='_'){
					$val=$row[$value->campoDbf];
					if($val) {
						$this->$key->setVal($val);					
					//}else{
					//	echo '<BR>missing val: '.$key;
					}
				}
			}
		}
	}
	*/
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
		$this->addProp('_dbName');
		$this->_dbName->setVal('ANAGRAFICAVETTORI');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();		
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
		$this->addProp('_dbName');
		$this->_dbName->setVal('CAUSALIPAGAMENTO');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();	
	}
}

class CausaleIva extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODIVA');
		$this->addProp('descrizione',				'F_DESIVA');
		
		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('CAUSALIIVA');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
	}
}

class ListinoPrezzi extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODPRO');
		$this->addProp('prezzo',					'F_PREZZO');
		
		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('LISTINOPREZZI');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
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
		$this->addProp('_dbName');
		$this->_dbName->setVal('ANAGRAFICABANCHE');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
	}
}

class CausaliMagazzino extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODCAU');
		$this->addProp('descrizione',				'F_DESCAU');
		$this->addProp('causale_trasporto',			'F_CAUTRA');
		$this->addProp('segno',						'F_SEGGIA');
		
		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('CAUSALIMAGAZZINO');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
	}
}

class CausaliSpedizione extends MyClass {
	function __construct($params) {
		$this->addProp('codice',					'F_CODSPE');
		$this->addProp('descrizione',				'F_DESSPE');
				
		//configurazione database
		$this->addProp('_dbName','');
		$this->_dbName->setVal('CAUSALISPEDIZIONE');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
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
		$this->addProp('_dbName');
		$this->_dbName->setVal('ANNOTAZIONIDDT');

		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('codice'));
				
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
	}
}

class MyList {
/*
example usage
$test=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('=','17/02/12'),		
		'data'=>array('>','28/03/09'),
		'data'=>array('<','17/02/12'),
		'data'=>array('<>','01/01/09','01/01/11'),
		'data'=>'28/03/09',	
		'numero'=>'784'
	)
);
*/
	function __construct($params) {
$numeroDiValori=0;
		//inizializzo larray che conterrà gli oggetti della lista
		$this->arr=array();
	
		$objType=$params['_type'];
		$fakeObj=new $objType(array('_autoExtend'=>'-1'));

		$condition=array();
		$i=0;		
		$operator=null;
		$newVal=null;
		$newKey=null;		
		
		foreach ($params as $key => $value) {
			//se non si tratta di una proprietà interna
			if($key['0']!='_'){
				//se c'è un operatore '=' '<' '>' '<>' '>=' '<=' '!='
				//il primo valore della variabile $value sarà la stringa dell'operatore
				//altrimenti è solo un "valore/array di possibili valori" per la $key
				switch ($value[0]){
					case '=':
						$tOperator='=';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						$numeroDiValori=count($value);
						break;
					case '<':
						$tOperator='<';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '>':
						$tOperator='>';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '<=':
						$tOperator='<=';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '>=':
						$tOperator='>=';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '<>'://compreso tra
						//inverto i simboli per mia comodita
						$tOperator=array('>=','<=');
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						break;
					case '!='://diverso da
						$tOperator='<>';
						array_shift($value);//rimuovo la condizione e lascio il valore/valori
						//$numeroDiValori=count($value);
						break;
					default:
						//se non è nessuno dei precedenti vuol dire che ho passato solo uno /dei valori da confrontare
						//e quindi presumo che l'operatore sia '='
						$tOperator='=';
						//$numeroDiValori=count($value);
						break;
				}
				//se ho un array di valori e un arrai di operatori (caso del '<>' compreso tra)
				if (is_array($value) && is_array($tOperator)){
					foreach ($value as $tKey => $tVal){
						$operator[]=$tOperator[$tKey];
						$newVal[]=$value[$tKey];	
						$newKey[]=$key;						
					}
				//altrimenti si ho un array di valori ma un solo operatore allora presumo che l'operatore sia lo stesso per tutti i valori
				}else if (is_array($value) && !is_array($tOperator)){
					foreach ($value as $tVal){
						$operator[]=$tOperator;
						$newVal[]=$tVal;
						$newKey[]=$key;								
					}
				}else{
				//se innfino ho un solo valore e un solo operatore allora è tutto semplice 
					$operator[]=$tOperator;
					$newVal[]=$value;
					$newKey[]=$key;							
				}
			}
		}

		//trasferisco il tutto dentro l'array conditions
		for ($h=0; $h<count($operator); $h++){
				$val=$fakeObj->$newKey[$h]->setVal($newVal[$h]);
				//echo $operator;
				$condition[$newKey[$h]][$operator[$h]][]=$val;
		}			


		$where='WHERE ';
		$order=' ORDER BY ';

		//recupero i campi di ordinamento // clausola order
		$indexes=$fakeObj->_dbIndex->getVal();
		foreach($indexes as $key => $property){
			if($key>0){
				$order.=',';
			}
			$order.=$fakeObj->$property->campoDbf;
		}
		//e creo la clausola where
		//per ogni chiave
		$c1=0;
		foreach($condition as $key => $operator){
			$myKey=$key;
			//per ogni operatore della chiave
			if ($c1>0){
				$where.=' AND (';
			}
			$c2=0;
			foreach($operator as $operatorKey => $operatorValue){
				$myOperatorKey=$operatorKey;
				//per ogni valore dell'operatore
				if ($c2>0){
					$where.=' AND ';
				}
				$c3=0;
				foreach ($operatorValue as $val){
					//echo $c3.' '.$myOperatorKey.'<br>';
					if ($c3>0){
						if($myOperatorKey=='='){
							$where.=' OR ';						
						}else{
							$where.=' AND ';						
						}
					}	
			
					$property=$myKey;
					$val=$val;
					$operator=$myOperatorKey;
					
					$info=$fakeObj->$property->getDataType();
					switch($info['type']){
						case 'Date': $separatore="#";break;
						case 'Numeric': $separatore="";break;
						default: $separatore="'";break;
			
					}
					$where.=$fakeObj->$property->campoDbf.$operator.$separatore.odbc_access_escape_str($val).$separatore;
					$c3++;
				}
				$c2++;
			}
			if ($c1>0){
				$where.=') ';
			}
			$c1++;
		}	

		//eseguo la query
		$result=dbFrom($fakeObj->_dbName->getVal(), 'SELECT *', $where.$order);
		
		//debug info 
		//print_r($condition);
		//print_r($result);
		foreach ($result as $row){
			//print_r($row);
			$obj=new $objType(array(
					'_result'=>$row,
					'_autoExtend'=>'intestazione',
			));
			//print_r($obj);
			$this->add($obj);
		}		
		
	}
	function sum($propName){
		//restituisce la somma della proprietà indicata degli oggetti della lista
		$out=0;
		foreach ($this->arr as $key => $value){
			$out+=$value->$propName->getVal();
		}
		return $out;
	}
	function add($newObj){
		//add a new object to the current array
		array_push($this->arr, $newObj);
	}
	function remove(){
	}
	function iterate($function,$args=null){
		//esegue una funzione su ogni riga
		foreach ($this->arr as $key => $value){
			$function($value,$args);
		}
	}
}

function page_start(){
	//zipped content
	//ob_start('ob_gzhandler');
	//normal content
	ob_start();
	global $log, $queryStats, $pageStats, $cache, $statsQrueyCached, $statsQrueyExecuted, $out;
	$log = FirePHP::getInstance(true);
	$queryStats= new execStats('query');
	$pageStats= new execStats('page');
	$pageStats->start();
	$cache=array();
	$statsQrueyCached=0;
	$statsQrueyExecuted=0;
}
function page_end(){
	global $log, $queryStats, $pageStats, $cache, $statsQrueyCached, $statsQrueyExecuted, $out;

	$log->info($queryStats->printStats());
	$log->info('Queri Eseguite: '.$statsQrueyExecuted.' | Query Risparmiate (cache): '.$statsQrueyCached);

	$pageStats->stop();
	$log->info($pageStats->printStats());
	ob_flush() ;
	ob_end_flush ();
}
?>