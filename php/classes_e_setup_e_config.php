<?php
/* -------------------------------------------------------------------------------------------------------
Questa libreria cotiene alcune classi/funzioni relativa a 
- fatture
- ddt
con validazione dei dati inseriti
----------------------------------------------------------------------------------------------------------
*/

include 'config.inc.php';
/*----------------------------------------------------
*/
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
//
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

//la mia classe di base con proprietà e metodi aggiuntivi
class MyClass extends DefaultClass{
   public function addProp($nome, $validatore, $dbTipo, $dbIndice, $dbLunghezza, $valore) {
      $this->$nome=new Proprietà($nome, $validatore, $dbTipo, $dbIndice, $dbLunghezza, $valore);
		return $this;
  	}
  	public function getName(){
  		return get_class($this);	
	}
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
}

/*----------------------------------------------------

*/
class Proprietà extends DefaultClass
{
	function __construct($nome, $validatore, $dbTipo='varchar', $dbIndice=false, $dbLunghezza, $valore) {
	 	$this->nome=$nome;
	 	$this->valore=$valore;
	 	$this->validatore=$validatore;
	 	$this->dbTipo=$dbTipo;
	 	$this->dbIndice=$dbIndice;
	 	$this->dbLunghezza=$dbLunghezza;
	 	$this->ValidateParams=array();
	}
	
  public function setVal($newVal)
  {
     return $this->valore=$newVal;
  }

  public function getVal()
  {
     return $this->valore;
  }

  public function validate()
  {
     //echo ' sto validando valido '.$this->nome;
     return Validate($this,$this->ValidateParams);
  }
}
/*----------------------------------------------------

*/
class Fattura extends MyClass{
	function __construct() {
		$this->addProp('id','','VARCHAR',TRUE,10);
		$this->addProp('numero','','VARCHAR',FALSE,6);
  		$this->addProp('data','','VARCHAR',FALSE,15);
  		$this->addProp('tipo','','VARCHAR',FALSE,5);
		$this->addProp('cod_destinatario','','VARCHAR',FALSE,7);
		$this->addProp('cod_destinazione','','VARCHAR',FALSE,7);
		$this->addProp('ddt','','VARCHAR',FALSE,1000);
		//$this->addProp('tot_fattura','','VARCHAR',FALSE,20);
		$this->addProp('cod_banca','','VARCHAR',FALSE,7);
		$this->addProp('cod_condizioni_pagamento','','VARCHAR',FALSE,7);

		$this->data->ValidateParams=array(
			'minLength'=>9,
			'isNumeric'=>true,
		);	
		$this->cod_destinazione->ValidateParams=array(
			'notEmpty'=>false,
		);	
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
  	public function stampa(){
  		echo $this->cod_destinatario->getVal();	
  	}
}
/*----------------------------------------------------

*/
class Ddt  extends MyClass {
	function __construct() {
		$this->addProp('id','','VARCHAR',TRUE,10);
		$this->addProp('numero','','VARCHAR',FALSE,6);
  		$this->addProp('data','','VARCHAR',FALSE,15);
		$this->addProp('cod_destinatario','','VARCHAR',FALSE,7);
		$this->addProp('cod_destinazione','','VARCHAR',FALSE,7);
		$this->addProp('cod_causale','','VARCHAR',FALSE,7);
		$this->addProp('cod_mezzo','','VARCHAR',FALSE,7);
		$this->addProp('cod_vettore','','VARCHAR',FALSE,7);
		$this->addProp('ora_inizio_trasporto','','VARCHAR',FALSE,15);
		$this->addProp('aspetto_beni','','VARCHAR',FALSE,30);
		$this->addProp('annotazioni','','VARCHAR',FALSE,1000);
		$this->addProp('tot_colli','','VARCHAR',FALSE,20);
		$this->addProp('tot_peso','','VARCHAR',FALSE,20);
		$this->addProp('righe','','VARCHAR',FALSE,1000);//contiene un array con gli id delle righe della fattura
	}
  	public function getChildObjects(){
		//get RIGHE
  		$riga=split(',', $this->righe->getVal());
  		foreach ($riga as $key => $value){
  			$this->oRiga[$key]=$GLOBALS['wc']->obj->riga->getFromDbById($value);
  		}
  	}
}
/*----------------------------------------------------

*/
class Riga  extends MyClass {
	function __construct() {
		$this->addProp('id','','VARCHAR',TRUE,10);
		$this->addProp('cod_articolo','','VARCHAR',FALSE,7);
		$this->addProp('unita_misura','','VARCHAR',FALSE,2);
		$this->addProp('colli','','VARCHAR',FALSE,20);
		$this->addProp('cod_imballo','','VARCHAR',FALSE,7);
		$this->addProp('peso_lordo','','VARCHAR',FALSE,20);
		$this->addProp('peso_netto','','VARCHAR',FALSE,20);
		$this->addProp('tara','','VARCHAR',FALSE,20);
		$this->addProp('origine','','VARCHAR',FALSE,2);
		$this->addProp('categoria','','VARCHAR',FALSE,2);
		$this->addProp('lotto','','VARCHAR',FALSE,50);
		$this->addProp('cod_iva','','VARCHAR',FALSE,7);
	}
}
/*----------------------------------------------------

*/
class Articolo extends MyClass {
	function __construct() {
		$this->addProp('id','','VARCHAR',TRUE,10);
		$this->addProp('codice','','VARCHAR',FALSE,7);
		$this->addProp('descrizione','','VARCHAR',FALSE,30);
	}
}
/*----------------------------------------------------

*/
class Imballaggio extends MyClass {
	function __construct() {
		$this->addProp('id','','VARCHAR',TRUE,10);
		$this->addProp('codice','','VARCHAR',FALSE,7);
		$this->addProp('descrizione','','VARCHAR',FALSE,100);
		$this->addProp('tara_acquisto','','VARCHAR',FALSE,20);
		$this->addProp('tara_vendita','','VARCHAR',FALSE,20);
	}
}
/*----------------------------------------------------

*/
class ClienteFornitore extends MyClass {
	function __construct() {
		$this->addProp('id','','VARCHAR',TRUE,10);
		$this->addProp('codice','','VARCHAR',FALSE,7);
		$this->addProp('rag_sociale','','VARCHAR',FALSE,100);
		$this->addProp('via','','VARCHAR',FALSE,100);
		$this->addProp('paese','','VARCHAR',FALSE,100);
		$this->addProp('citta','','VARCHAR',FALSE,2);
		$this->addProp('cod_pagamento','','VARCHAR',FALSE,7);
		$this->addProp('cod_banca','','VARCHAR',FALSE,7);
		$this->addProp('cod_mezzo','','VARCHAR',FALSE,7);
		$this->addProp('cod_vettore','','VARCHAR',FALSE,7);
		$this->addProp('p_iva','','VARCHAR',FALSE,11);
		$this->addProp('cod_fiscale','','VARCHAR',FALSE,16);
		$this->addProp('cod_iva','','VARCHAR',FALSE,7);
		$this->addProp('lettera_intento_num','','VARCHAR',FALSE,10);
		$this->addProp('lettera_intento_data','','VARCHAR',FALSE,15);
		$this->addProp('lettera_intento_numinterno','','VARCHAR',FALSE,10);
		$this->addProp('provvigione','','VARCHAR',FALSE,3);
	}
}

/*----------------------------------------------------

*/
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


/*----------------------------------------------------

*/
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

/*----------------------------------------------------

*/
class Logger {
   function __construct() {
		$this->log='';   	
	}
	function log($msg){
		$now= microtime();
		$this->log[]="$now - ".$msg;
	}
	function stampa(){
		echo '<pre><br>------------------------------------';
		echo '<br>|Printing LOG-MSG from older to newer';
		echo '<br>------------------------------------<br>|';
		echo implode('<br>|',$this->log);
		echo '<br>------------------------------------</pre>';
	}
}

$log=new Logger();


$wc=new WebContab();
//eseguo il setup/instllazione iniziale  di webContab sul database
$wc->setup();

/*
$fattura=$wc->obj->fattura->getFromDbById(1);

echo '<pre>';
print_r($fattura);
echo '</pre>';


//$newFattura=$wc->obj->fattura;
//$newFattura->id->setVal('');
//$newFattura->saveToDb();
$fattura->getChildObjects();


//ha problemi
//$fattura->validate();


$log->stampa();

echo '<pre>'.json_encode($fattura).'</pre>';
*/
?>