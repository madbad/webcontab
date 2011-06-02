<?php
/* -------------------------------------------------------------------------------------------------------
File di configurazione con i dati per la connessione al DataBase MySql


----------------------------------------------------------------------------------------------------------
*/
global $conf;

$conf['db']['host']=      'localhost';
$conf['db']['name']=      'webContab';
$conf['db']['user']=      'lafavorita';
$conf['db']['password']=  'dagliitaliani';




/*
function treeGenerator($multiplier){
	for ($i = 0; $i < $multiplier; ++$i) {
		$tree = $tree.'-------';

	} 
	return $tree;
}





function loop($obj, $key = null)
{
	global $multiplier;
   if (is_object($obj)) {

   		$test=get_class($obj);
   	if($test!='Proprietà'){
			$multiplier++;
         echo treeGenerator($multiplier).get_class($obj)."<br>";
         foreach ($obj as $x => $value) {
            loop($value, $x);
         }
         $multiplier--;
			if($tableName==get_class($obj)){$newTable=false}else{$newTable=true}

         $tableName=get_class($obj);
      }else{
      	if($newTable){
      		$wc->db->createTable($tableName,$tableFields)
      	}
      	$multiplier++;
         echo treeGenerator($multiplier)." $key: ".$obj->getVal()."<br>"; 
        	$multiplier--;   
        	$tableFields[]=$obj;         	
      }
   } else {

      //echo "Key: $key, value: $obj<br>";
   }
}

loop($wc->obj);











*/

?>