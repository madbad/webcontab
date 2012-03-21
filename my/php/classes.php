<?php
require_once('FirePHPCore/FirePHP.class.php');
include ('./config.inc.php');

/*
$firephp->log('Plain MessagePHP');     // or FB::
$firephp->info('Info MessagePHP');     // or FB::
$firephp->warn('Warn MessagePHP');     // or FB::
$firephp->error('Error MessagePHP');   // or FB::
 
$firephp->log('Message','Optional Label');
 
//$firephp->fb('Message', FirePHP::*);
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



function myEcho($myArray){
	echo '<pre style="font-size:12px;">';
	print_r(odbc_fetch_array($myArray));
	echo '</pre>';
}
function debugger ($txt){
	if ($GLOBALS['config']['debugger']) echo "\n".'<br><b style="color:red">debugger</b>:: '.time().' :: '.$txt;
}
function dbFrom($dbName, $toSelect, $conditions){
	global $queryStats;
	global $cached;
	global $executed;
	global $cache;
	global $out;
	global $log;
	
	$thisqueryStats= new execStats('thisquery');
	$thisqueryStats->start();
		
	$queryStats->start();
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
	$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']['pathToDbFiles'].";Exclusive=NO;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=false;"; //DELETTED=1??
	//$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']['pathToDbFiles'].";collate=Machine;NULL=NO;DELETED=1;"; //DELETTED=1??
	//echo $dsn;
	//connect to database
	$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_IF_NEEDED ) or die('Could Not Connect to ODBC Database!');
	//$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_DRIVER ) or die('Could Not Connect to ODBC Database!');
	//$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_ODBC ) or die('Could Not Connect to ODBC Database!');
	//$odbc=odbc_connect($dsn," "," ") or die('Could Not Connect to ODBC Database!');

	//query string
	//	$query= "SELECT * FROM ".$dbFile." WHERE F_DATBOL >= #".$startDate."# AND F_DATBOL <= #".$endDate."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE ";
	$query= $toSelect." FROM ".$dbFile." $conditions";

	//echo '<br>'.$query;
	$cacheEnabled=FALSE;
	if(array_key_exists($query, $cache) && $cacheEnabled){
		//uso il risultato della cache
		$result=$cache[$query];
		$cached++;
	}else{
		//query execution
		$result = odbc_exec($odbc, $query) or die ( debugger($query.odbc_errormsg()) );
		$executed++;
		$cache[$query]=$result;
		$thisqueryStats->stop();
		$log->info($query.' ***** '.$thisqueryStats->printStats());
	}

	//chiudo la connessione al databse
	//odbc_close($odbc);

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
	//inserisco la query nella cache

	$queryStats->stop();
	return $result;
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
      $this->$nome=new Proprietà($nome, $campoDbf, $validatore, $this);
		return $this;
  	}
  	public function getName(){
  		return get_class($this);	
	}
	public function getDataFromDb(){
		$result=dbFrom($this->_dbName->getVal(), 'SELECT *', "WHERE ".$this->codice->campoDbf."='".odbc_access_escape_str($this->codice->getVal())."'");
		while($row = odbc_fetch_array($result)){
		    foreach($this as $key => $value) {
				//escludo le prorpietà che iniziano con "_" in quanto sono solo ad uso interno e non le devo ricavare dal database
				if($key[0]!='_'){
					$val=$code=$row[$value->campoDbf];
					$this->$key->setVal($val);
				}
			}
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
/**/
			if(is_array($this->$key)){
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
/**/
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

			//se la data ha il formato gg/mm/aaaa la trasformo in mm-gg-aaaa
			//come richiesto dal database
			if($type['type']=='Date' && preg_match('/.*\/.*\/.*/',$newVal)){
				$arr=explode("/", $newVal);
										//mese    //giorno //anno
				$newVal=mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]);
				$newVal=date ( 'm-d-Y' , $newVal);
			}
		}

		return $this->valore=$newVal;
	}

	public function getVal(){
		return $this->valore;
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
		$parentObj=$this->_parent;
		$result=dbFrom($parentObj->_dbName->getVal(), 'SELECT '.$this->campoDbf, "");

		$out=Array();
		$out['name']=odbc_field_name($result,1);//nome del campo
		$out['len']=odbc_field_len($result,1);//lunghezza
		$out['type']=odbc_field_type($result,1);//tipo=Date/Numeric/Char
		$out['num']=odbc_field_num($result,1); //bho - vuoto?
		$out['scale']=odbc_field_scale($result,1); //bho - vuoto?
		$out['precision']=odbc_field_precision($result,1); //bho - vuoto?
		/*
		echo '<pre>';
		print_r($out);
		*/
		return $out;
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

		//configurazione database
		$this->addProp('_dbName','');
		$this->_dbName->setVal('INTESTAZIONEFT');
		//imposto il codice che mi sono passato nel costruttore
		//todo
		//$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		//$this->getDataFromDb();	
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
	
		
		$this->righe=array();
		
		//configurazione database
		$this->addProp('_dbName','');
		$this->_dbName->setVal('INTESTAZIONEDDT');
		$this->addProp('_dbName2','');
		$this->_dbName2->setVal('RIGHEDDT');
		//imposto il codice che mi sono passato nel costruttore
		$this->numero->setVal($params['numero']);		
		$this->data->setVal($params['data']);
		//ricavo i dati dal database
		$this->getDataFromDb();
		//echo '<pre>';
		//print_r($this);
	}
	
	public function getDataFromDb(){
		//questa rimpiazza la funzione con stesso nome ereditata dalla classe MyClass
		//recupero l'intestazione del ddt
		$result=dbFrom($this->_dbName->getVal(), 'SELECT *', "WHERE ".$this->numero->campoDbf."='".odbc_access_escape_str($this->numero->getVal())."' AND ".$this->data->campoDbf."=#".odbc_access_escape_str($this->data->getVal()."#"));
		while($row = odbc_fetch_array($result)){
			foreach($this as $key => $value) {
				//escludo le prorpietà che iniziano con "_" in quanto sono solo ad uso interno e non le devo ricavare dal database
				if($key[0]!='_' && is_object($this->$key)){
					$val=$row[$value->campoDbf];
					if($val) {
						$this->$key->setVal($val);					
					//}else{
					//	echo '<BR>missing val: '.$key;
					}
				}
			}
		}
		
		//recupero le righe del ddt
		$result=dbFrom($this->_dbName2->getVal(), 'SELECT *', "WHERE ".$this->numero->campoDbf."='".odbc_access_escape_str($this->numero->getVal())."' AND ".$this->data->campoDbf."=#".odbc_access_escape_str($this->data->getVal()."#"));
		while($row = odbc_fetch_array($result)){
			array_push($this->righe, new Riga(array('ddt_numero'=>$this->numero->getVal(),'ddt_data'=>$this->data->getVal(),'numero'=>$row['F_PROGRE'])));
			/*todo fix righe*/
			//echo 'test';
		}

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
		$this->addProp('peso_netto',				'F_QTA');
//		$this->addProp('tara',						'F_');
//		$this->addProp('origine',					'F_');
//		$this->addProp('categoria',					'F_');
//		$this->addProp('lotto',						'F_');
		$this->addProp('cod_iva',					'F_CODIVA');
		$this->addProp('stato',						'F_STATO');
		$this->addProp('cod_cliente',				'F_CODCLI');

		/* TODO= FIX RIGHE FATTURE*/
		//configurazione database
		$this->addProp('_dbName','');
		$this->_dbName->setVal('RIGHEDDT');
		//imposto il codice che mi sono passato nel costruttore
		$this->numero->setVal($params['numero']);		
		$this->ddt_data->setVal($params['ddt_data']);
		$this->ddt_numero->setVal($params['ddt_numero']);
		
		//ricavo i dati dal database
		$this->getDataFromDb();
		//todo
	}
	public function getDataFromDb(){
		//questa rimpiazza la funzione con stesso nome ereditata dalla classe MyClass
		$result=dbFrom($this->_dbName->getVal(), 'SELECT *', "WHERE ".$this->ddt_numero->campoDbf."='".odbc_access_escape_str($this->ddt_numero->getVal())."' AND ".$this->ddt_data->campoDbf."=#".odbc_access_escape_str($this->ddt_data->getVal()."# AND ".$this->numero->campoDbf."=".odbc_access_escape_str($this->numero->getVal()).""));
		while($row = odbc_fetch_array($result)){
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('ANAGRAFICAARTICOLI');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();		
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

		//configurazione database
		$this->addProp('_dbName','');
		$this->_dbName->setVal('ANAGRAFICACLIENTI');
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
		$this->addProp('codice',					'F_SUFFCLI');
		$this->addProp('cod_cliente',				'F_CODCLI');
		$this->addProp('ragionesociale',			'F_RAGSOC');	
		$this->addProp('via',						'F_INDIRI');
		$this->addProp('paese',						'F_LOCALI');
		$this->addProp('citta',						'F_PROV');
		$this->addProp('cap',						'F_CAP');
		$this->addProp('note',						'F_NOTE');
		
		//configurazione database
		$this->addProp('_dbName','');
		$this->_dbName->setVal('DESTINAZIONICLIENTI');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		$this->cod_cliente->setVal($params['cod_cliente']);
		
		//ricavo i dati dal database
		$this->getDataFromDb();			
	}
	public function getDataFromDb(){
		//questa rimpiazza la funzione con stesso nome ereditata dalla classe MyClass
		$result=dbFrom($this->_dbName->getVal(), 'SELECT *', "WHERE ".$this->codice->campoDbf."='".odbc_access_escape_str($this->codice->getVal())."' AND ".$this->cod_cliente->campoDbf."='".odbc_access_escape_str($this->cod_cliente->getVal())."'");
		while($row = odbc_fetch_array($result)){
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('ANAGRAFICAVETTORI');
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('CAUSALIPAGAMENTO');
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('CAUSALIIVA');
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('LISTINOPREZZI');
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('ANAGRAFICABANCHE');
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('CAUSALIMAGAZZINO');
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('CAUSALISPEDIZIONE');
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
		$this->addProp('_dbName','');
		$this->_dbName->setVal('ANNOTAZIONIDDT');
		//imposto il codice che mi sono passato nel costruttore
		$this->codice->setVal($params['codice']);
		//ricavo i dati dal database
		$this->getDataFromDb();	
	}
}

class MyList {
	function __construct() {
		$this->arr=array();
  	}
	function createFromQuery(){
		$result=dbFrom('INTESTAZIONEDDT', 'SELECT *', "WHERE ".'F_DATBOL'.">#".'07-29-2011'."#");
		while($row = odbc_fetch_array($result)){
			$newObj=new Ddt(array('numero'=>$row['F_NUMBOL'],'data'=>$row['F_DATBOL']));
			$this->add($newObj);
		}
	}
	function sum($prop){
		//restituisce la somma della proprietà indicata degli oggetti della lista
		foreach ($this->arr as $key => $value){
			/*
			echo $value->numero->getVal();
			echo ' '.$value->data->getVal();
			echo ' '.$value->cod_destinatario->extend()->ragionesociale->getVal();
			echo '<br>';
			*/
			echo $value->$prop->getVal().'<br>';
		}
	}
	function add($newObj){
		//add a new object to the current array
		array_push($this->arr, $newObj);
	}
	function remove(){
	}
	function iterate($function){
		//restituisce la somma della proprietà indicata degli oggetti della lista
		foreach ($this->arr as $key => $value){
			$function($value);
		}
	}
}

function page_start(){
	ob_start();
	global $log, $queryStats, $pageStats, $cache, $cached, $executed, $out;
	$log = FirePHP::getInstance(true);
	$queryStats= new execStats('query');
	$pageStats= new execStats('page');
	$pageStats->start();
	$cache=array();
	$cached=0;
	$executed=0;
}
function page_end(){
	global $log, $queryStats, $pageStats, $cache, $cached, $executed, $out;

	$log->info($queryStats->printStats());
	$log->info('Queri Eseguite: '.$executed.' | Query Risparmiate (cache): '.$cached);

	$pageStats->stop();
	$log->info($pageStats->printStats());
	ob_flush() ;
	ob_end_flush ();
}
?>