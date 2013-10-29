<?php
include ('./config.inc.php');

$myClasses=array('Ddt','Riga','Articolo','Imballaggio','ClienteFornitore','Iva','Causale','Mezzo','Um'); 

foreach ($myClasses as $myClass) {
	$test = new $myClass();
	$test->createDb();

}
?>
