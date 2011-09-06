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
				font-size:x-small;
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
		</style>
	</head>

	<body>
<?php 
$today = date("n-j-Y"); 
if(@$_GET['startDate']){$startDate=$_GET['startDate'];}else{$startDate=$today;}
if(@$_GET['endDate']){$endDate=$_GET['endDate'];}else{$endDate=$today;}

if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

?>
<form name="input" action="./index.php?mode=print" method="get">
	<input type="text" name="mode" value="print" style="display:none"/>
	<label>Start date</label> <input type="text" name="startDate" value="<?php echo $startDate ?>"/>
	<label>End date</label> <input type="text" name="endDate" value="<?php echo $endDate ?>"/>
	
	<label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
	<label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>	
	<label>Includi Radicchi</label> <input name="extraProducts" type="CHECKBOX" <?php if(@$_GET['extraProducts']) echo 'checked'  ?>/>
	<button type="submit">Search</button>
</form> 

<?php

if (@$_GET['mode']=='print'){
	//log start date for time execution calc
	$start = (float) array_sum(explode(' ',microtime()));


	function getArticleTable($articlesCode, $startDate, $endDate, $calopesoAlCollo){
		$out=null;

		//database connection string
		$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=C:\Programmi\EasyPHP-5.3.6.0\www\WebContab\calcoloCosti\FILEDBF\CONTAB;Exclusive=YES;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=true;"; //DELETTED=1??
		//connect to database
		$odbc=odbc_connect($dsn," "," ") or die('Could Not Connect to ODBC Database!');
		//query string
		$query= "SELECT * FROM 03BORIGD.DBF WHERE F_DATBOL >= #".$startDate."# AND F_DATBOL <= #".$endDate."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE ";
		//query execution
		$result = odbc_exec($odbc, $query) or die (odbc_errormsg());

		$out.="<table><tr><th colspan='5'>cod:".join(",", $articlesCode)." ( $startDate > $endDate )</th></tr>";	
		$out.='<tr><th>Data</th><th>Cliente</th><th>Colli</th><th>p.Netto</th><th>md</th></tr>';
		//this will containt table totals
		$sum=array('NETTO'=>0,'F_NUMCOL'=>0);
		$dbClienti=getDbClienti();
		while($row = odbc_fetch_array($result))
		{
		$codCliente=$row['F_CODCLI'];
		$tipoCliente=$dbClienti["$codCliente"];
		if (in_array($row['F_CODPRO'],$articlesCode) && ($tipoCliente=='mercato' || $tipoCliente=='supermercato')){
			$calopeso=round(round($row['F_NUMCOL'])*$calopesoAlCollo);
			$netto=$row['F_PESNET']-$calopeso;
			$media=round($netto/$row['F_NUMCOL'],1);
			$out.="\n<tr><td>$row[F_DATBOL]</td><td>$row[F_CODCLI]</td><td>".round($row['F_NUMCOL'])."</td><td>$netto</td><td>$media</td></tr>";
			$sum['NETTO']+=$netto;
			$sum['F_NUMCOL']+=$row['F_NUMCOL'];
		}	
		}

		$out.="<tr><th>Totali</th><th>-</th><th class='totali'>".round($sum['F_NUMCOL'])."</th><th class='totali' colspan='2'>".$sum['NETTO']."</th></tr>";
		$out.='</table><BR>';

		//DISCONNECT FROM DATABASE
		odbc_close($odbc);
		return $out;
	}
	function getDbClienti(){
		$db=array();
		$news=fopen("./dbClienti.txt","r");  //apre il file
		while (!feof($news)) {
			$buffer = fgets($news, 4096);
			$arr=explode(', ',$buffer);
			$codCliente=trim($arr[0]);
			$tipoCliente=trim($arr[2]);
			$db["$codCliente"]=$tipoCliente;
			//echo "$codCliente=$tipoCliente<br>"; //riga letta
		}
		fclose ($news); #chiude il file
		return $db;
	}

	$table='<table id="rimanenze">';
	$table.='<tr><td>Rimanenze</td><td></td></tr>';
	$table.='<tr><td></td><td></td></tr>';
	$table.='<tr><td></td><td></td></tr>';
	$table.='<tr><td></td><td></td></tr>';
	$table.='<tr><td></td><td></td></tr>';
	$table.='<tr><td></td><td></td></tr>';
	$table.='<tr><td>Tot.</td><td></td></tr>';
	$table.='</table>';
	$html='';

	$html.="<div>";
	$html.="<h1>Riccia</h1>";
	$html.=getArticleTable(array('01'),$startDate,$endDate,0.3);
	$html.=getArticleTable(array('701','801'),$startDate,$endDate,0.7);
	$html.=$table;

	$html.="</div><div>";
	$html.="<h1>Scarola</h1>";
	$html.=getArticleTable(array('03'),$startDate,$endDate,0.3);
	$html.=getArticleTable(array('703','803'),$startDate,$endDate,0.7);
	$html.=$table;

	if(@$_GET['extraProducts']){
		//$html.='<div style="page-break-before: always"></div>';
		$html.="</div><div>";
		$html.="<h1>Tondo</h1>";
		$html.=getArticleTable(array('08'),$startDateR,$endDateR,0.3);
		$html.=getArticleTable(array('708','808'),$startDateR,$endDateR,0.4);
		$html.=$table;

		$html.="</div><div>";
		$html.="<h1>Lungo</h1>";
		$html.=getArticleTable(array('29'),$startDateR,$endDateR,0.3);
		$html.=getArticleTable(array('729','829'),$startDateR,$endDateR,0.4);
		$html.=$table;

		//$html.='<div style="page-break-before: always"></div>';
		$html.="</div><div>";
		$html.="<h1>P.di Zucchero</h1>";
		$html.=getArticleTable(array('31'),$startDateR,$endDateR,0.3);
		$html.=getArticleTable(array('731','831'),$startDateR,$endDateR,.4);
		$html.=$table;
		
		$html.="</div><div>";
		$html.="<h1>Finocchio</h1>";
		$html.=getArticleTable(array('26'),$startDateR,$endDateR,0.3);
		$html.=getArticleTable(array('726','826'),$startDateR,$endDateR,.4);
		$html.=$table;
		

	}

	/*
	$html.="</div><div>";
	$html.="<h1>Zucche</h1>";
	$html.=getArticleTable(array('50'),$startDate,$endDate,0);
	$html.=getArticleTable(array('750','850'),$startDate,$endDate,0);
	$html.=$table;
	*/
	
/*	
	$html.="</div><div>";
	$html.="<h1>Sedano</h1>";
	$html.=getArticleTable(array('36'),$startDate,$endDate,0);
	$html.=getArticleTable(array('736','836'),$startDate,$endDate,0);
	$html.=$table;
*/	

/*
	$html.="</div><div>";
	$html.="<h1>Capucci</h1>";
	$html.=getArticleTable(array('19'),$startDate,$endDate,0);
	$html.=getArticleTable(array('719','819'),$startDate,$endDate,0);
	$html.=$table;
*/	
	$html.='</div>';
	echo $html;

	 //log end date for time execution calc
	$end = (float) array_sum(explode(' ',microtime()));
	 //print execution time
	echo "<br><br>Exec time: ". sprintf("%.4f", ($end-$start))." seconds"; 
}
?>
</body>