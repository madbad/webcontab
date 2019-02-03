<?php

$dir= './dati/fattureElettronicheAcquisto/2019/02/';
$dir= './core/stampe/ftXml/';


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
?>