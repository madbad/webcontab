<?php
include ('./config.inc.php');

//mi preparo i parametri di ricerca della fattura
$params=array(
	'numero' => $_GET["numero"],
	'data'   => $_GET["data"],
	'tipo'  => $_GET["tipo"]
);
//genero il mio oggetto fattura
$myFt= new Fattura($params);

//eseguo l'azione richiesta
switch ($_GET["do"]){
	case 'inviaPec':
		$myFt->inviaPec();
		break;
	case 'visualizza':
		$myFt->visualizzaPdf();
		break;
	case 'stampaCliente':
		$myFt->stampa();
		//memorizzo la data di stampa
		$myFt->__datastampa->setVal(date("d/m/Y"));
		$myFt->saveSqlDbData();		
		break;	
}
?>