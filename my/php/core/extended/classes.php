<?php
/*to fix anche il tipo andrebbe inserito nelle primary key delle fatture?? se una fattura e una nota credito hanno stesso $NUMERO e $DATA cosa succede??*/



//require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'//webContab/my/php/libs/FirePHPCore/FirePHP.class.php');
//classe per l'invio di email
//require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'//webContab/my/php/libs/phpmailer/class.phpmailer.php');

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
   public function addProp($nome, $validatore=null) {
		$this->$nome=new Proprietà($nome, $validatore, $this);
		return $this;
	}
	public function getName(){
		return get_class($this);
	}
	public function getDbName(){
		return get_class($this);
	}
	public function getPropertiesNames(){
		$props = get_object_vars($this);
		$out='';
		foreach ($props as $prop){
			$out[]= $prop->nome;
		}
		print_r($out);
		return $out;
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
		FUNZIONI RELATIVE AL $DATABASE ESTERNO SQLITE
   */#########################################################
	public function createDb(){
		//genera un $DATAbase esterno sqLite se non presente sulla base delle proprietà sqLite definite nella classe dell'oggetto
		$fields=$this->getPropertiesNames();
		$table=$this->getDbName();
		$indexes=$this->getDbKeys();
		
		$sqlite=$GLOBALS['config']->sqlite;
		
		$fieldsToAdd='';
		//campi normali
		$fieldsToAdd.=implode($fields,' TEXT, ').' TEXT,';
		//campi indice
		//$fieldsToAdd.=implode($indexes,' TEXT NOT NULL, ');
		//chiavi primarie
		$fieldsToAdd.=' PRIMARY KEY ('.implode($indexes,',').')';
		echo $sqlite->database;
		//apro il $DATAbase
		$db = new SQLite3($sqlite->database);
		//creo la tabella
		$query="CREATE TABLE if not exists $table($fieldsToAdd)";
		$db->exec($query) or die($query);
		return;
	}
	public function saveToDb(){
		//salva i dati nel $DATAbase sqLite
		$fields=$this->getPropertiesNames();
		$table=$this->getDbName();
		$indexes=$this->getDbKeys();
		
		$sqlite=$GLOBALS['config']->sqlite;
		
		//elenco di tutti i campi da aggiornare
		$fields= array_merge ($fields, $indexes);
		
		//creo l'elenco di tutti i valori da memorizzare
		$values=array();
		foreach ($fields as $field){
			$val=$this->$field->getVal();
			$values[]=(string) $this->$field->getVal();
			if($val=='' && in_array($field, $indexes)){
				//abortisco una delle chiavi primarie è nulla: non posso salvare nel $DATAbase (e comunque non avrebbe senso farlo)
				return;
			}
		}
		//aggiungo le '' per evitare che il $TESTO venga trattato numericamente
		$values=implode($values,"','");
		$values="'".$values."'";

		//apro il $DATAbase
		$db = new SQLite3($sqlite->database);
		//creo la tabella
		//to fix : letto su internet che se vado ad aggiornare una riga com esempio solo 3 campi su quattro il campo che non vado ad aggiornare in questo momento con i nuovi valori viene resettato al valore di default o messo a null
		$query="INSERT OR REPLACE INTO $table (".implode($fields,',').") VALUES ($values)";

		$db->exec($query) or die($query);
		return;
	}
	
	public function getFromDb(){
		//ricava tutti i dati presente nel $DATAbase
		$fields=$this->getPropertiesNames();
		$table=$this->getDbName();
		$indexes=$this->getDbKeys();
		
		$sqlite=$GLOBALS['config']->sqlite;
		
		$where='WHERE ';
		$order=' ORDER BY ';
		
		foreach($indexes as $key => $property){
			if($key>0){
				$where.=' AND ';
				$order.=',';
			}
			$where.=$this->$property->nome."=".$separatore.$this->$property->getVal().$separatore;
			$order.=$this->$property->nome;
		}			
		//la stringa della query
		$query='SELECT * FROM '.$table.' '.$where.$order;
		//apro il $DATAbase ed eseguo la query
		$db = new SQLite3($sqlite->database);
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

class Proprietà extends DefaultClass {
	function __construct($nome, $validatore='', $parent){
	 	$this->nome=$nome;
	 	$this->validatore=$validatore;
	 	$this->valore='';
		$this->_parent=$parent;
	}
	public function setVal($newVal){
		return $this->valore=$newVal;
	}
	public function getVal(){
		return $this->valore;
	}
	public function getFormatted($params=''){
	}
	public function validate(){
	}
	public function getDataType(){
	}
}
Class Validatore {
	function __construct($params) {
		$this->lunghezza='';
		$this->interi='';
		$this->decimali='';
		$this->tipo = $params['tipo']; //testo $NUMERO misto
		$this->canBeNull='';
	}
	function validate ($obj){
		return 'true/false';
	}
}
$NUMERATORE = 	new Validatore(array('tipo'=>'INTEGER'));
$DATA = 			new Validatore(array('tipo'=>'INTEGER'));
$CODICE = 		new Validatore(array('tipo'=>'TEXT'));
$IMPORTO = 		new Validatore(array('tipo'=>'REAL'));
$TESTO = 		new Validatore(array('tipo'=>'TEXT'));
$NUMERO = 		new Validatore(array('tipo'=>'INTEGER'));
$ALBO = 			new Validatore(array('tipo'=>'TEXT'));
$PARTITAIVA = 	new Validatore(array('tipo'=>'INTEGER'));
$CODFISCALE = 	new Validatore(array('tipo'=>'TEXT'));
$TELEFONO = 		new Validatore(array('tipo'=>'TEXT'));
$MAIL = 			new Validatore(array('tipo'=>'TEXT'));
$URL = 			new Validatore(array('tipo'=>'TEXT'));

/*########################################################################################*/
class Ddt  extends MyClass {
	function __construct($params) {
		$this->addProp('numero', $NUMERATORE);
  		$this->addProp('data', $DATA);
		$this->addProp('causale_codice', $CODICE);
		$this->addProp('mezzo_codice', $CODICE);
		$this->addProp('vettore_codice', $CODICE);
		$this->addProp('fattura_numero', $NUMERATORE);
		$this->addProp('fattura_data', $DATA);
		$this->addProp('note');
		
		//$this->righe/**/
		//$this->righe=array();
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('numero','data');
	}
}

class Riga extends MyClass {
	function __construct($params) {
		$this->addProp('ddt_data', $DATA);
		$this->addProp('ddt_numero', $NUMERATORE);
		$this->addProp('numero', $NUMERATORE);
		$this->addProp('articolo_codice', $CODICE);
		$this->addProp('um_codice', $CODICE);
		$this->addProp('prezzo', $IMPORTO);
		$this->addProp('colli', $NUMERO);
		$this->addProp('imballo_codice', $CODICE);
		$this->addProp('pesolordo', $NUMERO);
		$this->addProp('tara', $NUMERO);
		$this->addProp('pesonetto', $NUMERO);
		$this->addProp('lotto', $TESTO);
		$this->addProp('iva_codice', $CODICE);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('ddt_data','ddt_numero','numero');
	}
}

class Articolo extends MyClass {
	function __construct($params) {
		$this->addProp('codice', $CODICE);
		$this->addProp('descrizione', $TESTO);
		$this->addProp('um_codice', $CODICE);
		$this->addProp('iva_codice', $CODICE);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('codice');
	}
}

class Imballaggio extends MyClass {
	function __construct($params) {
		$this->addProp('codice', $CODICE);
		$this->addProp('descrizione', $TESTO);
		$this->addProp('taraacquisto', $NUMERO);
		$this->addProp('taravendita', $NUMERO);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('codice');
	}
}

class ClienteFornitore extends MyClass {
	function __construct($params) {
		$this->addProp('codice', $CODICE);
		$this->addProp('ragionesociale', $TESTO);
		$this->addProp('via', $TESTO);
		$this->addProp('paese', $TESTO);
		$this->addProp('provincia', $TESTO);
		$this->addProp('cap', $NUMERO);
		$this->addProp('alboautotrasportatori', $ALBO);

		$this->addProp('mezzo_codice', $CODICE);
		$this->addProp('vettore_codice', $CODICE);
		$this->addProp('piva', $PARTITAIVA);
		$this->addProp('codfiscale', $CODICEFISCALE);
		$this->addProp('iva_codice', $CODICE);
		$this->addProp('telefono', $TELEFONO);
		$this->addProp('cellulare', $TELEFONO);
		$this->addProp('fax', $TELEFONO);
		$this->addProp('email', $MAIL);
		$this->addProp('web', $URL);
		$this->addProp('valuta', $TESTO);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('codice');
	}
}

class Iva extends MyClass {
	function __construct($params) {
		$this->addProp('codice', $CODICE);
		$this->addProp('descrizione', $TESTO);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('codice');
	}
}

class Causale extends MyClass {
	function __construct($params) {
		$this->addProp('codice', $CODICE);
		$this->addProp('descrizione', $TESTO);
		$this->addProp('segno', $SEGNO);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('codice');
	}
}
class Mezzo extends MyClass { //mittente / destinatario / vettore carico mittente / vettore carico destinatario
	function __construct($params) {
		$this->addProp('codice', $CODICE);
		$this->addProp('descrizione', $TESTO);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('codice');
	}
}
class Um extends MyClass {
	function __construct($params) {
		$this->addProp('codice', $CODICE);
		$this->addProp('descrizione', $TESTO);
		
		//importo eventuali valori delle proprietà che mi sono passato come $params
		//$this->mergeParams($params);
	}
	function getDbKeys(){
		return array('codice');
	}
}

?>
