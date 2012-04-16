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
$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
<form name="input" action="./vr.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<button type="submit">Search</button>
</form> 

<?php

if (@$_GET['mode']=='print'){

	/**
	 * sum values in array
	 *
	 * @param array $arr
	 * @param string [optional]$index
	 * @return int result
	 */
	function array_sum_key( $arr, $index = null ){
		if(!is_array( $arr ) || sizeof( $arr ) < 1){
			return 0;
		}
		$ret = 0;
		foreach( $arr as $id => $data ){
			if( isset( $index )  ){
				$ret += (isset( $data[$index] )) ? $data[$index] : 0;
			}else{
				$ret += $data;
			}
		}
		return $ret;
	}


	/*
		//log start date for time execution calc
		$start = (float) array_sum(explode(' ',microtime()));
		
		$html="<h1>Semil.</h1>";
		$html.=getArticleTable2(array('05'),$startDateR,$endDateR, 'martinelli');
		$html.=getArticleTable2(array('05'),$startDateR,$endDateR, 'mercato');
		$html.=getArticleTable2(array('905'),$startDateR,$endDateR, 'mercato');
		$html.=getArticleTable2(array('705','805'),$startDateR,$endDateR, 'supermercato');
		$html.=getArticleTable2(array('19','919'),$startDateR,$endDateR, 'mercato');
		
		echo $html;

		 //log end date for time execution calc
		$end = (float) array_sum(explode(' ',microtime()));
		 //print execution time
		echo "<br><br>Exec time: ". sprintf("%.4f", ($end-$start))." seconds";
	*/
	$stampaRighe= function ($obj){
		echo '<tr>';
		echo '<td>'.$obj->ddt_numero->getVal().'</td>';
		echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo '<td>'.$obj->cod_cliente->getVal().'</td>';
		echo '<td>'.$obj->colli->getVal().'</td>';				
		echo '<td>'.$obj->peso_netto->getVal().'</td>';
		echo '<td>'.$obj->prezzo->getVal().'</td>';
		echo '<td>'.round($obj->peso_netto->getVal()/$obj->colli->getVal(),2).'</td>';
		echo '<td>'.round($obj->peso_netto->getVal()*$obj->prezzo->getVal(),2).'</td>';
		echo '<td>'.$obj->imponibile->getVal().'</td>';			
		echo '</tr>';
	};
	$stampaTotali= function ($obj){
		echo '<tr>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('colli').'</td>';				
		echo '<td>'.$obj->sum('peso_netto').'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.round($obj->sum('peso_netto')/$obj->sum('colli'),2).'</td>';
		echo '<td>'.'-'.'</td>';
		echo '<td>'.$obj->sum('imponibile').'</td>';			
		echo '</tr>';
	};

//martinelli
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'05',
			'cod_cliente'=>'MARTI',
		)
	);
	echo '<table>';
	echo '<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso Netto</td><td>Prezzo</td><td>Media peso</td><td>Imponibile Calc.</td><td>Imponibile Memo.</td></tr>';
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo '</table><br><br>';

//mercato
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'05',
			'cod_cliente'=>array('!=','MARTI','FACCG','FACCI')
		)
	);
	echo '<table>';
	echo '<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso Netto</td><td>Prezzo</td><td>Media peso</td><td>Imponibile Calc.</td><td>Imponibile Memo.</td></tr>';
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo '</table><br><br>';
	page_end();
}
?>
</body>