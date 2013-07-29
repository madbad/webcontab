<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style_print.css" media="print">


<?php
include ('./core/config.inc.php');
set_time_limit ( 0);

//seleziono l'anno di cui mostrare le fatture
if(@$_GET['anno']){
	$anno=$_GET['anno'];
}else{
	$anno= date("Y");
}

//creo la tabella di "selezione" anno
$html='<table class="titleTable"">';
$html.='<tr>';
$annoprec=$anno-1;
$html.="<td><a href=?anno=$annoprec>< $annoprec</a></td>";
$html.="<td>Fatture anno:<br>$anno</td>";
$annosuc=$anno+1;
$html.="<td><a href=?anno=$annosuc>$annosuc ></a></td>";
$html.='</tr><table>';

//mostro le fatture 
$test=new MyList(
	array(
		'_type'=>'Ddt',
		'data'=>array('<>','01/01/'.$anno,'31/12/'.$anno),
		//'cod_cliente'=>'SEVEN'
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
	$html.= '<td>'.$cliente->ragionesociale->getVal().'</td>';

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