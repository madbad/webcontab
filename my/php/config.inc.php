<?php
require_once('./classes.php');
$config['pathToDbFiles']=dirname($_SERVER['SCRIPT_FILENAME']).'/'.'FILEDBF'.'/'.'CONTAB';
$config['debugger']=0;//1=acceso || 0=spento
error_reporting(-1); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo
//set_time_limit (10); //0=nessun limite di tempo

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

$azienda= new ClienteFornitore($params);
$azienda->addProp('_emailpec');
$azienda->addProp('_bndoo');
$azienda->addProp('_rea');
$azienda->addProp('_capitalesociale');
$azienda->addProp('_registroimprese');
$azienda->addProp('_logo');
$azienda->addProp('_ragionesocialeestesa');


$azienda->_emailpec->setVal				('lafavorita_srl@pec.it');
$azienda->_bndoo->setVal				('001691/VR');
$azienda->_rea->setVal					('VR-185024');
$azienda->_capitalesociale->setVal		('€ 41.600,00');
$azienda->_registroimprese->setVal		('VR 01588530236');
$azienda->_logo->setVal					('./img/logo.gif');
$azienda->_ragionesocialeestesa->setVal	('DI BRUN G. & G. S.R.L. Unipersonale');
?>
