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
		case 'RIGHEDDT': 				$dbFile='03BORIGD.DBF' ;break;
		case 'RIGHEFT': 				$dbFile='03FARIGD.DBF' ;break; 
		case 'INTESTAZIONEDDT':			$dbFile='03BOTESD.DBF' ;break; 
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
/*
###########################################################################################
###########################################################################################
###########################################################################################
###########################################################################################
*/	
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

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
/* -------------------------------------------------------------------------------------------------------
Questa libreria cotiene alcune classi/funzioni relativa a 
- fatture
- ddt
con validazione dei dati inseriti
----------------------------------------------------------------------------------------------------------
*/

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
   public function addProp($nome, $validatore, $dbTipo, $dbIndice, $dbLunghezza, $valore, $campoDbf) {
      $this->$nome=new Proprietà($nome, $validatore, $dbTipo, $dbIndice, $dbLunghezza, $valore, $campoDbf);
		return $this;
  	}
  	public function getName(){
  		return get_class($this);	
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

/*----------------------------------------------------

*/
class Proprietà extends DefaultClass
{
	function __construct($nome, $validatore, $dbTipo='varchar', $dbIndice=false, $dbLunghezza, $valore, $campoDbf) {
	 	$this->nome=$nome;
	 	$this->valore=$valore;
	 	$this->validatore=$validatore;
	 	$this->dbTipo=$dbTipo;
	 	$this->dbIndice=$dbIndice;
	 	$this->dbLunghezza=$dbLunghezza;
	 	$this->ValidateParams=array();
		$this->campoDbf=$campoDbf;
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
/*
TODO: FATTURA FATESD // FARIGD
*/

class Ddt  extends MyClass {
	function __construct() {
		$this->addProp('numero',					'','VARCHAR',FALSE,6,	'','F_NUMBOL');
  		$this->addProp('data',						'','VARCHAR',FALSE,15,	'','F_DATBOL');
		$this->addProp('cod_destinatario',			'','VARCHAR',FALSE,7,	'','F_CODCLI');
		$this->addProp('cod_destinazione',			'','VARCHAR',FALSE,7,	'','F_SUFCLI');
		$this->addProp('cod_causale',				'','VARCHAR',FALSE,7,	'','F_TIPODOC');//V=VENDITA D=DEPOSITO
		$this->addProp('cod_mezzo',					'','VARCHAR',FALSE,7,	'','F_SPEDIZ');
		$this->addProp('cod_vettore',				'','VARCHAR',FALSE,7,	'','F_VET');
		$this->addProp('ora_inizio_trasporto',		'','VARCHAR',FALSE,15,	'','F_');
		$this->addProp('aspetto_beni',				'','VARCHAR',FALSE,30,	'','F_');
		$this->addProp('annotazioni',				'','VARCHAR',FALSE,1000,'','F_');
		$this->addProp('peso_lordo',				'','VARCHAR',FALSE,20,	'','F_PLORDO');
		$this->addProp('peso_netto',				'','VARCHAR',FALSE,20,	'','F_PNETTO');
		$this->addProp('tot_colli',					'','VARCHAR',FALSE,20,	'','F_TOTCOLLI');
		$this->addProp('tot_peso',					'','VARCHAR',FALSE,20,	'','F_QTATOT');
		$this->addProp('cod_pagamento',				'','VARCHAR',FALSE,7,	'','F_CONPAG');
		$this->addProp('cod_banca',					'','VARCHAR',FALSE,7,	'','F_BANCA');
		$this->addProp('stato',						'','VARCHAR',FALSE,7,	'','F_STATO');
		$this->addProp('valuta',					'','VARCHAR',FALSE,3,	'','F_CODVAL');
		$this->addProp('fatturabile',				'','VARCHAR',FALSE,3,	'','F_SINOFATT');
		$this->addProp('tipocodiceclientefornitore','','VARCHAR',FALSE,3,	'','F_TIPOCF');
		$this->addProp('fattura_numero',			'','VARCHAR',FALSE,3,	'','F_NUMFATT');
		$this->addProp('fattura_data',				'','VARCHAR',FALSE,3,	'','F_DATFATT');
		$this->addProp('note',						'','VARCHAR',FALSE,3,	'','F_NOTE');
		$this->addProp('note1',						'','VARCHAR',FALSE,3,	'','F_NOTE1');
		$this->addProp('note2',						'','VARCHAR',FALSE,3,	'','F_NOTE2');

		
		
		$this->addProp('righe',						'','VARCHAR',FALSE,1000,'','F_');//contiene un array con gli id delle righe della fattura
	}
  	public function getChildObjects(){
		//get RIGHE
  		$riga=split(',', $this->righe->getVal());
  		foreach ($riga as $key => $value){
  			$this->oRiga[$key]=$GLOBALS['wc']->obj->riga->getFromDbById($value);
  		}
  	}
  	public function getNumber(){
		$this->numero=8;
		return $this->numero;
  	}
}
/*----------------------------------------------------

*/
class Riga extends MyClass {
	function __construct() {
		$this->addProp('numero',					'','VARCHAR',FALSE,7,	'','F_PROGRE');	
		$this->addProp('cod_articolo',				'','VARCHAR',FALSE,7,	'','F_CODPRO');
		$this->addProp('descrizione',				'','VARCHAR',FALSE,7,	'','F_DESPRO');
		$this->addProp('unita_misura',				'','VARCHAR',FALSE,2,	'','F_UM');
		$this->addProp('prezzo',					'','VARCHAR',FALSE,20,	'','F_PREUNI');
		$this->addProp('imponibile',				'','VARCHAR',FALSE,20,	'','F_IMPONI');
		$this->addProp('importo_iva',				'','VARCHAR',FALSE,20,	'','F_IMPIVA');
		$this->addProp('importo_totale',			'','VARCHAR',FALSE,20,	'','F_IMPORTO');		
		$this->addProp('colli',						'','VARCHAR',FALSE,20,	'','F_NUMCOL');
		$this->addProp('cod_imballo',				'','VARCHAR',FALSE,7,	'','F_');
		$this->addProp('peso_lordo',				'','VARCHAR',FALSE,20,	'','F_');
		$this->addProp('peso_netto',				'','VARCHAR',FALSE,20,	'','F_PESNET');
		$this->addProp('peso_netto',				'','VARCHAR',FALSE,20,	'','F_QTA');
		$this->addProp('tara',						'','VARCHAR',FALSE,20,	'','F_');
		$this->addProp('origine',					'','VARCHAR',FALSE,2,	'','F_');
		$this->addProp('categoria',					'','VARCHAR',FALSE,2,	'','F_');
		$this->addProp('lotto',						'','VARCHAR',FALSE,50,	'','F_');
		$this->addProp('ddt_data',					'','VARCHAR',FALSE,7,	'','F_DATBOL');
		$this->addProp('ddt_numero',				'','VARCHAR',FALSE,7,	'','F_NUMBOL');
		$this->addProp('cod_iva',					'','VARCHAR',FALSE,7,	'','F_CODIVA');
		$this->addProp('stato',						'','VARCHAR',FALSE,7,	'','F_STATO');
		$this->addProp('cod_cliente',				'','VARCHAR',FALSE,7,	'','F_CODCLI');		
	}
}
/*----------------------------------------------------

*/
class Articolo extends MyClass {
	function __construct() {
		$this->addProp('codice',					'','VARCHAR',FALSE,7,	'','F_CODPRO');
		$this->addProp('descrizione',				'','VARCHAR',FALSE,30,	'','F_DESPRO');
		$this->addProp('descrizione2',				'','VARCHAR',FALSE,30,	'','F_DESPR2');
		$this->addProp('unitadimisura',				'','VARCHAR',FALSE,30,	'','F_UMACQ');
		$this->addProp('cod_iva',					'','VARCHAR',FALSE,7,	'','F_CODIVA');		
	}
}
/*----------------------------------------------------

*/
class Imballaggio extends MyClass {
	function __construct() {
		$this->addProp('codice',					'','VARCHAR',FALSE,		'','F_CODCLI');
		$this->addProp('descrizione',				'','VARCHAR',FALSE,100,	'','F_CODCLI');
		$this->addProp('tara_acquisto',				'','VARCHAR',FALSE,20,	'','F_CODCLI');
		$this->addProp('tara_vendita',				'','VARCHAR',FALSE,20,	'','F_CODCLI');
	}
}
/*----------------------------------------------------

*/
class ClienteFornitore extends MyClass {
	function __construct($params) {

		//$this->addProp('codice',					'','VARCHAR',FALSE,7,	'','F_CODFOR');	
		$this->addProp('codice',					'','VARCHAR',FALSE,7,	'','F_CODCLI');
		$this->addProp('ragionesociale',			'','VARCHAR',FALSE,100,	'','F_RAGSOC');
		$this->addProp('via',						'','VARCHAR',FALSE,100,	'','F_INDIRI');
		$this->addProp('paese',						'','VARCHAR',FALSE,100,	'','F_LOCALI');
		$this->addProp('citta',						'','VARCHAR',FALSE,2,	'','F_PROV');
		$this->addProp('cap',						'','VARCHAR',FALSE,2,	'','F_CAP');
		$this->addProp('cod_destinazione',			'','VARCHAR',FALSE,7,	'','F_CODDESTABI');
		$this->addProp('cod_pagamento',				'','VARCHAR',FALSE,7,	'','F_CONPAG');
		$this->addProp('cod_banca',					'','VARCHAR',FALSE,7,	'','F_BANCA');
		$this->addProp('cod_mezzo',					'','VARCHAR',FALSE,7,	'','F_SPEDIZ');
		$this->addProp('cod_vettore',				'','VARCHAR',FALSE,7,	'','F_VET');
		$this->addProp('p_iva',						'','VARCHAR',FALSE,11,	'','F_PIVA');
		$this->addProp('cod_fiscale',				'','VARCHAR',FALSE,16,	'','F_CODFIS');
		$this->addProp('cod_iva',					'','VARCHAR',FALSE,7,	'','F_CODIVA');
		$this->addProp('lettera_intento_num',		'','VARCHAR',FALSE,10,	'','F_NUMINTEN');
		$this->addProp('lettera_intento_data',		'','VARCHAR',FALSE,15,	'','F_DATINTEN');
		$this->addProp('lettera_intento_numinterno','','VARCHAR',FALSE,10,	'','F_REGIS');
		$this->addProp('provvigione',				'','VARCHAR',FALSE,3,	'','F_CODPROVV');
		$this->addProp('tipo',						'','VARCHAR',FALSE,3,	'','F_GRCOCLI'); //15==CLIENTE //61==FORNITORE
		$this->addProp('telefono',					'','VARCHAR',FALSE,3,	'','F_TELEF');
		$this->addProp('cellulare',					'','VARCHAR',FALSE,3,	'','F_TELEX');
		$this->addProp('fax',						'','VARCHAR',FALSE,3,	'','F_TELEFAX');
		$this->addProp('email',						'','VARCHAR',FALSE,3,	'','F_EMAIL');
		$this->addProp('website',					'','VARCHAR',FALSE,3,	'','F_HOMEPAGE');
		$this->addProp('valuta',					'','VARCHAR',FALSE,3,	'','F_CODVAL');		

		
		//$params['codice']= str_ireplace("'", "/'", $params['codice']);
		$this->codice->setVal($params['codice']);
		$this->getDataFromDb();
	}
	/*
	public function getRagioneSociale($codice){
		$result=dbFrom('ANAGRAFICACLIENTI', 'SELECT *', "WHERE F_CODCLI='".$this->codice->getVal()."'");
		while($row = odbc_fetch_array($result)){
			$this->ragionesociale->setVal($row['F_RAGSOC']);
		}	
		return $this->ragionesociale->getVal();
	}
	*/
	public function getDataFromDb(){
		$result=dbFrom('ANAGRAFICACLIENTI', 'SELECT *', "WHERE F_CODCLI='".odbc_access_escape_str($this->codice->getVal())."'");
		while($row = odbc_fetch_array($result)){
		    foreach($this as $key => $value) {
				$val=$code=$row[$value->campoDbf];
				$this->$key->setVal($val);
			}
		}
		
		$dbClienti=getDbClienti();
		$codCliente=$this->codice->getVal();
		$this->tipo->setVal($dbClienti["$codCliente"]['tipo']);
		
		return $this->ragionesociale;
		
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


$wc=new WebContab();
//eseguo il setup/instllazione iniziale  di webContab sul database
//$wc->setup();






































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
?>