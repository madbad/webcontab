<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
echo 'prova';
//$data_source = 'DRIVER={Microsoft dBASE Driver (*.dbf)};datasource=/home/dir/file.dbf;';
$data_source = 'DRIVER={Microsoft dBASE Driver (*.dbf)};DriverID=277;datasource=C:\Program Files\EasyPHP-5.3.6.0\www\webContab\FILEDBF\provaaaadd.dbf;';
$user='';
$password='';
$odbc=odbc_connect($data_source,$user,$password) or die('Could Not Connect to ODBC Database!');
if (!$odbc)
{
	exit("Connection Failed:" . odbc_errormsg() );
}else{
	echo '<br>all seems ok!';
}

$strsql= 'SELECT * FROM provaaaadd.dbf';
$query = odbc_exec($odbc, $strsql) or die (odbc_errormsg());

while($row = odbc_fetch_array($query))
{
    echo 'ddt n.: '.$row['F_NUMBOL'].'<br>';
    //echo 'Client Phone Number: '.$row['phone'].'';
    //echo '<hr />';
}
odbc_close($odbc);


?>