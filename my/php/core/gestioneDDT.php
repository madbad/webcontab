<?php
include ('./config.inc.php');

//mi preparo i parametri di ricerca della fattura
$params=array(
	'numero' => $_GET["numero"],
	'data'   => $_GET["data"],
	'cod_causale'  => $_GET["cod_causale"]
);
//genero il mio oggetto ddt
$myDdt= new Ddt($params);

//eseguo l'azione richiesta
switch ($_GET["do"]){
	case 'inviaPec':
		$myDdt->inviaPec();
		break;
	case 'visualizza':
		$myDdt->visualizzaPdf();
		break;
	case 'stampaCliente':
		$myDdt->stampa();
		//memorizzo la data di stampa
		$myDdt->__datastampa->setVal(date("d/m/Y"));
		$myDdt->saveSqlDbData();
		break;
	case 'mail':
		$myDdt->inviaMail();
		break;
	case 'stampa':
		//needed to prevent a bug flushing output to the broser
		header( 'Content-type: text/html; charset=utf-8' );

		$myDdt->generaPdf();
		$myDdt->getPdfFileUrl();
		if(true){
			$acroreaderexe = '"C:\Programmi\Adobe\Reader 11.0\Reader\AcroRd32.exe"';
			//$acroreaderexe = '"C:\Programmi\Foxit Software\Foxit Reader\FoxitReader.exe"';
			$filename = '"'.$myDdt->getPdfFileUrl().'"';
			$printername = '"HP LaserJet M1530 MFP Series PCL 6"';
			$drivername = '"Hp LaseJet M1530 MFP Series PCL 6"';
			//$portname = '"IP_192.168.10.110"';
			$portname = '"HPLaserJetM1536dnfMFP_copy_1"';
			// acroreader.exe /t <filename> <printername> <drivername> <portname>
			//"C:\Program Files (x86)\Adobe\Reader 11.0\Reader\AcroRd32.exe" /h /t "C:\Folder\File.pdf" "Brother MFC-7820N USB Printer" "Brother MFC-7820N USB Printer" "IP_192.168.10.110"
			$printCommand = $acroreaderexe.' /T '.$filename.' '.$printername.' '.$drivername.' '.$portname;
			echo '<br>'.$printCommand;
		
		}else{
			$sumatrapdfexe = 'C:\Programmi\SumatraPDF\SumatraPDF.exe';
			$filename = '"'.$myDdt->getPdfFileUrl().'"';
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
		}

		//flush the output to the browser
		flush();
		ob_flush();
		exec($printCommand);
		break;
}
?>