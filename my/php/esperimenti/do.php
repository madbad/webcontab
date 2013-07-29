<?php
include ('./config.inc.php');
set_time_limit ( 0);

//the action to perform with the given params
$action = $_POST["action"];

//the params for the action
$params = $_POST["params"];

//print_r($params);

switch ($action){
	case 'getOne':
		$obj = new $params['_type'](
			$params
		);
		echo $obj->toJson();
		
		break;
	case 'getAll':
	//print_r($params);
		$list = new MyList(
			$params
		);
		//print_r($list);
		echo $list->toJson();
		
		break;
	case 'getList':
		$list = new MyList(
			array(
				'_type'=>'Ddt',
				'data'=>array('<>','01/01/'.$anno,'31/12/'.$anno),
			)
		);
		
		//echo $list->toJson(){}
		
		break;
	case 'save':
		
		break;
	case 'saveList':
		
		break;
	default:
	echo 'No action to perform specified!';
}
