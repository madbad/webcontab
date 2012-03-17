<?php
include ('./config.inc.php');
include ('./classes.php');

$articlesCode=731;
$startDate='01-01-2012';
$endDate='02-28-2012';
$out='';
$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_CODPRO='".$articlesCode."' AND F_DATBOL >= #".$startDate."# AND F_DATBOL <= #".$endDate."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE");

//this will containt table totals
$out='<table>';
while($row = odbc_fetch_array($result)){
	$out.='<tr><td>'.$row['F_DATBOL'].'</td><td>'.round($row['F_NUMCOL']).'</td><td>'.round($row['F_PESNET']).'</td><td>'.round($row['F_PREUNI']*100).'</td></tr>';
}
$out.='</table>';
echo $out;
?>