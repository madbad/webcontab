<?php
include ('./config.inc.php');

//mi preparo i parametri di ricerca della fattura
$params=array(
	'numero' => $_GET["numero"],
	'data'   => $_GET["data"],
	'cod_causale'  => $_GET["cod_causale"]
);
//genero il mio oggetto ddt
$myDdt= new Ddt($params);

//eseguo l'azione richiesta
switch ($_GET["do"]){
	case 'inviaPec':
		$myDdt->inviaPec();
		break;
	case 'visualizza':
		$myDdt->visualizzaPdf();
		break;
	case 'stampaCliente':
		$myDdt->stampa();
		//memorizzo la data di stampa
		$myDdt->__datastampa->setVal(date("d/m/Y"));
		$myDdt->saveSqlDbData();		
		break;	
}
?>