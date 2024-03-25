<?php
$startDateFormatted = '';
$endDateFormatted = '';
$CurrentURL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$startDate='';
$endDate='';

if(array_key_exists('startDate', $_POST)){
	/*todo da sistemare*/
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];

	$myDate = explode('-',$_POST['startDate']);
	$startDateFormatted = $myDate[0].'-'.$myDate[1].'-'.$myDate[2];
	
	$myDate = explode('-',$_POST['endDate']);
	$endDateFormatted = $myDate[0].'-'.$myDate[1].'-'.$myDate[2];

	$myDate = explode('-',$_POST['startDate']);
	$startDate = $myDate[2].'/'.$myDate[1].'/'.$myDate[0];

	$myDate = explode('-',$_POST['endDate']);
	$endDate = $myDate[2].'/'.$myDate[1].'/'.$myDate[0];

	
}else{
	$giorno = date('d', time());
	$mese = date('m', time())-1;
	$anno = date('Y', time());
	$giorniDelMese = cal_days_in_month(CAL_GREGORIAN,$mese,$anno); 
	
	$startDate = '01'.'/'.$mese.'/'.$anno;
	$endDate = $giorniDelMese.'/'.$mese.'/'.$anno;
	
	$startDateFormatted = $anno.'-'.$mese.'-'.'01';
	$endDateFormatted = $anno.'-'.$mese.'-'.$giorniDelMese;

}


?>

<form action="<?php echo $CurrentURL;?>" class="dateform hideOnPrint" method="post"> 
	<span class="dateformtitle">Selezione parametri</span>
	<br> <span class="dateselectordescription">From:</span>
	<input class="dateselector" type="date" id="startDate" name="startDate" value="<?php echo $startDateFormatted ?>">
	<br> <span class="dateselectordescription">to:</span>
	<input class="dateselector" type="date" id="endDate" name="endDate" value="<?php echo $endDateFormatted ?>">
	<?php echo $additionalForm; ?>
	<input type="submit" value="Submit" style="padding:1em;width:20em;">
</form>

