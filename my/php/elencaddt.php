<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style_print.css" media="print">

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

//creo la tabella di "selezione" anno e/o giorno
$html='<table class="titleTable"">';
$html.='<tr>';
$annoprec=$anno-1;
$html.="<td><a href=?anno=$annoprec>< $annoprec</a>";
$html.="<br><a href=?date=$prevDate><$prevDate </a></td>";
$html.="</td>";
$html.="<td>Ddt anno:<br><a href=?anno=$anno>$anno</a><br>$Sdate</td>";
$annosuc=$anno+1;
$html.="<td><a href=?anno=$annosuc>$annosuc ></a>";
$html.="<br><a href=?date=$nextDate>$nextDate> </a></td>";
$html.='</tr><table class="spacedTable, borderTable">';

//mostro le ddt 
$test=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('<>',$Sdate, $Edate),
		//'data'=>array('<>','01/07/17', '31/07/17'),
		//'cod_destinatario'=>array('!=','SGUJI','BELFR','AMATO','CALIM','DANFR','FTESI','GARLE','LAME2','MANTG','ESPOS','AZGI','BENIE','BISSM','BOLLA','BONER','CASAR','CHIE3','CORTE','DICAM','DOROM','FABBR','FACCG','FARED','FARET','FORMA','GAZZO','GIAC1','GIMMI2','GREE5','LEOPA','LORAL','MACER','MAEST','MARTI','MORAN','MUNAR','NOVUS','STEMI','ORTO3','PRIMF','SBIFL','SBIZZ','TARCI','TESI','TIATI','ZAPPO','SEVEN','SMA','ULISS','NIZZ2'),
		//'cod_causale' => 'D'
	)
);

//ottengo una lista dei codici clienti 
//(mi serve per creare una cache degli oggetti cliente in modo da non dover fare poi una query per ogni singolo oggetto cliente)
$codiciCliente =array('=');
$test->iterate(function($obj){
	global $codiciCliente;
	$codiceCliente=$obj->cod_destinatario->getVal();
	$codiciCliente[$codiceCliente]= $codiceCliente;
});

if(count($codiciCliente)<2){
	echo $html;
	echo '<br><h2><center>Nessun ddt per questa data</center></h2>';
	exit();
	
}

/////////////////////////////////////////
//per i clienti
/////////////////////////////////////////
	//ricavo dalla lista precedente gli "oggetti" cliente
	$dbClienti=new MyList(
		array(
			'_type'=>'ClienteFornitore',
			'codice'=>$codiciCliente,
		)
	);

	//creo un nuovo array che ha per "indice" il codice cliente in modo da rendere pi� semplice ritrovare "l'oggetto" cliente
	$dbClientiWithIndex = array();
	$dbClienti->iterate(function($myCliente){
		global $dbClientiWithIndex;
		$codcliente = $myCliente->codice->getVal();
		$dbClientiWithIndex[$codcliente]= $myCliente;
	});

/////////////////////////////////////////
//per i fornitori
/////////////////////////////////////////
	//ricavo dalla lista precedente gli "oggetti" cliente
	$dbFornitori=new MyList(
		array(
			'_type'=>'ClienteFornitore',
			'codice'=>$codiciCliente,
			'_tipo'=>'fornitore',
		)
	);

	//creo un nuovo array che ha per "indice" il codice cliente in modo da rendere pi� semplice ritrovare "l'oggetto" cliente
	$dbFornitoriWithIndex = array();
	$dbFornitori->iterate(function($myFornitore){
		global $dbFornitoriWithIndex;
		$codfornitore = $myFornitore->codice->getVal();
		$dbFornitoriWithIndex[$codfornitore]= $myFornitore;
	});
	//print_r($dbFornitoriWithIndex);
/////////////////////////////////////////
/////////////////////////////////////////


$test->iterate(function($obj){
	global $html;
	global $dbClientiWithIndex;
	
	$cod_causale=$obj->cod_causale->getVal();
	$html.= "<tr class='$cod_causale'>";

	if( $obj->tipocodiceclientefornitore->getVal() == "C"){
		$cliente = $dbClientiWithIndex[$obj->cod_destinatario->getVal()];
	}else{ // if fornitore
		//this is a stub... creo un codice cliente vuoto giusto per non far andare in panico ilprogramma... in realt� dovrei cercare un codice fornitore 
		$cliente = new ClienteFornitore(array(
			'codice'=>$obj->cod_destinatario->getVal(),
			'_tipo'=>'fornitore',
		));
		//print_r($cliente);
	}
	$html.= '<td><input type="checkbox"></td>';
	$html.= '<td>'.$obj->cod_causale->getVal().'</td>';
	$html.= '<td>'.$obj->numero->getVal().'</td>';
	$html.= '<td>'.$obj->data->getFormatted().'</td>';
	$html.= '<td>( '.$obj->cod_destinazione->getVal().'**'.$obj->cod_destinatario->getVal().' ) '.$cliente->ragionesociale->getVal().'</td>';

	$link= '<a href="./core/gestioneDdt.php?';
	$link.= 'numero='.$obj->numero->getVal();
	$link.= '&data='.$obj->data->getVal();
	$link.= '&cod_causale='.$obj->cod_causale->getVal();
	
	/*
	//mail
	if($cliente->__pec->getVal()!=''){
		$dataInvioPec=$obj->__datainviopec->getVal();
		if($dataInvioPec){
			$html.= '<td style="background-color:#66ff00;">Inviata il '.$dataInvioPec.$link.'&do=inviaPec"><br>Reinvia?</a></td>';
		}else{
			$html.= '<td>'.$link.'&do=inviaPec">Invia Mail</a></td>';
		}
	}else{
		$datastampa=$obj->__datastampa->getVal();
		if($datastampa){
			@$html.= '<td style="background-color:#66ff00;">Stampata il '.$dataInvioPec.$link.'&do=stampaCliente"><br>Ristampa?</a></td>';
		}else{
			$html.= '<td>'.$link.'&do=stampaCliente">Stampa copia cliente</a></td>';
		}	
	}
	*/

	//visulizza
	$html.= '<td>'.$link.'&do=visualizza">Visualizza</a></td>';
	$html.= '<td>'.$link.'&do=mail">Mail</a></td>';
	$html.= '<td>'.$link.'&do=mail&force_nascondiprezzo=true">Mail (prezzo nascosto)</a></td>';
	$html.= '<td>'.$link.'&do=stampa">Stampa</a></td>';

	//link fattura
	$link= '<a target="_blank" href="./core/gestioneFatture.php?';
	$link.= 'numero='.number_format($obj->fattura_numero->getVal()*1,$decimali=0,$separatoreDecimali=',',$separatoreMigliaia='.');
	$link.= '&data='.$obj->fattura_data->getVal();
	$link.= '&cod_causale='.'F';
	$html.= '<td>'.$link.'&do=visualizza">Fattura '.number_format($obj->fattura_numero->getVal()*1,$decimali=0,$separatoreDecimali=',',$separatoreMigliaia='.').' del '.$obj->fattura_data->getVal().' </a></td>';
	
	
	$html.="</tr>\n";
//	$html.= '<td><a href=""><img src="./img/printer.svg" alt="Stampa" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/pdf.svg" alt="Visualizza PDF" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/email.svg" alt="Invia PEC" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/ok.svg" alt="Stato: OK" width="30px"></a></td>';
});
$html.='</table>';
echo $html;
