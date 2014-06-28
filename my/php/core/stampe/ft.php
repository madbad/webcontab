<?php
/* -------------------------------------------------------------------------------------------------------
	Questa libreria esegue la stampa di 
	e lei ne prepara la stampa
----------------------------------------------------------------------------------------------------------
*/
function addIntestazione ($pdf){
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	//imposto carattere
	$pdf->SetFont($def_font, 'I', $def_size-1);
	
	//importo i dati dell'azienda emittente della fattura
	$azienda=$GLOBALS['config']->azienda;
	
	//logo + intestazione
	$html ='';
	$html.= '<img src="'.$azienda->_logo->getVal().'" width="265" height="100"><br><span style="font-size:40px;font-weight:bold;">'.$azienda->_ragionesocialeestesa->getVal().'</span>';
	$html.= '<br>'.$azienda->via->getVal().' - '.$azienda->cap->getVal().' '.$azienda->paese->getVal().' ('.$azienda->citta->getVal().')';
	$html.= '<br>Telefono '.$azienda->telefono->getVal().' - Fax '.$azienda->fax->getVal().'';
	$html.= '<br>Capitale Sociale '.$azienda->_capitalesociale->getVal().' i.v.';
	$html.= '<br>R.E.A. '.$azienda->_rea->getVal();
	$html.= '<br>Reg.Imprese '.$azienda->_registroimprese->getVal();
	$html.= '<br>Codice Fiscale '.$azienda->cod_fiscale->getVal();
	$html.= '<br>Partita IVA '.$azienda->p_iva->getVal();	
	$html.= '<br>BNDOO n.'.$azienda->_bndoo->getVal();
	$html.= '<br>PEC: '.$azienda->_emailpec->getVal();
	$pdf->writeHTMLCell($w=0, $h=0, $x='15', $y='5', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
}

function addDestinatario ($ft,$pdf){
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	$mod=14;
/*
	//destinazione se diversa dal destinatario
	if($ft->cod_cliente->getVal()!=''){
		//bordo destinazione
		$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$pdf->RoundedRect(110, 30+$mod, 80, 24, 5.0, '1010', 'DF', $style, $def_bianco);
	
		$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);	
		$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>Z<BR>I<BR>O<BR>N<BR>E';
		$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=44+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);
	
		$destinazione=$ft->cod_destinazione->extend();
		//dati intestazione ddt
		$pdf->SetFont($def_font, 'b', $def_size+1.4);
		$pdf->Text(114, 37+$mod, $destinazione->ragionesociale->getVal());
		//$pdf->Text(114, 41+$mod, 'Unipersonale');
		$pdf->SetFont($def_font, '', $def_size);
		$pdf->Text(114, 45+$mod, $destinazione->via->getVal());
		$pdf->Text(114, 49+$mod, $destinazione->cap->getVal().' '.$destinazione->paese->getVal(). ' ('.$destinazione->citta->getVal().')');
	}	
*/	
	
	//destinatario
	$cliente=$ft->cod_cliente->extend();
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(110, 10+$mod, 80, 25, 5.0, '1010', 'DF', $style, array(230,230,230));//grigio chiaro
	$pdf->SetFont($def_font, 'b', $def_size+0.8);
	$pdf->Text(114, 11+$mod, $cliente->ragionesociale->getVal());
	//$pdf->Text(114, 15+$mod, 'Unipersonale'); //TODO SECONDA RIGA RAG.SOCIALE
	$pdf->SetFont($def_font, '', $def_size);

	
	$pdf->Text(114, 20+$mod, $cliente->via->getVal());
	$pdf->Text(114, 23+$mod, $cliente->cap->getVal().' '.$cliente->paese->getVal(). ' ('.$cliente->citta->getVal().')');
	$pdf->SetFont($def_font, 'b', $def_size+1);
	
	if($cliente->p_iva->getVal()!=''){
		$piva=$cliente->p_iva->getVal();
	}else{
	//echo $cliente->sigla_paese->getVal()+'*******';
		$piva=$cliente->sigla_paese->getVal().' '.$cliente->p_iva_cee->getVal();
	}
	$pdf->Text(114, 27+$mod, 'Partitita IVA: '.$piva);
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->Text(114, 31+$mod, 'Codice Fiscale: '.$cliente->cod_fiscale->getVal());

	$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);
	$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>T<BR>A<BR>R<BR>I<BR>O';
	$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=20+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);
}

function addDatiFattura ($ft,$pdf){
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 70, 100, 10, 5.0, '0101', 'DF', $style, $def_verde);
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(18, 71, 'Tipo doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	
	$tipoDoc='';
	if ($ft->tipo->getVal()=='F' || $ft->tipo->getVal()=='f'){
		$tipoDoc='Fattura';
	}
	if ($ft->tipo->getVal()=='N' || $ft->tipo->getVal()=='n'){
		$tipoDoc='Nota di accredito';
	}
	$html= $tipoDoc.' <span style="font-size:17px"></span>';
	$pdf->writeHTMLCell($w=185, $h=30, $x=18, $y=74, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(65, 71, 'Numero Doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	//$pdf->Text(65, 74,  trim($ft->numero->getFormatted(0)));

	//aggiungo la stringa "/anno" se dopo il 2013
	$anno=explode('/',$ft->data->getFormatted());
	if ($anno[2]*1>=2013){
		$anno=$anno[2];
		$aggiungiAnno='/'.$anno;
		$aggiungiAnno='<span style="font-size:22px;">'.$aggiungiAnno.'</span>';
	}
	
	$html = '<div style="text-align:right;">'.trim($ft->numero->getFormatted(0)).$aggiungiAnno.'</div>';
	$pdf->writeHTMLCell($w=20, $h=9, $x=60, $y=74, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='left', $autopadding=false);
	
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(85, 71, 'Data Doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(85, 74, $ft->data->getFormatted());
	
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(125, 70, 65, 10, 5.0, '0101', 'DF', $style, $def_bianco);
	
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(138, 71, 'Valuta');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(138, 74, $ft->valuta->getVal());	
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(177, 71, 'Pagina');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(177, 74, $ft->pagina);
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 82, 175, 10, 5.0, '0101', 'DF', $style, $def_bianco);
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(18, 83, 'Condizioni di pagamento');
	$pdf->SetFont($def_font, '', $def_size+5);


	$perAnticipoFatture=FALSE;
	if ($perAnticipoFatture){
		//per anticipi fatture metto pagamento a 2 mesi e aggiungo la scadenza
		$pagamentoAMesi=2;
		$data=explode('/',$ft->data->getFormatted());
		$anno=$data[2];
		$mese=$data[1]+$pagamentoAMesi;
		if($mese>12){
			$anno=$anno+1;
			$mese=$mese-12;
		}
		$giorni=$num = cal_days_in_month(CAL_GREGORIAN, $mese, $anno); 
		$pdf->Text(18, 86, strtolower('bonif.bancario '.($pagamentoAMesi*30).' gg df fm - Scadenza '.$giorni.'/'.$mese.'/'.$anno));
		
		
		//modifico la banca di appoggio
		//$ft->cod_banca->setVal('10'); //09 cerea banca
										//10 popolare di vicenza
										//01 cassa di risparmio del veneto
		
	}else{
		$pdf->Text(18, 86, strtolower($ft->cod_pagamento->extend()->descrizione->getVal()));
		/*
		//non decommentare questa parte di codice in quanto normalmente non indichiamo la scadenza delle fatture
		$pdf->SetFont($def_font, '', $def_size-3);
		$pdf->Text(78, 83, 'Scadenza pagamento');
		$pdf->SetFont($def_font, '', $def_size+5);
		$pdf->Text(78, 86, $ft->getScadenzaPagamento());
		*/
	}
	
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(125, 83, 'Banca di appoggio');
	$pdf->SetFont($def_font, '', $def_size+4);
	
	//se non è presente un codice banca il programma crashava
	if($ft->cod_banca->extend()){
//		$pdf->Text(125, 86, strtolower($ft->cod_banca->extend()->__iban->getVal()));

		$pdf->Text(125, 86, $ft->cod_banca->extend()->__iban->getVal());	
	}

}

function addFineCorpoFattura($ft, $pdf){
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	//**********************************************************
	//**********************************************************
	$pdf->SetFont($def_font, '', $def_size);
	$ft->html.= '</tr></table>';
	$pdf->writeHTMLCell($w=164, $h=10, $x=15, $y=95.5, $ft->html, $border=1, $ln=1, $fill=0, $reseth=true, $align='right', $autopadding=false);

}

function addTotaliFattura($ft, $pdf){
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	//**********************************************************
	//**********************************************************
	//annotazioni
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect($x=15, $y=263, $w=93, $h=31, 5.0, '1010', 'DF', $style, $def_bianco);
	
	$pdf->SetFont($def_font, '', $def_size);
	//$pdf->Text($x=15, $y=263, "-CONTRIBUTO CONAI ASSOLTO OVE DOVUTO \n -ALTRO dsfsdf sfsfsf sdf sfsfs");

	$html = '<ul style="color:'.$def_verde.'"><LI>-</LI><li>PESI NETTI RISCONTRATI ALL\'ARRIVO</li><li>CONTRIBUTO CONAI ASSOLTO OVE DOVUTO</li><li>TOTALE FATTURA SALVO ERRORI E OMISSIONI</li></ul>';

	$html = '<ul><li style="color:white;">-</li>';//ne lascio uno bianco per evitare un bug che creava un punto della lista più grande degli altri
	$html .='<li>PESI NETTI RISCONTRATI ALL\'ARRIVO</li>';
	$html .='<li>CONTRIBUTO CONAI ASSOLTO OVE DOVUTO</li>';
	$html .='<li>SALVO ERRORI E OMISSIONI</li>';
	$html .='<li>ASSOLVE AGLI OBBLIGHI DI CUI ALL\'ART.62, COMMA 1, DEL DECRETO LEGGE 24/01/2012, N.1, CONVERTITO CON MODIFICAZIONI DALLA LEGGE 24/03/12, N.27.<li>';

	
	//il cliente ha lettera di intento? se si stampo la dicitura che la riguarda
	$cliente=$ft->cod_cliente->extend();
	if ($cliente->lettera_intento_num->getVal()){
		$numero=$cliente->lettera_intento_num->getVal();
		$data=$cliente->lettera_intento_data->getFormatted();
		$numeroInterno=$cliente->lettera_intento_numinterno->getVal();
		$html .='<li><b>Vs.Dichiarazione di intento n. '.$numero.' Registrata al n. '.$numeroInterno.' del '.$data.'</b></li>';
	
	}
	$html .='</ul>';

	$pdf->writeHTMLCell($w=93, $h=31, $x=15, $y=263, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
	
	//dettaglio IVA
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect($x=110, $y=263, $w=80, $h=19, 5.0, '1010', 'DF', $style, $def_bianco);

	//imponibili e iva
	$imponibili=$ft->calcolaTotaliImponibiliIva();
	$col1="width=25px;";
	$col2="width=100px;";
	
	$html = '<table style="border:0px solid #000000;margin:0px;padding:0px;text-align:right;">';
	$html.="<tr><td $col1>Cod.</td><td $col2>Descr.IVA</td><td>Imponibile</td><td>Importo IVA</td></tr>";
	foreach ($imponibili as $codIva =>$val){
		$iva=new CausaleIva(array('codice'=>(string)$codIva));
		//$codIva
		$descrizioneIva=$iva->descrizione->getVal();
		$imponibileIva=number_format($val['imponibile']*1,$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.');
		$importoIva=number_format($val['importo_iva']*1,$decimali=2,$separatoreDecimali=',',$separatoreMigliaia='.');
		$html.="<tr><td $col1>".$codIva."</td><td $col2>".$descrizioneIva."</td><td>".$imponibileIva."</td><td>".$importoIva."</td></tr>";	
	}		

	$html.= "</table>";
	$pdf->writeHTMLCell($w=80, $h=19, $x=110, $y=263, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

	//totale fattura
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect($x=110, $y=285, $w=80, $h=9, 5.0, '1010', 'DF', $style, $def_verde);

	$html = 'Totale Documento';
	$pdf->writeHTMLCell($w=80, $h=9, $x=110, $y=285, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='right', $autopadding=false);

	$pdf->SetFont($def_font, '', $def_size+5);
	$html = $ft->valuta->getVal();
	$pdf->writeHTMLCell($w=80, $h=9, $x=123, $y=288, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='right', $autopadding=false);
	
	$pdf->SetFont($def_font, '', $def_size+9);
	//$pdf->Text(167, 286, $ft->importo->getFormatted());
	$html = '<div style="text-align:right;">'.$ft->importo->getFormatted(2).'</div>';
	$pdf->writeHTMLCell($w=58, $h=9, $x=130, $y=286, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='left', $autopadding=false);
}
function addInizioCorpoFattura($ft, $pdf){
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect($x=15, $y=95, $w=175, $h=166, 5.0, '0000', 'DF', $style, $def_bianco);
	$pdf->SetFont($def_font, '', $def_size-3);
	$ft->html = '<table style="border:0px solid #000000;margin:0px;padding:5px;"><tr>';
	$ft->html.= '<td width="70px;">Cod.Articolo</td><td  width="200px;">Descrizione dei Beni (natura e qualita)</td><td width="40px;">Colli</td><td width="60px;">Prezzo</td><td width="40px;">U.M.</td><td width="80px;">Quantita</td><td  width="90px;">Imponibile</td><td  width="40px;">IVA</td>';

	$ft->html.= MyOwnRow('','','','','','','','','' );
	
	// righe verticali nelle righe del ddt
	$style3 = array('width' => 0.2, 'cap' => 'butt', 'join' => 'round', 'dash' => '0', 'color' => $def_verde);
	$dist=33;
	$inizioRigaV=100;
	$fineRigaV=258;
	$pdf->Line($dist, $inizioRigaV, $dist, $fineRigaV, $style3);
	$dist+=58;
	$pdf->Line($dist, $inizioRigaV, $dist, $fineRigaV, $style3);
	$dist+=11;
	$pdf->Line($dist, $inizioRigaV, $dist, $fineRigaV, $style3);
	$dist+=17;
	$pdf->Line($dist, $inizioRigaV, $dist, $fineRigaV, $style3);
	$dist+=11;
	$pdf->Line($dist, $inizioRigaV, $dist, $fineRigaV, $style3);
	$dist+=23;
	$pdf->Line($dist, $inizioRigaV, $dist, $fineRigaV, $style3);
	$dist+=25;
	$pdf->Line($dist, $inizioRigaV, $dist, $fineRigaV, $style3);		

}


	//**********************************************************
	//*********************************************************
	
function MyOwnRow($a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9){
	$mystyle='style="text-align:left;padding:20px;" padding="2" align="left"';
	$mystyle2='style="text-align:right;padding:20px;" padding="2"  align="right"';
	$mystyle3='style="text-align:center;padding:20px;" padding="2"  align="center"';		
	//$mystyle=$mystyle2='';
	$out= '</tr><tr>';
	$out.= "<td width='62px;' $mystyle>$a1</td>"; //codice
	$out.= "<td width='205px;' $mystyle>$a2</td>"; //descrizione
	$out.= "<td width='40px;' $mystyle2>$a3 &nbsp;</td>"; //colli
	$out.= "<td width='51px;' $mystyle2>$a4  </td>"; //prezzo
	$out.= "<td width='47px;' $mystyle3>$a5</td>"; //um
	$out.= "<td width='82px;' $mystyle2>$a6 &nbsp;</td>"; //lordo
	$out.= "<td width='85px;' $mystyle2>$a7 &nbsp;&nbsp;</td>"; //tara
	$out.= "<td width='40px;' $mystyle2>$a8</td>"; //netto
	if ($a9!=''){
		$out.="</tr><tr><td></td><td colspan='3'><span style='font-size:4px;'>         Lotto: $a9</span></td><td></td><td></td><td></td><td></td>";
	}
	return $out;
}









//**********************************************************
//**********************************************************
//**********************************************************
 /*-----------------------------------------------------*/
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/tcpdf/config/lang/ita.php');
require_once(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/tcpdf/tcpdf.php');
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	//Page header
	public function Header() {
		// full background image
		// store current auto-page-break status
		$bMargin = $this->getBreakMargin();
		$auto_page_break = $this->AutoPageBreak;
		$this->SetAutoPageBreak(false, 0);
		$this->Image($GLOBALS['img_file'], 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
	}
}



//**********************************************************
function generaPdfFt($ft){
	global $azienda;
	$ft->pagina=1;
	$printTime=time();/*todo e se io volessi modificarlo a mio piacimento?*/

	//to fix... se vedo che funziona correttamente posso eliminare questo passaggio	
	$ft->verificaCalcoli();	
	
	
	$GLOBALS['img_file']='';
	
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);





	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('La Favorita di Brun G. & G. Srl Unip.');
	$pdf->SetTitle('Fattura');

	// set header and footer fonts
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	//set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(0);
	$pdf->SetFooterMargin(0);

	// remove default footer
	$pdf->setPrintFooter(false);

	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 0);

	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	//set some language-dependent strings
	@$pdf->setLanguageArray($l);

	//-----------------------------------------------------
	//   DDT PAGE
	//-----------------------------------------------------
	//aggiungo una pagina vuota
	$pdf->AddPage();
	
	//aggiungo l'intestazione alla pagina
	addIntestazione($pdf);
	addDestinatario($ft, $pdf);
	addDatiFattura($ft, $pdf);
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//max 42 righe
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$contaRighe=0;
	$ft->html='';
	addInizioCorpoFattura($ft, $pdf);
	
	foreach ($ft->righe as $key => $value) {
		if($contaRighe>41){
			//chiudo il corpo fattura corrente
			addFineCorpoFattura($ft, $pdf);
			
			//aggiungo una nota sul fatto che la fattura continua sulla pagina successiva
			$pdf->SetFont($def_font, 'I', $def_size);
			$pdf->Text($x=150, $y=270, 'continua su pagina '.($ft->pagina+1));
			
			//inizio una nuova pagina
			$pdf->AddPage();
			//aggiorno il contatore delle pagine
			$ft->pagina++;

			addIntestazione($pdf);
			addDestinatario($ft, $pdf);
			addDatiFattura($ft, $pdf);

			//azzero l'html per il corpo fattura
			$ft->html='';
			
			//azzero il contatore delle righe
			$contaRighe=0;
			//ricomincio il corpo fattura sulla nuova pagina
			addInizioCorpoFattura($ft, $pdf);			
		}
		$contaRighe++;
		$riga=$ft->righe[$key];
		//echo "Key: $key; Value: $value<br />\n";
		// number_format($number, 2, ',', ' ');
		
		//riga normale
		$ft->html.= MyOwnRow(substr($riga->cod_articolo->getVal(),0,8),//abbrevio per questioni di spazio il codice articolo a 8 caratteri
							(substr($riga->descrizione->getVal(),0,9)=='D.d.T. N.' ? '<b><i>'.$riga->descrizione->getVal().'</i></b>' : $riga->descrizione->getVal()),
							($riga->colli->getVal()*1>0 ? $riga->colli->getFormatted(0) : ''),
							($riga->prezzo->getVal()*1>0 ? $riga->prezzo->getFormatted(3) : ''),
							$riga->unita_misura->getVal(),
							($riga->peso_lordo->getVal()*1>0 ? $riga->peso_lordo->getFormatted(2) : ''), //peso lordo
							($riga->imponibile->getVal()!='0.0'? $riga->imponibile->getFormatted(2) : ''), 
							($riga->cod_iva->getVal()),
							'' ); //lotto se presente todo
							
		//se c'è un codice articolo
		if ($riga->cod_articolo->getVal()!=''){
			//seconda descrizione
			$descrizione2=$riga->cod_articolo->extend()->descrizione2->getVal();
			//var_dump($descrizone2);
			if($descrizione2!=''){
				$ft->html.= MyOwnRow('',$descrizione2,'','','','','','','' );
			}

			//descrizione lunga
			$descrizioneL=$riga->cod_articolo->extend()->descrizionelunga->getVal();
			if($descrizioneL!=' '){
				$righeL=explode("\n",$descrizioneL);
				foreach ($righeL as $rigaL){
					if(strlen($rigaL)>1){
						//var_dump($rigaL);
						$ft->html.= MyOwnRow('',$rigaL,'','','','','','','' );
					}
				}
			}
		}

	}
	//chiudo il corpo fattura corrente
	addFineCorpoFattura($ft, $pdf);
	//**********************************************************
	//**********************************************************
	addTotaliFattura($ft, $pdf);

	//**********************************************************
	//**********************************************************
	$nomefile=$ft->getPdfFileName();
	//salvo il file
	@$pdf->Output($GLOBALS['config']->pdfDir."/ft/".$nomefile, 'F');
	//e ne invio una copia al browser per visualizzarlo
	//@$pdf->Output($nomefile, 'I');
	
}
?>