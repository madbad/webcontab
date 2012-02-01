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
        <style type="text/css">
			@PAGE landscape {size: landscape;}
			TABLE {PAGE: landscape;}
			@page rotated { size : landscape }
            .totali{
                 font-size:1.5em;
            }
			.righe table, .righe tr, .righe td, .righe th{
                font-size:x-small;
                padding:0px;
                margin:0;
                text-align:right;
                border:1px solid #000000;
                    border-collapse: collapse;
                margin-left:0.5em;
			}
            .righe td, .righe th{
                padding-left:4px;
                padding-right:4px;
            }
            .righe th{
                font-weight:bold;
                text-align:left;
            }
            .righe hr{
                margin-top:150px;
            }
            .rimanenze td{
                height:3.5em;
                width:9em;
                text-align:left;
				padding-left:1em;

            }
			.rimanenze table, .rimanenze tr, .rimanenze td, .rimanenze th{
                font-size:x-small;
                border:1px solid #000000;
                border-collapse: collapse;
								margin:1em;
			}			
            span div {
                float:left;
            }
            .totali{
                 font-size:1.5em;
            }
			.tableContainer{
				padding:0.2em;
			}
			h1{
				font-size:2em;
			}
        </style>
		<style type="text/css" media="print" />      
			.hideOnPrint{
				display:none;
			}
			@PAGE landscape {size: landscape;}
			TABLE {PAGE: landscape;}
			@page rotated { size : landscape }
		</style>		
    </head>
     <body>
	<?php
	$today = date("m-d-Y");
	if(@$_POST['startDate']){$startDate=$_POST['startDate'];}else{$startDate=$today;}
	if(@$_POST['endDate']){$endDate=$_POST['endDate'];}else{$endDate=$today;}
  
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
error_reporting(1); //0=spento -1=acceso
//max_execution_time(60);
    //log start date for time execution calc
    $start = (float) array_sum(explode(' ',microtime()));

$myArray=Array();

require_once('./classes.php');

$radicchi=array('05','705','805','08','708','808','808-','808--','29','729','829');
$insalate=array('01','701','801','03','703','803');
$pdzucchero=array('31','731','831');

	//echo $_POST['startDate'];
if($_POST['startDate']!=null && $_POST['endDate']!=null){
	$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_DATBOL >= #".$_POST['startDate']."# AND F_DATBOL <= #".$_POST['endDate']."#");
	//$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_DATBOL = #".$_POST['startDate']."#");
	//echo odbc_num_rows($result).'****';
	//$test=0;
	while($row = odbc_fetch_array($result)){
		$test++;
		//echo ':'.$test;
		$cliente=new ClienteFornitore(array('codice'=>$row['F_CODCLI']));
		//$row['F_PESNET']
		//echo $cliente->ragionesociale->getVal().':'.$cliente->tipo->getVal().'<br>';
		//echo '|';
		$tipo=$cliente->tipo->getVal();
		$articolo=$row['F_CODPRO'];
		$descrizione=$row['F_DESPRO'];
		$peso=$row['F_PESNET'];
		if (in_array($articolo,$radicchi)) $descrizione=$articolo='**radicchi';
		if (in_array($articolo,$insalate)) $descrizione=$articolo='**insalate';
		if (in_array($articolo,$pdzucchero)) $descrizione=$articolo='**pdzucchero';
	
		$done=false;
		if($peso>0){
			if ($tipo=='mercato' || $tipo=='supermercato'){
				$myArray['lavorato'][$articolo.'*'.$descrizione]+=$peso;
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
				echo $cliente->codice->getVal().':'.$cliente->ragionesociale->getVal().'='.$peso.'<br>';
			}
		}		

	}
}
echo '<pre>';
print_r($myArray);
echo '<pre>';


     //log end date for time execution calc
    $end = (float) array_sum(explode(' ',microtime()));
     //print execution time
    echo "<br>Exec time: ". sprintf("%.4f", ($end-$start))." seconds";
?>
</span>
</body>