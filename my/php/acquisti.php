<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Elenca fatture</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	
	<script>
const copyToClipboard = str => {
  const el = document.createElement('textarea');  // Create a <textarea> element
  el.value = str;                                 // Set its value to the string that you want copied
  el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
  el.style.position = 'absolute';                 
  el.style.left = '-9999px';                      // Move outside the screen to make it invisible
  document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
  const selected =            
    document.getSelection().rangeCount > 0        // Check if there is any content selected previously
      ? document.getSelection().getRangeAt(0)     // Store selection if found
      : false;                                    // Mark as false to know no selection existed before
  el.select();                                    // Select the <textarea> content
  document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
  document.body.removeChild(el);                  // Remove the <textarea> element
  if (selected) {                                 // If a selection existed before copying
    document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
    document.getSelection().addRange(selected);   // Restore the original selection
  }
};
	</script>
</head>
<body>
<?php

?>

<script>
function searchFormCancel(){
	console.log('cancel form');
	document.querySelector("input[name='searchString'").value="";
}

function searchFormSubmit(){
	//fix the url
	console.log('test1')
	newurl = window.location.href;
	console.log('test2')

	newurl = newurl.replace('&searchString=<?php if(isset($_GET['searchString'])){echo $_GET['searchString'];}?>','')
	console.log('test3')
	/*
	if(!newurl.search('\?')){
		newurl+='?dummy=0'
	}
	*/
	console.log('test4')
	newurl+="&searchString="+document.querySelector("input[name='searchString'").value;
	console.log('test5')
	window.location.href= newurl;
	console.log('test6')
}
</script>

<input type="text" name="searchString"
placeholder="cerca e filtra"
style="
width: 80%;
border: 1px solid darkblue;
border-radius: 0.3em;
font-size: 2em;
color: darkblue;
padding: 0.3em;
margin: 0.5em;"
value="<?php if(isset($_GET['searchString'])){echo $_GET['searchString'];}?>"
>

<button 
onclick="searchFormCancel()"
style="font-size:2em;
width:2em;
height:2em;
color:white;
border: 1px solid darkred;
border-radius: 0.3em;
font-size: 2em;
background-color: darkred;
padding: 0.3em;
margin: 0.1em;">
&#x1F5D1; </button>


<button 
onclick="searchFormSubmit()"
style="font-size:2em;
width:2em;
height:2em;
color:white;
border: 1px solid darkgreen;
border-radius: 0.3em;
font-size: 2em;
background-color: darkgreen;
padding: 0.3em;
margin: 0.1em;">
&#x1f50d;
</button>
<script>
document.querySelector("input[name='searchString'").select();
document.querySelector("input[name='searchString'").addEventListener("keyup", function(event) {
  // Number 13 is the "Enter" key on the keyboard
  if (event.keyCode === 13) {
	searchFormSubmit();
}});
</script>
<?php
include ('./core/config.inc.php');

//acquisti
$tipo = './dati/fattureElettronicheAcquisto/';

if( isset($_GET['anno']) && isset($_GET['mese'])){
	//se ho impostato anno e mese
	$anno = $_GET['anno'];
	$mese = $_GET['mese'];
	//
	$dir= $tipo.$anno.'/'.$mese.'/';
	
}else if ( isset($_GET['anno']) && !isset($_GET['mese'])){
	// ho impostato solo l'anno
	$anno = $_GET['anno'];

}else{
	// in tutti gli altri casi
	// mostro il mese e anno correnti
	$anno = date('Y');
	$mese = date('m');
}


//vendite
if(isset($_GET['mode'])){
	if($_GET['mode']=='vendite'){
		$dir= './core/stampe/ftXml/';
	}
}

//selezione anno
$html='<table class="titleTable">';
$html.='<tr>';
$annoprec=$anno-1;
$html.="<td><a href=?anno=$annoprec&mese=$mese>< $annoprec</a></td>";
$html.="<td>Fatture acquisto:<br>$anno</td>";
$annosuc=$anno+1;
$html.="<td><a href=?anno=$annosuc&mese=$mese>$annosuc ></a></td>";
$html.='</tr><table class="spacedTable, borderTable">'."\n";
echo $html;

//selezione mese
$html='<table class="titleTable">';
$html.='<tr>';
$meseprec=str_pad(($mese-1).'',2,'0',STR_PAD_LEFT);
$html.="<td><a href=?anno=$anno&mese=$meseprec>< $meseprec</a></td>";
$html.="<td>$mese</td>";
$mesesuc=str_pad(($mese+1).'',2,'0',STR_PAD_LEFT);
$html.="<td><a href=?anno=$anno&mese=$mesesuc>$mesesuc ></a></td>";
$html.='</tr><table class="spacedTable, borderTable">'."\n";
echo $html;
echo $dir."<br>";



//tabella vera e propria
if( isset($anno) && isset($mese)){
	//mostro il mese richiesto
	$dir= $tipo.$anno.'/'.$mese.'/';
	mostraMese($dir);
}elseif(isset($anno) && !isset($mese)){
	//mostro l'anno richiesto
	for ($i = 1; $i <= 11; $i++) {
		if(strlen($i) < 2 ){
			$mese = '0'.$i;	
		}else{
			$mese = $i;				
		}
		$dir= $tipo.$anno.'/'.$mese.'/';
		mostraMese($dir);
	}		
}



// ##############################################
//  seguono funzioni
// ##############################################
function cercaFilesNellaDirectory($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess','_MT_001.xml','_MT_002.xml','zip','.cache');
	$ignoredFiles = '_MT_001.xml';

    $files = array();    
    foreach (scandir($dir) as $file) {
		if (strpos($file,'_MT_001.xml') || strpos($file,'_MT_002.xml') || strpos($file,'zip')) continue; //ignora i file ricevuta e i file zip
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
	$files=array_reverse($files,true);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

function mostraMese($dir){
	$files = cercaFilesNellaDirectory($dir);

	foreach ($files as $key => $file) {
		
		$givePrintedClass = '';
		if(xmlPrintedStatus($dir.$file)){
			$givePrintedClass = ' class="printed" '; 
		}else{
			$givePrintedClass = ' class="notprinted" '; 
		}

		
		$fileDate = date('d-m-Y h:i:s',filemtime($dir.$file));
		$fileUrl = " <a $givePrintedClass href='./visualizzaFattureXml.php?fileUrl=$dir$file' target='_blank'>$file</a><br>\n";

		$xmlDomDocument = leggiFatturaXml($dir.$file);
		//se non sono riuscito a leggere la fattura passo al documento successivo
		if(!$xmlDomDocument){
			echo '<tr><td>Non sono riuscito a leggere il documento.'.$dir.$file.'</td></tr>';
			continue;
		}
		
		
		$xmlDomDocumentXpath = new DOMXpath($xmlDomDocument);
		
	@	$filename = 	$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/Allegati/NomeAttachment')->item(0)->nodeValue;
	@	$fileblob64 = 	$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/Allegati/Attachment')->item(0)->nodeValue;
		if($filename){
			$linkAllegato = "<a href='data:application/octet-stream;base64,".$fileblob64."' download='$filename' title='$filename'>All.to</a>";
		}else{
			$linkAllegato = '';
		}
		
		//echo $xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody')->item(0)->textContent;
		$trovataStringa = false;
		if(stripos($xmlDomDocument->saveXML(),'CL824RV') || stripos($xmlDomDocument->saveXML(),'CL 824 RV')
			|| stripos($xmlDomDocument->saveXML(),'FY330NX') || stripos($xmlDomDocument->saveXML(),'FY 330 NX')){
			$trovataStringa = true;
		}
		
		//stampa solo se trovo questra stringa
//		if(!stripos($xmlDomDocument->saveXML(),'FACCINI')){

		if(isset($_GET['searchString'])){
			if(!stripos($xmlDomDocument->saveXML(),$_GET['searchString'])){
				continue;
			}
		}

		
		?>

		<tr>
			<td style="font-size:0.6em;"><?php echo $fileDate; ?></td>
			<td><?php echo $fileUrl; ?></td>
			<td>
				<?php 
					echo $linkAllegato;
					if($trovataStringa){
						echo 'CAMION!!';
					}
				?>
			
			</td>
			<td>
				<?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione')->item(0)->nodeValue; ?>
				<?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome')->item(0)->nodeValue; ?>
				<?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Cognome')->item(0)->nodeValue; ?>
			</td>
			<td style="text-align:left"><?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero')->item(0)->nodeValue; ?></td>
			<td style="text-align:right"><?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Data')->item(0)->nodeValue; ?></td>
			<!--
			<td style="text-align:right"><?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiPagamento/DettaglioPagamento/ImportoPagamento')->item(0)->nodeValue; ?></td>
			-->
			<td style="text-align:right"><?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/ImportoTotaleDocumento')->item(0)->nodeValue; ?></td>
			
			<td style="text-align:right">
				<?php
					if($xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiPagamento/DettaglioPagamento/IBAN')->item(0)->nodeValue){
						$iban = $xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiPagamento/DettaglioPagamento/IBAN')->item(0)->nodeValue;
						echo '<a href="javascript:return false;" onclick="copyToClipboard(this.title)" title="'.$iban.'">IBAN</a>';
					}
				?>
			</td>
		</tr>
		<?php
	}
}
?>
</table>
<br>Fine<br>
<script>
	//scroll to the end of the page
	window.scrollTo(0,document.body.scrollHeight);
</script>
</body>
</html>
