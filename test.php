<?php

$odbc = odbc_connect ('dd', ' ', ' ') or die('Could Not Connect to ODBC Database!');
//require_once('odbc.php');
$strsql= 'SELECT * FROM 03BORIGD.DBF';
$query = odbc_exec($odbc, $strsql) or die (odbc_errormsg());

while($row = odbc_fetch_array($query))
{
    echo 'ddt n.: '.$row['F_NUMBOL'].'<br>';
    //echo 'Client Phone Number: '.$row['phone'].'';
    //echo '<hr />';
}
odbc_close($odbc);


/*
$dsn = "Driver={Microsoft Visual FoxPro Driver};SourceType=DBF;SourceDB=C:\Programmi\EasyPHP-5.3.6.0\www\WebContab\FILEDBF;Exclusive=NO;collate=Machine;NULL=NO;DELETED=NO;BACKGROUNDFETCH=NO;";
$conn=odbc_connect($dsn,"","");
*/
?>