<?php
//error_reporting( E_ALL | E_STRICT );
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_COMPILE_ERROR);
ini_set('display_errors','On');
//attiva o disattiva i messaggi di debug
$config->debugger=0;//1=acceso || 0=spento
error_reporting(-1); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo
//set_time_limit (10); //0=nessun limite di tempo


require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/core/extended/classes.php');


//creo l'oggetto che conterrà tutte le configurazioni
$config=new stdClass();
global $config;



/*-------------------------------------
 **  PATH
-------------------------------------*/

// path alla cartella che contiene i file dbf di contab
$config->pathToDbFiles=realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/dati/'.'FILEDBF'.'/'.'CONTAB';
// path alla cartella che conterrà le stampe pdf generate
$config->pdfDir=realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/core/stampe';

/*-------------------------------------
**    sqLite
-------------------------------------*/
$config->sqlite=new stdClass();
$config->sqlite->database =realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/core/extended/magazzino.sqlite3';
/*-------------------------------------
**    DATI AZIENDALI
-------------------------------------*/
$params= Array(
	'ragionesociale'=> 'La Favorita di Brun G. & G. Srl Unip.',
	'via'			=> 'Camagre, 38/B',
	'paese'			=> 'Isola della Scala',
	'citta'			=> 'VR',
	'cap'			=> '37063',
	'p_iva'			=> '01588530236',
	'cod_fiscale'	=> '01588530236',
	'telefono'		=> '045-6630397',
	'fax'			=> '045-7302598',
	'email'			=> 'lafavorita_srl@libero.it',
//	'emailpec'		=> 'lafavorita_srl@pec.it',
	'website'		=> 'http://lafavorita.awardspace.com',
	'_autoExtend'	=> -1,
);

$config->azienda= new ClienteFornitore($params);
$config->azienda->addProp('_emailpec');
$config->azienda->addProp('_bndoo');
$config->azienda->addProp('_rea');
$config->azienda->addProp('_capitalesociale');
$config->azienda->addProp('_registroimprese');
$config->azienda->addProp('_logo');
$config->azienda->addProp('_ragionesocialeestesa');
$config->azienda->addProp('_titolare');

$config->azienda->_emailpec->setVal				('lafavorita_srl@pec.it');
$config->azienda->_bndoo->setVal				('001691/VR');
$config->azienda->_rea->setVal					('VR-185024');
$config->azienda->_capitalesociale->setVal		('€ 41.600,00');
$config->azienda->_registroimprese->setVal		('VR 01588530236');
$config->azienda->_logo->setVal					(realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/img/logo.gif');
$config->azienda->_ragionesocialeestesa->setVal	('DI BRUN G. & G. S.R.L. Unipersonale');
$config->azienda->_titolare->setVal				('Brun Gionni');
?>
