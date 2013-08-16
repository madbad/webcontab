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


function addIntestazioneDdt ($pdf){
	$html= '<img src="'.realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/'.'/img/ddt.svg" height="1040">';
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
		
		//MODIFICO IL VETTORE A MIO PIACIMENTO
		$destinatario->cod_vettore->setVal('02');//02=translusia	24=facchini
		
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