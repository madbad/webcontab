<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Ortomercato</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
	</head>
	<body>
<?php
//http://localhost/webContab/my/php/ortomercato.php?dal=01/01/2010&al=29/02/2010

include ('./core/config.inc.php');

function fixDate($date){
	//se la data ha il formato gg/mm/aaaa la trasformo in mm-gg-aaaa
	//come richiesto dal database
	if(preg_match('/.*\/.*\/.*/',$date)){

		$arr=explode("/", $date);
								//mese   //giorno //anno
		$date=mktime(0, 0, 0, $arr[1], $arr[0], $arr[2]);
		$date=date ( 'm-d-Y' , $date);
	}
	return $date;
}


$startDate=fixDate($_GET['dal']);
$endDate=fixDate($_GET['al']);

$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE ".'F_DATBOL'." >= #".$startDate."# AND ".'F_DATBOL'." <= #".$endDate."# AND F_CODCLI='SEVEN'  ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE");

$sum=0;
echo '<table class="spacedTable">';
foreach($result as $id => $row) {
	echo '<tr>';
	echo '<td>'.$row['F_DATBOL'].'</td>';
	echo '<td>'.$row['F_NUMBOL'].'</td>';
	echo '<td>'.$row['F_PROGRE'].'</td>';
	echo '<td>'.$row['F_CODPRO'].'</td>';
	echo '<td>'.$row['F_DESPRO'].'</td>';
	echo '<td>'.($row['F_PESNET']*1).'</td>';
	echo '</tr>';
	$sum+=$row['F_PESNET'];
}
echo '</table>';
echo $sum;
page_end();
?>
</body>