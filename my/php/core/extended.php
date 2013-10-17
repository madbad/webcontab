<?php
class Ddt  extends MyClass {
	function __construct($params) {
		$this->addProp('numero');
  		$this->addProp('data');
		
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
		$this->addProp('ddt_data');
		$this->addProp('ddt_numero');

		$this->addProp('numero');
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

		//configurazione database
		//chiave(i) di ricerca del database
		$this->addProp('_dbIndex');
		$this->_dbIndex->setVal(array('ddt_data','ddt_numero','numero'));
}

?>