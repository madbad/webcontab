<?php
//log start date for time execution calc
$start = (float) array_sum(explode(' ',microtime()));


/*
DBase ODBC Connection String
Driver={Microsoft dBASE Driver (*.dbf)};DriverID=277;Dbq=c:\directory;
DBase OLEDB Connection String
Provider=Microsoft.Jet.OLEDB.4.0;Data Source=c:\directory;Extended Properties=dBASE IV;User ID=Admin;Password= 

*/


//READONLY=true
//DELETTED=1
$dsn = "Driver={Microsoft dBASE Driver (*.dbf)};SourceType=DBF;DriverID=21;Dbq=C:\Programmi\EasyPHP-5.3.6.0\www\WebContab\FILEDBF\CONTAB;Exclusive=YES;collate=Machine;NULL=NO;DELETED=NO;BACKGROUNDFETCH=NO;";
$odbc=odbc_connect($dsn," "," ") or die('Could Not Connect to ODBC Database!');

//questa funzionava
//$odbc = odbc_connect ('prova', ' ', ' ') or die('Could Not Connect to ODBC Database!');

//$strsql= "SELECT * FROM 03BORIGD.DBF WHERE f_numbol = '     795'";
//$strsql= "SELECT * FROM 03BORIGD.DBF WHERE ((F_DATBOL >= CTOD('18/07/2011')) and (F_DATBOL <= CTOD('24/07/2011')))";
//$strsql= "SELECT * FROM 03BORIGD.DBF WHERE month(F_DATBOL) between '12' and '12' AND year(F_DATBOL) between '2010' and '2010' AND day(F_DATBOL) between '10' and '10'";
//$strsql= "SELECT * FROM 03BORIGD.DBF WHERE month(F_DATBOL) between 12 and 12 AND year(F_DATBOL) between 2009 and 2010 AND day(F_DATBOL) between 9 and 10 ORDER BY F_NUMBOL, F_PROGRE ";

$strsql= "SELECT * FROM 03BORIGD.DBF WHERE F_DATBOL >= '01/01/2010'  AND F_DATBOL < '01/02/2010'";


echo $strsql;
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