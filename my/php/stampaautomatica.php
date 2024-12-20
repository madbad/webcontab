<?php
$enableDirectAccess=true;
include ('./core/config.inc.php');

$myfileUrl = './dati/ultimoddtstampato.txt';
//leggo il numero dell'ultimo ddt dal file
$myfile = fopen($myfileUrl, "r") or die("Unable to open file!");
$ultimoDdtNumero = fread($myfile,filesize($myfileUrl));
$ultimoDdtNumero = $ultimoDdtNumero *1;
fclose($myfile);

#$ultimoDdtNumero = "102";
$data = date('d/m/y');
//print_r($data);

//mostro le ddt 
$listaDDT=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('<>',$data, $data),
		'numero'=>array('>',$ultimoDdtNumero),
		//'data'=>array('<>','01/07/17', '31/07/17'),
		//'cod_destinatario'=>array('!=','SGUJI','BELFR','AMATO','CALIM','DANFR','FTESI','GARLE','LAME2','MANTG','ESPOS','AZGI','BENIE','BISSM','BOLLA','BONER','CASAR','CHIE3','CORTE','DICAM','DOROM','FABBR','FACCG','FARED','FARET','FORMA','GAZZO','GIAC1','GIMMI2','GREE5','LEOPA','LORAL','MACER','MAEST','MARTI','MORAN','MUNAR','NOVUS','STEMI','ORTO3','PRIMF','SBIFL','SBIZZ','TARCI','TESI','TIATI','ZAPPO','SEVEN','SMA','ULISS','NIZZ2'),
		//'cod_causale' => 'D'
	)
);
#print_r($listaDDT);

$listaDDT->iterate(function($myDdt){
		global $myfileUrl;
		$myDdt = new Ddt(
		array(
			'numero'=>$myDdt->numero->getVal(),
			'data'=>$myDdt->data->getVal()
		));
		
		$myDdt->getRighe();
		$myDdt->generaPdf();
		$myDdt->getPdfFileUrl();
		

		$numeroCopie = 0;
		
		if($myDdt->cod_mezzo->getVal() == '02' || $myDdt->cod_mezzo->getVal() == '03'){//se e a mezzo destinatario o mtttente stampo solo 2 copie
			$numeroCopie = 2;
		}else{
			$numeroCopie = 3;
		}
		//FORZA LA STAMPA DI UNA SOLA COPIA
		//$numeroCopie = 1;

		$sumatrapdfexe = 'C:\Programmi\SumatraPDF\SumatraPDF.exe';
		$filename = '"'.$myDdt->getPdfFileUrl().'"';
		//$printername = '"HP LaserJet M1530 MFP Series PCL 6"';
		$printername = '"HPNUOVA"';
		//$printername = '"\\\\SERVER\PDFCreator"';
		//$drivername = '"Hp LaseJet M1530 MFP Series PCL 6"';
		//$portname = '"IP_192.168.10.110"';
		//$portname = '"HPLaserJetM1536dnfMFP_copy_1"';
		// acroreader.exe /t <filename> <printername> <drivername> <portname>
		//"C:\Program Files (x86)\Adobe\Reader 11.0\Reader\AcroRd32.exe" /h /t "C:\Folder\File.pdf" "Brother MFC-7820N USB Printer" "Brother MFC-7820N USB Printer" "IP_192.168.10.110"
		//for command line options see: https://github.com/sumatrapdfreader/sumatrapdf/wiki/Command-line-arguments
		$printCommand = $sumatrapdfexe.' -print-to '.$printername.' -print-settings "'.$numeroCopie.'x,fit" -silent -exit-when-done '.$filename;
		echo '<br>'.$printCommand;
		exec($printCommand);
		
		//mi ricordo di averlo stampato
		$myfile = fopen($myfileUrl, "w") or die("Unable to open file!");
		fwrite($myfile, $myDdt->numero->getVal());
		fclose($myfile);
		echo "Stampato ddt ".$myDdt->numero->getVal().' del '.$myDdt->data->getVal().' a mezzo '.$myDdt->cod_mezzo->getVal();
});

?>
