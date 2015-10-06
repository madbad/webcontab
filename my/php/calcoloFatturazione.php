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
		
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
    </head>
     <body>
	<?php
	$today = date("m-d-Y");
	if(@$_POST['startDate']){$startDate=$_POST['startDate'];}else{$startDate=$today;}
	if(@$_POST['endDate']){$endDate=$_POST['endDate'];}else{$endDate=$today;}
	echo '<h1>Totali delle vendite effettuate <br>dal  '.$_POST['startDate'];
	echo '<br> al '.$_POST['endDate'].'</h1><hr><br><br>';
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
include ('./core/config.inc.php');
//error_reporting(0); //0=spento || -1=acceso
page_start();

$dbClienti=getDbClienti();

$myArray=Array();
$myArray['lavorato']='';
$myArray['grezzo']='';
$myArray['semilavorato']='';

$totale=0;

$radicchi=array('05','705','705-','705--','805','805-','805--','08','708','708-','708--','808','808-','808--','29','729','829');
$insalate=array('01','01S','701','701S','801','801S','03','03S','703','703S','803','803S','25', '803-', '801-');
$pdzucchero=array('31','731','831');


function fixArticolo ($articolo){
	$articolo = str_replace('-', '',$articolo);//remove the "-" "--"

	if (@$articolo[0]== 8 || @$articolo[0]== 7){
		$articolo = substr($articolo, 1);
	}

	return $articolo;
}


if($_POST['startDate']!=null && $_POST['endDate']!=null){
	//$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE F_DATBOL >= #".$_POST['startDate']."# AND F_DATBOL <= #".$_POST['endDate']."#");

	$test=new MyList(
		array(
			'_type'=>'Riga',
			//'ddt_data'=>array('<>',$_POST['startDate'],$_POST['endDate']),
			'ddt_data'=>array('=','04/08/15','06/08/15','11/08/15','13/08/15','18/08/15','20/08/15','25/08/15','27/08/15' ),
			'cod_cliente'=> array('!=','SGUJI','FACCI','FACCG','VIOLA'),
		)
	);
	
	$test->iterate(function($row){
		global $myArray;
		global $totale;
		global $dbClienti;
		$tipo = $dbClienti[$row->cod_cliente->getVal()]['__classificazione'];
		
		//fix articolo mi consente di raggrupppare per esempio 08 708 708-- tutto sotto 08
		$articolo=fixArticolo($row->cod_articolo->getVal());
		
		$oArticolo = new Articolo (Array('codice'=>$articolo));
		
		$descrizione= $oArticolo->descrizione->getVal();
		$peso=$row->peso_netto->getVal();
		$colli=$row->colli->getVal();
		
		
		if (in_array($articolo,$radicchi)) $descrizione=$articolo='**radicchi';
		if (in_array($articolo,$insalate)) $descrizione=$articolo='**insalate';
		if (in_array($articolo,$pdzucchero)) $descrizione=$articolo='**pdzucchero';
		
	
		$done=false;
		if($peso>0){
			if ($tipo=='mercato' || $tipo=='supermercato'){
			$myArray['lavorato'][$articolo.'*'.$descrizione]+=$peso;
 //controllo singole righe
if($articolo=='08' && $peso >500){echo $row->cod_cliente->getVal().'='.$peso."\n<br>";};

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
	});
}
echo '<pre>';
ksort($myArray['lavorato']);
ksort($myArray['grezzo']);
ksort($myArray['semilavorato']);
print_r($myArray);

echo '<hr><h1>Peso totale: '.$totale.'</h1>';
echo '<pre>';

echo '<br>Ps.: Contab conta solo le righe con articoli invece noi contiamo anche le righe condescrizione ma senza articolo)</b><BR><BR>';

page_end();
?>
</span>
</body>