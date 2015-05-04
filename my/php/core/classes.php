<?php
/*to fix anche il tipo andrebbe inserito nelle primary key delle fatture?? se una fattura e una nota credito hanno stesso numero e data cosa succede??*/



require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'//webContab/my/php/libs/FirePHPCore/FirePHP.class.php');
//genera i file pdf dei ddt
include(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/core/stampe/ddt.php');
//genera i file pdf delle fatture
include (realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/core/stampe/ft.php');
//classe per l'invio di email
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'//webContab/my/php/libs/phpmailer/class.phpmailer.php');

$DataTypeInfo=array();//contiene i paramatri del database per ogni campo del database stesso
page_start();
/*
//$log->log('Plain MessagePHP');     // or FB::
//$log->info('Info MessagePHP');     // or FB::
//$log->warn('Warn MessagePHP');     // or FB::
//$log->error('Error MessagePHP');   // or FB::
 
//$log->log('Message','Optional Label');
 
//$log->fb('Message', FirePHP::*);
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
		case 'TOTFT': 					$dbFile='03FASEFD.DBF' ;break;  //* 
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
	$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']->pathToDbFiles.";Exclusive=NO;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=false;"; //DELETTED=1??
	//$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']->pathToDbFiles.";collate=Machine;NULL=NO;DELETED=1;"; //DELETTED=1??

	//connect to database
	$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_IF_NEEDED ) or die('Non riesco a connettermi al database:<br>'.$GLOBALS['config']->pathToDbFiles.'<br><br>Dns:<br>'.$dns);
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
////$log->info($query.' ***** [CACHED]');
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
				////$log->info($DataTypeInfo);
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
		//$log->info($query.' ***** '.$thisqueryStats->printStats());
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
/*
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
	$myDbArray=$db;
*/
	/*to fix=> dovebbe essere più generico*/
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
	return $myDbArray;
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
				//$log->warn("Oggetto dichiarato NON estensibile!");
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
						
						//se il valore contiene delle " devo convertirle in \" in quanto json altrimenti va in conflitto
						$val = str_replace('"', '\"', $val);
						/**/
						
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
		
		//rimpiazzo gli a capo con uno spazio in quanto in json non sono consentiti
		$out=str_replace("\r", " ", $out);
		$out=str_replace("\n", " ", $out);
		
		return $out;
	}		
	
   /*#########################################################
		FUNZIONI RELATIVE AL DATABASE ESTERNO SQLITE
   */#########################################################
	public function generateSqlDb(){
		//genera un database esterno sqLite se non presente sulla base delle proprietà sqLite definite nella classe dell'oggetto
		$fields=$this->getSqlDbFieldsNames();
		if (!count($fields)){
			//se non ci sono campi sqLite allora esco subito
			return;
		}
		
		$sqlite=$GLOBALS['config']->sqlite;
		$table=$this->_dbName->getVal();	
		$indexes=$this->_dbIndex->getVal();
		
		$fieldsToAdd='';
		//campi normali
		$fieldsToAdd.=implode($fields,' TEXT, ').' TEXT, ';
		//campi indice
		$fieldsToAdd.=implode($indexes,' TEXT NOT NULL, ').' TEXT NOT NULL, ';
		//chiavi primarie
		$fieldsToAdd.=' PRIMARY KEY ('.implode($indexes,',').')';
		
		//apro il database
		$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
		//creo la tabella
		$query="CREATE TABLE if not exists $table($fieldsToAdd)";
		$db->exec($query) or die($query);
		return;
	}
	public function saveSqlDbData(){
		//salva i dati nel database sqLite
		$fields=$this->getSqlDbFieldsNames();
		if (!count($fields)){
			//se non ci sono campi sqLite allora esco subito
			return;
		}
		
		
		$sqlite=$GLOBALS['config']->sqlite;
		$table=$this->_dbName->getVal();	
		$indexes=$this->_dbIndex->getVal();
		
		//elenco di tutti i campi da aggiornare
		$fields= array_merge ($fields, $indexes);
		
		//creo l'elenco di tutti i valori da memorizzare
		$values=array();
		foreach ($fields as $field){
			$val=$this->$field->getVal();
			$values[]=(string) $this->$field->getVal();
			if($val=='' && in_array($field, $indexes)){
				//abortisco una delle chiavi primarie è nulla: non posso salvare nel database (e comunque non avrebbe senso farlo)
				return;
			}
		}
		//aggiungo le '' per evitare che il testo venga trattato numericamente
		$values=implode($values,"','");
		$values="'".$values."'";

		//apro il database
		$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
		//creo la tabella
		//to fix : letto su internet che se vado ad aggiornare una riga com esempio solo 3 campi su quattro il campo che non vado ad aggiornare in questo momento con i nuovi valori viene resettato al valore di default o messo a null
		$query="INSERT OR REPLACE INTO $table (".implode($fields,',').") VALUES ($values)";

		//echo $query.'<br>';
		$db->exec($query) or die($query);
		return;
	}
	
	public function getSqlDbData(){
		//ricava tutti i dati presente nel database esterno sqLite
		$fields=$this->getSqlDbFieldsNames();
		if (!count($fields)){
			//se non ci sono campi sqLite allora esco subito
			return;
		}
		
		$sqlite=$GLOBALS['config']->sqlite;
		$key=$this->_dbIndex->getVal();
		$table=$this->_dbName->getVal();	
				
		$where='WHERE ';
		$order=' ORDER BY ';
		$indexes=$this->_dbIndex->getVal();
			
		foreach($indexes as $key => $property){
			if($key>0){
				$where.=' AND ';
				$order.=',';
			}
			$info=$this->$property->getDataType();
			//echo $info['type'].'***************';
			switch($info['type']){
				//case 'Date': $separatore="#";break;
				//case 'Numeric': $separatore="";break;
				default: $separatore="'";break;
			
			}
			$where.=$this->$property->nome."=".$separatore.odbc_access_escape_str($this->$property->getVal()).$separatore;
			$order.=$this->$property->nome;
		}			
		//la stringa della query
		$query='SELECT * FROM '.$table.' '.$where.$order;
		//apro il database ed eseguo la query
		$db = new SQLite3($sqlite->dir.'/myDb.sqlite3');
		$results = $db->query($query) or die($query);
		//importo i risultati nel mio oggetto
		while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
			foreach ($row as $key => $value){
				$this->$key->setVal($value);
			}
		}
		//fine estensione oggetto		
		return;
	}
	public function getSqlDbFieldsNames(){
	//restituisce un array contenente i nomi di proprietà che fanno parte del database esterno sqLite
		$result=array();
		foreach ($this as $key => $value){
			if(substr($key,0,2)=='__'){
				$result[]=$key;
			}
		}
		return $result;		
	}
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
			else if($type['type']=='Date' && preg_match('/....-..-../',$newVal)){
				$arr=explode("-", $newVal);
										//mese   //giorno //anno
				$newVal=mktime(0, 0, 0, $arr[1], $arr[2], $arr[0]);
				$newVal=date ( 'm-d-Y' , $newVal);
			}
			
			//se la data ha il formato gg/mm/aaaa la trasformo in mm-gg-aaaa
			//come richiesto dal database

			else if($type['type']=='Date' && preg_match('/.*\/.*\/.*/',$newVal)){
				$arr=explode("/", $newVal);
										//mese   //giorno //anno
				$newVal=mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]);
				$newVal=date ( 'm-d-Y' , $newVal);
			}
			else if($type['type']=='Memo'){
				//tolgo un carattere strano che compare alcune volte nei campi di tipo Memo
				$newVal=str_replace("ì","",$newVal);
			}
		}
		return $this->valore=$newVal;
	}

	public function getVal(){
		return $this->valore;
	}
	public function getFormatted($params=''){
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
			case 'Numeric':
				if($this->valore!=''){
					$decimali=$params*1;
					$out=number_format($this->valore*1,$decimali,$separatoreDecimali=',',$separatoreMigliaia='.');
				}else{
					$out=$this->valore;
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
		/*to fix FLCAMBIO*/
		$this->addProp('importo',		'FLCAMBIO');
	//	$this->addProp('iva',			'');
	//	$this->addProp('imponibile',	'');
	//	$this->addProp('pagato',		'F_PAGATO');
	
		//proprietà aggiunte nel file sql
		$this->addProp('__datainviopec','');
		$this->addProp('__datastampa','');
		
		$this->righe=array();

		//configurazione database
		$this->addProp('_dbName');
		$this->_dbName->setVal('INTESTAZIONEFT');
		$this->addProp('_dbName2');
		$this->_dbName2->setVal('TOTFT');
		$this->addProp('_dbName3');
		$this->_dbName3->setVal('RIGHEFT');
		
		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('data','numero'));
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
		
		//genero il database sqLite
		$this->generateSqlDb();
  	}
	
	public function getDataFromDbCallBack(){
		//recupero l'importo della fattura
		if ($this->_params['_autoExtend']!='intestazione'){
			$result=dbFrom($this->_dbName2->getVal(), 'SELECT *', "WHERE ".'F_NUMFAT'."=".odbc_access_escape_str($this->numero->getVal())." AND ".'F_DATFAT'."=#".odbc_access_escape_str($this->data->getVal()."#"));
/*todo fix*///$result=dbFrom($this->_dbName2->getVal(), 'SELECT *', "WHERE ".'F_NUMFAT'."=".odbc_access_escape_str($this->numero->getVal())." AND ".'F_DATFAT'."=#".odbc_access_escape_str($this->data->getVal()."#")." AND F_TIPODOC=".odbc_access_escape_str($this->tipo->getVal()));
			

			foreach($result as $row){
					$this->importo->setVal($row['F_IMPORTO']);
			}
		}
		
		//recupero le righe della fattura	
		if ($this->_params['_autoExtend']!='intestazione'){
			$result=dbFrom($this->_dbName3->getVal(), 'SELECT *', "WHERE ".$this->numero->campoDbf."='".odbc_access_escape_str($this->numero->getVal())."' AND ".$this->data->campoDbf."=#".odbc_access_escape_str($this->data->getVal()."#"));
				foreach($result as $row){
					//array_push($this->righe, new Riga(array('ddt_numero'=>$this->numero->getVal(),'ddt_data'=>$this->data->getVal(),'numero'=>$row['F_PROGRE'])));
					/*todo fix righe*/
					//echo 'test';
					$params=array(
						'ft_numero'=>$this->numero->getVal(),
						'ft_data'=>$this->data->getVal(),
						'numero'=>$row['F_PROGRE'],
						'_result'=>$row,
					);
					array_push($this->righe, new Riga($params));
			}
		}
		//recupero i dati dal database sqLite
		$this->getSqlDbData();
	}
	
	public function calcolaTotaliImponibiliIva(){
		$imponibili=Array();
		foreach ($this->righe as $riga){
			if ($riga->imponibile->getVal()*1!='0.0'
				&& $riga->imponibile->getVal()*1!=''){
				$codIva=$riga->cod_iva->getVal();
				@$imponibili[$codIva]['imponibile']+=$riga->imponibile->getVal();
				@$imponibili[$codIva]['importo_iva']+=$riga->importo_iva->getVal();		
			}
		}
		return $imponibili;
	}
	
	public function getPdfFileName(){
		$numero=str_replace(" ", "0", $this->numero->getVal());
		$tipo=$this->tipo->getVal();
		
		$arr=explode("-", $this->data->getVal());
								//mese   //giorno //anno
		$newVal=mktime(0, 0, 0, $arr[0], $arr[1], $arr[2]);
		$newVal=date ( 'Ymd' , $newVal);
		$data=$newVal;
		
		$nomefile=$data.'_'.$tipo.$numero.'.pdf';
		return $nomefile;	
	}
	
	public function getPdfFileUrl(){
		//il nome del file esempio: 20120121_N00000001.pdf
		$filename=$this->getPdfFileName();
		//la cartella principale delle stampe
		$dirDelleStampe=$GLOBALS['config']->pdfDir;
		//l'url completo del file esempio: c:/Program%20Files/EasyPHP-5.3.6.0/www/webcontab/my/php/stampe/ft/20120121_N00000001.pdf
		$fileUrl=$dirDelleStampe.'/ft/'.$filename;
		
		//verifichiamo che il file esista prima di comunicarlo
		//altrimenti lo generiamo "al volo"
		if(!file_exists($fileUrl)){
			//echo 'il file non esiste devo generarlo!!';
			$this->generaPdf();
		}
		return $fileUrl;	
	}
	
	public function generaPdf(){
		return generaPdfFt($this);	
	}
	
	public function visualizzaPdf(){
		$this->generaPdf($this);	
		//url completo del file pdf
		$pdfUrl=$this->getPdfFileUrl();
		// impostiamo l'header di un file pdf
		header('Content-type: application/pdf');
		// e inviamolo al browser
		readfile($pdfUrl);
	}	
	public function stampa(){

	}
	
	public function inviaPec(){
		//rigenero il file pdf della fattura
		$this->generaPdf($this);	
	
		//importo i dati di configurazione della pec
		$pec=$GLOBALS['config']->pec;
		$cliente=$this->cod_cliente->extend();
		//var_dump($cliente);
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP

		try {
			$mail->Host       = $pec->Host;
			$mail->SMTPDebug  = $pec->SMTPDebug;
			$mail->SMTPAuth   = $pec->SMTPAuth;
			$mail->Port       = $pec->Port;
			$mail->Username   = $pec->Username;
			$mail->Password   = $pec->Password;
			//$mail->AddAddress($cliente->ragionesociale->getVal(), $cliente->pec->getVal()); //destinatario
			$mail->AddAddress($cliente->__pec->getVal(), $cliente->ragionesociale->getVal()); //destinatario
			
			//mi faccio mandare la ricevuta di lettura
			$mail->ConfirmReadingTo=$pec->ReplyTo->Mail;
			$mail->SetFrom($pec->From->Mail, $pec->From->Name);
			$mail->AddReplyTo($pec->ReplyTo->Mail, $pec->ReplyTo->Name);
			$mail->Subject = 'Invio documento commerciale '.$this->getPdfFileName(); //oggetto
			//$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
			//  $mail->MsgHTML(file_get_contents('contents.html'));
			$message="[Messaggio automatizzato] <br><br>\n\n Si trasmette in allegato<br>\n";
			$message.=$this->tipo->getVal().'. Nr. '.$this->numero->getVal().' del '.$this->data->getFormatted();
			$message.="<br><br>Distinti saluti<br>".$GLOBALS['config']->azienda->ragionesociale->getVal();;

			$mail->MsgHTML($message);
			//$mail->Body($message); 

			//allego il pdf della fattura
			$mail->AddAttachment($this->getPdfFileUrl()); 
			//var_dump($mail);
			
			if($mail->Send()){
			//	$html= '<h2 style="color:green">Messaggio Inviato</h2>';
			//	$html.= '<br>Il messaggio con oggetto: ';
			//	$html.= '<b>'.$mail->Subject.'</b>';
			//	$html.='<br>E\' stato inviato a: <b>'.$cliente->ragionesociale->getVal().'</b>';
			//	$html.='<br>all\'indirizzo: <b>'.$cliente->__pec->getVal().'</b>';
			//	$html.='<br>con allegato il file: <b>'.$this->getPdfFileUrl().'</b>';
				
				//memorizzo la data di invio
				$this->__datainviopec->setVal(date("d/m/Y"));
				$this->saveSqlDbData();
				//mostro il messaggio di avvenuto invio
			//	echo $html;
			//	var_dump($message);
				//all seems ok
				return true;
			}
		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
		}
		return false;
	}
	public function getScadenzaPagamento(){
		$dataFt=$this->data->getVal();
		$condizioni=$this->cod_pagamento->extend();
		$giorni=$condizioni->scadenza->getVal();
		$fineMese=$condizioni->finemese->getVal();
		
		$test=explode('-',$dataFt);
		
		// ore-minuti-secondi-mesi-giorni-anni
		$dataFt==mktime (0,0,0,$test[0],$test[1],$test[2]);

		//calcolo quanti giorni mancano al fine mese dalla data della fattura
		$giorniDelMese=date("t",$dataFt);
		$giorniAFineMese=$giorniDelMese-$test[1];
		
		//genero la nuova data: quella di scadenza della fattura
		$scadenza=mktime (0,0,0,$test[0],$test[1]+$giorniAFineMese+$giorni,$test[2]);
		$scadenza=date ( 'd/m/Y' , $scadenza);

	return $scadenza; 
	}
	public function verificaCalcoli(){
		$imponibili=$this->calcolaTotaliImponibiliIva();
		$totFatturaDaImponibili=0;
		$totFattura=abs($this->importo->getVal()*1);//calcolo il valore assoluto altrimenti le note di accreito che sono precedute da un - causano un errore
		
		foreach ($imponibili as $val){
			$totFatturaDaImponibili+=$val['imponibile']*1 + ($val['importo_iva']*1);
		}
		//$verifica=$totFattura.''!=$totFatturaDaImponibili.'';
		
		//se i totali non concidono lancio un errore
		if($totFattura.'' != $totFatturaDaImponibili.''){
			trigger_error("[ERRORE] Il totale fattura non coincide: <br>".var_dump($totFattura).' Da Fattura <br>'.var_dump($totFatturaDaImponibili).' Da imponibili<br>'.$verifica.'<br>',E_USER_ERROR);
		}
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
		$this->_dbIndex->setVal(array('data','numero'));
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
	public function jsonList(){
		$out= "{\n\"numero\": \"".$this->numero->getFormatted()."\",";
		$out.= "\n\"data\": \"".$this->data->getFormatted()."\",";
		$out.= "\n\"cliente\": \"".$this->cod_destinatario->extend()->ragionesociale->getFormatted()."\"";
		$out.= "\n},";
		return $out;
	}	
	/*WORK IN PROGRESS*/
	public function getPdfFileName(){/*TODO QUESTA FUNZIONE A PRIMA VISTA è UGUALE A QUELLA DELLE FATTURE VEDI DI UNIRLE????*/
		$numero=str_replace(" ", "0", $this->numero->getVal());
		//$tipo=$this->tipo->getVal();
		
		$arr=explode("-", $this->data->getVal());
								//mese   //giorno //anno
		$newVal=mktime(0, 0, 0, $arr[0], $arr[1], $arr[2]);
		$newVal=date ( 'Ymd' , $newVal);
		$data=$newVal;
		
		$nomefile=$data.'_DdT'.$numero.'.pdf';
		return $nomefile;	
	}
	
	public function getPdfFileUrl(){
		//il nome del file esempio: 20120121_N00000001.pdf
		$filename=$this->getPdfFileName();
		//la cartella principale delle stampe
		$dirDelleStampe=$GLOBALS['config']->pdfDir;
		//l'url completo del file esempio: c:/Program%20Files/EasyPHP-5.3.6.0/www/webcontab/my/php/stampe/ft/20120121_N00000001.pdf
		$fileUrl=$dirDelleStampe.'/ddt/'.$filename;
		
		//verifichiamo che il file esista prima di comunicarlo
		//altrimenti lo generiamo "al volo"
		if(!file_exists($fileUrl)){
			//echo 'il file non esiste devo generarlo!!';
			$this->generaPdf();
		}
		return $fileUrl;
	}
	
	public function generaPdf(){
		return generaPdfDdt($this);	
	}
	
	public function visualizzaPdf(){
		$this->generaPdf($this);	
		//url completo del file pdf
		$pdfUrl=$this->getPdfFileUrl();
		// impostiamo l'header di un file pdf
		header('Content-type: application/pdf');
		// e inviamolo al browser
		readfile($pdfUrl);
	}	
	public function inviaMail(){
		//rigenero il file pdf del ddt
		$this->generaPdf($this);
	
		//importo i dati di configurazione della pec
		$gmail=$GLOBALS['config']->gmail;
		$cliente=$this->cod_destinatario->extend();
		//var_dump($cliente);
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP

		try {
			$mail->Host       = $gmail->Host;
			$mail->SMTPDebug  = $gmail->SMTPDebug;
			$mail->SMTPAuth   = $gmail->SMTPAuth;
			$mail->Port       = $gmail->Port;
			$mail->Username   = $gmail->Username;
			$mail->Password   = $gmail->Password;
			
			$mail->SMTPSecure = "tls";
			
			//$mail->AddAddress($cliente->ragionesociale->getVal(), $cliente->pec->getVal()); //destinatario
			if($cliente->__mailddt->getVal()==''){
				echo 'Impossibile procedere all\'invio del ddt. Nessun indirizzo email associato al cliente';
				echo '<br>'.$this->cod_destinatario->getVal();
				echo '<br>'.$cliente->ragionesociale->getVal();
			}
			//qui dovrei avere un elenco di indirizzi email separati da una virgola","
			//invio la mail ad ogni indirizzo
			$indirizzimail = explode(",", $cliente->__mailddt->getVal());
			foreach ($indirizzimail as $mailcliente){
				$mail->AddAddress($mailcliente, $cliente->ragionesociale->getVal()); //destinatario
			}
			//ne invio una copia anche a me per conoscenza
			$mail->AddAddress('lafavorita_srl@libero.it', 'La Favorita Srl'); //mia copia per conoscenza

			//mi faccio mandare la ricevuta di lettura
			$mail->ConfirmReadingTo=$gmail->ReplyTo->Mail;
			$mail->SetFrom($gmail->From->Mail, $gmail->From->Name);
			$mail->AddReplyTo($gmail->ReplyTo->Mail, $gmail->ReplyTo->Name);

			$mail->Subject = 'Invio DDT '.$this->getPdfFileName(); //oggetto
			//$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
			//  $mail->MsgHTML(file_get_contents('contents.html'));
			$message="[Messaggio automatizzato] <br><br>\n\n Si trasmette in allegato<br>\n";
			$message.='DDT'.'. Nr. '.$this->numero->getVal().' del '.$this->data->getFormatted();
			$message.="<br><br>Distinti saluti<br>".$GLOBALS['config']->azienda->ragionesociale->getVal();;

			$mail->MsgHTML($message);
			//$mail->Body($message); 

			//allego il pdf della fattura
			$mail->AddAttachment($this->getPdfFileUrl()); 
			//var_dump($mail);

			if($mail->Send()){
			//	$html= '<h2 style="color:green">Messaggio Inviato</h2>';
			//	$html.= '<br>Il messaggio con oggetto: ';
			//	$html.= '<b>'.$mail->Subject.'</b>';
			//	$html.='<br>E\' stato inviato a: <b>'.$cliente->ragionesociale->getVal().'</b>';
			//	$html.='<br>all\'indirizzo: <b>'.$cliente->__pec->getVal().'</b>';
			//	$html.='<br>con allegato il file: <b>'.$this->getPdfFileUrl().'</b>';
				
				//memorizzo la data di invio
//				$this->__datainviopec->setVal(date("d/m/Y"));
//				$this->saveSqlDbData();
				//mostro il messaggio di avvenuto invio
			//	echo $html;
			//	var_dump($message);
				//all seems ok
				return true;
			}

		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
		}
		return false;
	}
}

class Riga extends MyClass {
	function __construct($params) {
	
		//di che tipo di riga si tratta?
		//se non lo so cerco di indovinare dagli altri parametri che mi sono passato
		if (!array_key_exists('_tipoRiga', $params)){
			if (array_key_exists('ddt_data',$params)){
				$params['_tipoRiga']='ddt';
			}
			if (array_key_exists('ft_data', $params)){
				$params['_tipoRiga']='ft';
			}
		}
		if (!array_key_exists('_tipoRiga', $params)){
				//se non conosco il tipo lancio un errore
				//$callee = next(debug_backtrace());
				//trigger_error("Impossibile determinare il tipo di Riga richiesta".' in <strong>'.$callee['file'].'</strong> on line <strong>'.$callee['line'].'</strong>', E_USER_ERROR);
				var_dump($params);
				trigger_error("Impossibile determinare il tipo di Riga richiesta", E_USER_ERROR);			
		}

		
		$this->addProp('numero',					'F_PROGRE');
		
		$this->addProp('cod_articolo',				'F_CODPRO');
		$this->addProp('descrizione',				'F_DESPRO');
		$this->addProp('unita_misura',				'F_UM');
		$this->addProp('prezzo',					'F_PREUNI');
		$this->addProp('imponibile',				'F_IMPONI');
		$this->addProp('importo_iva',				'F_IMPIVA');
		$this->addProp('importo_totale',			'F_IMPORTO');
		$this->addProp('colli',						'F_NUMCOL');
//		$this->addProp('cod_imballo',				'F_');
		$this->addProp('peso_lordo',				'F_QTA');
//		$this->addProp('tara',						'F_');
//		$this->addProp('origine',					'F_');
//		$this->addProp('categoria',					'F_');
//		$this->addProp('lotto',						'F_');
		$this->addProp('cod_iva',					'F_CODIVA');
		$this->addProp('stato',						'F_STATO');
		$this->addProp('cod_cliente',				'F_CODCLI');

		//configurazione database
		$this->addProp('_dbName');
		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		
		//definisco alcune proprietà specifiche a seconda che si tratti di una riga ddt o di una riga fattura
		switch ($params['_tipoRiga']){
			case 'ft':
				//per righe delle fatture 
				$this->addProp('ft_data',					'F_DATFAT');
				$this->addProp('ft_numero',					'F_NUMFAT');
				$this->_dbName->setVal('RIGHEFT');
				$this->_dbIndex->setVal(array('ft_data','ft_numero','numero'));
				break;
			case  'ddt':
				//per righe dei ddt
				$this->addProp('ddt_data',					'F_DATBOL');
				$this->addProp('ddt_numero',				'F_NUMBOL');
				$this->addProp('peso_netto',				'F_PESNET');
				$this->_dbName->setVal('RIGHEDDT');
				$this->_dbIndex->setVal(array('ddt_data','ddt_numero','numero'));
				break;
		}
	
		$this->addProp('_totImponibileNetto');
		$this->_totImponibileNetto->setVal(0);

		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);
		
		//avvio il recupero dei dati
		$this->autoExtend();
	}
	
	public function getPrezzoLordo(){/*to fix: non dovrebbe usare 'getDbClienti' ma la funzione interna all'oggetto cliente*/
		$dbClienti=getDbClienti();
		$codCliente=$this->cod_cliente->getVal();
		$provvigione=$dbClienti["$codCliente"]['__provvigione'];
		if ($provvigione*1>0){
			return $this->prezzo->getVal();
		}else{
			$provvigione=12;
			return $this->prezzo->getVal()*(100)/(100-$provvigione);
		}
	}
	public function getPrezzoNetto(){/*to fix: non dovrebbe usare 'getDbClienti' ma la funzione interna all'oggetto cliente*/
		$dbClienti=getDbClienti();
		$codCliente=$this->cod_cliente->getVal();
		$provvigione=$dbClienti["$codCliente"]['__provvigione'];
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
		$this->addProp('p_iva_cee',					'F_PIVA_CEE');
		$this->addProp('sigla_paese',				'F_CODNAZ');
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

		//proprietà aggiunte nel file sql
		$this->addProp('__pec',						'');
		$this->addProp('__provvigione',				'');
		$this->addProp('__classificazione',			'');
		$this->addProp('__mailddt',			'');
		$this->addProp('__mailft',			'');
		$this->addProp('__mail',			'');
		
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
		
		//genero il database sql se non esiste
		$this->generateSqlDb();
	}

	public function getDataFromDbCallBack(){
		//ricavo ulteriori dati dal database sqLite
		$this->getSqlDbData();
	/*
		if($this->_params['_autoExtend']!=-1){
			//importo altri dati dal mio database esterno
			$dbClienti=getDbClienti();
			$codCliente=$this->codice->getVal();
			$this->_classificazione->setVal($dbClienti["$codCliente"]['__tipo']);
		}
	*/	
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
		
		//proprietà aggiunte nel file sql
		$this->addProp('__iban',					'');	
		
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
		
		//genero il database sqLite
		$this->generateSqlDb();
	}
	public function getDataFromDbCallBack(){
		//ricavo ulteriori dati dal database sqLite
		$this->getSqlDbData();
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
		'_select'=>'numero,data',		
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
	
		$tiporiga='ddt';
		//$tiporiga='ft';
	
		$this->_params=$params;
		$numeroDiValori=0;
		//inizializzo larray che conterrà gli oggetti della lista
		$this->arr=array();
	
		$objType=$params['_type'];
		$fakeObj=new $objType(array('_autoExtend'=>'-1'
									,'_tipoRiga'=>$tiporiga     /*hack to fix (non è detto che sia una riga ddt, anzi non è proprio detto che sia una riga)*/
									));
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
		
		/*compose the select statement
			if nothing is specified just select all
		*/
		if($params['_select']){
			$indexes = explode(",", $params['_select']);
			foreach($indexes as $key => $property){
				if($key>0){
					$select.=',';
				}
				$select.=$fakeObj->$property->campoDbf;
			}
		}else{
			$select = '*';
		}


		//eseguo la query
		$result=dbFrom($fakeObj->_dbName->getVal(), 'SELECT '.$select, $where.$order);
		
		//debug info 
		//print_r($condition);
		//print_r($result);
		foreach ($result as $row){
			//print_r($row);
			$obj=new $objType(array(
					'_result'=>$row,
					'_autoExtend'=>'intestazione',
					'_tipoRiga'=>$tiporiga     /*hack to fix (non è detto che sia una riga ddt, anzi non è proprio detto che sia una riga)*/
					
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
	/* need to find a way to store the $out
	function toJson(){
		//try to use the toJson function of the object to create a json of the list
		$out='';
		$this->iterate(function ($myObj){
			global $out;
			$out.="{";
			$out.= $myObj->toJson(1);
		});
		$out=substr($out, 0, -1);
		$out.= "]";
		echo "provaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa".$out;
		return $out;
	}
	*/
}

function page_start(){

	//zipped content
	//ob_start('ob_gzhandler');
	//normal content
//	ob_start();
	global $log, $queryStats, $pageStats, $cache, $statsQrueyCached, $statsQrueyExecuted, $out;
	//$log = FirePHP::getInstance(true);
	$queryStats= new execStats('query');
	$pageStats= new execStats('page');
	$pageStats->start();
	$cache=array();
	$statsQrueyCached=0;
	$statsQrueyExecuted=0;

}
function page_end(){

	global $log, $queryStats, $pageStats, $cache, $statsQrueyCached, $statsQrueyExecuted, $out;

	//$log->info($queryStats->printStats());
	//$log->info('Queri Eseguite: '.$statsQrueyExecuted.' | Query Risparmiate (cache): '.$statsQrueyCached);

	$pageStats->stop();
	//$log->info($pageStats->printStats());
//	ob_flush() ;
//	ob_end_flush ();

}

function CassaIFCO($nome, $costo, $tara, $cassePerBancale){
	$cassa['nome']=$nome;
	$cassa['costo']=$costo;
	$cassa['tara']=$tara;
	$cassa['cassePerBancale']=$cassePerBancale;

	return $cassa;
}

$ifco=array('IFCO 4310'=>CassaIFCO('IFCO 4310',0.555,1.0,160),
			'IFCO 4314'=>CassaIFCO('IFCO 4314',0.555,1.0,130),
			'IFCO 6410'=>CassaIFCO('IFCO 6410',0.67,1.3,80),
			'IFCO 6413'=>CassaIFCO('IFCO 6413',0.68,1.4,70),
			'IFCO 6416'=>CassaIFCO('IFCO 6416',0.70,1.6,60)
);
?>