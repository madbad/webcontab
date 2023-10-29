<?php

//require_once('./classes.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/core/classes.php');


//creo l'oggetto che conterrà tutte le configurazioni
$config=new stdClass();
global $config;

//attiva o disattiva i messaggi di debug
$config->debugger=0;//1=acceso || 0=spento
error_reporting(0); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo
//set_time_limit (10); //0=nessun limite di tempo

/*-------------------------------------
 **  PATH
-------------------------------------*/

// path alla cartella che contiene i file dbf di contab
$config->pathToDbFiles=realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/dati/'.'FILEDBF'.'/'.'CONTAB';
// path alla cartella che conterrà le stampe pdf generate
$config->pdfDir=realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/core/stampe';
$config->xmlDir=realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/core/stampe/ftXml';
$config->openSSLDir=realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/libs/openssl-1.0.2q-i386-win32';


/*-------------------------------------
**    PEC
-------------------------------------*/
$config->pec=new stdClass();
$config->pec->Host       = "ssl://smtps.pec.aruba.it"; // SMTP server //ricordarsi di decommentare "extension=php_openssl.dll" nel file php.ini !!! per abilitare l'autenticazione SSL
$config->pec->SMTPDebug  = 2;                     // (0)don't output any debug info (2)enables SMTP debug information (for testing)
$config->pec->SMTPAuth   = true;                  // enable SMTP authentication
$config->pec->Port       = 465;                    // set the SMTP port
$config->pec->Username   = ""; // SMTP account username
$config->pec->Password   = "";        // SMTP account password
$config->pec->From=new stdClass();
$config->pec->From->Mail ='lafavorita_srl@pec.it';     //chi invia
$config->pec->From->Name ='La Favorita Srl';     //chi invia
$config->pec->ReplyTo=new stdClass();
$config->pec->ReplyTo->Mail ='lafavorita_srl@pec.it';     //chi invia
$config->pec->ReplyTo->Name ='La Favorita Srl';     //chi invia     //chi invia

/*-------------------------------------
**    MAIL
-------------------------------------*/
/*
$config->gmail=new stdClass();
$config->gmail->Host       = "smtp.gmail.com"; // SMTP server //ricordarsi di decommentare "extension=php_openssl.dll" nel file php.ini !!! per abilitare l'autenticazione SSL
$config->gmail->SMTPDebug  = 0;                     // (0) disattivato (2)enables SMTP debug information (for testing)
$config->gmail->SMTPAuth   = true;                  // enable SMTP authentication
$config->gmail->Port       = 587;                    // set the SMTP port
$config->gmail->Username   = ""; // SMTP account username
$config->gmail->Password   = "";        // SMTP account password
$config->gmail->From=new stdClass();
$config->gmail->From->Mail ='favoritasrl@gmail.com';     //chi invia
$config->gmail->From->Name ='La Favorita Srl';     //chi invia
$config->gmail->ReplyTo=new stdClass();
$config->gmail->ReplyTo->Mail ='amministrazione@lafavoritasrl.it';     //chi invia
$config->gmail->ReplyTo->Name ='La Favorita Srl';     //chi invia     //chi invia
*/
/*
$config->gmail=new stdClass();
$config->gmail->Host       = "smtps.aruba.it"; // SMTP server //ricordarsi di decommentare "extension=php_openssl.dll" nel file php.ini !!! per abilitare l'autenticazione SSL
$config->gmail->SMTPDebug  = 0;                     // (0) disattivato (2)enables SMTP debug information (for testing)
$config->gmail->SMTPAuth   = true;                  // enable SMTP authentication
$config->gmail->Port       = 465;                    // set the SMTP port
$config->gmail->Username   = ""; // SMTP account username
$config->gmail->Password   = "";        // SMTP account password
$config->gmail->From=new stdClass();
$config->gmail->From->Mail ='amministrazione@lafavoritasrl.it';     //chi invia
$config->gmail->From->Name ='La Favorita Srl';     //chi invia
$config->gmail->ReplyTo=new stdClass();
$config->gmail->ReplyTo->Mail ='amministrazione@lafavoritasrl.it';     //chi invia
$config->gmail->ReplyTo->Name ='La Favorita Srl';     //chi invia     //chi invia
*/

$config->gmail=new stdClass();
$config->gmail->Host       = "smtp.lafavoritasrl.it"; // SMTP server //ricordarsi di decommentare "extension=php_openssl.dll" nel file php.ini !!! per abilitare l'autenticazione SSL
$config->gmail->SMTPDebug  = 0;                     // (0) disattivato (2)enables SMTP debug information (for testing)
$config->gmail->SMTPAuth   = true;                  // enable SMTP authentication
$config->gmail->Port       = 587;                    // set the SMTP port
$config->gmail->Username   = ""; // SMTP account username
$config->gmail->Password   = "";        // SMTP account password
$config->gmail->From=new stdClass();
$config->gmail->From->Mail ='amministrazione@lafavoritasrl.it';     //chi invia
$config->gmail->From->Name ='La Favorita Srl';     //chi invia
$config->gmail->ReplyTo=new stdClass();
$config->gmail->ReplyTo->Mail ='amministrazione@lafavoritasrl.it';     //chi invia
$config->gmail->ReplyTo->Name ='La Favorita Srl';     //chi invia     //chi invia




/*-------------------------------------
**    sqLite
-------------------------------------*/
$config->sqlite=new stdClass();
$config->sqlite->dir =realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/dati/sqliteDb';
/*-------------------------------------
**    DATI AZIENDALI
-------------------------------------*/
$params= Array(
	'ragionesociale'=> 'La Favorita Di Brun G. & G. S.R.L.',
	'via'			=> 'Camagre 38/B',
	'paese'			=> 'Isola della Scala',
	'citta'			=> 'VR',
	'cap'			=> '37063',
	'p_iva'			=> '01588530236',
	'sigla_paese'	=> 'IT',	
	'cod_fiscale'	=> '01588530236',
	'telefono'		=> '045-6630397',
	'fax'			=> '045-7302598',
	'email'			=> 'amministrazione@lafavoritasrl.it',
//	'emailpec'		=> 'lafavorita_srl@pec.it',
	'website'		=> 'http://lafavoritasrl.it',
	'_autoExtend'	=> -1,
);

$config->azienda= new ClienteFornitore($params);
$config->azienda->addProp('_emailpec');
$config->azienda->addProp('_bndoo');
$config->azienda->addProp('_rea');
$config->azienda->addProp('_capitalesociale');
$config->azienda->addProp('_registroimprese');
$config->azienda->addProp('_logo');
$config->azienda->addProp('_logobg');
$config->azienda->addProp('_ragionesocialeestesa');
$config->azienda->addProp('_titolare');

$config->azienda->_emailpec->setVal				('lafavorita_srl@pec.it');
$config->azienda->_bndoo->setVal				('001691/VR');
$config->azienda->_rea->setVal					('VR-185024');
$config->azienda->_capitalesociale->setVal		('€ 41.600,00');
$config->azienda->_registroimprese->setVal		('VR 01588530236');
$config->azienda->_logo->setVal					(realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/img/logo.gif');
$config->azienda->_logobg->setVal				(realpath($_SERVER["DOCUMENT_ROOT"]).'/webcontab/my/php/img/logobg.svg');
$config->azienda->_ragionesocialeestesa->setVal	('DI BRUN G. & G. S.R.L. Unipersonale');
$config->azienda->_titolare->setVal				('Brun Gionni');

/*-------------------------------------
**    DATI SDI
-------------------------------------*/
//$config->SDIpec = 'sdi01@pec.fatturapa.it';
$config->SDIpec = 'gionni.brun@gmail.com';
?>
