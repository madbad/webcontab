<?php
/* -------------------------------------------------------------------------------------------------------
	Questa libreria esegue la stampa di 
	e lei ne prepara la stampa
----------------------------------------------------------------------------------------------------------
*/
function intestazione ($pdf){
	//importo i dati dell'azienda emittente della fattura
	global $azienda;
	
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
	$html.= '<br>PEC Mail: '.$azienda->_emailpec->getVal();
	$pdf->writeHTMLCell($w=0, $h=0, $x='15', $y='5', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
}




function printFt($ft){
	global $azienda;
	$printTime=time();/*todo e se io volessi modificarlo a mio piacimento?*/


	$style='';
	$GLOBALS['img_file']='';

	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	 /*-----------------------------------------------------*/
	require_once('./tcpdf/config/lang/ita.php');
	require_once('./tcpdf/tcpdf.php');


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
	$pdf->setLanguageArray($l);

	//-----------------------------------------------------
	//   DDT PAGE
	//-----------------------------------------------------
	//imposto carattere
	$pdf->SetFont($def_font, 'I', $def_size-1);
	
	//aggiungo una pagina vuota
	$pdf->AddPage();
	
	//aggiungo l'intestazione alla pagina
	intestazione($pdf);

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
	$pdf->RoundedRect(110, 10+$mod, 80, 25, 5.0, '1010', 'DF', $style, array(200,200,200));
	$pdf->SetFont($def_font, 'b', $def_size+1.4);
	$pdf->Text(114, 11+$mod, $cliente->ragionesociale->getVal());
	//$pdf->Text(114, 15+$mod, 'Unipersonale'); //TODO SECONDA RIGA RAG.SOCIALE
	$pdf->SetFont($def_font, '', $def_size);

	
	$pdf->Text(114, 20+$mod, $cliente->via->getVal());
	$pdf->Text(114, 23+$mod, $cliente->cap->getVal().' '.$cliente->paese->getVal(). ' ('.$cliente->citta->getVal().')');
	$pdf->SetFont($def_font, 'b', $def_size+1);
	$pdf->Text(114, 27+$mod, 'Partitita IVA: '.$cliente->p_iva->getVal());
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->Text(114, 31+$mod, 'Codice Fiscale: '.$cliente->cod_fiscale->getVal());

	$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);
	$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>T<BR>A<BR>R<BR>I<BR>O';
	$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=20+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);

	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 70, 175, 10, 5.0, '0101', 'DF', $style, $def_verde);
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(18, 71, 'Tipo doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	
	$tipoDoc='';
	if ($ft->tipo->getVal()=='F'){
		$tipoDoc='Fattura';
	}
	if ($ft->tipo->getVal()=='N'){
		$tipoDoc='Nota di accredito';
	}
	$html= $tipoDoc.' <span style="font-size:17px">(DPR 14/08/1996 n.472)</span>';
	$pdf->writeHTMLCell($w=175, $h=30, $x=18, $y=74, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(98, 71, 'Numero Doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(98, 74, $ft->numero->getVal());
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(125, 71, 'Data Doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(125, 74, $ft->data->getFormatted());
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(177, 71, 'Pagina');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(177, 74, '1/1');
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 82, 175, 10, 5.0, '0101', 'DF', $style, $def_verde);
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(18, 83, 'Condizioni di pagamento');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(18, 86, strtolower($ft->cod_pagamento->extend()->descrizione->getVal()));/*todo ritorna solo "V" invece che vendita e/ o c/commissione*/
													/*uppure "D" invece che "redo da c/deposito" "c/riparazone""omaggio" */
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(98, 83, 'Scadenza pagamento');
	$pdf->SetFont($def_font, '', $def_size+5);
	//imposto tutto a caratteri piccoli con la prima lettera in caratteri grandi nb: di suo era tutto grande
	$pdf->Text(98, 86, $ft->cod_pagamento->extend()->scadenza->getVal());
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(125, 83, 'Banca di appoggio');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(125, 86, strtolower($ft->cod_banca->extend()->ragionesociale->getVal()));

	//**********************************************************
	//**********************************************************

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

	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect($x=15, $y=95, $w=175, $h=166, 5.0, '0000', 'DF', $style, $def_bianco);
	$pdf->SetFont($def_font, '', $def_size-3);
	$html = '<table style="border:0px solid #000000;margin:0px;padding:5px;"><tr>';
	$html.= '<td width="70px;">Cod.Articolo</td><td  width="200px;">Descrizione dei Beni (natura e qualita)</td><td width="40px;">Colli</td><td width="60px;">Prezzo</td><td width="40px;">U.M.</td><td width="80px;">Quantita</td><td  width="90px;">Imponibile</td><td  width="40px;">IVA</td>';

	$pdf->SetFont($def_font, '', $def_size);
	$html.= MyOwnRow('','','','','','','','','' );

	foreach ($ft->righe as $key => $value) {
		$riga=$ft->righe[$key];
		//echo "Key: $key; Value: $value<br />\n";
		// number_format($number, 2, ',', ' ');
		$html.= MyOwnRow(	$riga->cod_articolo->getVal(),
							$riga->descrizione->getVal(),
							($riga->colli->getVal()*1>0 ? number_format ($riga->colli->getVal(),2) : ''),
							($riga->prezzo->getVal()*1>0 ? '€ '.number_format ($riga->prezzo->getVal(),3): ''),
							$riga->unita_misura->getVal(),
							($riga->peso_lordo->getVal()*1>0 ? number_format ($riga->peso_lordo->getVal(),1): ''), //peso lordo
							($riga->imponibile->getVal()), 
							($riga->cod_iva->getVal()),
							'' ); //lotto se presente todo
	}

	$html.= '</tr></table>';
	$pdf->writeHTMLCell($w=164, $h=10, $x=15, $y=95.5, $html, $border=1, $ln=1, $fill=0, $reseth=true, $align='right', $autopadding=false);

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
	
	
	//**********************************************************
	//**********************************************************

	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect($x=15, $y=263, $w=80, $h=28, 5.0, '1010', 'DF', $style, $def_bianco);

	//imponibili e iva
	$col1="width=25px;";
	$col2="width=100px;";
	
	$html = '<table style="border:0px solid #000000;margin:0px;padding:0px;text-align:right;">';
	$html.="<tr><td $col1>Cod.</td><td $col2>Descr.IVA</td><td>Imponibile</td><td>Importo IVA</td></tr>";
	$html.="<tr><td $col1>4</td><td $col2>Iva 4%</td><td>99.999,00</td><td>5.000,00</td></tr>";	
	$html.="<tr><td $col1>4</td><td $col2>Escluso Art.15</td><td>99.999,00</td><td>5.000,00</td></tr>";	
	$html.="<tr><td $col1>4</td><td $col2>Escluso Art.8/1c</td><td>99.999,00</td><td>5.000,00</td></tr>";	
	$html.= "</table>";
	$pdf->writeHTMLCell($w=80, $h=28, $x=15, $y=263, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

	//totali imponibile
	
	//totale iva
	
	//totale fattura

	//**********************************************************
	//**********************************************************
	//inviamo il file pdf
	$pdf->Output('DDT_'.$ft->numero->getVal().'__'.$ft->data->getVal().'.pdf', 'I');
}
?>