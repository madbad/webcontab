<?php
include ('./config.inc.php');

switch ($_POST["action"]){
	case "getOne":
		$myObj = new $_POST["params"]["_type"]($_POST["params"]);
		echo $myObj->toJson();
		break;
	case "getAll":
		$out="[";
		$myObjList = new MyList($_POST["params"]);
		//echo $myObjList->toJson();
		
		$myObjList->iterate(function ($myObj){
			global $out;
			$out.="{";
			$out.= $myObj->toJson(1);
			//$out.="}";
			//$out.= ',';
		});
		//remove the last ","
		$out=substr($out, 0, -1);
		$out.= "]";
		echo $out;
		
		break;
	default:
		break;
}
?>