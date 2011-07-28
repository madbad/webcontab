<?php
//log start date for time execution calc
$start = (float) array_sum(explode(' ',microtime()));


/*
DBase ODBC Connection String
Driver={Microsoft dBASE Driver (*.dbf)};DriverID=277;Dbq=c:\directory;
DBase OLEDB Connection String
Provider=Microsoft.Jet.OLEDB.4.0;Data Source=c:\directory;Extended Properties=dBASE IV;User ID=Admin;Password= 

*/



$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;Dbq=C:\Programmi\EasyPHP-5.3.6.0\www\WebContab\FILEDBF;Exclusive=YES;collate=Machine;NULL=NO;DELETED=NO;BACKGROUNDFETCH=NO;";
$odbc=odbc_connect($dsn," "," ") or die('Could Not Connect to ODBC Database!');

//questa funzionava
//$odbc = odbc_connect ('prova', ' ', ' ') or die('Could Not Connect to ODBC Database!');

$strsql= "SELECT * FROM 03BORIGD.DBF WHERE f_numbol = '     795'";
$query = odbc_exec($odbc, $strsql) or die (odbc_errormsg());

while($row = odbc_fetch_array($query))
{
    echo 'ddt n.: '.$row['F_NUMBOL'].' : '.$row['F_DATBOL'].' : '.$row['F_PROGRE'].'<br>';
    //echo 'Client Phone Number: '.$row['phone'].'';
    //echo '<hr />';
}
odbc_close($odbc);







 

 //log end date for time execution calc
$end = (float) array_sum(explode(' ',microtime()));
 //print execution time
echo "<br><br>Processing time: ". sprintf("%.4f", ($end-$start))." seconds"; 
?>