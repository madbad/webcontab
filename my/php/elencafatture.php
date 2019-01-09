<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Elenca fatture</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script>
	function sendMails(){
		var fatture = document.querySelectorAll("input:checked");
		var infobox = document.querySelector("#infobox");
		var localinfobox = document.querySelector("#localinfobox");
		var serverinfobox = document.querySelector("#serverinfobox");
		var spinner = document.querySelector("#infobox");
		var mails=[];
		var confirmMessage='';
		
		for (var i = 0; i < fatture.length; ++i) {
			//console.log(fatture[i].dataset.json);
			var oFattura = JSON.parse(fatture[i].dataset.json);
			//console.log(oFattura);
			confirmMessage += "\n"+oFattura.tipo+" "+oFattura.numero+" del "+oFattura.data+" - "+oFattura.cliente;
			//confirmMessage+= "\n"+oFattura.tipo;
			mails[i]= JSON.parse(fatture[i].dataset.json);
			delete mails[i].cliente; //to prevent trouble with strange chars (& etc..) on json parsing on server side
		}
		
		// chiedo conferma prima di proseguire
		if(!confirm ("Si desidera veramente inviare le seguenti \n"+fatture.length+"\n fatture?\n\n"+confirmMessage)){
			return;
		}
		
		serverinfobox.innerHtml='';
		localinfobox.innerHTML ='Sending <b>'+fatture.length+'<b> mails!';
		spinner.classList.add('spinner');
		infobox.classList.remove('hidden');
		
		
		//una stringa json che identifica tutte le fatture da inviare
		var jsonMails=JSON.stringify(mails);
		
		//SEND THE REQUEST TO THE SERVER
		var xmlhttp;
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}else{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		//cosa faccio quando la richiesta è finita?
		xmlhttp.onreadystatechange=function(){
			//request finished succefully
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				serverinfobox.innerHTML = xmlhttp.responseText;
				localinfobox.innerHTML += '<br><B>DONE!!</B>';
				spinner.classList.remove('spinner');
				clearInterval(interval);
				infobox.querySelector("button").classList.remove('hidden');
				infobox.querySelector("button").onclick=function (){
					infobox.classList.add('hidden');
					infobox.querySelector("button").classList.add('hidden');
					infobox.querySelector("button").onclick=function(){};
				}
			}
			//request still pending but some data is available
			if(xmlhttp.readyState == 3) {
				serverinfobox.innerHTML = xmlhttp.responseText;
			}
		}
		
		//xmlhttp.open("POST","./wait.php",true);
		xmlhttp.open("POST","./core/gestioneFatture.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("do=inviaPec&fatture="+jsonMails);
		
		// function to update the infobox with the server reply
		function statusUpdate() {
			//console.log('Update');
			serverinfobox.innerHTML = xmlhttp.responseText;
		}
		 
		// Check for new content from the server every 0.5 seconds
		var interval = setInterval(statusUpdate, 500);
	}
	function stampa(mode){
		var fatture = document.querySelectorAll("input:checked");
		var infobox = document.querySelector("#infobox");
		var localinfobox = document.querySelector("#localinfobox");
		var serverinfobox = document.querySelector("#serverinfobox");
		var spinner = document.querySelector("#infobox");
		var mails=[];
		var confirmMessage='';
		
		for (var i = 0; i < fatture.length; ++i) {
			//console.log(fatture[i].dataset.json);
			var oFattura = JSON.parse(fatture[i].dataset.json);
			//console.log(oFattura);
			confirmMessage += "\n"+oFattura.tipo+" "+oFattura.numero+" del "+oFattura.data+" - "+oFattura.cliente;
			
			//confirmMessage+= "\n"+oFattura.tipo;
			mails[i]= JSON.parse(fatture[i].dataset.json);
			delete mails[i].cliente; //to prevent trouble with strange chars (& etc..) on json parsing on server side
		}
		
		// chiedo conferma prima di proseguire
		if(!confirm ("Si desidera veramente stampare le seguenti \n"+fatture.length+"\n fatture?\n\n"+confirmMessage)){
			return;
		}
		
		serverinfobox.innerHtml='';
		localinfobox.innerHTML ='Printing <b>'+fatture.length+'<b> files!';
		spinner.classList.add('spinner');
		infobox.classList.remove('hidden');
		
		//una stringa json che identifica tutte le fatture da inviare
		var jsonMails=JSON.stringify(mails);
		
		//SEND THE REQUEST TO THE SERVER
		var xmlhttp;
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}else{// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		//cosa faccio quando la richiesta è finita?
		xmlhttp.onreadystatechange=function(){
			//request finished succefully
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				serverinfobox.innerHTML = xmlhttp.responseText;
				localinfobox.innerHTML += '<br><B>DONE!!</B>';
				spinner.classList.remove('spinner');
				clearInterval(interval);
				infobox.querySelector("button").classList.remove('hidden');
				infobox.querySelector("button").onclick=function (){
					infobox.classList.add('hidden');
					infobox.querySelector("button").classList.add('hidden');
					infobox.querySelector("button").onclick=function(){};
				}
			}
			//request still pending but some data is available
			if(xmlhttp.readyState == 3) {
				serverinfobox.innerHTML = xmlhttp.responseText;
			}
		}
		//get the string to append to the url to have anticipo fatture enabled
		var strAntFt=getAnticipoFattureParams();
		
		//xmlhttp.open("POST","./wait.php",true);
		xmlhttp.open("POST","./core/gestioneFatture.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		if(mode=='stampaInterna'){
			xmlhttp.send("do=stampaInterna"+strAntFt+"&fatture="+jsonMails);
		}else if(mode=='stampaCliente'){
			xmlhttp.send("do=stampaCliente"+strAntFt+"&fatture="+jsonMails);
		}
		else{
			xmlhttp.send("do=stampa"+strAntFt+"&fatture="+jsonMails);
		}
		xmlhttp.send("do=stampa"+strAntFt+"&fatture="+jsonMails);
		console.log("do=stampa"+strAntFt+"&fatture="+jsonMails);
		// function to update the infobox with the server reply
		function statusUpdate() {
			//console.log('Update');
			serverinfobox.innerHTML = xmlhttp.responseText;
		}
		 
		// Check for new content from the server every 0.5 seconds
		var interval = setInterval(statusUpdate, 500);
	}
	function selectAll(){
		var fatture = document.querySelectorAll("input[type=checkbox]");
		for (var i = 0; i < fatture.length; ++i) {
			fatture[i].checked = true;
		}
	}
	function deselectAll(){
		var fatture = document.querySelectorAll("input[type=checkbox]");
		for (var i = 0; i < fatture.length; ++i) {
			fatture[i].checked = false;
		}
	}
	function selectNotSent(){
		deselectAll();
		var fatture = document.querySelectorAll("input[data-pecsent]");
		for (var i = 0; i < fatture.length; ++i) {
			fatture[i].checked = true;
		}
	}
	function selectNotPrintedForReceiver(){
		deselectAll();
		var fatture = document.querySelectorAll("input[data-printedforreceiver]");
		for (var i = 0; i < fatture.length; ++i) {
			fatture[i].checked = true;
		}
	}
	function showOnlyNotSent(){
		var fatture = document.querySelectorAll("input[type=checkbox]");
		for (var i = 0; i < fatture.length; ++i) {
			var target=fatture[i].parentNode.parentNode.parentNode; //the tr
			target.classList.remove('hidden');
			//console.log(fatture[i].dataset.pecsent);
			if(fatture[i].dataset.pecsent=="false"){
				target.classList.remove('hidden');
			}else{
				target.classList.add('hidden');
			}
		}
	}
	function showOnlyClient(){
		cliente = prompt("Cliente da mostrare", "Ragione sociale");
		var fatture = document.querySelectorAll("input[type=checkbox]");
		//console.log(fatture);
		for (var i = 0; i < fatture.length; ++i) {
			var target=fatture[i].parentNode.parentNode.parentNode; //the tr
			target.classList.remove('hidden');
			console.log(JSON.parse(fatture[i].dataset.json).cliente.toLowerCase(), cliente.toLowerCase(), JSON.parse(fatture[i].dataset.json).cliente.toLowerCase().indexOf(cliente.toLowerCase()));

			if(JSON.parse(fatture[i].dataset.json).cliente.toLowerCase().indexOf(cliente.toLowerCase()) < 0){
				target.classList.add('hidden');
			}else{
				target.classList.remove('hidden');
			}
		}
	}
	function showAll(){
		var fatture = document.querySelectorAll("input[type=checkbox]");
		for (var i = 0; i < fatture.length; ++i) {
			var target=fatture[i].parentNode.parentNode.parentNode; //the tr
			target.classList.remove('hidden');
		}
	}
	function getAnticipoFattureParams(){
		var antfatture = document.querySelector("#anticipofatture");
		var enabled=antfatture.querySelector("[name=toggle]");
		var mesi=antfatture.querySelector("[name=mesi]");
		var banca=antfatture.querySelector("[name=banca]");

		var string='';
		
		if(enabled.value=="true"){
			string+='&anticipofatture='+enabled.value;
			string+='&mesi='+mesi.value;
			if(banca.value!=''){
				string+='&banca='+banca.value;
			}
		}
		console.log(string);
		return string;
	}
	
	</script>
</head>
<body>
<div class="fixedTopBar">
	<div class="dropdownMenu">
	  <span class="dropItem">Seleziona</span>
	  <div class="dropdownMenu-content">
		<a href="javascript:selectAll()">Tutte</a>
		<a href="javascript:deselectAll()">Nessuna</a>
		<a href="javascript:selectNotSent()">Non inviate</a>
		<a href="javascript:selectNotPrintedForReceiver()">Da stampare</a>
	  </div>
	</div>
	<div class="dropdownMenu">
	  <span class="dropItem">Invia PEC</span>
	  <div class="dropdownMenu-content">
		<a href="javascript:sendMails()">Invia PEC FT spuntate</a>
	  </div>
	</div>
	<div class="dropdownMenu">
	  <span class="dropItem">Stampa</span>
	  <div class="dropdownMenu-content">
		<a href="javascript:stampa('stampaInterna')">Interna</a>
		<a href="javascript:stampa('stampaCliente')">Cliente</a>
	  </div>
	</div>
	<div class="dropdownMenu">
	  <span class="dropItem">Mostra</span>
	  <div class="dropdownMenu-content">
		<a href="javascript:showAll()">Tutte</a>
		<a href="javascript:showOnlyNotSent()">Non inviate</a>
		<a href="javascript:showOnlyClient()">Filtra per cliente</a>
	  </div>
	</div>
</div>

<div id="infobox" class="hidden">
	<span id="localinfobox">No pending request</span>
	<br>
	<span id="serverinfobox"></span>
	<br>
	<button class="hidden">Hide</button>
</div>
<br><br><br>

<div id="anticipofatture" style="font-size:1em;">
	Stampa per anticipo fatture:
	<select name="toggle">
		<option value="false">no</option>
		<option value="true">yes</option>
	</select>
	Mesi:
	<input type="number" name="mesi" value="2" style="width:4em;">
	Banca:
	<select name="banca">
		<option value="">Lascia Default</option>
		<option value="01">01 - C.R.Veneto</option>
		<option value="02">02 - B.Pop.Verona</option>
		<option value="09">09 - Cerea Banca</option>
		<option value="10">10 - B.Pop.Vicenza</option>
	</select>
</div>
<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

//elenco dei codici cliente che richiedono stampa della fattura
$clientiDaStampare = array('TOMAS','TESI','MORAN','TESTO','FARET','VIOLA','PALAZ','MAHAL','AMBRN','DOROD');

//seleziono l'anno di cui mostrare le fatture
if(@$_GET['anno']){
	$anno=$_GET['anno'];
}else{
	$anno= date("Y");
}

//creo la tabella di "selezione" anno
$html='<table class="titleTable">';
$html.='<tr>';
$annoprec=$anno-1;
$html.="<td><a href=?anno=$annoprec>< $annoprec</a></td>";
$html.="<td>Fatture anno:<br>$anno</td>";
$annosuc=$anno+1;
$html.="<td><a href=?anno=$annosuc>$annosuc ></a></td>";
$html.='</tr><table class="spacedTable, borderTable">'."\n";

//ottengo la lista fatture
$test=new MyList(
	array(
		'_type'=>'Fattura',
		'data'=>array('<>','01/01/'.$anno,'31/12/'.$anno),
		//'_select'=>'numero,data,cod_cliente,tipo' //this was a try to optimize the select statement but gives no performance increase... It is even a little bit slower
		//'cod_cliente'=>'SEVEN'
	)
);

//ottengo una lista dei codici clienti 
//(mi serve per creare una cache degli oggetti cliente in modo da non dover fare poi una query per ogni singolo oggetto cliente)
$codiciCliente =array('=');
$test->iterate(function($obj){
	global $codiciCliente;
	$codiceCliente=$obj->cod_cliente->getVal();
	$codiciCliente[$codiceCliente]= $codiceCliente;
});

//ricavo dalla lista precedente gli "oggetti" cliente
$dbClienti=new MyList(
	array(
		'_type'=>'ClienteFornitore',
		'codice'=>$codiciCliente,
	)
);

//creo un nuovo array che ha per "indice" il codice cliente in modo da rendere più semplice ritrovare "l'oggetto" cliente
$dbClientiWithIndex = array();
$dbClienti->iterate(function($myCliente){
	global $dbClientiWithIndex;
	$codcliente = $myCliente->codice->getVal();
	$dbClientiWithIndex[$codcliente]= $myCliente;
});


//stampo la lista delle fatture

$test->iterate(function($obj){
	global $html;
	global $dbClientiWithIndex;
	global $clientiDaStampare;
	$dataInvioPec=$obj->__datainviopec->getVal();
	$dataStampaPerDestinatario=$obj->__datastampa->getVal();
	$dataStampaInterna=$obj->__datastampainterna->getVal();

	
	$tipo=$obj->tipo->getVal();
	$cliente = $dbClientiWithIndex[$obj->cod_cliente->getVal()];

	$jsonData='{
		"data":"'.$obj->data->getFormatted().'",
		"numero":"'.$obj->numero->getVal().'",
		"tipo":"'.$tipo.'",
		"cliente":"'.str_replace("'"," ",$cliente->ragionesociale->getVal()).'"
		}';
		//in cliente replace ' with a space since is causing troubles in json parsing
		
//		"cliente":"'.$cliente->ragionesociale->getVal().'"
//		"cliente":'.json_encode($cliente->ragionesociale->getVal()).'

	$html.= "<tr class='$tipo'>";
	
	if($dataInvioPec){
		$pecSent=''; //'data-pecsent="true"';
		$checked='';
	}else{
		$pecSent='data-pecsent="false"';
		$checked='checked';
	}
	if($dataStampaPerDestinatario){
		$printedForReceiver=''; //'data-printedforreceiver="true"';
	}else{
		if (in_array($obj->cod_cliente->getVal(), $clientiDaStampare)){
			$printedForReceiver='data-printedforreceiver="false"';
		}else{
			$printedForReceiver=''; //'data-printedforreceiver="true"';
		}
	}
	
	$html.= '<td><label class="container"><input type="checkbox" data-json=\''.$jsonData.'\' '.$pecSent.' '.$printedForReceiver.' '.$checked.'><span class="checkmark"></span></label></td>';
	$html.= '<td>'/*.$obj->tipo->getVal().*/.$obj->cod_pagamento->getVal().' **** '.$obj->cod_pagamento->extend()->descrizione->getVal().'</td>';
	$html.= '<td>'.$obj->numero->getVal().'</td>';
	$html.= '<td>'.$obj->data->getFormatted().'</td>';
	//$html.= '<td>'.$obj->importo->getFormatted().'</td>';
	$html.= '<td><small>('.$cliente->codice->getVal().')</small> '.$cliente->ragionesociale->getVal().'</td>';
	$html.= '<td><small>'.$cliente->__pec->getVal().'</small></td>';
	
	$link= '<a target="_blank" href="./core/gestioneFatture.php?';
	$link.= 'numero='.$obj->numero->getVal();
	$link.= '&data='.$obj->data->getVal();
	$link.= '&tipo='.$tipo;
	
	//mail
	//if($cliente->__pec->getVal()!=''){

		if($dataInvioPec){
			$html.= '<td style="background-color:#66ff00;">Inviata il '.$dataInvioPec.$link.'&do=inviaPec"><br>Reinvia?</a></td>';
		}else{
			if($cliente->__pec->getVal()!=''){
				$html.= '<td>'.$link.'&do=inviaPec">Invia Mail</a></td>';
			}else{
				$html.= '<td>Impossibile inviare la PEC: Manca l\'indirizzo!</td>';
			}
		}
	//}else{

		if($dataStampaPerDestinatario){
			@$html.= '<td style="background-color:#66ff00;">Stampata il '.$dataStampaPerDestinatario.$link.'&do=stampaCliente"><br>Ristampa?</a></td>';
		}else{
			$html.= '<td>'.$link.'&do=stampaCliente">Stampa copia cliente</a></td>';
		}
		if($dataStampaInterna){
			@$html.= '<td style="background-color:#66ff00;">Stampata il '.$dataStampaInterna.$link.'&do=stampaInterna"><br>Ristampa?</a></td>';
		}else{
			$html.= '<td>'.$link.'&do=stampaInterna">Stampa copia interna</a></td>';
		}


//}
$temp = '<br>SDI:'.$cliente->__SDIcodice->getVal();
$temp.='<br>PEC:'.$cliente->__SDIpec->getVal();
$temp.='<br>RAGSOC:'.$cliente->__ragionesociale->getVal();
	//visulizza
	$html.= '<td>'.$link.'&do=visualizza">Visualizza</a></td>';
	if($cliente->__SDIcodice->getVal()!='' || $cliente->__SDIpec->getVal()!=''){
		$html.= '<td style="background-color:#66ff00;">'.$link.'&do=generaXml">Genera XML'.$temp.'</a><br>';	
		$html.= $link.'&do=inviaSDI">Invio al SDI</a></td>';	

	}else{
		$html.= '<td style="background-color:red">'.$link.'&do=generaXml">Anagrafica incompleta. Mancano codice SDI o PEC</a><br>';
		$html.= $link.'&do=inviaSDI">Invio al SDI</a></td>';	
	}
	$html.="</tr>\n";
//	$html.= '<td><a href=""><img src="./img/printer.svg" alt="Stampa" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/pdf.svg" alt="Visualizza PDF" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/email.svg" alt="Invia PEC" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/ok.svg" alt="Stato: OK" width="30px"></a></td>';
});

$html.='</table>';
echo $html;
?>

</body>
</html>
