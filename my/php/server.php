<?php
include ('./config.inc.php');

$action=$_GET['do'];
switch ($action) {
    case 'DdtPrint':
		$params=array(
			'numero' => '813',
			'data'   => '11-05-2008'
		);
		$myDdt= new Ddt($params);
		$myDdt->doPrint();
        break;
    case 1:
        echo "i equals 1";
        break;
    case 2:
        echo "i equals 2";
        break;
}
page_end();
?>