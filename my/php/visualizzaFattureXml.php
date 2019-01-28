<?php
error_reporting(-1); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo

if($_GET['fileUrl']==''){
	echo '
	
	
<form action="?" method="post" enctype="multipart/form-data">
<input type="file" name="fileToUpload" id="fileToUpload">
<input type="submit" value="Upload Image" name="submit">
</form>	
	
		<form>
		<label for="avatar">Select an xml file to display:</label>

		<input type="file"
			   id="avatar" name="fileUrl"
			   accept=".xml, .xml.p7m">
			   
		<input type="submit">
		</form> ';

	
}else{
	$urlFileFattura ='';
	$fileTemporaneo ='';

	$openSSLDir='C:/Program Files (x86)/EasyPHP-5.3.9/www/webcontab/my/php/libs/openssl-1.0.2q-i386-win32/';
	$openSSLDirExecutableUrl = $openSSLDir."openssl.exe";
	//C:\Program Files (x86)\EasyPHP-5.3.9\www\webcontab\my\php\libs\openssl-1.0.2q-i386-win32>openssl.exe  smime -decrypt -verify -inform DER -in "test.xml.p7m" -noverify -out "test.xml"
	$urlFileFattura=$openSSLDir.'test.xml.p7m';
	$fileTemporaneo =$openSSLDir.'test.xml';

	//echo '"'.$openSSLDirExecutableUrl.'" smime -verify -noverify -in "'.$urlFileFattura.'" -inform DER -out "'.$fileTemporaneo.'"';

	exec('"'.$openSSLDirExecutableUrl.'" smime -verify -noverify -in "'.$urlFileFattura.'" -inform DER -out "'.$fileTemporaneo.'"');




	if (file_exists($fileTemporaneo)) {
		$xml = simplexml_load_file($fileTemporaneo);
	 
		//print_r($xml);
	} else {
		exit('Failed to open test.xml.');
	}




	//add the stylesheet tag
	$node   = dom_import_simplexml($xml);
	$pi     = $node->ownerDocument->createProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="http://127.0.0.1/webcontab/my/php/fatturaordinaria_v1.2.1.xsl"');
	$firstSibling = $node->parentNode->firstChild;  
	$result = $node->parentNode->insertBefore( $pi, $firstSibling );


	//create  a new document to add the formatting
	$xmlDocument = new DOMDocument('1.0');
	$xmlDocument->preserveWhiteSpace = false;
	$xmlDocument->formatOutput = true;
	//import our old document
	$xmlDocument->loadXML($xml->asXML());

	//send it to the browser
	header("Content-disposition: inline; filename=".$urlFileFattura);
	header('Content-type: text/xml');
	echo $xmlDocument->saveXML();
}
?>