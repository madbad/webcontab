<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Elenca fatture</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<?php
include ('./core/config.inc.php');

//acquisti
$tipo = './dati/fattureElettronicheAcquisto/';

if( isset($_GET['anno']) && isset($_GET['mese'])){
	$anno = $_GET['anno'];
	$mese = $_GET['mese'];
}else{
	$anno = date('Y');
	$mese = date('m');
}




$dir= $tipo.$anno.'/'.$mese.'/';
if(isset($_GET['mode'])){
	if($_GET['mode']=='vendite'){
		$dir= './core/stampe/ftXml/';
	}
}
//$dir= './core/stampe/ftXml/';
//$dir= './dati/fattureElettronicheAcquisto/2019/02/Ricevute Consegna/';

/*
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) 
    { 
		if(strpos($file,'_MT_001.xml')){
		 echo '';
		}else{
			if ($file != "." && $file != "..") {
				
				$file_parts = pathinfo($file);

				switch($file_parts['extension'])
				{
					case "xml":
					//echo 'xml';
					break;

					case "p7m":
					//echo 'xml.p7m';
					break;

					case "": // Handle file extension for files ending in '.'
					case NULL: // Handle no file extension
					break;
				}
			
			echo date('Y-m-d h:i:s',filemtime($dir.$file));
			echo " <a href='./visualizzaFattureXml.php?fileUrl=$dir$file' target='_blank'>$file</a><br>\n"; 
				//echo filemtime($dir.$file);




				} 
		}
	}
    closedir($handle); 
}
*/

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





function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess','_MT_001.xml','_MT_002.xml');
	$ignoredFiles = '_MT_001.xml';

    $files = array();    
    foreach (scandir($dir) as $file) {
		if (strpos($file,'_MT_001.xml') || strpos($file,'_MT_002.xml')) continue; //ignora i file ricevuta
        if (in_array($file, $ignored)) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
	$files=array_reverse($files,true);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

$files = scan_dir($dir);

foreach ($files as $key => $file) {
	$fileDate = date('d-m-Y h:i:s',filemtime($dir.$file));
	$fileUrl = " <a href='./visualizzaFattureXml.php?fileUrl=$dir$file' target='_blank'>$file</a><br>\n";

	$xmlDomDocument = leggiFatturaXml($dir.$file);
	$xmlDomDocumentXpath = new DOMXpath($xmlDomDocument);
	
@	$filename = 	$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/Allegati/NomeAttachment')->item(0)->nodeValue;
@	$fileblob64 = 	$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/Allegati/Attachment')->item(0)->nodeValue;

	$linkAllegato = "<a href='data:application/octet-stream;base64,".$fileblob64."' download='$filename' >$filename</a>";

	?>
	
	<tr>
		<td><?php echo $fileDate; ?></td>
		<td><?php echo $fileUrl; ?></td>
		<td> <?php echo $linkAllegato; ?></td>
		<td>
			<?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione')->item(0)->nodeValue; ?>
			<?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome')->item(0)->nodeValue; ?>
			<?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Cognome')->item(0)->nodeValue; ?>		
		</td>
		<td style="text-align:right"><?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiPagamento/DettaglioPagamento/ImportoPagamento')->item(0)->nodeValue; ?></td>
		<td style="text-align:right"><?php echo @$xmlDomDocumentXpath->evaluate('//FatturaElettronicaBody/DatiPagamento/DettaglioPagamento/IBAN')->item(0)->nodeValue; ?></td>
	<tr>
	<?php
}



?>

</body>
</html>