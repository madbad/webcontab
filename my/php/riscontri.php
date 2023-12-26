<?php
include ('./core/config.inc.php');

?>

<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>WebContab RISCONTRI</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		body{
			color: black;
			font-size: 12px;
			font-family: tahoma,arial,verdana,sans-serif;
		}
		.dateform{
			border: 1px solid #00a3f5;
			padding: 0.2em;
		}
		.dateformtitle{
			background-color: #00a3f5;
			width:100%;
			padding:0.5em;
			display:inline-block;
			font-size:1.5em;
		}
		.dateselector{
			padding:0.3em;
			font-size:1.2em;
		}
		.dateselectordescription{
			width:10em;
			display:inline-block;
			padding:0.8em;
		}
		dateselectorcheckbox{
			font-size:2em;
			padding:0.3em;
			transform: scale(2);
			
		}
		tr:has(input:checked){
			background-color:#e0ffc2;
		}
		tr:hover td{
			background-color:gray;
		}

		</style>
		<script>
		function aggiornaListaCodici(){
			listaCodici = [];
			var checkboxes = document.getElementsByClassName("checkboxCodici");
			for (index in checkboxes){
				checkbox = checkboxes[index];
				//console.log('1', checkbox);
				if (checkbox.checked){
					listaCodici.push(checkbox.value);
				}
			}
			console.log(listaCodici);
			var inputListaCodici = document.getElementsByName("listaCodici")[0];
			inputListaCodici.value = listaCodici.join();
		}	
		function checkAll(){
			var checkboxes = document.getElementsByClassName("checkboxCodici");
			for (index in checkboxes){
				checkbox = checkboxes[index];
				checkbox.checked=true;
			}
			aggiornaListaCodici();
		}
		function unCheckAll(){
			var checkboxes = document.getElementsByClassName("checkboxCodici");
			for (index in checkboxes){
				checkbox = checkboxes[index];
				checkbox.checked=false;
			}
			aggiornaListaCodici();
		}
		function fixNumber(number){
			number = String(number).replace('.','');//rimpiazzo il sepratatore migliaia
			number = String(number).replace(',','.');//sostiuisco la virgola col punto
			return Number(number);	
		}
		/*somma selezionati*/
		document.addEventListener("DOMContentLoaded", function() {
			myTables = document.getElementsByClassName("tabellaRiscontri");
			console.log(myTables);
			for (var i= 0; i < myTables.length; i++){
				var myTable = myTables[i];
				aggiornaTotaliJs(myTable);
				myTable.addEventListener("click",function(event){
					//reverse the status of the checkbox
					var myCheckbox = event.target.parentElement.getElementsByTagName("input")[0];
					myCheckbox.checked = !myCheckbox.checked;
					var myTable = event.target.parentElement.parentElement.parentElement
					aggiornaTotaliJs(myTable);
					
				});
				
			}
		});
		
		function aggiornaTotaliJs(myTable){
			var totColli = 0;
			var totPeso = 0;
			var totImponibile = 0;
			//var myTable = myTables[i];
			for (var h= 0; h < myTable.rows.length; h++){
				var myRow = myTable.rows[h];
				//console.log(myRow);
				if(myRow.getElementsByClassName("colli").length > 0){
					//console.log('found');
					if(myRow.getElementsByClassName("riscontriCheckbox")[0].checked){
						totColli += fixNumber(myRow.getElementsByClassName("colli")[0].innerText);
						totPeso += fixNumber(myRow.getElementsByClassName("peso")[0].innerText);
						totImponibile += fixNumber(myRow.getElementsByClassName("imponibile")[0].innerText);
					}
				}
			}
			
			let totJsColli = 0;
			let totJsPeso = 0;
			let totJsMediaPrezzo = 0;
			let totJsMediaPeso = 0;
			let totJsImponibile = 0;
			
			//se non è gia presente aggiungo la riga dei totali javascript
			if(myTable.getElementsByClassName("totJsColli").length > 0){
				totJsColli = myTable.getElementsByClassName("totJsColli")[0];
				totJsPeso = myTable.getElementsByClassName("totJsPeso")[0];
				totJsMediaPrezzo = myTable.getElementsByClassName("totJsMediaPrezzo")[0];
				totJsMediaPeso = myTable.getElementsByClassName("totJsMediaPeso")[0];
				totJsImponibile = myTable.getElementsByClassName("totJsImponibile")[0];
			}else{
				//aggiunge una riga alla fine della tabella
				let row = myTable.insertRow(-1); // We are adding at the end

				// Create table cells
				row.insertCell(0);
				row.insertCell(1);
				row.insertCell(2);
				row.insertCell(3);
				row.insertCell(4);
				totJsColli = row.insertCell(5);
				row.insertCell(6);
				totJsPeso = row.insertCell(7);
				row.insertCell(8);
				row.insertCell(9);
				totJsMediaPrezzo = row.insertCell(10);
				totJsMediaPeso = row.insertCell(11);
				totJsImponibile = row.insertCell(12);
				
				//aggiungo le classi
				totJsColli.classList.add("totJsColli");
				totJsPeso.classList.add("totJsPeso");
				totJsMediaPrezzo.classList.add("totJsMediaPrezzo");
				totJsMediaPeso.classList.add("totJsMediaPeso");
				totJsImponibile.classList.add("totJsImponibile");
				
				//aggiungo un toggle che seleziona de seleziona tutto
				var myCheckbox = document.createElement("INPUT");
				myCheckbox.setAttribute("type", "checkbox");
				myCheckbox.checked=true;
				myCheckbox.addEventListener("change",function(event){
					console.log('clicked');
					var changeTo = !myTable.rows[0].getElementsByTagName("input")[0].checked;
					console.log(changeTo);
					for (var h = 0; h < myTable.rows.length; h++){
						var myRow = myTable.rows[h];
						if(myRow.getElementsByTagName("input").length > 0){
							myRow.getElementsByTagName("input")[0].checked = changeTo;
						}
					}
				});
				myTable.rows[0].cells[0].innerHTML="";
				myTable.rows[0].cells[0].appendChild(myCheckbox);
			}
			//aggiorno i totali
			totJsColli.innerText = totColli;
			totJsPeso.innerText = totPeso.toFixed(2);
			totJsMediaPrezzo.innerText = (totImponibile/totPeso).toFixed(3);
			totJsMediaPeso.innerText = (totPeso/totColli).toFixed(2);
			totJsImponibile.innerText = totImponibile.toFixed(2);
		}
		
		</script>
	</head>
	<body>
<?php
//$today = date("j/n/Y"); 

$lastDayOfPrevMonth = date('Y-m-d', strtotime('last day of previous month'));
$firstDayOfPrevMonth = date('Y-m-d', strtotime('first day of previous month'));

//$today = date("m-d-Y");

if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$firstDayOfPrevMonth;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$lastDayOfPrevMonth;}
if(@$_GET['articolo']){$articolo=$_GET['articolo'];}else{
	echo('Specificare un articolo da cercare');
}
if(@$_GET['listaCodici']){$listaCodici=$_GET['listaCodici'];}
?>

<form name="input" action="./riscontri.php?mode=print" method="get" class="dateform hideOnPrint">
	<input type="text" name="mode" value="print" style="display:none"/>

	<span class="dateformtitle">Selezione parametri</span>

	<br> <span class="dateselectordescription">Start Date:</span>
	<input class="dateselector" type="date" name="startDateR" value="<?php echo $startDateR ?>"/>

	<br> <span class="dateselectordescription">End Date:</span>
	<input class="dateselector" type="date" name="endDateR" value="<?php echo $endDateR ?>"/>

	<br> <span class="dateselectordescription">Articolo:</span>
	<input type="text" name="articolo" value="<?php echo $articolo ?>"/>
	
	<input type="text" name="codiceArticoloDaRimuovere" value="<?php echo $_GET['codiceArticoloDaRimuovere'] ?>"/>	

	<br> <span class="dateselectordescription">Liste codici:</span>
	<input type="text" name="listaCodici" value="<?php echo $listaCodici ?>"/>
	
	<br>
	<input type="submit" value="Submit" style="padding:1em;width:20em;">
	
	<!--
	<textarea name="query" rows="4" cols="50"  style="font-size: 0,5em;width:100%;height:30%">
\$test=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','01/01/16','31/01/16'),
		'cod_articolo'=>array('=','850'),
		//cod_cliente'=>array('!=','FACCI','FACCG'),
		//'cod_destinatario'=>array('=','RAVEN'),
		//'colli'=>array('!=','0'),
		//'prezzo'=>array('!=','0.001')
		//'cod_iva'=>array('=','840'),
	)
);
	</textarea> 
	-->
</form>
<br>
<input type="button" onclick="checkAll()" value="Check all">
/
<input type="button" onclick="unCheckAll()" value="UN-Check all">
<br>
<?php

if($listaCodici!=''){
	$listaCodici = explode(',',$listaCodici);
	$listaArticoli=new MyList(
		array(
			'_type'=>'Articolo',
			'codice'=>$listaCodici,
		)
	);	
	
}else{
	$listaArticoli=new MyList(
		array(
			'_type'=>'Articolo',
			//'descrizione'=>array('LIKE','%ZUCCHI%'),
			'descrizione'=>array('LIKE','%'.$articolo.'%'),
		)
	);	
}



$codiciArticoli="'='";
$arrayCodiciArticoli=array();

$listaArticoli->iterate(function ($obj){
	global $codiciArticoli;
	global $arrayCodiciArticoli;
	
	$codiciArticoli.=",'".$obj->codice->getVal()."'";
	$arrayCodiciArticoli[] = $obj->codice->getVal();

	echo '<br><label>';
	echo '<input class="checkboxCodici" type="checkbox" onclick="aggiornaListaCodici()" value="'.$obj->codice->getVal().'" checked>';
	echo '';
	echo $obj->codice->getVal();
	echo ' => '.$obj->descrizione->getVal();
	echo '</label>';

});



if (@$_GET['mode']=='print'){
	$stampaRighe= function ($obj){
	$color='';
	if ($obj->prezzo->getVal()=='0.001'){
		$color=' style="background-color:red;color:white;" ';
		$checkedString="";
	}else{
		$checkedString=" checked";
	}

		echo "\n".'<tr '.$color.'> ';
		
		echo "\n".'<td><input type="checkbox" class="riscontriCheckbox" '.$checkedString.'></td>';
		echo "\n".'<td>'.$obj->ddt_numero->getVal().'</td>';
		echo "\n".'<td>'.$obj->ddt_data->getFormatted().'</td>';
		echo "\n".'<td>'.$obj->cod_articolo->getVal().'</td>';
//		echo "\n".'<td>'.$obj->cod_cliente->getVal().' # '.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
		echo "\n".'<td>'.$obj->cod_cliente->getVal().'</td>';
		echo "\n".'<td class="colli">'.$obj->colli->getFormatted(0).'</td>';
		if($obj->peso_lordo->getVal() != $obj->peso_netto->getVal()){$warningPeso='<span style="color:orange"><b>*****</b></span>';}else{$warningPeso='';};
		echo "\n".'<td>'.$obj->peso_lordo->getFormatted(2).'</td>';
		echo "\n".'<td class="peso">'.$obj->peso_netto->getFormatted(2).$warningPeso.'</td>';

		echo "\n".'<td>'.$obj->prezzo->getFormatted(3).'</td>';
		$number = number_format($obj->getPrezzoLordo(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo "\n".'<td>'.$number.'</td>';
		$number = number_format($obj->getPrezzoNetto(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo "\n".'<td>'.$number.'</td>';
		$number = number_format($obj->peso_netto->getVal()/$obj->colli->getVal(),2,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo "\n".'<td>'.$number.'</td>';
		$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
		$number = number_format($impNetto,2,$separatoreDecimali=',',$separatoreMigliaia='.');
		echo "\n".'<td  class="imponibile">'.$number.'</td>';
		$obj->_totImponibileNetto->setVal($impNetto);
		//echo '<td>'.$obj->imponibile->getVal().'</td>';
		echo "\n".'</tr>';
	};
	$stampaTotali= function ($obj){
		echo "\n".'<tr>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.$obj->sum('colli').'</td>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.$obj->sum('peso_netto').'</td>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.'-'.'</td>';
		echo "\n".'<td>'.round($obj->sum('_totImponibileNetto')/$obj->sum('peso_netto'),4).'</td>'; //media del prezzo
		echo "\n".'<td>'.round($obj->sum('peso_netto')/$obj->sum('colli'),2).'</td>';
		echo "\n".'<td>'.$obj->sum('_totImponibileNetto').'</td>';
		echo "\n".'</tr>';
	};
	$calcolaImponibileNetto= function ($obj){
		$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
		$obj->_totImponibileNetto->setVal($impNetto);
	};


	$tabellaH="\n".'<table class="borderTable tabellaRiscontri">';
	$tabellaH.="\n".'<tr><td>y/n</td><td>Numero</td><td>Data</td><td>Art.</td><td>Cliente</td><td>Colli</td><td>Peso lordo<td>Peso netto</td><td>Prezzo</td><td>Prezzo L.</td><td>Prezzo N.</td><td>Media peso</td><td>Imponibile Calc.</td></tr>'; //<td>Imponibile Memo.</td>
	$tabellaF="\n".'</table><br><br>';

//==============================================================================================================================
/*
echo '<h1>'.$startDateR.'</h1><hr>';
$startDateR='01/05/20';
$endDateR='31/05/20';


//martinelli
	echo '<h1>Martinelli</h1>';
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('05','VAS05'),
			'cod_cliente'=>'MARTI',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

	
//ortom
	echo '<h1>Ortomercato</h1>';
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','805','605'),
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

//sogegross
	echo '<h1>Sogegross</h1>';
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'05',
			'cod_cliente'=>'SOGEG',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;
	
//mercato
	echo '<h1>Mercato</h1>';
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','05','05P','05G','05PZ8','no 05PZ15','VAS05'),
			'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SISA','SOGEG','GIAC1','BERTO'),
			'prezzo'=>array('!=','0.001')
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

//II
	echo '<h1>Mercato II</h1>';
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>'905',
		)
	);
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;

*/
//==============================================================================================================================


//zucchine
//$startDateR='01/06/20';
//$endDateR='30/06/20';
//print_r($arrayCodiciZucchine);

$parametriRicerca = array(
	'_type'=>'Riga',
	'ddt_data'=>array('<>',$startDateR,$endDateR),
	'cod_articolo'=>$arrayCodiciArticoli,
);

//ortom
	$parametriRicerca['cod_cliente']='SEVEN';
	$test=new MyList($parametriRicerca);
	if(count($test->arr)>0){
		echo '<h1>Ortomercato</h1>';
		echo $tabellaH;
		//tutte le righe
		$test->iterate($stampaRighe);
		$stampaTotali($test);

		//fai i totali di quelli con prezzo
		$parametriRicerca['prezzo']=array('!=','0.001');
		$test=new MyList($parametriRicerca);
		$test->iterate($calcolaImponibileNetto);
		$stampaTotali($test);
		echo $tabellaF;
	}


//abbascia
	$parametriRicerca['cod_cliente']='ABBAS';
	unset($parametriRicerca['prezzo']);
	$test=new MyList($parametriRicerca);
	if(count($test->arr)>0){
		echo '<h1>Abbascia</h1>';
		echo $tabellaH;
		//tutte le righe
		$test->iterate($stampaRighe);
		$stampaTotali($test);

		//fai i totali di quelli con prezzo
		$parametriRicerca['prezzo']=array('!=','0.001');
		$test=new MyList($parametriRicerca);
		$test->iterate($calcolaImponibileNetto);
		$stampaTotali($test);
		echo $tabellaF;
	}

//mediglia
	$parametriRicerca['cod_cliente']='LAME2';
	unset($parametriRicerca['prezzo']);
	$test=new MyList($parametriRicerca);
	if(count($test->arr)>0){
		echo '<h1>Mediglia</h1>';
		echo $tabellaH;
		//tutte le righe
		$test->iterate($stampaRighe);
		$stampaTotali($test);

		//fai i totali di quelli con prezzo
		$parametriRicerca['prezzo']=array('!=','0.001');
		$test=new MyList($parametriRicerca);
		$test->iterate($calcolaImponibileNetto);
		$stampaTotali($test);
		echo $tabellaF;
	}

//martinelli
	$parametriRicerca['cod_cliente']='MARTI';
	unset($parametriRicerca['prezzo']);
	$test=new MyList($parametriRicerca);
	if(count($test->arr)>0){
		echo '<h1>Martinelli</h1>';
		echo $tabellaH;
		//tutte le righe
		$test->iterate($stampaRighe);
		$stampaTotali($test);

		//fai i totali di quelli con prezzo
		$parametriRicerca['prezzo']=array('!=','0.001');
		$test=new MyList($parametriRicerca);
		$test->iterate($calcolaImponibileNetto);
		$stampaTotali($test);
		echo $tabellaF;
	}

//$codiceDaRimuovere = '4721+';
/*
$codiceDaRimuovere = $_GET['codiceArticoloDaRimuovere'];
$tobeRemoved = array_search($codiceDaRimuovere, $arrayCodiciArticoli);

if(count($tobeRemoved)){
	unset($arrayCodiciArticoli[$tobeRemoved]);
}

$parametriRicerca['cod_articolo']=$arrayCodiciArticoli;
*/

//mercato
	$parametriRicerca['cod_cliente']=array('!=','ABBAS','SEVEN','LAME2','MARTI');
	unset($parametriRicerca['prezzo']);
	$test=new MyList($parametriRicerca);

	if(count($test->arr)>0){
		echo '<h1>Mercato</h1>';
		echo $tabellaH;
		//tutte le righe
		$test->iterate($stampaRighe);
		$stampaTotali($test);

		//fai i totali di quelli con prezzo
		$parametriRicerca['prezzo']=array('!=','0.001');
		$test=new MyList($parametriRicerca);
		$test->iterate($calcolaImponibileNetto);
		$stampaTotali($test);
		echo $tabellaF;
	}
/*
if(count($tobeRemoved)){

	$parametriRicerca=array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>$codiceDaRimuovere,
			'cod_cliente'=>array('!=','ABBAS','SEVEN','LAME2','MARTI'),
	);


	$test=new MyList($parametriRicerca);
	if(count($test->arr)>0){
		echo '<h1>Mercato seconda </h1>';
		echo $tabellaH;
		//tutte le righe
		$test->iterate($stampaRighe);
		$stampaTotali($test);

		//fai i totali di quelli con prezzo
		$parametriRicerca['prezzo']=array('!=','0.001');
		$test=new MyList($parametriRicerca);
		$test->iterate($calcolaImponibileNetto);
		$stampaTotali($test);
	}
}
*/
//==============================================================================================================================
/*
17 CAPPUCCI ROSSI
19 CAPPUCCI
36 SEDANO
37 CATALOGNA
20 VERZE
21 CAVOLFIORI
39 LATTUGA
40 GENTILE
41 GENTILE ROSSA
42 PORRI
43 CIPOLLOTTI
45 BIANCO
47 ZUCCHINE
49 MELANZANE
56 MELANZANE VIOLA
50 ZUCCA
52 CETRIOLI
60 PEPERONE
*/
/*
//FINO AL 29/06/17 GIa CONTROLLATO PAN DI ZUCHERO DORO MERCATO

//SOLO TUTTI I MERCATI
$dbClienti=getDbClienti();
$mercati[]='=';
foreach ($dbClienti as $cliente){
//print_r($cliente);
	if (	//$cliente['__classificazione']=='supermercato' ||
		$cliente['__classificazione']=='mercato'){
		$mercati[]= addslashes($cliente['codice']);
	}
};
//echo $codiciArticoli;
$strMercati ='array(\''.implode("','",$mercati).'\')';
//echo "*****(".$strMercati.")*****";
$query = "
	\$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>','01/01/20','30/06/20'),
			//'cod_articolo'=>array(".$codiciArticoli."),
			'cod_articolo'=>array(".$codiciMeloni."),
			//'cod_articolo'=>array('==','631FLOW'),			
			//'cod_articolo'=>array('=','39'),
			//'cod_articolo'=>array('=','05'),
			//'cod_articolo'=>array('=','619','619+','619-','19'),
			//'cod_articolo'=>array('=','639'),
			//'cod_articolo'=>array('=','640','639'),
			//'cod_iva'=>array('=','42',''), 
			//'cod_articolo'=>array('=','03','01'), 
			//'cod_articolo'=>array('=','52'), 
			//'cod_articolo'=>array('=','100'), //CONF.NATALIZIA
			//'cod_articolo'=>array('=','36','836'), //SEDANO
			//'cod_articolo'=>array('=','17'), //CAPPUCCI ROSSI
			//'cod_articolo'=>array('=','18','818'), //CAPPUCCI CUOR DI BUE
			//'cod_articolo'=>array('=','96'), //PISELLI
			//'cod_articolo'=>array('=','51'), //FAGIOLINI
			//'cod_articolo'=>array('=','819','819','19','619','619+'), //CAPPUCCI
			//'cod_articolo'=>array('=','20','820'),  //VERZE
			//'cod_articolo'=>array('=','21','21V','621','821','921'),  //CAVOLFIORI
			//'cod_articolo'=>array('=','50','50ZA','850','650','650+','650-','650ZA'), //ZUCCHE
			//'cod_articolo'=>array('=','650','650+'), //ZUCCHE
			//'cod_articolo'=>array('=','65'), //PEPERONCINI PICCANTI
			//'cod_articolo'=>array('=','8111'), //MELONI
			//'cod_articolo'=>array('=','849','49','56','856','649','646'), //MELANZANE
			//'cod_articolo'=>array('=','843','43','57','857'),//CIPOLLE CIPOLLOTTI
			//'cod_articolo'=>array('=','847','647','47','471421','47714','947'),//ZUCCHINE
			//'cod_articolo'=>array('=','52'),//CETRIOLI
			//'cod_articolo'=>array('=','01','01-','801','801-','03','03-','803','803-'),
			//'cod_articolo'=>array('=','42','942','842','842-','642','642+','642-'), //PORRI
			//'cod_articolo'=>array('=','45','845','645','645+' ),  //BIANCO
			//'cod_articolo'=>array('=','645'),  //BIANCO
			//'cod_articolo'=>array('=','801-','803-','01','03'),
			//'cod_articolo'=>array('=','11','911','113','111','1113050','1113040','91113040','1114060', '8111','8112','112','9112', '8111-', '9111'),
			//'cod_articolo'=>array('=','842'),
			//'cod_articolo'=>array('=','817','17','917'),
			//'cod_articolo'=>array('=','947','47','471421','47714','847','647'),
			//'cod_articolo'=>array('=','32'),
			//'cod_articolo'=>array('=','01','03','01S','03S','01F','03F'),
			//'cod_articolo'=>array('=','45','08B'),
			//'cod_cliente'=>".$strMercati.",
			//'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI','ORTO3','GIAC1','LAME2','PASTA'),
			//'cod_cliente'=>array('!=','VIOLA','SEVEN','MARTI'),
			'cod_cliente'=>array('=','SEVEN'),
			//'cod_cliente'=>array('=','SOGEG'),
			//'cod_cliente'=>array('=','BRUNF'),
			//'cod_cliente'=>array('=','ABBAS'),
			//'cod_articolo'=>array('!=','BSEVEN','631FLOW','31FLOW','631FLOW6'),
			//'cod_articolo'=>array('=','631FLOW','31FLOW','631FLOW6'),
			//'cod_articolo'=>array('=','BSEVEN'),
			//'cod_articolo'=>array('=','829','629','829-'),
			//'cod_cliente'=>array('!=','ABBAS','SEVEN'),
			//'cod_cliente'=>array('!=','SEVEN','TESI','GIAC1','MARTI','BRUNF','NERIO','LAME2'),
			//'cod_cliente'=>array('=','MARTI'),
			//'cod_articolo'=>array('=','29','VAS29'),
			//'cod_cliente'=>array('!=','SEVEN','MARTI','VIOLA','GIMM2'),
			//'cod_cliente'=>array('!=','SEVEN'),			
			//'cod_cliente'=>array('=','FACCG'),
			//'cod_destinatario'=>array('=','RAVEN'),
			//'colli'=>array('!=','0'),
			//'prezzo'=>array('!=','0.001','0.000')
		)
	);
";
//echo $query;
eval ($query);

//var_dump($test);
	var_dump($test->_params['cod_articolo']);
	var_dump($test->_params['ddt_data']);
	echo '<table><tr><td style="background-color:red;color:white;" >------</td><td>= manca ricavo</td></tr></table>';
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;





*/


//==============================================================================================================================
/*
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
		//	'cod_articolo'=>array('=','11','111','112','113',
		//						      '911','9111','9112','9113','05'
		//	),
			'cod_articolo'=>array('=','18','19'),
			'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI'),
			//'prezzo'=>array('!=','0.001'),
			
		//	'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI')
			//'cod_articolo'=>array('=','11','111'),
			//'cod_cliente'=>array('!=','MARTI','FACCG','FACCI','SEVEN','SMA','SGUJI')
		)
	);	
*/
//==============================================================================================================================

/*
	$test=new MyList(
		array(
			'_type'=>'Riga',
			'ddt_data'=>array('<>',$startDateR,$endDateR),
			'cod_articolo'=>array('=','20'),
			'cod_cliente'=>array('!=','VIOLA'),
			//'prezzo'=>array('!=','0.001'),
		)
	);
	echo '<pre style="font-size:x-small;">';
	print_r($test->_params);
	echo '</pre>';
	echo $tabellaH;
	$test->iterate($stampaRighe);
	$stampaTotali($test);
	echo $tabellaF;
*/
	page_end();
}
?>
<table>
<tr>
<td>
<table style="font-size:1.2em;" class="borderTable spacedTable">
	<tr>
		<td></td>
		<td>Colli</td>
		<td>_Peso_</td>
	</tr>
	<tr>
		<td>Rim.Iniziale</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Entrate</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Uscite</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>Rim.Finale</td>
		<td></td>
		<td></td>
	</tr>
</table>
</td>
<td>
<table style="font-size:1.2em;" class="borderTable spacedTable">
		<tr><td>Ricavo Lordo</td><td width="100px"></td></tr>
		<tr><td>cassa</td><td></td></tr>
		<tr><td>trasporto</td><td></td></tr>
		<tr><td>provvigione</td><td></td></tr>
		<tr><td>provvigione</td><td></td></tr>
		<tr><td>manodopera</td><td></td></tr>
		<tr><td><b>netto<b></td><td></td></tr>
		<tr><td>prezzo pagabile</td><td></td></tr>
</table>
</td>
</tr>
</table>
</body>
