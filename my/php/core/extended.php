<?php

class DefaultClass {
	public function __call($method, $args){
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
	public function addProp($nome, $validatore, $isKey) {
	  $this->$nome=new Proprietà($nome, $validatore, $isKey, $this);
		return $this;
	}
	public function getKeys() {
		$keys=[];
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
	public function getFromDb(){
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
	public function saveToDb(){
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
}
/*==========================================================================================================================*/

class Proprietà extends DefaultClass {
	function __construct($nome, $validatore='', $isKey=FALSE, $parent){
	 	$this->nome=$nome;
	 	$this->valore='';
		$this->isKey=$isKey;
		$this->_parent=$parent;
	}

	public function setVal($newVal){
		return $this->valore=$newVal;
	}

	public function getVal(){
		return $this->valore;
	}
	
	public function validate(){
	/*stub*/
	}
}


/*==========================================================================================================================*/




class Ddt  extends MyClass {
	function __construct($params) {
		$this->addProp('numero', '', TRUE);
  		$this->addProp('data', '', TRUE);
		
		$this->addProp('cod_destinatario');
		$this->addProp('cod_destinazione');
		
		$this->addProp('cod_causale');
		
		$this->addProp('cod_mezzo'); //mittente / destinatario / vettore carico mittente / vettore carico destinatario
		$this->addProp('cod_vettore');
		
		$this->addProp('fatturabile');
		$this->addProp('fattura_numero');
		$this->addProp('fattura_data');

		$this->addProp('note');
		//$this->righe/**/

		$this->righe=array();
		
		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('data','numero'));

		//importo eventuali valori delle proprietà che mi sono passato come $params
		$this->mergeParams($params);

	}
}

class Riga extends MyClass {
	function __construct($params) {
		$this->addProp('ddt_data', TRUE);
		$this->addProp('ddt_numero', TRUE);

		$this->addProp('numero', TRUE);
		$this->addProp('cod_articolo');
		$this->addProp('unita_misura');
		$this->addProp('prezzo');
		$this->addProp('colli');

		$this->addProp('cod_imballo');
		$this->addProp('peso_lordo');
		$this->addProp('tara');
		$this->addProp('peso_netto');

		$this->addProp('lotto');
		$this->addProp('cod_iva');
		$this->addProp('stato');

		$this->mergeParams($params);
	}
}

?>