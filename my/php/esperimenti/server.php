<?php
include ('./config.inc.php');
//print_r($_GET);

@ $action = $_GET['do'] ? $_GET['do'] : '';
switch ($action) {
    case 'DdtPrint':
		$params=array(
			'numero' => '1317',
			'data'   => '2012-05-03'
		);
		$myDdt= new Ddt($params);
		$myDdt->doPrint();
        break;
	case 'DdtList':
		$test=new MyList(
			array(
				'_type'=>'Ddt',
		//		'data'=>array('=','17/02/12'),		
				'data'=>array('>=','20/04/2012'),
		//		'data'=>array('<','17/02/12'),
		//		'data'=>array('<>','01/01/09','01/01/11'),
		//		'data'=>'28/03/09',	
		//		'numero'=>'784'
			)
		);
		echo "{\"dataRoot\":[";
		global $out;
		$out='';
		$test->iterate(function($obj){
						$GLOBALS['out'].=$obj->jsonList();
						});
		//rimuovo l'ultima virgola
		$out= substr($out,0,-1);
		echo $out;
		echo ']}';
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