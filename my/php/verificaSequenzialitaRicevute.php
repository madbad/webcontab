<?php
error_reporting(-1); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo

$dir    = './core/stampe/RICEVUTE';
$files = scandir($dir);

//print_r($files);

$counter = 0; 

foreach ($files as $file){
	$counter++;
	$file = str_replace('IT01588530236_','',$file);
	$file = str_replace('_RC_003.xml','',$file);
	$file = $file * 1;
	if ($file != $counter){
		if (in_array($counter, array('26','129','130','131','132','133','134','135','136','137','138','139','140','141','142','143','144','145','146','147','148','152'))){
			echo "Ok ".$counter." was not meant to be here<br>\n";
			$counter =$file;					
			echo $file." is fine.\n<br>";
		}else{
			echo "We are doing ".$file." instead of ".$counter."<br>\n";
			$counter =$file;				
		}
	}else{
		echo $file." is fine.\n<br>";
	}
}
/*
if($_GET['fileUrl']!=''){
	$urlFileFattura =$_GET['fileUrl'];
	$fileTemporaneo ='';

	
	$file_parts = pathinfo($_GET['fileUrl']);
	switch($file_parts['extension'])
	{
		case "xml":
			$fileTemporaneo = $_GET['fileUrl'];
			//echo 'xml';
		break;
		
		case "XML":
			$fileTemporaneo = $_GET['fileUrl'];
			//echo 'xml';
		break;
		
		case "p7m":
			//echo 'xml.p7m';
			$openSSLDir='C:/Program Files (x86)/EasyPHP-5.3.9/www/webcontab/my/php/libs/openssl-1.0.2q-i386-win32/';
			//$openSSLDir='C:/Programmi/EasyPHP-5.3.9/www/webcontab/my/php/libs/openssl-1.0.2q-i386-win32/';
			$openSSLDirExecutableUrl = $openSSLDir."openssl.exe";
			//C:\Program Files (x86)\EasyPHP-5.3.9\www\webcontab\my\php\libs\openssl-1.0.2q-i386-win32>openssl.exe  smime -decrypt -verify -inform DER -in "test.xml.p7m" -noverify -out "test.xml"
			//$urlFileFattura=$openSSLDir.'test.xml.p7m';
			//$urlFileFattura=$openSSLDir.$_GET['fileUrl'];
			$urlFileFattura=$_GET['fileUrl'];
			$fileTemporaneo =$openSSLDir.'test.xml';
			//echo '"'.$openSSLDirExecutableUrl.'" smime -verify -noverify -in "'.$urlFileFattura.'" -inform DER -out "'.$fileTemporaneo.'"';
			exec('"'.$openSSLDirExecutableUrl.'" smime -verify -noverify -in "'.$urlFileFattura.'" -inform DER -out "'.$fileTemporaneo.'"');
		break;

		case "": // Handle file extension for files ending in '.'
		case NULL: // Handle no file extension
		break;
	}

	
	if (file_exists($fileTemporaneo)) {
		$xml = simplexml_load_file($fileTemporaneo);
	 
		//print_r($xml);
	} else {
		exit('Failed to open test.xml.'.$fileTemporaneo);
	}

	//add the stylesheet tag
	$node   = dom_import_simplexml($xml);
	//$pi     = $node->ownerDocument->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="http://127.0.0.1/webcontab/my/php/fatturaordinaria_v1.2.1.xsl"');
	$pi     = $node->ownerDocument->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="http://localhost/webcontab/my/php/fatturaordinaria_v1.2.1.xsl"');

	$firstSibling = $node->parentNode->firstChild;  
	$result = $node->parentNode->insertBefore( $pi, $firstSibling );


	//create  a new document to add the formatting
	$xmlDocument = new DOMDocument('1.0');
	$xmlDocument->preserveWhiteSpace = false;
	$xmlDocument->formatOutput = true;
	//import our old document
	$xmlDocument->loadXML($xml->asXML());

	//send it to the browser
	//header("Content-disposition: inline; filename=".$urlFileFattura);
	//header('Content-type: text/xml');
	//echo $xmlDocument->saveXML();
	
	//print_r($xml);
	//echo "test\n";
	$test=$xml->xpath('//FatturaElettronicaBody/Allegati');
	//print_r($test);
	
	
	//	text/plain
	//	text/html
		//text/javascript
	//	text/css
	//	image/jpeg
	//	image/png
	//	audio/mpeg
	//	audio/ogg
	//	video/mp4

	//	application/json
	//	application/ecmascript
	//	application/octet-stream	
	//	
	//	application/pdf
	//	application/zip
	
	
	
	
	
	$filename = 	$xml->xpath('//FatturaElettronicaBody/Allegati/NomeAttachment');
	$fileblob64 = 	$xml->xpath('//FatturaElettronicaBody/Allegati/Attachment');

	$filename = 	$filename[0];
	$fileblob64 = 	$fileblob64[0];

	$filetype = '';
	echo "<a href='data:application/octet-stream;base64,".$fileblob64."' download='$filename' >$filename</a>";
	
	
	
}
* */
?>
