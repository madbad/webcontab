<?php
/* -------------------------------------------------------------------------------------------------------
	Questa libreria esegue la stampa di un DDT
	La devo adattare per ricevere in input un DDT completo
	attualmente i campi sono tutti hardcoded
	e lei ne prepara la stampa
----------------------------------------------------------------------------------------------------------
*/
function addIntestazioneDdt ($pdf){
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

function addDestinatarioDdt ($ddt,$pdf){
	$mod=14;
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	
	$destinatario=$ddt->cod_destinatario->extend();	
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(110, 10+$mod, 80, 25, 5.0, '1010', 'DF', $style, array(230,230,230)); //grigio chiaro
	$pdf->SetFont($def_font, 'b', $def_size+1.4);
	$pdf->Text(114, 11+$mod, $destinatario->ragionesociale->getVal());
	//$pdf->Text(114, 15+$mod, 'Unipersonale'); //TODO SECONDA RIGA RAG.SOCIALE
	$pdf->SetFont($def_font, '', $def_size);

	
	$pdf->Text(114, 20+$mod, $destinatario->via->getVal());
	$pdf->Text(114, 23+$mod, $destinatario->cap->getVal().' '.$destinatario->paese->getVal(). ' ('.$destinatario->citta->getVal().')');
	$pdf->SetFont($def_font, 'b', $def_size+1);
	$pdf->Text(114, 27+$mod, 'Partitita IVA: '.$destinatario->p_iva->getVal());
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->Text(114, 31+$mod, 'Codice Fiscale: '.$destinatario->cod_fiscale->getVal());

	$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);
	$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>T<BR>A<BR>R<BR>I<BR>O';
	$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=20+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);
}
function addDestinazioneDdt ($ddt,$pdf){
	$mod=14;
	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);

	//bordo destinazione
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(110, 30+$mod, 80, 24, 5.0, '1010', 'DF', $style, $def_bianco);

	$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);	
	$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>Z<BR>I<BR>O<BR>N<BR>E';
	$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=44+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);

	$destinazione=$ddt->cod_destinazione->extend();
	//dati intestazione ddt
	$pdf->SetFont($def_font, 'b', $def_size+1.4);
	$pdf->Text(114, 37+$mod, $destinazione->ragionesociale->getVal());
	//$pdf->Text(114, 41+$mod, 'Unipersonale');
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->Text(114, 45+$mod, $destinazione->via->getVal());
	$pdf->Text(114, 49+$mod, $destinazione->cap->getVal().' '.$destinazione->paese->getVal(). ' ('.$destinazione->citta->getVal().')');
}
function generaPdfDdt($ddt){
	$azienda=$GLOBALS['config']->azienda;
	
	$printTime=time();/*todo e se io volessi modificarlo a mio piacimento?*/
	//$ddt=new Ddt($numero,$data);

	$style='';
	$GLOBALS['img_file']='';

	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(168,236,134);
	$def_bianco= array(999,999,999);
	 /*-----------------------------------------------------*/
/*TO FIX QUESTO VIENE UTILIZZATO TALE E QUALE ANCHE NELLA STAMPA DELLE FTTURE E ECAUSA UN ERRORE IN QUANTO VIENE RIDICHIARATA LA STESSA CLASSE CON LO STESSO NOME*/
/*
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
*/

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('La Favorita di Brun G. & G. Srl Unip.');
	$pdf->SetTitle('Documento di Trasporto');

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
	$pdf->SetFont($def_font, 'I', $def_size-1);
	$pdf->AddPage();
	
	/*
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
	*/
	addIntestazioneDdt($pdf);
	$mod=14;


	//destinazione se diversa dal destinatario
	if($ddt->cod_destinazione->getVal()!=''){
	/*
		//bordo destinazione
		$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$pdf->RoundedRect(110, 30+$mod, 80, 24, 5.0, '1010', 'DF', $style, $def_bianco);
	
		$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);	
		$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>Z<BR>I<BR>O<BR>N<BR>E';
		$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=44+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);
	
		$destinazione=$ddt->cod_destinazione->extend();
		//dati intestazione ddt
		$pdf->SetFont($def_font, 'b', $def_size+1.4);
		$pdf->Text(114, 37+$mod, $destinazione->ragionesociale->getVal());
		//$pdf->Text(114, 41+$mod, 'Unipersonale');
		$pdf->SetFont($def_font, '', $def_size);
		$pdf->Text(114, 45+$mod, $destinazione->via->getVal());
		$pdf->Text(114, 49+$mod, $destinazione->cap->getVal().' '.$destinazione->paese->getVal(). ' ('.$destinazione->citta->getVal().')');
	*/
		addDestinazioneDdt($ddt, $pdf);
	}	
	

	//destinatario
	/*
	$destinatario=$ddt->cod_destinatario->extend();	
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(110, 10+$mod, 80, 25, 5.0, '1010', 'DF', $style, array(230,230,230)); //grigio chiaro
	$pdf->SetFont($def_font, 'b', $def_size+1.4);
	$pdf->Text(114, 11+$mod, $destinatario->ragionesociale->getVal());
	//$pdf->Text(114, 15+$mod, 'Unipersonale'); //TODO SECONDA RIGA RAG.SOCIALE
	$pdf->SetFont($def_font, '', $def_size);

	
	$pdf->Text(114, 20+$mod, $destinatario->via->getVal());
	$pdf->Text(114, 23+$mod, $destinatario->cap->getVal().' '.$destinatario->paese->getVal(). ' ('.$destinatario->citta->getVal().')');
	$pdf->SetFont($def_font, 'b', $def_size+1);
	$pdf->Text(114, 27+$mod, 'Partitita IVA: '.$destinatario->p_iva->getVal());
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->Text(114, 31+$mod, 'Codice Fiscale: '.$destinatario->cod_fiscale->getVal());

	$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);
	$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>T<BR>A<BR>R<BR>I<BR>O';
	$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=20+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);
*/
	addDestinatarioDdt($ddt, $pdf);
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 70, 175, 10, 5.0, '0101', 'DF', $style, $def_verde);
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(18, 71, 'Tipo doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	$html= 'Documento di Trasporto <span style="font-size:17px">(DPR 14/08/1996 n.472)</span>';
	$pdf->writeHTMLCell($w=175, $h=30, $x=18, $y=74, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

	// (DPR 472 del 18/08/96)
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(98, 71, 'Numero Doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(98, 74, $ddt->numero->getVal());
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(125, 71, 'Data Doc.');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(125, 74, $ddt->data->getFormatted());
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
	$pdf->Text(18, 83, 'Causale del trasporto');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(18, 86, $ddt->cod_causale->getVal());/*todo ritorna solo "V" invece che vendita e/ o c/commissione*/
													/*uppure "D" invece che "redo da c/deposito" "c/riparazone""omaggio" */
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(58, 83, 'Aspetto dei beni');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(58, 86, 'Visibile');/*todo*/
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(98, 83, 'Trasporto a mezzo');
	$pdf->SetFont($def_font, '', $def_size+5);
	//imposto tutto a caratteri piccoli con la prima lettera in caratteri grandi nb: di suo era tutto grande
	$pdf->Text(98, 86, ucfirst(strtolower($ddt->cod_mezzo->extend()->descrizione->getVal())));
	//
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(125, 83, 'Inizio trasporto');
	$pdf->SetFont($def_font, '', $def_size+5);
	$pdf->Text(125, 86, 'il '.date('d/m/Y',$printTime).' alle '.date('H:i',$printTime));//todo: rendere dinamico

	//**********************************************************
	//**********************************************************

	function MyOwnDdtRow($a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9){
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
		$out.= "<td width='45px;' $mystyle2>$a7 &nbsp;&nbsp;</td>"; //tara
		$out.= "<td width='80px;' $mystyle2>$a8</td>"; //netto
		if ($a9!=''){
			$out.="</tr><tr><td></td><td colspan='3'><span style='font-size:4px;'>         Lotto: $a9</span></td><td></td><td></td><td></td><td></td>";
		}
		return $out;
	}

	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 95, 175, 118, 5.0, '0000', 'DF', $style, $def_bianco);
	$pdf->SetFont($def_font, '', $def_size-3);
	$html = '<table style="border:0px solid #000000;margin:0px;padding:5px;"><tr>';
	$html.= '<td width="70px;">Cod.Articolo</td><td  width="200px;">Descrizione dei Beni (natura e qualita)</td><td width="40px;">Colli</td><td width="60px;">Prezzo</td><td width="40px;">U.M.</td><td width="80px;">Peso Lordo</td><td  width="50px;">Tara</td><td  width="80px;">Peso netto</td>';

	$pdf->SetFont($def_font, '', $def_size);
	$html.= MyOwnDdtRow('','','','','','','','','' );

	foreach ($ddt->righe as $key => $value) {
		$riga=$ddt->righe[$key];

		//riga normale
		$html.= MyOwnDdtRow(	$riga->cod_articolo->getVal(),
							$riga->descrizione->getVal(),
							($riga->colli->getVal()*1>0 ? $riga->colli->getFormatted(0) : ''),
							($riga->prezzo->getVal()*1>0 ? $riga->prezzo->getFormatted(3) : ''),
							$riga->unita_misura->getVal(),
							($riga->peso_lordo->getVal()*1>0 ? $riga->peso_lordo->getFormatted(2) : ''), //peso lordo
							($riga->peso_lordo->getVal()*1>0 ? number_format ($riga->peso_lordo->getVal()-$riga->peso_netto->getVal(),1): ''), //todoTara
							($riga->peso_lordo->getVal()*1>0 ? $riga->peso_lordo->getFormatted(2): ''),
							'' ); //lotto se presente todo
							
		//se c'è un codice articolo
		if ($riga->cod_articolo->getVal()!=''){
			//seconda descrizione
			$descrizione2=$riga->cod_articolo->extend()->descrizione2->getVal();
			//var_dump($descrizone2);
			if($descrizione2!=''){
				$html.= MyOwnDdtRow('',$descrizione2,'','','','','','','' );
			}

			//descrizione lunga
			$descrizioneL=$riga->cod_articolo->extend()->descrizionelunga->getVal();
			if($descrizioneL!=' '){
				$righeL=explode("\n",$descrizioneL);
				foreach ($righeL as $rigaL){
					if(strlen($rigaL)>1){
						//var_dump($rigaL);
						$html.= MyOwnDdtRow('',$rigaL,'','','','','','','' );
					}
				}
			}
		}
	}

	$html.= '</tr></table>';
//	$pdf->setCellPaddings(1, 1, 1, 1);
//	$pdf->SetCellPaddings 	(1,1,1,1);
	$pdf->writeHTMLCell($w=175, $h=10, $x=15, $y=95.5, $html, $border=1, $ln=1, $fill=0, $reseth=true, $align='right', $autopadding=false);

	//**********************************************************
	//**********************************************************

	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 215, 175, 10, 5.0, '1010', 'DF', $style, $def_bianco);
	//$pdf->SetFont($def_font, '', $def_size-3);
	$html = '<table style="border:0px solid #000000;margin:0px;padding:0px;text-align:left;"><tr>';
	$html.= '<td width="70px;"> </td><td  width="200px;"> </td><td width="40px;">Colli</td><td width="100px;" colspan="2">Imponibile</td><td width="80px;">Peso Lordo</td><td  width="50px;">Tara</td><td  width="80px;">Peso netto</td>';

	$html.= MyOwnDdtRow('<b>Totali</b>','',$ddt->tot_colli->getVal(),'€ todo','',$ddt->tot_peso->getVal(),'todo','todo netto','' );

	$html.= '</tr></table>';
	$pdf->writeHTMLCell($w=175, $h=10, $x=15, $y=216, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

	
	// righe verticali nelle righe del ddt
	$style3 = array('width' => 0.2, 'cap' => 'butt', 'join' => 'round', 'dash' => '0', 'color' => $def_verde);
	$dist=33;
	$pdf->Line($dist, 100, $dist, 211, $style3);
	$dist+=58;
	$pdf->Line($dist, 100, $dist, 211, $style3);
	$dist+=11;
	$pdf->Line($dist, 100, $dist, 211, $style3);
	$dist+=17;
	$pdf->Line($dist, 100, $dist, 211, $style3);
	$dist+=11;
	$pdf->Line($dist, 100, $dist, 211, $style3);
	$dist+=23;
	$pdf->Line($dist, 100, $dist, 211, $style3);
	$dist+=13;
	$pdf->Line($dist, 100, $dist, 211, $style3);	
	//**********************************************************
	//**********************************************************
	//se la spedizione è con vettore stampo i dati della scheda di trasporto
	if ($ddt->cod_mezzo->getVal()=='01'){
		$committente=$caricatore=$proprietario=$azienda;
		
		$vettore=$ddt->cod_destinatario->extend()->cod_vettore->extend();
		//si presenta il caso in cui la spedizione è stata fatta con vettore ma non sappiamo quale
		//perchè non ce ne è uno predefinito nel codice cliente quindi gliene assegnamo uno vuoto
		if($vettore==''){
			$vettore=new Vettore(array('_autoExtend'=>-1));
		}
		
		$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$pdf->RoundedRect(15, 226, 175, 25, 5.0, '1010', 'DF', $style, $def_bianco);
		$pdf->SetFont($def_font, '', $def_size-2);		
			
		$html = '<table><tr><td colspan="4" style="text-align:center;"><I><B>DATI SCHEDA DI TRASPORTO</B> (D.Lgs. 286/2005)</I></td></tr>';
		$html.= '<tr>';
		$html.= '<td><table><tr><td><b>Committente</b></td></tr>';
		$html.= '<tr><td>'.$committente->ragionesociale->getVal().'</td></tr>';
		$html.= '<tr><td>'.$committente->via->getVal().'</td></tr>';
		$html.= '<tr><td>'.$committente->cap->getVal().' '.$committente->paese->getVal(). ' ('.$committente->citta->getVal().')</td></tr>';
		$html.= '<tr><td>P.Iva: '.$committente->p_iva->getVal().'</td></tr>';
		$html.= '<tr><td>Cod.Fiscale: '.$committente->cod_fiscale->getVal().'</td></tr>';
		$html.= '</table></td>';

		$html.= '<td><table><tr><td><b>Caricatore</b></td></tr>';
		$html.= '<tr><td>'.$caricatore->ragionesociale->getVal().'</td></tr>';
		$html.= '<tr><td>'.$caricatore->via->getVal().'</td></tr>';
		$html.= '<tr><td>'.$caricatore->cap->getVal().' '.$caricatore->paese->getVal(). ' ('.$caricatore->citta->getVal().')</td></tr>';
		$html.= '<tr><td>P.Iva: '.$caricatore->p_iva->getVal().'</td></tr>';
		$html.= '<tr><td>Cod.Fiscale: '.$caricatore->cod_fiscale->getVal().'</td></tr>';
		$html.= '</table></td>';

		$html.= '<td><table><tr><td><b>Proprietario</b></td></tr>';
		$html.= '<tr><td>'.$proprietario->ragionesociale->getVal().'</td></tr>';
		$html.= '<tr><td>'.$proprietario->via->getVal().'</td></tr>';
		$html.= '<tr><td>'.$proprietario->cap->getVal().' '.$proprietario->paese->getVal(). ' ('.$proprietario->citta->getVal().')</td></tr>';
		$html.= '<tr><td>P.Iva: '.$proprietario->p_iva->getVal().'</td></tr>';
		$html.= '<tr><td>Cod.Fiscale: '.$proprietario->cod_fiscale->getVal().'</td></tr>';
		$html.= '</table></td>';

		$html.= '<td><table><tr><td><b>Vettore</b></td></tr>';
		$html.= '<tr><td>'.$vettore->ragionesociale->getVal().'</td></tr>';
		$html.= '<tr><td>'.$vettore->via->getVal().'</td></tr>';
		$html.= '<tr><td>'.$vettore->paese->getVal().'</td></tr>';
		$html.= '<tr><td></td></tr>';//$html.= '<tr><td>P.Iva: '.$vettore->p_iva->getVal().'</td></tr>';
		$html.= '<tr><td></td></tr>';//$html.= '<tr><td>Cod.Fiscale: '.$vettore->cod_fiscale->getVal().'</td></tr>';
		$html.= '<tr><td>Albo trasportatori: -</td></tr>';
		$html.= '</table></td>';

		$html.= '</tr></table>';
		$pdf->writeHTMLCell($w=175, $h=30, $x=15, $y=226, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

		//**********************************************************
		//**********************************************************
		$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$pdf->RoundedRect(15, 252, 175, 10, 5.0, '1010', 'DF', $style, $def_bianco);

		$pdf->SetFont($def_font, '', $def_size-3);
		$html= '<table>';
		$html.= '<tr><td>Luogo di carico</td><td>Luogo di scarico</td><td>Luogo e data di comilazione</td><td>Dati compilatore e firma</td></tr>';
		$html.= '</table>';
		$pdf->writeHTMLCell($w=175, $h=10, $x=15, $y=252, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
		$pdf->SetFont($def_font, '', $def_size);
		$html= '<table>';
		$html.= '<tr><td>'.$committente->paese->getVal(). ' ('.$committente->citta->getVal().') </td>';
		
		$destinazione=$ddt->cod_destinazione->extend();
		$html.= '<td>'.$destinazione->paese->getVal(). ' ('.$destinazione->citta->getVal().') </td>';
		$html.= '<td>'.$committente->paese->getVal(). ' ('.$committente->citta->getVal().') '.date('d/m/Y',$printTime).'</td>';
		$html.= '<td>'.$azienda->_titolare->getVal().'</td></tr>';
		$html.= '</table>';
		$pdf->writeHTMLCell($w=175, $h=10, $x=15, $y=256, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
	}
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 265, 120, 13, 5.0, '0000', 'DF', $style, $def_bianco);

	$pdf->SetFont($def_font, '', $def_size-4);
	$pdf->Text(15, 266, 'Condizioni di vendita');
	$html= '<span style="text-align:justify;">Estratto condizioni di vendita:  (1)  La merce viaggia a rischio del cliente anche se franco destino.  (2)  Eventuali reclami dovuti a perdite o avarie dovranno essere rivolti al vettore al momento del ritiro della merce.  (3)  Altre contestazioni e reclami dovranno pervenire in forma scritta entro 10 giorni dalla consegna.  (4)  Eventuali reclami riguardanti una singola consegna di merce non liberano l’acquirente dall’impegno di ritirare la restante entro i limiti dell’ordine.  (5)  I termini di pagamento sono tassativi. (6)  Noi conserviamo la proprietà della merce anche se in possesso del cliente fino al completo pagamento della stessa secondo le modalità pattuite.  (7)  Non si accettano resi di materiale se non prima autorizzati in forma scritta.  (8)  Per ogni azione legale il foro di competenza è quello della nostra provincia.</span>';
	//$pdf->writeHTML($w=120, $h=20, $x=15, $y=268, $html, true, 0, true, true);
	$pdf->writeHTMLCell($w=120, $h=20, $x=15, $y=268, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(15, 277, 120, 13, 5.0, '0000', 'DF', $style, $def_bianco);

	$pdf->SetFont($def_font, '', $def_size-4);
	$pdf->Text(15, 278, 'Annotazioni');
	$pdf->SetFont($def_font, '', $def_size-2);
	$html= '- Contributo CONAI assolto ove dovuto<br>- Peso da verificare all\'arrivo.<br>- '.$ddt->note->getVal().$ddt->note1->getVal().$ddt->note2->getVal();
	$pdf->writeHTMLCell($w=120, $h=10, $x=15, $y=280, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);
	//**********************************************************
	//**********************************************************
	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(135, 265, 55, 17, 5.0, '1000', 'DF', $style, $def_verde);

	$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$pdf->RoundedRect(135, 277, 55, 13, 5.0, '1000', 'DF', $style,  $def_bianco);

	$pdf->SetFont($def_font, '', $def_size-4);
	$pdf->Text(140, 266, 'Firma conducente');
	$pdf->SetFont($def_font, '', $def_size-4);
	$pdf->Text(140, 278, 'Firma detinatario');

	//inviamo il file pdf
	//$pdf->Output('DDT_'.$ddt->numero->getVal().'__'.$ddt->data->getVal().'.pdf', 'I');
	//salvo il file
	@$pdf->Output($GLOBALS['config']->pdfDir."/ddt/".$nomefile, 'F');	
}
?>