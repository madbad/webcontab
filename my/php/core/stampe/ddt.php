<?php
/* -------------------------------------------------------------------------------------------------------
	Questa libreria esegue la stampa di un DDT
	La devo adattare per ricevere in input un DDT completo
	attualmente i campi sono tutti hardcoded
	e lei ne prepara la stampa
----------------------------------------------------------------------------------------------------------
*/
/*
TODO:
- verifica per più di una pagina
- modifica se presente solo destinatario e non destinazione
- modifica per orario di stampa
- modifica per causale ** c/commissione
*/
error_reporting(-1); //0=spento || -1=acceso
function buildEmptyModule($pdf){

	$style='';
	$def_font='helvetica';
	$def_size=8;
	$def_verde= array(85,190,180);
	$def_bianco= array(999,999,999);
	/*##############################
	  #   RIQUADRI                 #  
	  ##############################*/
	//stile della linea dei riquadri
	$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $def_verde));
	//RIQUADRO DESTINATARIO
	$pdf->RoundedRect($x=107, $y=24, $w=72, $h=32.6, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO NUMERO DOCUMENTO
	$pdf->RoundedRect($x=17.5, $y=56.5, $w=22.8, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO DATA DOCUMENTO
	$pdf->RoundedRect($x=40.3, $y=56.5, $w=28, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TIPOMERCE
	$pdf->RoundedRect($x=68.3, $y=56.5, $w=110.7, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO CAUSALE DEL TRASPORTO
	$pdf->RoundedRect($x=17.5, $y=64.5, $w=43.8, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO CAUSALE A MEZZO
	$pdf->RoundedRect($x=61.3, $y=64.5, $w=43.8, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO INIZIO TRASPORTO
	$pdf->RoundedRect($x=105.1, $y=64.5, $w=60, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO PAGINA
	$pdf->RoundedRect($x=165.1, $y=64.5, $w=13.9, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO COD.ARTICOLO
	$pdf->RoundedRect($x=17.5, $y=72.5, $w=18.5, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO DESCRIZIONE BENI
	$pdf->RoundedRect($x=36, $y=72.5, $w=83, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO UM
	$pdf->RoundedRect($x=119, $y=72.5, $w=7, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO COLLI
	$pdf->RoundedRect($x=126, $y=72.5, $w=11, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO PESO LORDO
	$pdf->RoundedRect($x=137, $y=72.5, $w=16.5, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO PESO NETTOLORDO
	$pdf->RoundedRect($x=153.5, $y=72.5, $w=16.5, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TARA
	$pdf->RoundedRect($x=170, $y=72.5, $w=9, $h=153, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO ASPETTO DEI BENI
	$pdf->RoundedRect($x=17.5, $y=225.5, $w=119.5, $h=7, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TOT COLLI
	$pdf->RoundedRect($x=137, $y=225.5, $w=21, $h=7, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO TOT PESO
	$pdf->RoundedRect($x=158, $y=225.5, $w=21, $h=7, 2.0, '1111', 'DF', $style, $def_bianco);
	
	//RIQUADRO FIRMA VETTORE
	$pdf->RoundedRect($x=17.5, $y=232.5, $w=101.5, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO FIRMA DEL CONDUCENTE
	$pdf->RoundedRect($x=119, $y=232.5, $w=60, $h=8, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO INCARICATO DEL TRASPORTO
	$pdf->RoundedRect($x=17.5, $y=240.5, $w=101.5, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO DATA E ORA DEL RITIRO
	$pdf->RoundedRect($x=119, $y=240.5, $w=60, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);

	//RIQUADRO ANNOTAZIONI
	$pdf->RoundedRect($x=17.5, $y=251.5, $w=101.5, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);
	//RIQUADRO FIRMA DESTINATARIO
	$pdf->RoundedRect($x=119, $y=251.5, $w=60, $h=11, 2.0, '1111', 'DF', $style, $def_bianco);

	/*##############################
	  #   SCRITTE RIQUADRI         #  
	  ##############################*/
	$def_font='helvetica';
	$def_size=4.5;

	//
	$pdf->SetTextColor(109,109,109);
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->Text($x=107, $y=24+0.5, "DESTINATARIO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=17.5, $y=56.5+0.5, "NUM.DOC.");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=40.3, $y=56.5+0.5, "DATA.DOC.");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=68.3, $y=56.5+0.5, "TIPO MERCE");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=17.5, $y=64.5+0.5, "CAUSALE DEL TRASPORTO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=61.3, $y=64.5+0.5, "A MEZZO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=105.1, $y=64.5+0.5, "INIZIO TRASPORTO");
	//----DIFF---
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=105.1, $y=69.5+0.5, "DATA");
	//----DIFF---
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=139.1, $y=69.5+0.5, "ORA");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=165.1, $y=64.5+0.5, "PAGINA");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=20, $y=72.5+0.5, "COD.ARTICOLO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=60, $y=72.5+0.5, "DESCRIZIONE DEI BENI (Natura - Qualita')");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=119.5, $y=72.5+0.5, "U.M.");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=128, $y=72.5+0.5, "COLLI");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=138.8, $y=72.5+0.5, "PESO LORDO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=155.5, $y=72.5+0.5, "PESO NETTO");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=171.5, $y=72.5+0.5, "TARA");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=17.5, $y=225.5+0.5, "ASPETTO ESTERIORE DEI BENI");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=137, $y=225.5+0.5, "N. COLLI");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=158, $y=225.5+0.5, "PESO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=17.5, $y=232.5+0.5, "FIRMA DEL VETTORE");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=119, $y=232.5+0.5, "FIRMA DEL CONDUCENTE");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=17.5, $y=240.5+0.5, "INCARICATO DEL TRASPORTO: DITTA, RESIDENZA, O DOMICILIO (Comune, Via, N.)");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=119, $y=240.5+0.5, "DATA E ORA DEL RITIRO");
	//
	$pdf->SetFont($def_font, '', $def_size);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=17.5, $y=251.5+0.5, "ANNOTAZIONI - VARIAZIONI");
	//
	$pdf->SetFont($def_font, 'B', $def_size);
	$pdf->SetTextColor(85,190,180);
	$pdf->Text($x=119, $y=251.5+0.5, "FIRMA DEL DESTINATARIO");
	
	/*##############################
	  #   INTESTAZIONE         #  
	  ##############################*/
  	$azienda=$GLOBALS['config']->azienda;
	$html= '<img src="'.$azienda->_logo->getVal().'" width="238" height="90">';
	$pdf->writeHTMLCell($w=0, $h=0, $x='17.5', $y='5', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);	  
	  
	  
	$pdf->SetFont($def_font, 'B', $def_size+4);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=17.5, $y=33, "DI BRUN G. & G. S.R.L. Unipersonale"); 

	$pdf->SetFont($def_font, '', $def_size+2);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=17.5, $y=37, "Via Camagre, 38/B - 37063 ISOLA DELLA SCALA (Verona)");
	$pdf->Text($x=17.5, $y=40, "Telefono 045 6630397 - Fax 045 7302598");
	$pdf->Text($x=17.5, $y=43, "Capitale Sociale € 41.600,00 i.v.");
	$pdf->Text($x=17.5, $y=46, "R.E.A. VR- 185024");
	$pdf->Text($x=17.5, $y=49, "Reg.Imprese di Verona, Codice Fiscale e Partita IVA 01588530236");
	$pdf->Text($x=17.5, $y=52, "BNDOO n° 0001691/VR - Indirizzo PEC: lafavorita_srl@pec.it");
	
	/*##############################
	  #   SCRITTE VARIE            #  
	  ##############################*/
	// ddt
	$pdf->SetFont($def_font, 'B', $def_size+3);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=110, $y=18, "DOCUMENTO DI TRASPORTO");

	// ddt
	$pdf->SetFont($def_font, '', $def_size+1.5);
	$pdf->SetTextColor(109,109,109);
	$pdf->Text($x=150, $y=19, "(D.P.R. 472 del 18/08/96)");
	
	// reclami (sul fondo)
	$pdf->SetFont($def_font, 'B', $def_size+3);
	$pdf->SetTextColor(0,0,0);
	$pdf->Text($x=50, $y=270, "NON SI ACCETTANO RECLAMI DOPO L'USCITA DELLA MERCE DAL MAGAZZINO");
	
	// codice (sul lato sinistro
	$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $def_bianco));
	$pdf->SetFont($def_font, 'B', $def_size+2);
	$pdf->StartTransform();
$pdf->setXY(14,226.5);
	$pdf->Rotate(90);
	$pdf->Cell(0,0,'CODICE DI CONVERSIONE ALFA/NUMERICO D.M. 30.03.92: A=1 E=2 G=3 H=4 M=5 P=6 S=7 T=8 K=9 Z=0 ,=,',1,1,'L',0,'');
	//$pdf->Text($x=100, $y=100, "CODICE DI CONVERSIONE ALFA/NUMERICO D.M. 30.03.92: A=1 E=2 G=3 H=4 M=5 P=6 S=7 T=8 K=9 Z=0 ,=,");
	$pdf->StopTransform();	
	
	$pdf->SetTextColor(0,0,0);
}

function addIntestazioneDdt ($pdf){
//	$html= '<img src="'.realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/img/ddt.svg" height="1040">';
//	$html= '<img src="'.realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/img/ddt.png" height="1040">';

	$pdf->writeHTMLCell($w=0, $h=0, $x='0', $y='0', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);

}

function addDestinatarioDdt ($ddt,$pdf){
	$mod=28;
	$riga=4;
	$def_font='helvetica';
	$def_size=7;
	
	$fromLeft=107;
	
	$destinatario=$ddt->cod_destinatario->extend();	
	$pdf->SetFont($def_font, 'b', $def_size+1.4);
	$pdf->Text($fromLeft, 0*$riga+$mod, $destinatario->ragionesociale->getVal());
	$pdf->SetFont($def_font, '', $def_size);
	
	$pdf->Text($fromLeft, 1*$riga+$mod, $destinatario->via->getVal());
	$pdf->Text($fromLeft, 2*$riga+$mod, $destinatario->cap->getVal().' '.$destinatario->paese->getVal(). ' ('.$destinatario->citta->getVal().')');
	//$pdf->SetFont($def_font, 'b', $def_size+1);
	//$pdf->Text($fromLeft, 3*$riga+$mod, 'Partitita IVA: '.$destinatario->p_iva->getVal());
	//$pdf->SetFont($def_font, '', $def_size);
	//$pdf->Text($fromLeft, 4*$riga+$mod, 'Codice Fiscale: '.$destinatario->cod_fiscale->getVal());

}
function addDestinazioneDdt ($ddt,$pdf){
	$mod=44;
	$riga=4;
	$def_font='helvetica';
	$def_size=7;

	$fromLeft=107;
	
	//HACK
	//$ddt->cod_destinazione->setVal('OROGE');
	
	$destinazione=$ddt->cod_destinazione->extend();

	$pdf->SetFont($def_font, '', $def_size+1.4);
	$html='<i style="text-align:center">- - - - - - - - - - -   DESTINAZIONE   - - - - - - - - - - -</i>';	
	$pdf->writeHTMLCell($w=70, $h=4, $fromLeft, $mod-4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='center', $autopadding=false);

	
	$pdf->SetFont($def_font, 'b', $def_size+1.4);
	$pdf->Text($fromLeft, 0*$riga+$mod, $destinazione->ragionesociale->getVal());
	$pdf->SetFont($def_font, '', $def_size);
	
	$pdf->Text($fromLeft, 1*$riga+$mod, $destinazione->via->getVal());
	$pdf->Text($fromLeft, 2*$riga+$mod, $destinazione->cap->getVal().' '.$destinazione->paese->getVal(). ' ('.$destinazione->citta->getVal().')');
}

function generaPdfDdt($ddt){
	$massimoNumeroRigheCorpo=30;
	$def_font='helvetica';
	$def_size=12;

	// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	//load some custom fonts
	//$pdf->addTTFfont(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/fonts/SourceSansPro-Regular.ttf', 'TrueTypeUnicode', '', 32);
	//$pdf->addTTFfont(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/fonts/ARIALN.TTF', 'TrueTypeUnicode', '', 32);
	//$pdf->AddFont('arial','',realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/fonts/ARIALN.php');
	//$pdf->AddFont('ArialNarrowB','B','ARIALNB.php');
	//$pdf->AddFont('ArialNarrowBI','BI','ARIALNBI.php');
	//$fontname = TCPDF_FONTS::addTTFfont(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/fonts/ARIALN.TTF', 'TrueTypeUnicode', '', 96);
	
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
	$pdf->AddPage();
	
buildEmptyModule($pdf);
	
	//immagine di sfondo del DDT inclusa l'intestazione
	addIntestazioneDdt($pdf);
	
	//destinatatio
	addDestinatarioDdt($ddt, $pdf);	
	
	//destinazione se diversa dal destinatario
	if($ddt->cod_destinazione->getVal()!=''){
		addDestinazioneDdt($ddt, $pdf);
	}	

	//
	$pdf->SetFont($def_font, '', $def_size);

	//numero
	$pdf->Text(18, 58, $ddt->numero->getVal());
	
	//data
	$pdf->Text(41, 58, $ddt->data->getFormatted());
	
	//pagina
	$pdf->Text(170, 58+8, '1/1');
	
	//causale del trasporto
	if($ddt->cod_causale->getVal()=='V'){
		//si tratta di "VENDITA" "C/COMMISSIONE"
		$causale='VENDITA';
	}else if($ddt->cod_causale->getVal()=='D'){
		//si tratta di "redo da c/deposito" "c/riparazone" "omaggio" etc...
		$causale='RESO DA C/DEP.TO';
	}
	$pdf->Text(18, 58+8, $causale);

	//trasporto a mezzo
	$pdf->Text(61, 58+8, $ddt->cod_mezzo->extend()->descrizione->getVal());
	
	//data
	$printTime=time();
	
	//$pdf->Text(112, 58+8, date('d/m/Y',$printTime));//todo: rendere dinamico
	$pdf->Text(112, 58+8, $ddt->data->getFormatted());//todo: rendere dinamico
	
	//ora
	$pdf->Text(145, 58+8, date('H:i',$printTime));//todo: rendere dinamico
	
	//aspetto dei beni
	$pdf->Text(18, 58+8*21.2, 'VISIBILE');/*todo*/
	
	//totale colli
	$pdf->Text(140, 58+8*21.2, $ddt->tot_colli->getFormatted(0));	
	
	//totale peso lordo
	$pdf->Text(160, 58+8*21.2, $ddt->tot_peso->getFormatted(2));
	
	//note
	$pdf->SetFont($def_font, '', $def_size-3);
	$pdf->Text(18, 58+8*24.5, $ddt->note->getVal());
	$pdf->Text(18, 58+8*25.5, $ddt->note1->getVal().$ddt->note2->getVal());
	//**********************************************************
	//**********************************************************
	$righeCorpoUsate=0;
	function MyOwnDdtRow($a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8){
		$righeCorpoUsate++;
		$toRigth='style="text-align:rigth;" align="rigth"';

		$html= "<tr>";
		$html.= "<td width='70px;'>$a1</td>"; //ARTICOLO
		$html.= "<td  width='245px;'>$a2</td>"; //DESCRIZIONE
		$html.= "<td width='40px;' $toRigth>$a3</td>"; //PREZZO
		$html.= "<td width='25px;' $toRigth>$a4</td>"; //UM
		$html.= "<td width='40px;' $toRigth>$a5</td>"; //COLLI
		$html.= "<td width='56px;' $toRigth>$a6</td>"; //LORDO
		$html.= "<td width='56px;' $toRigth>$a7</td>"; //NETTO
		$html.= "<td  width='35px;' $toRigth>$a8</td>"; //TARA
		$html.= "</tr>";
		return $html;
	}

	$html = '<table style="border:0px solid #000000;margin:0px;padding:5px;">';
	foreach ($ddt->righe as $key => $value) {
		$riga=$ddt->righe[$key];

		//riga normale
		$html.= MyOwnDdtRow(	$riga->cod_articolo->getVal(),
							$riga->descrizione->getVal(),
							($riga->prezzo->getVal()*1>0 ? $riga->prezzo->getFormatted(2) : ''),
							$riga->unita_misura->getVal(),
							($riga->colli->getVal()*1>0 ? $riga->colli->getFormatted(0) : ''),
							($riga->peso_lordo->getVal()*1>0 ? $riga->peso_lordo->getFormatted(2): ''), //NETTO
							($riga->peso_netto->getVal()*1>0 ? $riga->peso_netto->getFormatted(2) : ''), //peso lordo
							($riga->peso_lordo->getVal()*1>0 ? number_format ($riga->peso_lordo->getVal()-$riga->peso_netto->getVal(),1): '') //todoTara
							);
							
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

	$html.= '</table>';
	$pdf->SetFont($def_font, '', $def_size-5);	
	$pdf->writeHTMLCell($w=175, $h=10, $x=17, $y=75, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='right', $autopadding=false);

	//**********************************************************
	//**********************************************************
	//se la spedizione è con vettore stampo i suoi dati
	if ($ddt->cod_mezzo->getVal()=='01'){
		//$vettore=$ddt->cod_destinatario->extend()->cod_vettore->extend();
		
		$destinatario=$ddt->cod_destinatario->extend();
		
		//SE NON è IMPOSTATO NESSUN VETTORE PRESUMO CHE SIA LA TRANSLUSIA
		if ($destinatario->cod_vettore->getVal() *1 == 0){
			$destinatario->cod_vettore->setVal('02');
		} 

		//MODIFICO IL VETTORE A MIO PIACIMENTO
		//$destinatario->cod_vettore->setVal('13');//02=translusia	24=facchini
		
		$vettore= $destinatario->cod_vettore->extend();

		//si presenta il caso in cui la spedizione è stata fatta con vettore ma non sappiamo quale
		//perchè non ce ne è uno predefinito nel codice cliente quindi gliene assegnamo uno vuoto
		if($vettore==''){
			$vettore=new Vettore(array('_autoExtend'=>-1));
		}
		
		//imposto le dimensioni del font
		$pdf->SetFont($def_font, '', $def_size-3);

		//rag.sociale vettore
		$pdf->Text(18, 58+8*23.2, $vettore->ragionesociale->getVal());

		//indirizzo
		$pdf->Text(18, 58+8*23.7, $vettore->via->getVal().' - '.$vettore->paese->getVal());	

	}

	//inviamo il file pdf
	//$pdf->Output('DDT_'.$ddt->numero->getVal().'__'.$ddt->data->getVal().'.pdf', 'I');
	$nomefile=$ddt->getPdfFileName();

	@$pdf->Output($GLOBALS['config']->pdfDir."/ddt/".$nomefile, 'F');	
}
?>