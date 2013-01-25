<?php
include ('./config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab Calcolo costi</title>
		<meta charset="utf-8">
<style type="text/css" media="print" />		
 form{
	display:none;
 }
</style>
		
		<style type="text/css">
@PAGE landscape {size: landscape;}
TABLE {PAGE: landscape;} 
@page rotated { size : landscape }
			body{
			
			//	column-count: 3;
			//	-moz-column-count: 3;
			//	-webkit-column-count: 3;
			//	column-rule: 2px solid black;
			//	-moz-column-rule: 2px solid black;
			//	-webkit-column-rule: 2px solid black;
			//	font-size:x-small;
			}
			table, tr, td , th{
				font-size:small;
				padding:0px;
				margin:0;
				text-align:right;
				border:1px solid #000000;
				    border-collapse: collapse;
				margin-left:0.5em;
			}
			td, th{
				padding-left:4px;
				padding-right:4px;
			}
			th{
				font-weight:bold;
				text-align:left;
			}
			hr{
				margin-top:150px;
			}
			#rimanenze td{
				height:2em;
				width:9em;
				text-align:left;
			}
			div {
				float:left;
			}
			form label{
display:block;
font-weight:bold;
width:15 em;
			}
			.totali{
			 	 font-size:1.5em;
			}
tr:nth-child(odd) { background-color: #e1e1e1;}
		</style>
	</head>

	<body>

<?php

	$array='';
	$stampaRighe= function ($obj){
	if($obj->cod_articolo->getVal()!=''){
	global $array;

		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['colli']=$obj->colli->getVal();
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['peso']=$obj->peso_netto->getVal();
		$array[$obj->ddt_data->getFormatted()][$obj->cod_articolo->getVal()]['prezzo']=$obj->prezzo->getVal();
		
		}
		return;
	};


	$tabellaH='<table>';
	$tabellaH.='<tr><td>Data</td><td>Art.</td><td>Colli</td><td>Peso</td><td>Prezo</td></tr>';
	$tabellaF='</table>';


	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>','01/01/13','25/01/13'),
			'cod_cliente'=>'SMA',
		)
	);
	$test->iterate($stampaRighe);

	foreach ($array as $key => $value){

		echo '<tr > ';
		echo '<td>'.$key.'</td>';
		echo '<td>'.$value['art'].'</td>';
		echo '<td>'.$value['colli'].'</td>';
		echo '<td>'.$value['peso'].'</td>';
		echo '<td>'.$value['prezzo'].'</td>';
		echo '</tr>';
	
	}
	
	echo $tabellaH;
	echo $tabellaF;


?>
</body>