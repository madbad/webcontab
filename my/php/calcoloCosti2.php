<?php
include ('./core/config.inc.php');
//require_once('./classes.php');
//page_start();

$copertina = new stdClass();
$copertina->costo = 0.075;

$vassoio = new stdClass();
$vassoio->costo= 0.049;

$film = new stdClass();//su m2
$film->costo= 0.01;

$cassapl304013nera = new stdClass();
$cassapl304013nera->costo = 0.50;
$cassapl304013nera->collipedana = 150;
$cassapl305013nera = new stdClass();
$cassapl305013nera->costo = 0.51;
$cassapl305013nera->collipedana = 120;
$cassapl305016nera = new stdClass();
$cassapl305016nera->costo = 0.56;
$cassapl305016nera->collipedana = 104;
$cassapl305023nera = new stdClass();
$cassapl305023nera->costo = 0.77;
$cassapl305023nera->collipedana = 72;



$cassapolymer4416 = new stdClass();
$cassapolymer4416->costo = 0.54;
$cassapolymer4416->collipedana = 88;
$cassapolymer6413 = new stdClass();
$cassapolymer4416->costo = 0.71;
$cassapolymer4416->collipedana = 48;



// Takes raw data from the request
$json = file_get_contents('php://input');
// Converts it into a PHP object
$data = json_decode($json);
//print_r($data);

if(isset($data->salvadati)){
	echo 'php saving...';
	$filename='./dati/rimanenze/'.$data->data.'.txt';
	file_put_contents ($filename , json_encode($data->dati));
	exit;
}

function formatData($data){
	$newData=explode("-", $data);
	return $newData[2].'/'.$newData[1];
}
$dataStorage = array();

//mi memorizzo il database clienti	(dove ho salvato se sono mercati supermercati o altro)
$dbClienti=getDbClienti();
/*------------------------------------------------------------------------------------------

*/
	function getData($params, $storageName, $subName){
		global $dbClienti;
		global $dataStorage;
		if (!array_key_exists($storageName, $dataStorage)){
			$dataStorage[$storageName]=array();
		}
		/*
		$params = array("articles" => "31",
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 33,
						"colliPedana" => 104,
						"costoCassa" => 0.43);
		*/

		$out=null;
		//preparo la stringa di query con le condizioni di query per i prodotti
		$condizioniProdotti='';
		foreach ($params['articles'] as $value){
			$condizioniProdotti.="OR F_CODPRO='$value'";
		}
		//rimuovo il primo 'OR ' nella parte iniziale della stringa
		$condizioniProdotti=substr($condizioniProdotti, 3);
		$condizioniProdotti="($condizioniProdotti) AND";
		//decommentare la linea seguente per usare il vecchio metodo che selezionava tutti i prodotti (anche quelli che non ci interesavano)
		//$condizioniProdotti='';
		if ($params["cliente"]){
			$condizioniCliente=" AND F_CODCLI='".$params["cliente"]."' ";
		}else{
			$condizioniCliente='';
		}
		
		$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE $condizioniProdotti F_DATBOL >= #".$params['startDate']."# AND F_DATBOL <= #".$params['endDate']."# $condizioniCliente ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE");

		foreach ($result as $id => $row){
		//while($row = odbc_fetch_array($result)){
			$codCliente=$row['F_CODCLI'];
			$tipoCliente=$dbClienti["$codCliente"]['__classificazione'];
			
			
			//può essere che un nuovo cliente non sia ancora stato classificato
			//stampo un allert così se necessario vado ad inserirlo
			if(!array_key_exists($codCliente,$dbClienti)){
				echo '<br>!!! Cliente non presente nel nostro database !!! =>'.$codCliente;
				//echo array_keys($dbClienti);
			}

		
//			if (in_array($row['F_CODPRO'],$params['articles']) && ($tipoCliente=='mercato' || $tipoCliente=='supermercato') && ($codCliente!="MARTI") /*| $codCliente=='BRUNF'*//*ABILITA UNO SPECIFICO CODICE CLIENTE ANCHE SE NON RIENTRA TRA IL LAVORTO*/){
			if (
				(in_array($row['F_CODPRO'],$params['articles']) && ($tipoCliente=='mercato' || $tipoCliente=='supermercato') && ($codCliente!="MARTI"))
				||
				(($row['F_CODCLI']=="MARTI") && ($params['cliente']=="MARTI"))
				/*| $codCliente=='BRUNF'*//*ABILITA UNO SPECIFICO CODICE CLIENTE ANCHE SE NON RIENTRA TRA IL LAVORTO*/
				){

				//ci aggiungo un calo peso se necessario
				$calopeso=round(round($row['F_NUMCOL'])*$params['abbuonoPerCollo']);
				//se ho gia ricevuto il riscontro peso non tolgo il calo peso... il peso è già corretto!
				if($row['F_PESNET'] == $row['F_QTA']){
					$netto=$row['F_PESNET'];
				}else{
					$netto=$row['F_PESNET']-$calopeso;
				}
				
				//salvo i datinello storage
				$riga= array();
				$riga['data']=formatData($row['F_DATBOL']);
				$riga['cliente']=$row[F_CODCLI];
				$riga['colli']=round($row['F_NUMCOL']);
				$riga['netto']=$netto;
				$riga['media']=round($netto/$row['F_NUMCOL'],1);
				$riga['tara']=round(($row['F_QTA']-$row['F_PESNET'])/$row['F_NUMCOL'],3);

				
				if (!array_key_exists($subName, $dataStorage[$storageName])){
					$dataStorage[$storageName][$subName]=array();
					$dataStorage[$storageName][$subName]['righe']=array();
					$dataStorage[$storageName][$subName]['params']=$params;
				}
				array_push($dataStorage[$storageName][$subName]['righe'], $riga);
			}
			/*
			if(($row['F_CODCLI']=="MARTI") && ($params['cliente']=="MARTI")){
				//echo $row['F_CODCLI']
				$calopeso=round(round($row['F_NUMCOL'])*$params['abbuonoPerCollo']);
				
				//se ho gia ricevuto il riscontro peso non tolgo il calo peso... il peso è già corretto!
				if($row['F_PESNET'] == $row['F_QTA']){
					$netto=$row['F_PESNET'];
				}else{
					$netto=$row['F_PESNET']-$calopeso;
				}
				

				
				//salvo i datinello storage
				$riga= array();
				$riga['data']=formatData($row['F_DATBOL']);
				$riga['cliente']=$row[F_CODCLI];
				$riga['colli']=round($row['F_NUMCOL']);
				$riga['netto']=$netto;
				$riga['media']=round($netto/$row['F_NUMCOL'],1);
				$riga['tara']=round(($row['F_QTA']-$row['F_PESNET'])/$row['F_NUMCOL'],3);

				
				if (!array_key_exists($subName, $dataStorage[$storageName])){
					$dataStorage[$storageName][$subName]=array();
					$dataStorage[$storageName][$subName]['righe']=array();
					$dataStorage[$storageName][$subName]['params']=$params;
				}
				array_push($dataStorage[$storageName][$subName]['righe'], $riga);
			}
			*/
		}
	}
	
/*------------------------------------------------------------------------------------------

*/
	function printTable(){
		global $dataStorage;
		foreach ($dataStorage as $articleName => $articleData){
			echo "\n<div class='tableContainer'>";
			echo "\n<h1>".$articleName."</h1>";
			echo "\n<table  class='righe smallFontTable'>";
			foreach ($articleData as $subName => $subData){
				
				//titolo
				echo "\n<tr>";
				echo '<td colspan="6" ><h3>'.$subName.'</h3></td>';
				echo '</tr>';

				//intestazione
				echo "\n<tr>";
				echo "\n<th>Data</th>";
				echo "\n<th>Cliente</th>";
				echo "\n<th>Colli</th>";
				echo "\n<th>p.Net</th>";
				echo "\n<th>md</th>";
				echo "\n<th>tar</th>";
				echo '</tr>';
				
				//righe
				foreach ($subData['righe'] as $riga){
					echo "\n<tr>";
					echo "\n<td>".$riga['data'].'</td>';
					echo "\n<td>".$riga['cliente'].'</td>';
					echo "\n<td>".$riga['colli'].'</td>';
					echo "\n<td>".$riga['netto'].'</td>';
					echo "\n<td>".$riga['media'].'</td>';
					echo "\n<td>".$riga['tara'].'</td>';
					echo "\n</tr>";
				}
				//totali
				$totColli = array_sum(array_map(function($item) { 
					return $item['colli']; 
				}, $subData['righe']));
				$totNetto = array_sum(array_map(function($item) { 
					return $item['netto']; 
				}, $subData['righe']));
				echo "\n<tr>";
				echo "\n<td colspan='3'><h2>".$totColli.'</h2></td>';
				echo "\n<td colspan='3'><h2>".$totNetto.'</h2></td>';
				echo "\n</tr>";
				
				//costi
				//echo '*******************************'.$dataStorage[$subName]['params']['costoCassa'];
				$costoCassa = round($subData['params']['costoCassa']*$totColli/$totNetto,3);
				$costoTrasporto = round($subData['params']['costoPedana']/(($totNetto/$totColli)*$subData['params']['colliPedana']),3);
				echo "\n<tr>";
				echo "\n<td colspan='3'>Imballaggio</td>";
				echo "\n<td colspan='3'>".$costoCassa."</td>";
				echo '</tr>';
				echo "\n<tr>";
				echo "\n<td colspan='3'>Trasporto</td>";
				echo "\n<td colspan='3'>".$costoTrasporto."</td>";
				echo '</tr>';
			}
			echo '</table>';
			echo "</div>";
		}
		//print_r($dataStorage);
	}
?>
<!DOCTYPE HTML>
<html lang="IT">
    <head>
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		body{
			color: black;
			font-size: 12px;
			font-family: tahoma,arial,verdana,sans-serif;
		}
		@page {
		  size: A4;
		  margin: 0mm;
		}
		@media print {
			html{
				/*
				width: 210mm;
				height: 287mm;
				*/
				width: 280mm;
				height: 200mm;
				/*background-color:red;*/
				margin: 0mm;
				padding:0mm;
			}
			/*.pagebreak {page-break-after: always;}*/
			body{ 
				width: 478mm;
				height: 200mm;  /*297*//*350*/
				/*border:1mm solid black;*/
				margin: 0mm;
				padding:0mm;
				scale: 0.60;
				/*margin-top: -40mm;*/
				/*margin-left: -80mm;*/
				transform-origin: top left;
				/*baCKGROUND-COLOR:YELLOW;*/
				overflow:hidden;
				
			}
		}
		@media print{@page {size: landscape}}
		#output{
			padding-top: 4em;	
		}
		
		</style>
    </head>
     <body>
	<?php
	$today = date("m-d-Y");
	if(@$_POST['startDate']){$startDate=$_POST['startDate'];}else{$startDate=$today;}
	if(@$_POST['endDate']){$endDate=$_POST['endDate'];}else{$endDate=$today;}
	  
	if(@$_POST['startDateR']){$startDateR=$_POST['startDateR'];}else{$startDateR=$today;}
	if(@$_POST['endDateR']){$endDateR=$_POST['endDateR'];}else{$endDateR=$today;}
	  
	?>
<style>
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
</style>
<form action="./calcoloCosti2.php" class="dateform hideOnPrint" method="post"> 
	<span class="dateformtitle">Selezione parametri</span>
	<br> <span class="dateselectordescription">From:</span>
	<input class="dateselector" type="date" id="startDate" name="startDate" value="<?php echo $startDate ?>">
	<br> <span class="dateselectordescription">to:</span>
	<input class="dateselector" type="date" id="endDate" name="endDate" value="<?php echo $endDate ?>">
	<br> <span class="dateselectordescription">From (R):</span>
	<input class="dateselector" type="date" id="startDateR" name="startDateR" value="<?php echo $startDateR ?>">
	<br> <span class="dateselectordescription">to (R):</span>
	<input class="dateselector" type="date" id="endDateR" name="endDateR" value="<?php echo $endDateR ?>">
	<br>
	<input class="dateselectorcheckbox" type="checkbox" id="extraProducts" name="extraProducts" style="padding:2em;" <?php if(in_array("extraProducts",$_POST)){echo ' checked';}?>><label for="extraProducts" class="dateselectorcheckbox" > Includi radicchi <?PHP echo $_POST['extraProducts'];print_r($_POST)?></label>
	<br>
	<input name="mode" value="print" style="display: none;">

	<input type="submit" value="Submit" style="padding:1em;width:20em;">
	<button onclick="savedata();return false;" style="padding:1em;width:20em;">Save</button>
</form>
<script>
if(document.getElementById('startDate').value==""){
	

	var myDate = new Date();

	
	document.getElementById('startDate').valueAsDate = myDate;
	document.getElementById('endDate').valueAsDate = myDate;
	document.getElementById('endDateR').valueAsDate = myDate;
	myDate.setDate(myDate.getDate() -1);	
	document.getElementById('startDateR').valueAsDate = myDate;
}
</script>
<?php
/*
$costoPolistirolo3050
$costoPlastica3050bianca
$costoPolistirolo3050
$costoPolistirolo3050
*/

if (@$_POST['mode']=='print'){
    $table='<table class="rimanenze alignRight" >';
    $table.='<tr><td colspan="2">Rimanenze</td></tr>';
    $table.='<tr><td class="rimanenzecellone"></td><td class="rimanenzecelltwo"></td></tr>';
    $table.='<tr><td><b>- pl</b></td><td></td></tr>';
    $table.='<tr><td><b>- Poly</b></td><td></td></tr>';
    $table.='<tr><td><b>- Pad.</b></td><td></td></tr>';
    $table.='<tr><td>+ pl</td><td></td></tr>';
    $table.='<tr><td>+ Poly</td><td></td></tr>';
    $table.='<tr><td>+ Pad.</td><td></td></tr>';
    $table.='<tr><td colspan="2" style="border:4px solid #000000">Tot.<br><br><br></td></tr>';
    $table.='</table>';
	//riccia
	// mercato
	$params = array("articles" => array('01','01S','01F','01SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.25, //0.3
					"costoPedana" => 50,
					"colliPedana" => $cassapl305016nera->collipedana,
					"costoCassa" => $cassapl305016nera->costo + $copertina->costo);
	getData($params,'RICCIA','MERCATI');
	
	$params = array("articles" => array('01','01S','01F','01SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" =>  0.25, //0.3
					"costoPedana" => 30,
					"colliPedana" => $cassapl305016nera->collipedana,
					"costoCassa" => $cassapl305016nera->costo + $copertina->costo,
					"cliente"=>"MARTI");//cassa vecchia
	getData($params,'RICCIA','MARTINELLI');
	//supermercati
	$params = array("articles" => array('801','801-','801F-','601'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.3,
					"costoPedana" => 4,
					"colliPedana" => $polymer4416->costo,
					"costoCassa" => $polymer4416->costo);
	getData($params,'RICCIA','SUPERMERCATI');
	
//scarola  
	// mercato
	$params = array("articles" => array('03','03S','03F','03SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" =>  0.25, //0.3
					"costoPedana" => 50,
					"colliPedana" => $cassapl305016nera->collipedana,
					"costoCassa" => $cassapl305016nera->costo + $copertina->costo);
	getData($params,'SCAROLA','MERCATI');
	$params = array("articles" => array('03','03S','03F','03SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" =>  0.25, //0.3
					"costoPedana" => 30,
					"colliPedana" => $cassapl305016nera->collipedana,
					"costoCassa" => $cassapl305016nera->costo + $copertina->costo,
					"cliente"=>"MARTI");//cassa vecchia
	getData($params,'SCAROLA','MARTINELLI');
	//supermercati
	$params = array("articles" => array('803','803-','803F-','603'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.3,
					"costoPedana" => 4,
					"colliPedana" => $polymer4416->collipedana,
					"costoCassa" => $polymer4416->costo);
	getData($params,'SCAROLA','SUPERMERCATI');


    if(@$_POST['extraProducts']){
//chioggia
		// mercato
		$params = array("articles" => array('08','08B','08F','08G','08P','08POL','08PZ11','08TRAD'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.25, //0.3
						"costoPedana" => 50,
						"colliPedana" => $cassapl305013nera->collipedana,
						"costoCassa" => $cassapl305013nera->costo + $copertina->costo);
		getData($params,'CH','MERCATI');
		// supermercati
		$params = array("articles" => array('708','808','808+','708-','808-','708--','808--','608','608-','608+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.2,
						"costoPedana" => 4,
						"colliPedana" => $polymer6413->collipedana,
						"costoCassa" => $polymer6413->costo);
		getData($params,'CH','SUPERMERCATI');
		// VASSOI
		// MARTINELLI
		$params = array('articles' => array('08'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0, //0.3//0.5
						"costoPedana" => 30,
						"colliPedana" => $cassapl305023nera->collipedana,
						"costoCassa" =>  $cassapl305023nera->costo + $vassoio->costo * 6 + $film->costo * 6,
						"cliente"=>"MARTI");
		getData($params,'CH','MARTINELLI');
		
//treviso
		// mercato
		$params = array("articles" => array('29'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.3, //0.3
						"costoPedana" => 50,					
						"colliPedana" => $cassapl305013nera->collipedana,
						"costoCassa" => $cassapl305013nera->costo + $copertina->costo);
		getData($params,'TV','MERCATI');
		// supermercati
		$params = array("articles" => array('729','829','729-','829-','629','629-','629+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 4,
						"colliPedana" => $polymer6413->collipedana,
						"costoCassa" => $polymer6413->costo);
		getData($params,'TV','SUPERMERCATI');
		//VASSOI
		// MARTINELLI
		$params = array('articles' => array('29'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0, //0.3//0.5
						"costoPedana" => 30,
						"colliPedana" => $cassapl305013nera->collipedana,
						"costoCassa" =>  $cassapl305013nera->costo + $vassoio->costo * 6 + $film->costo * 6,
						"cliente"=>"MARTI");
		getData($params,'TV','MARTINELLI');
		
//pan di zucchero
		$params = array("articles" => array('31'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4, //0.3
						"costoPedana" => 50,
						"colliPedana" => $cassapl305016nera->collipedana,
						"costoCassa" => $cassapl305013nera->costo);
		getData($params,'PDZ','MERCATI');
		$params = array("articles" => array('731','831','831-','631','631+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0,
						"costoPedana" => 0,
						"colliPedana" => 0,
						"costoCassa" => 0);
		getData($params,'PDZ','FLOWPACK');
		$params = array("articles" => array('31FLOW'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.1,
						"costoPedana" => 0,
						"colliPedana" => 0,
						"costoCassa" => 0);
		getData($params,'PDZ','FLOPPATO');
		$params = array("articles" => array('631FLOW','631FLOW6','631FLOW6+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0,
						"costoPedana" => 0,
						"colliPedana" => 0,
						"costoCassa" => 0);
		getData($params,'PDZ','SEVEN-FLOW');

//verona
		// mercato
		$params = array('articles' => array('05','05G','05P','05PL','05PZ12','05PZ1215','05PZ15','05PZ8'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.1, //0.3//0.5
						"costoPedana" => 50,
						"colliPedana" => $cassapl305013nera->collipedana,
						"costoCassa" => $cassapl305013nera->costo + $copertina->costo);
	getData($params,'VR','MERCATI');
		// supermercati
		$params = array("articles" => array('705','805','705-','805-','705--','805--','605','605+' ),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 0,
						"colliPedana" => 0,
						"costoCassa" => 0);
	getData($params,'VR','SUPERMERCATI');
		// VASSOI
		// MARTINELLI
		$params = array('articles' => array('05','05P'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0, //0.3//0.5
						"costoPedana" => 30,
						"colliPedana" => $cassapl305013nera->collipedana,
						"costoCassa" => $cassapl305013nera->costo + $vassoio->costo * 6 + $film->costo * 6,
						"cliente"=>"MARTI");
	getData($params,'VR','MARTINELLI');
    
	}
}
//print_r($dataStorage);
$myJSON = json_encode($dataStorage);
echo '<div id="output"></div>';
echo '<script>';
echo 'dati = '.$myJSON.';';
echo 'console.log(dati);';
echo '</script>';
echo '<script type="text/javascript" src="./calcoloCosti.js"></script>';
//printTable();
page_end();
?>
</span>
<script>
function savedata(){
	myjson = {},
	myjson.data=document.querySelector('#endDate').value;
	myjson.salvadati=true;
	myjson.dati=dati;
	
	console.log('JS saving', myjson);
	var opts = { 
		method: 'POST', // *GET, POST, PUT, DELETE, etc.
		headers: {
		  'Content-Type': 'application/json'
		  // 'Content-Type': 'application/x-www-form-urlencoded',
		},
		body: JSON.stringify(myjson) // body data type must match "Content-Type" header
	};
	fetch('./calcoloCosti2.php', opts).then(function (response) {
		console.log('reponse',response);
		console.log('reponse',response.text());
	  //return response.json();
	})
	.then(function (body) {
		alert('Rimanenze salvate');
	});
}

function loaddata(){
	//INSALATE
	var loaddate = new Date(document.querySelector('#startDate').value);
	console.log(loaddate);
	loaddate.setDate(loaddate.getDate()-1);
	console.log(loaddate);
	var month =1 + 1*loaddate.getMonth();
	//console.log(month, month.length);
	if( month < 10){
		month='0'+month;
	}
	var day = loaddate.getDate();
	if( day < 10){
		day='0'+day;
	}
	var newdate = loaddate.getFullYear()+'-'+month+'-'+day;
	console.log(newdate);
	var fileUrl = './dati/rimanenze/'+newdate+'.txt';
	console.log(fileUrl);
	var client = new XMLHttpRequest();
	client.open('GET', fileUrl);
	client.onreadystatechange = function() {
		console.log(client.responseText);
		datiRimanenze = JSON.parse(client.responseText) 
		console.log(datiRimanenze);
		
		//leggiamo le rimanenze
		//per ogni articolo riccia scarola ch tv vr etc
		['RICCIA','SCAROLA'].forEach(key => {
			console.log(key);        // the name of the current key.
			console.log(datiRimanenze[key].RIMANENZAFINALE); // the value of the current key.
			datiRimanenze[key].RIMANENZAFINALE.righe.forEach(function(riga) {
				console.log(riga);
				index = datiRimanenze[key].RIMANENZAFINALE.righe.indexOf(riga);
				console.log(index);
				inputColli = document.querySelectorAll(`[data-articolo="`+key+`"][data-tiporimanenza="RIMANENZAINIZIALE"][name="colli"]`);
				inputPeso = document.querySelectorAll(`[data-articolo="`+key+`"][data-tiporimanenza="RIMANENZAINIZIALE"][name="peso"]`);
				console.log(inputColli);
				if(inputColli !=null && inputPeso!=null && riga !=null){
					inputColli[index].value= riga.colli * -1;
					inputPeso[index].value= riga.peso * -1;					
inputPeso[index].dispatchEvent(new Event('change', { 'bubbles': true }));
				}
			});
		});
	}
	client.send();
	
	
	
	
	
	//RADICCHIO
	var loaddate = new Date(document.querySelector('#startDateR').value);
	console.log(loaddate);
	loaddate.setDate(loaddate.getDate()-1);
	console.log(loaddate);
	var month =1 + 1*loaddate.getMonth();
	//console.log(month, month.length);
	if( month < 10){
		month='0'+month;
	}
	var day = loaddate.getDate();
	if( day < 10){
		day='0'+day;
	}
	var newdate = loaddate.getFullYear()+'-'+month+'-'+day;
	console.log(newdate);
	var fileUrl = './dati/rimanenze/'+newdate+'.txt';
	console.log(fileUrl);
	var client2 = new XMLHttpRequest();
	client2.open('GET', fileUrl);
	client2.onreadystatechange = function() {
		console.log(client2.responseText);
		datiRimanenze = JSON.parse(client2.responseText) 
		console.log(datiRimanenze);
		
		//leggiamo le rimanenze
		//per ogni articolo riccia scarola ch tv vr etc
		['CH','TV','PDZ','VR'].forEach(key => {
			console.log(key);        // the name of the current key.
			console.log(datiRimanenze[key].RIMANENZAFINALE); // the value of the current key.
			datiRimanenze[key].RIMANENZAFINALE.righe.forEach(function(riga) {
				console.log(riga);
				index = datiRimanenze[key].RIMANENZAFINALE.righe.indexOf(riga);
				console.log(index);
				inputColli = document.querySelectorAll(`[data-articolo="`+key+`"][data-tiporimanenza="RIMANENZAINIZIALE"][name="colli"]`);
				inputPeso = document.querySelectorAll(`[data-articolo="`+key+`"][data-tiporimanenza="RIMANENZAINIZIALE"][name="peso"]`);
				console.log(inputColli);
				if(inputColli !=null && inputPeso!=null && riga !=null){
					inputColli[index].value= riga.colli * -1;
					inputPeso[index].value= riga.peso * -1;
inputPeso[index].dispatchEvent(new Event('change', { 'bubbles': true }));				
				}
			});
		});
	}
	client2.send();
	
	
	
	
	
}
loaddata();
</script>

</body>

