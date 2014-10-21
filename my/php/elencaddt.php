<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style_print.css" media="print">


<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

//seleziono l'anno di cui mostrare i ddt
if(@$_GET['anno']){
	$anno=$_GET['anno'];
	$Sgiorno = '01';
	$Smese =  '01';
	$Sgiorno = '31';
	$Smese = '12';
}else{
	$anno= date("Y");
	$Sgiorno = $Egiorno = date("d");
	$Smese = $Emese =  date("m");
}

//creo la tabella di "selezione" anno
$html='<table class="titleTable"">';
$html.='<tr>';
$annoprec=$anno-1;
$html.="<td><a href=?anno=$annoprec>< $annoprec</a></td>";
$html.="<td>Ddt anno:<br>$anno</td>";
$annosuc=$anno+1;
$html.="<td><a href=?anno=$annosuc>$annosuc ></a></td>";
$html.='</tr><table>';

//mostro le ddt 
$test=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('<>',$Sgiorno.'/'.$Smese.'/'.$anno,$Egiorno.'/'.$Emese.'/'.$anno),
		//'cod_destinatario'=>array('!=','SGUJI','BELFR','AMATO','CALIM','DANFR','FTESI','GARLE','LAME2','MANTG','ESPOS','AZGI','BENIE','BISSM','BOLLA','BONER','CASAR','CHIE3','CORTE','DICAM','DOROM','FABBR','FACCG','FARED','FARET','FORMA','GAZZO','GIAC1','GIMMI2','GREE5','LEOPA','LORAL','MACER','MAEST','MARTI','MORAN','MUNAR','NOVUS','STEMI','ORTO3','PRIMF','SBIFL','SBIZZ','TARCI','TESI','TIATI','ZAPPO','SEVEN','SMA','ULISS','NIZZ2'),
		//'cod_causale' => 'D'
	)
);

$elenco=array();
$test->iterate(function($obj){
	global $html;
	$cod_causale=$obj->cod_causale->getVal();
	$html.= "<tr class='$cod_causale'>";

	$cliente=$obj->cod_destinatario->extend();
	
	$html.= '<td>'.$obj->cod_causale->getVal().'</td>';
	$html.= '<td>'.$obj->numero->getVal().'</td>';
	$html.= '<td>'.$obj->data->getFormatted().'</td>';
	$html.= '<td>( '.$obj->cod_destinatario->getVal().' ) '.$cliente->ragionesociale->getVal().'</td>';

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
	
	$html.="</tr>\n";
//	$html.= '<td><a href=""><img src="./img/printer.svg" alt="Stampa" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/pdf.svg" alt="Visualizza PDF" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/email.svg" alt="Invia PEC" width="30px"></a></td>';
//	$html.= '<td><a href=""><img src="./img/ok.svg" alt="Stato: OK" width="30px"></a></td>';

	
/****************************/

$params=array(
	'numero' => $obj->numero->getVal(),
	'data'   => $obj->data->getVal(),
	'cod_causale'  => $obj->cod_causale->getVal()
);

/*
	global $elenco;
	$cliente=$obj->cod_cliente->extend()->ragionesociale->getVal();
	$elenco[$cliente]='*'.$obj->cod_cliente->getVal().'*'.$obj->cod_cliente->extend()->p_iva->getVal().' <br>';
	//array_push($elenco,$obj->cod_cliente->extend()->ragionesociale->getVal());
*/
});
$html.='</table>';
echo $html;