<!DOCTYPE HTML>
<html lang="IT">
    <head>
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
		
		<!--ExtJs-->
		<script src="./../js/ext.js/ext-all.js" type="text/javascript"></script>

		<link href="./../js/ext.js/resources/css/ext-all.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
		Ext.require(['*']);
		</script>
 		<script src="./../js/ext.js/locale/ext-lang-it.js" type="text/javascript"></script>
		<style type="text/css" media="print" />      
			.hideOnPrint{
				display:none;
			}
			@PAGE landscape {size: landscape;}
			TABLE {PAGE: landscape;}
			@page rotated { size : landscape }
			pre{
			font-size:14px;
			}
		</style>		
    </head>
     <body>
	<?php
	$today = date("m-d-Y");
	if(@$_POST['startDate']){$startDate=$_POST['startDate'];}else{$startDate=$today;}
	if(@$_POST['endDate']){$endDate=$_POST['endDate'];}else{$endDate=$today;}
	echo 'dal '.$_POST['startDate'];
	echo '<br> al '.$_POST['endDate'].'<br><br>';
	?>
	
	<span class="hideOnPrint" id="myForm">	
	</span>
	<script>
		Ext.create('Ext.form.Panel', {
			//renderTo: Ext.getBody(),
			renderTo: document.getElementById('myForm'),
			name: 'input',
			method: 'POST',
			layout: {
				type: 'vbox',
				align: 'right'
			},
			height:230,
			url: './calcoloFatturazione.php',
			standardSubmit: true,
			bodyStyle: 'padding:10px',
			title: 'Selezione parametri',
			items: [{
				xtype: 'hiddenfield',
				name: 'mode',
				value: 'print'
			}, {
				xtype: 'datefield',
				format: 'm-d-Y',
				anchor: '100%',
				fieldLabel: 'From',
				name: 'startDate',
				value: '<?php echo $startDate ?>'  // defaults to today
			}, {
				xtype: 'datefield',
				format: 'm-d-Y',
				anchor: '100%',
				fieldLabel: 'To',
				name: 'endDate',
				value: '<?php echo $endDate ?>' // defaults to today
			}],
			// Reset and Submit buttons
			buttons: [{
				text: 'Submit',
				handler: function() {
					var form = this.up('form').getForm();
					form.submit();
				}
			}],
		});
	</script>

<span>

<?php
require_once('./config.inc.php');
require_once('./classes.php');
error_reporting(0); //0=spento || -1=acceso
page_start();

$myArray=Array();
$myArray['lavorato']='';
$myArray['grezzo']='';
$myArray['semilavorato']='';

$totale=0;

$radicchi=array('05','705','805','08','708','708-','808','808-','808--','29','729','829');
$insalate=array('01','701','801','03','703','803','25');
$pdzucchero=array('31','731','831');


if($_POST['startDate']!=null && $_POST['endDate']!=null){
	$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_DATBOL >= #".$_POST['startDate']."# AND F_DATBOL <= #".$_POST['endDate']."#");
	echo '<b>Attenzione se si riscontrano differenze con i dati di contab (contab presenta meno peso) è perchè contab conta solo le riche con articoli invece noi contiamo anche le righe condescrizione ma senza articolo)</b><BR><BR>';

	echo 'NB. forse restano da escludere (dalla query) le bolle di conto deposito (differenza residua di 150 kg su 30 giorni)';

	foreach($result as $row){

		$cliente=new ClienteFornitore(array('codice'=>$row['F_CODCLI']));

		$tipo=$cliente->__classificazione->getVal();
		//echo $tipo;
		$articolo=$row['F_CODPRO'];
		$descrizione=$row['F_DESPRO'];
		$peso=$row['F_PESNET'];
		$colli=$row['F_NUMCOL'];
		//$peso=$row['F_QTA'];
		
		//if (in_array($articolo,$radicchi)) $descrizione=$articolo='**radicchi';
		//if (in_array($articolo,$insalate)) $descrizione=$articolo='**insalate';
		//if (in_array($articolo,$pdzucchero)) $descrizione=$articolo='**pdzucchero';
	
		$done=false;
		//echo $cliente->codice->getVal().'='.$peso.'<br>';
		if($peso>0){
			if ($tipo=='mercato' || $tipo=='supermercato'){
				$myArray['lavorato'][$articolo.'*'.$descrizione]+=$peso;
			//	if(($peso/$colli)>10){
			//		echo $peso.':'.$colli.'='.round($peso/$colli,1).' ('.$descrizione.')<br>';
			//	}
				$done=true;
			}
			if ($tipo=='semilavorato'){
				$myArray['semilavorato'][$articolo.'*'.$descrizione]+=$peso;
				$done=true;
			}
			if ($tipo=='grezzo'){
				$myArray['grezzo'][$articolo.'*'.$descrizione]+=$peso;
				$done=true;
			}
			if ($done!=true){
				$myArray['altro'][$articolo.'*'.$descrizione]+=$peso;
				//echo '<br><b>WARNING (tipo cliente sconosciuto)</b>:'.$cliente->codice->getVal().':'.$cliente->ragionesociale->getVal().'='.$peso.'<br>';
			}
		}		
		$totale+=$peso;
	}
}
echo '<pre>';
ksort($myArray['lavorato']);
ksort($myArray['grezzo']);
ksort($myArray['semilavorato']);
print_r($myArray);

echo '<br><br>'.$totale;
echo '<pre>';

page_end();
?>
</span>
</body>