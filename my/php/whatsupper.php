<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

//seleziono l'anno di cui mostrare i ddt
if(@$_GET['anno']){
	$anno=$_GET['anno'];
	$Sgiorno = '01';
	$Smese =   '01';
	$Egiorno = '31';
	$Emese =   '12';
	$Sdate=$Sgiorno.'/'.$Smese.'/'.$anno;
	$Edate=$Egiorno.'/'.$Emese.'/'.$anno;
}else{
	if($_GET['date']){
		//echo $_GET['date'];
		$temp=$_GET['date'];
		$anno=explode("/",$temp);
		$anno=$anno[2];
		$Sdate = $Edate = $_GET['date'];
		//echo $anno;
		//echo $Sdate;
	}else{
		$anno=date('Y');
		$Sdate = $Edate = date('d/m/Y');
	}
	$miadata= explode("/",$Sdate);
	if(strlen($miadata[2])==2){$miadata[2]='20'.$miadata[2];}
	$miadatastr =$miadata[0].'-'.$miadata[1].'-'.$miadata[2]; 

	$prevDate =date('d/m/Y', strtotime($miadatastr .' -1 day'));
	$nextDate = date('d/m/Y', strtotime($miadatastr .' +1 day'));
	
}


$clientiWhatsup['AMAT2']="Amato Savino";
$clientiWhatsup['O2000']="Orto 2000 Gianni";
$clientiWhatsup['CALIM']="Calimero - STEFANO";
$clientiWhatsup['ABBA2']="Abbascia Domenico";
$clientiWhatsup['PRIMO']="Tesini";
//$fornitori['']="Gaspare ExAstro - Cavallaro"

$elencoDDT=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('<>',$Sdate, $Edate),
		//'data'=>array('<>','01/07/17', '31/07/17'),
		//'cod_destinatario'=>array('!=','SGUJI','BELFR','AMATO','CALIM','DANFR','FTESI','GARLE','LAME2','MANTG','ESPOS','AZGI','BENIE','BISSM','BOLLA','BONER','CASAR','CHIE3','CORTE','DICAM','DOROM','FABBR','FACCG','FARED','FARET','FORMA','GAZZO','GIAC1','GIMMI2','GREE5','LEOPA','LORAL','MACER','MAEST','MARTI','MORAN','MUNAR','NOVUS','STEMI','ORTO3','PRIMF','SBIFL','SBIZZ','TARCI','TESI','TIATI','ZAPPO','SEVEN','SMA','ULISS','NIZZ2'),
		//'cod_causale' => 'D'
	)
);


$myfile = fopen("./whatsup2.sh", "w");
$fileContent = '#!/usr/bin/env bash';


$elencoDDT->iterate(function($obj){
	global $fileContent;
	global $clientiWhatsup;
	
	//$obj->cod_destinazione->getVal()
	//$obj->cod_causale->getVal()
	if(array_key_exists ($obj->cod_destinatario->getVal(), $clientiWhatsup)){
		
		//ricavo il nome testuale del destinatario
		$destinatario = $clientiWhatsup[$obj->cod_destinatario->getVal()];
		
		//genero il pdf del ddt
		//e ricavo il nome del file da allegare (il nome deve essere quello della postazione che esegue selenium)
		//$obj->generaPdf();
		
		//copio il file in una cartella raggiungibile da linux
		copy($obj->getPdfFileUrl(),'c:/stampeddt/'.$obj->getPdfFileName());
		
		//$allegato = $obj->getPdfFileUrl();
		//$allegato = '/home/brungionni/mnt/www/webContab/my/php/core/stampe/ddt/'.$obj->getPdfFileName();
		$allegato = '/home/brungionni/mnt/dati/stampeddt/'.$obj->getPdfFileName();

		//python /home/brungionni/Utils/testselenium.py --destinatario="Gaspare ExAstro - Cavallaro" --allegato="/home/brungionni/mnt/www/webContab/my/php/core/stampe/ddt/20231019_DdT00003600.pdf"
		$fileContent .= "\n".'python /home/brungionni/Utils/testselenium.py --destinatario="'.$destinatario.'" --allegato="'.$allegato.'"';
	
	}
	
});

fwrite($myfile, $fileContent);
echo $fileContent;