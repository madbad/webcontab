<?php
/* -------------------------------------------------------------------------------------------------------
Questa libreria esegue la stampa di un DDT
La devo adattare per ricevere in input un DDT completo
attualmente i campi sono tutti hardcoded
e lei ne prepara la stampa

----------------------------------------------------------------------------------------------------------
*/

$def_font='helvetica';
$def_size=8;
$def_verde= array(168,236,134);
$def_bianco= array(999,999,999);
 /*-----------------------------------------------------*/
require_once('./my/php/tcpdf/config/lang/eng.php');
require_once('./my/php/tcpdf/tcpdf.php');


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
$pdf->SetAuthor('La Favorita');
$pdf->SetTitle('Documento di Trasporto');
$pdf->SetSubject('Documento di Trasporto');
$pdf->SetKeywords('Documento di Trasporto');

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
$pdf->SetFont($def_font, 'I', $def_size-1);
$pdf->AddPage();
//logo + intestazione
$html = '<img src="./img/logo.gif" width="265" height="100"><br><span style="font-size:40px;font-weight:bold;">DI BRUN G. & G. S.R.L. Unipersonale</span>';
$html.= '<br>Via Camagre 38/B - 37063 Isola della Scala (Verona)';
$html.= '<br>Telefono 045 6630397 - Fax 045 7302598';
$html.= '<br>Capitale Sociale € 41.600,00 i.v.';
$html.= '<br>R.E.A. VR-185024';
$html.= '<br>Reg.Imprese di Verna, Codice Fiscale e Partita IVA 01588530236';
$html.= '<br>BNDOO n.001691/VR';
$pdf->writeHTMLCell($w=0, $h=0, $x='15', $y='5', $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=true);
$mod=14;
//bordo destinazione
$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->RoundedRect(110, 30+$mod, 80, 24, 5.0, '1010', 'DF', $style, $def_bianco);



//destinatario
$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->RoundedRect(110, 10+$mod, 80, 25, 5.0, '1010', 'DF', $style, array(200,200,200));
$pdf->SetFont($def_font, 'b', $def_size+3);
$pdf->Text(114, 11+$mod, 'La Favorita di Brun G. & G. Srl');
$pdf->Text(114, 15+$mod, 'Unipersonale');
$pdf->SetFont($def_font, '', $def_size);
$pdf->Text(114, 20+$mod, 'Via camagre 38/b');
$pdf->Text(114, 23+$mod, '37063 Isola della Scala (VERONA)');
$pdf->SetFont($def_font, 'b', $def_size+1);
$pdf->Text(114, 27+$mod, 'Partitita IVA: 01588530236');
$pdf->SetFont($def_font, '', $def_size);
$pdf->Text(114, 31+$mod, 'Codice Fiscale: 01588530236');

$pdf->SetFont(PDF_FONT_MONOSPACED, 'B', $def_size-5);
$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>T<BR>A<BR>R<BR>I<BR>O';
$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=20+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);
$html='D<BR>E<BR>S<BR>T<BR>I<BR>N<BR>A<BR>Z<BR>I<BR>O<BR>N<BR>E';
$pdf->writeHTMLCell($w=0, $h=0, $x='110', $y=44+4, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='CENTER', $autopadding=true);


//dati intestazione ddt
$pdf->SetFont($def_font, 'b', $def_size+3);
$pdf->Text(114, 37+$mod, 'La Favorita di Brun G. & G. Srl');
$pdf->Text(114, 41+$mod, 'Unipersonale');
$pdf->SetFont($def_font, '', $def_size);
$pdf->Text(114, 45+$mod, 'Via camagre 38/b');
$pdf->Text(114, 49+$mod, '37063 Isola della Scala (VERONA)');
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
$pdf->Text(98, 74, '2507');
//
$pdf->SetFont($def_font, '', $def_size-3);
$pdf->Text(125, 71, 'Data Doc.');
$pdf->SetFont($def_font, '', $def_size+5);
$pdf->Text(125, 74, '20/08/2010');
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
$pdf->Text(18, 86, 'Vendita');
//
$pdf->SetFont($def_font, '', $def_size-3);
$pdf->Text(58, 83, 'Aspetto dei beni');
$pdf->SetFont($def_font, '', $def_size+5);
$pdf->Text(58, 86, 'Visibile');
//
$pdf->SetFont($def_font, '', $def_size-3);
$pdf->Text(98, 83, 'Trasporto a mezzo');
$pdf->SetFont($def_font, '', $def_size+5);
$pdf->Text(98, 86, 'Vettore');
//
$pdf->SetFont($def_font, '', $def_size-3);
$pdf->Text(125, 83, 'Inizio trasporto');
$pdf->SetFont($def_font, '', $def_size+5);
$pdf->Text(125, 86, 'il 20/08/2010 alle 11:47');

//**********************************************************
//**********************************************************

function MyOwnRow($a1,$a2,$a3,$a4,$a5,$a6,$a7,$a8,$a9){
	$out= "</tr><tr><td width='70px;'>$a1</td><td  width='200px;'>$a2</td><td width='40px;'>$a3</td><td width='60px;'>$a4</td><td width='40px;'>$a5</td><td width='80px;'>$a6</td><td  width='50px;'>$a7</td><td  width='80px;'>$a8</td>";
	if ($a9!=''){
		$out.="</tr><tr><td></td><td colspan='3'><span style='font-size:4px;'>         Lotto: $a9</span></td><td></td><td></td><td></td><td></td>";
	}
	return $out;
}

$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->RoundedRect(15, 95, 175, 118, 5.0, '0000', 'DF', $style, $def_bianco);
$pdf->SetFont($def_font, '', $def_size-3);
$html = '<table style="border:0px solid #000000;margin:0px;padding:0px;text-align:left;"><tr>';
$html.= '<td width="70px;">Cod.Articolo</td><td  width="200px;">Descrizione dei Beni (natura e qualita)</td><td width="40px;">Colli</td><td width="60px;">Prezzo</td><td width="40px;">U.M.</td><td width="80px;">Peso Lordo</td><td  width="50px;">Tara</td><td  width="80px;">Peso netto</td>';

$pdf->SetFont($def_font, '', $def_size);
$html.= MyOwnRow('','','','','','','','','' );

$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15Aooo999999999999999' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );
$html.= MyOwnRow('01G','Indivia Riccia ITALIA','10','€ 1,20','Kg.','10.548','1.456','10.999','FT186/P-13/10/2010-B/15A' );

$html.= '</tr></table>';

$pdf->writeHTMLCell($w=175, $h=10, $x=15, $y=95.5, $html, $border=1, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

//**********************************************************
//**********************************************************

$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->RoundedRect(15, 215, 175, 10, 5.0, '1010', 'DF', $style, $def_bianco);
//$pdf->SetFont($def_font, '', $def_size-3);
$html = '<table style="border:0px solid #000000;margin:0px;padding:0px;text-align:left;"><tr>';
$html.= '<td width="70px;"> </td><td  width="200px;"> </td><td width="40px;">Colli</td><td width="100px;" colspan="2">Imponibile</td><td width="80px;">Peso Lordo</td><td  width="50px;">Tara</td><td  width="80px;">Peso netto</td>';

$html.= MyOwnRow('<b>Totali</b>','','1.000','€ 11.521,20','','10.548','1.456','10.999','' );

$html.= '</tr></table>';
$pdf->writeHTMLCell($w=175, $h=10, $x=15, $y=216, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

//**********************************************************
//**********************************************************
$pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
$pdf->RoundedRect(15, 226, 175, 25, 5.0, '1010', 'DF', $style, $def_bianco);
$pdf->SetFont($def_font, '', $def_size-2);
$html = '<table><tr><td colspan="4" style="text-align:center;"><I><B>DATI SCHEDA DI TRASPORTO</B> (D.Lgs. 286/2005)</I></td></tr>';
$html.= '<tr>';
$html.= '<td><table><tr><td><b>Committente</b></td></tr>';
$html.= '<tr><td>La Favorita di Brun G. & G. Srl Unip.</td></tr>';
$html.= '<tr><td>Via San rocco, 14</td></tr>';
$html.= '<tr><td>37063 Pellegrina di Isola della Scala VR</td></tr>';
$html.= '<tr><td>P.Iva: 01588530236</td></tr>';
$html.= '<tr><td>Cod.Fiscale: 01588530236</td></tr>';
$html.= '</table></td>';

$html.= '<td><table><tr><td><b>Caricatore</b></td></tr>';
$html.= '<tr><td>La Favorita di Brun G. & G. Srl Unip.</td></tr>';
$html.= '<tr><td>Via San rocco, 14</td></tr>';
$html.= '<tr><td>37063 Pellegrina di Isola della Scala VR</td></tr>';
$html.= '<tr><td>P.Iva: 01588530236</td></tr>';
$html.= '<tr><td>Cod.Fiscale: 01588530236</td></tr>';
$html.= '</table></td>';

$html.= '<td><table><tr><td><b>Proprietario</b></td></tr>';
$html.= '<tr><td>La Favorita di Brun G. & G. Srl Unip.</td></tr>';
$html.= '<tr><td>Via San rocco, 14</td></tr>';
$html.= '<tr><td>37063 Pellegrina di Isola della Scala VR</td></tr>';
$html.= '<tr><td>P.Iva: 01588530236</td></tr>';
$html.= '<tr><td>Cod.Fiscale: 01588530236</td></tr>';
$html.= '</table></td>';

$html.= '<td><table><tr><td><b>Vettore</b></td></tr>';
$html.= '<tr><td>La Favorita di Brun G. & G. Srl Unip.</td></tr>';
$html.= '<tr><td>Via San rocco, 14</td></tr>';
$html.= '<tr><td>37063 Pellegrina di Isola della Scala VR</td></tr>';
$html.= '<tr><td>P.Iva: 01588530236</td></tr>';
$html.= '<tr><td>Cod.Fiscale: 01588530236</td></tr>';
$html.= '<tr><td>Albo trasportatori: TN2052205L</td></tr>';
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
$pdf->SetFont($def_font, '', $def_size+2);
$html= '<table>';
$html.= '<tr><td>Isola della Scala(VR) </td><td>Milano (MI)</td><td>Isola della scala 31/08/10</td><td>Brun Gionni</td></tr>';
$html.= '</table>';
$pdf->writeHTMLCell($w=175, $h=10, $x=15, $y=256, $html, $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);

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
$html= '- Contributo CONAI assolto ove dovuto<br>- Peso da verificare all\'arrivo.';
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



$pdf->Output('DDT n.', 'I');

?>