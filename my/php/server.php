<?php
include ('./config.inc.php');
//print_r($_GET);

@ $action = $_GET['do'] ? $_GET['do'] : '';
switch ($action) {
    case 'DdtPrint':
		$params=array(
			'numero' => '1083',
			'data'   => '2012-04-13'
		);
		$myDdt= new Ddt($params);
		$myDdt->doPrint();
        break;
	case 'ortomercato':
		include ('./stampe/ortomercato.php');
		break;
	default:
		echo 'Nothing to do for "'.$action.'"...';
		break;
}
page_end();
?>