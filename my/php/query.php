<!DOCTYPE HTML>
<html lang="IT">
    <head>
        <title>Query</title>
        <meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
    </head>
     <body>
	<form name="input" action="./query.php" method="get">
		<textarea name="query" rows="4" cols="50"  style="font-size: 0,5em;width:100%;height:30%">
<?php 

echo $_GET["query"] 

?>
		</textarea> 
		<button type="submit">Search</button>

	</form>
	<?php
	include ('./core/config.inc.php');

	$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$GLOBALS['config']->pathToDbFiles.";Exclusive=NO;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=false;"; //DELETTED=1??
	$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_IF_NEEDED ) or die('Non riesco a connettermi al database:<br>'.$GLOBALS['config']->pathToDbFiles.'<br><br>Dns:<br>'.$dns);
	//echo $GLOBALS['config']->pathToDbFiles;
	$query= "SELECT sum(F_IMPONI) AS IMPONIBILE FROM 03FARIGD.DBF "." WHERE F_NUMFAT='       1';";
	$query= "SELECT sum(F_IMPIVA) AS IVA FROM 03FARIGD.DBF "." WHERE F_NUMFAT='       1';";
	$query= "SELECT sum(F_IMPIVA) AS IVA FROM 03FARIGD.DBF "." WHERE F_NUMFAT='       1';";
	$query= "SELECT * FROM 03FARIGD.DBF "." WHERE F_NUMFAT='       1';";
	$query= "SELECT sum(F_IMPONI) AS IMPONIBILE FROM 03FARIGD.DBF "." WHERE F_CODCLI='MERCA' AND F_NUMFAT='      24' AND F_DATFAT=#2016-01-30# ";
	$query= "SELECT sum(F_IMPONI), F_CODIVA FROM 03FARIGD.DBF "." WHERE F_NUMFAT='      24' AND F_DATFAT=#2016-01-30# GROUP BY F_CODIVA";

	$query= "SELECT sum(F_IMPONI), F_CODCLI, F_STATO FROM 03FARIGD.DBF "." WHERE F_DATFAT > #2015-01-01# AND F_DATFAT < #2015-12-31# GROUP BY F_CODCLI, F_STATO ORDER BY sum(F_IMPONI) DESC";

	$query= "SELECT * FROM 03ANPROD.DBF WHERE F_DESPRO LIKE '%ZUCCHIN%' ORDER BY F_DESPRO DESC";
	//$query= "SELECT * FROM 03FARIGD.DBF "." WHERE F_NUMFAT='       1';";

	$result = odbc_exec($odbc, $query) or die (odbc_errormsg().'<br><br>La query da eseguire era:<br>'.$query);

		
	if(!array_key_exists($tableName,$DataTypeInfo)){
		$DataTypeInfo[$tableName]=Array();
		$columns=odbc_num_fields ($result);
		for ($i=1; $i<=$columns; $i++){
			$fieldName=odbc_field_name($result,$i);
			$DataTypeInfo[$tableName][$fieldName]=Array();
			$DataTypeInfo[$tableName][$fieldName]['name']=odbc_field_name($result,$i);//nome del campo
			$DataTypeInfo[$tableName][$fieldName]['len']=odbc_field_len($result,$i);//lunghezza
			$DataTypeInfo[$tableName][$fieldName]['type']=odbc_field_type($result,$i);//tipo=Date/Numeric/Char
			$DataTypeInfo[$tableName][$fieldName]['num']=odbc_field_num($result,$i); //bho - vuoto?
			$DataTypeInfo[$tableName][$fieldName]['scale']=odbc_field_scale($result,$i); //bho - vuoto?
			$DataTypeInfo[$tableName][$fieldName]['precision']=odbc_field_precision($result,$i); //bho - vuoto?
			//$DataTypeInfo['bho']=odbc_result_all(odbc_gettypeinfo($odbc));
			////$log->info($DataTypeInfo);
		}
		//print_r($DataTypeInfo);
	}
		
	$records=array();
	while($record = odbc_fetch_array($result)){
		$records[] = $record;
	}
	echo "<pre>";
	print_r($records);
	echo "</pre>";

	echo $_GET["query"];
	?>
	
	
</body>
</html>