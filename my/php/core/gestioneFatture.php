<?php
include ('./config.inc.php');

function execInBackground($cmd) {
    if (substr(php_uname(), 0, 7) == "Windows"){
        //pclose(popen("start /B ". $cmd, "r"));
        //popen("start /B ". $cmd, "r"); 
		$shell = new COM("WScript.Shell");
		$shell->run($command, 0, false);
    }
    else {
        exec($cmd . " > /dev/null &");  
    }
} 
if (@$_GET["do"]){
	//mi preparo i parametri di ricerca della fattura
	$params=array(
		'numero' => $_GET["numero"],
		'data'   => $_GET["data"],
		'tipo'  => $_GET["tipo"]
	);
	//genero il mio oggetto fattura
	$myFt= new Fattura($params);


	//eseguo l'azione richiesta
	switch ($_GET["do"]){
		case 'inviaPec':
			$myFt->inviaPec();
			break;
		case 'visualizza':
			$myFt->visualizzaPdf();
			break;
		case 'stampaCliente':
			$myFt->stampa();
			//memorizzo la data di stampa
			$myFt->__datastampa->setVal(date("d/m/Y"));
			$myFt->saveSqlDbData();
			break;	
	}
}
if (@$_POST["do"]){
	//eseguo l'azione richiesta
	switch ($_POST["do"]){
		case 'inviaPec':
			//needed to prevent a bug flushing output to the broser
			header( 'Content-type: text/html; charset=utf-8' );
			//
			$fatture = json_decode($_POST["fatture"]);
			//echo $_POST["fatture"];
			//mi preparo i parametri di ricerca della fattura
			$count=0;
			foreach ($fatture as $fattura){
				$count++;
				$params=array(
					'numero' => $fattura->numero,
					'data'   => $fattura->data,
					'tipo'  => $fattura->tipo
				);
				//genero il mio oggetto fattura
				$myFt= new Fattura($params);
				//$myFt->generaPdf();
				
				if($myFt->inviaPec()){
					echo "\n<br>$count) (OK) - ";
					echo $fattura->tipo;
					echo ' '.$fattura->numero;
					echo ' del '.$fattura->data;
				}else{
					echo "\n<br>$count) (ERROR) - ";
					echo $fattura->tipo;
					echo ' '.$fattura->numero;
					echo ' del '.$fattura->data;
				};
				
				//flush the output to the browser
				flush();
				ob_flush();	
			}
			break;
		case 'stampa':
			if(false){//stampa usando adobe reader
				exec('start /b "C:\Programmi\Adobe\Reader 11.0\Reader\AcroRd32.exe" "C:\Programmi\EasyPHP-5.3.9\www\webcontab\my\php\core\stampe\myfile.pdf"');
				//needed to prevent a bug flushing output to the broser
				header( 'Content-type: text/html; charset=utf-8' );
				echo $_POST["fatture"];
				$fatture = json_decode($_POST["fatture"]);
				print_r($fatture);
				//mi preparo i parametri di ricerca della fattura
				$count=0;
				foreach ($fatture as $fattura){
					$count++;
					$params=array(
						'numero' => $fattura->numero,
						'data'   => $fattura->data,
						'tipo'  => $fattura->tipo
					);
					//genero il mio oggetto fattura
					$myFt= new Fattura($params);
					$myFt->generaPdf();
					$myFt->getPdfFileUrl();
					
					//linux only
					//exec("lp file.pdf");
					
					//windows only
					$acroreaderexe = '"C:\Programmi\Adobe\Reader 11.0\Reader\AcroRd32.exe"';
					//$acroreaderexe = '"C:\Programmi\Foxit Software\Foxit Reader\FoxitReader.exe"';
					$filename = '"'.$myFt->getPdfFileUrl().'"';
					$printername = '"HP LaserJet M1530 MFP Series PCL 6"';
					$drivername = '"Hp LaseJet M1530 MFP Series PCL 6"';
					//$portname = '"IP_192.168.10.110"';
					$portname = '"HPLaserJetM1536dnfMFP_copy_1"';
					// acroreader.exe /t <filename> <printername> <drivername> <portname>
					//"C:\Program Files (x86)\Adobe\Reader 11.0\Reader\AcroRd32.exe" /h /t "C:\Folder\File.pdf" "Brother MFC-7820N USB Printer" "Brother MFC-7820N USB Printer" "IP_192.168.10.110"
					$printCommand = $acroreaderexe.' /T '.$filename.' '.$printername.' '.$drivername.' '.$portname;
					echo '<br>'.$printCommand;
					//flush the output to the browser
					flush();
					ob_flush();
					exec($printCommand);
				}
			}else{//stampa usando sumatraPdf
				//needed to prevent a bug flushing output to the broser
				header( 'Content-type: text/html; charset=utf-8' );
				echo $_POST["fatture"];
				$fatture = json_decode($_POST["fatture"]);
				print_r($fatture);
				//mi preparo i parametri di ricerca della fattura
				$count=0;
				foreach ($fatture as $fattura){
					$count++;
					$params=array(
						'numero' => $fattura->numero,
						'data'   => $fattura->data,
						'tipo'  => $fattura->tipo
					);
					//genero il mio oggetto fattura
					$myFt= new Fattura($params);
					$myFt->generaPdf();
					$myFt->getPdfFileUrl();
					
					$sumatrapdfexe = 'C:\Programmi\SumatraPDF\SumatraPDF.exe';
					$filename = '"'.$myFt->getPdfFileUrl().'"';
					$printername = '"HP LaserJet M1530 MFP Series PCL 6"';
					//$printername = '"\\\\SERVER\PDFCreator"';
					//$drivername = '"Hp LaseJet M1530 MFP Series PCL 6"';
					//$portname = '"IP_192.168.10.110"';
					//$portname = '"HPLaserJetM1536dnfMFP_copy_1"';
					// acroreader.exe /t <filename> <printername> <drivername> <portname>
					//"C:\Program Files (x86)\Adobe\Reader 11.0\Reader\AcroRd32.exe" /h /t "C:\Folder\File.pdf" "Brother MFC-7820N USB Printer" "Brother MFC-7820N USB Printer" "IP_192.168.10.110"
					//for command line options see: https://github.com/sumatrapdfreader/sumatrapdf/wiki/Command-line-arguments
					$printCommand = $sumatrapdfexe.' -print-to '.$printername.' -print-settings "1x,fit" -silent -exit-when-done '.$filename;
					echo '<br>'.$printCommand;
					//flush the output to the browser
					flush();
					ob_flush();
					exec($printCommand);
				}
			}
		break;
	}
}
?>