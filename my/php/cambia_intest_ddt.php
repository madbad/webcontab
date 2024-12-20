<form action="./cambia_intest_ddt.php" class="dateform" method="post"> 
	<br><input type="text" name="numero" value="" autofocus>
	<BR><input type="submit" value="Submit" style="padding:1em;width:20em;">
</form>
<?php
$vecchio_codice = 'GARL2';
$nuovo_codice = "L'OR";

include ('./core/config.inc.php');
//database connection string
$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=".$config->pathToDbFilesDirect.";Exclusive=NO;collate=Machine;NULL=NO;DELETED=1;BACKGROUNDFETCH=NO;READONLY=false;"; //DELETTED=1??
//connect to database
$odbc=odbc_connect($dsn," "," ", SQL_CUR_USE_IF_NEEDED ) or die('Non riesco a connettermi al database:<br>'.$GLOBALS['config']->pathToDbFiles.'<br><br>Dns:<br>'.$dns);
//il numero della bolla è composto da 8 caratteri (riempidi spazi)
$vecchio_codice = str_pad($vecchio_codice, 5, ' ', STR_PAD_LEFT);
$query= "SELECT * FROM 03BOTESD.DBF WHERE F_CODCLI ='".$vecchio_codice."' ORDER BY F_DATBOL,F_NUMBOL"; 
$result = odbc_exec($odbc, $query) or die (odbc_errormsg().'<br><br>La query da eseguire era:<br>'.$query);

while($record = odbc_fetch_array($result)){
	//print_r( $record);
	echo "<br>\n";
	echo " ".$record['F_DATBOL'];
	echo " ".$record['F_NUMBOL'];
	echo " ".$record['F_CODCLI'];
	echo "<a href='?numero=".$record['F_NUMBOL']."&data=".$record['F_DATBOL']."'>Sblocca fatturazione</a>";
	
}

$query="UPDATE 03BOTESD.DBF";
$query.=" SET F_CODCLI='".$nuovo_codice."'";
//$query.=" SET F_CODCLI=\"L'OR\"";
$query.=" WHERE F_NUMBOL ='".$_GET['numero']."' AND F_DATBOL=#".$_GET['data']."#";
echo "<br>".$query;
$result = odbc_exec($odbc, $query) or die (odbc_errormsg().'<br><br>La query da eseguire era:<br>'.$query);
?>
