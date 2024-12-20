<?php

$fatturaIntegrativa = new stdClass();
$fatturaIntegrativa->data = "2024-10-21";  //la data di ricezione
$fatturaIntegrativa->numero = "1"; //il progressivo di protocollo reverse charge
$fatturaIntegrativa->progressivoSDI = "2789"; //il progressivo di protocollo reverse charge

$fatturaEstera = new stdClass();
$fatturaEstera->data = "2024-10-02";
$fatturaEstera->numero = "252";

$fornitore = new stdClass();
$fornitore->ragione_sociale = "PROMAPRIM SAS";
$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
$fornitore->cap =             "30400";
$fornitore->paese =           "VILLENEUVE LES AVIGNON";
$fornitore->nazione =         "FR";
$fornitore->piva =            "57428690739";

$righe = array();
//DESCRIZIONE, PESO, UM, PREZZO, IVA
$righe[] = "INS.SCAROLA, 974.00, KG, 2.00, 4.0";
$righe[] = "INS.RICCIA, 346.00, KG, 2.00, 4.0";


/*
$fatturaIntegrativa = new stdClass();
$fatturaIntegrativa->data = "2024-10-21";  //la data di ricezione
$fatturaIntegrativa->numero = "2"; //il progressivo di protocollo reverse charge
$fatturaIntegrativa->progressivoSDI = "2790"; //il progressivo di protocollo reverse charge

$fatturaEstera = new stdClass();
$fatturaEstera->data = "2024-10-04";
$fatturaEstera->numero = "259";

$fornitore = new stdClass();
$fornitore->ragione_sociale = "PROMAPRIM SAS";
$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
$fornitore->cap =             "30400";
$fornitore->paese =           "VILLENEUVE LES AVIGNON";
$fornitore->nazione =         "FR";
$fornitore->piva =            "57428690739";

$righe = array();
//DESCRIZIONE, PESO, UM, PREZZO, IVA
$righe[] = "INS.SCAROLA, 1338.00, KG, 2.00, 4.0";
$righe[] = "INS.RICCIA, 659.00, KG, 2.00, 4.0";
*/

/*
$fatturaIntegrativa = new stdClass();
$fatturaIntegrativa->data = "2024-10-21";  //la data di ricezione
$fatturaIntegrativa->numero = "3"; //il progressivo di protocollo reverse charge
$fatturaIntegrativa->progressivoSDI = "2791"; //il progressivo di protocollo reverse charge

$fatturaEstera = new stdClass();
$fatturaEstera->data = "2024-10-07";
$fatturaEstera->numero = "261
";

$fornitore = new stdClass();
$fornitore->ragione_sociale = "PROMAPRIM SAS";
$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
$fornitore->cap =             "30400";
$fornitore->paese =           "VILLENEUVE LES AVIGNON";
$fornitore->nazione =         "FR";
$fornitore->piva =            "57428690739";

$righe = array();
//DESCRIZIONE, PESO, UM, PREZZO, IVA
$righe[] = "INS.SCAROLA, 817.00, KG, 1.80, 4.0";
$righe[] = "INS.RICCIA, 1600.00, KG, 1.80, 4.0";
*/



/*
$fatturaIntegrativa = new stdClass();
$fatturaIntegrativa->data = "2024-10-21";  //la data di ricezione
$fatturaIntegrativa->numero = "4"; //il progressivo di protocollo reverse charge
$fatturaIntegrativa->progressivoSDI = "2792"; //il progressivo di protocollo reverse charge

$fatturaEstera = new stdClass();
$fatturaEstera->data = "2024-10-10";
$fatturaEstera->numero = "266";

$fornitore = new stdClass();
$fornitore->ragione_sociale = "PROMAPRIM SAS";
$fornitore->indirizzo =       "64 AVENUE GABRIEL PERI";
$fornitore->cap =             "30400";
$fornitore->paese =           "VILLENEUVE LES AVIGNON";
$fornitore->nazione =         "FR";
$fornitore->piva =            "57428690739";

$righe = array();
//DESCRIZIONE, PESO, UM, PREZZO, IVA
$righe[] = "INS.RICCIA, 691.00, KG, 1.80, 4.0";

*/






function calcolaTotale(){
	global $righe;
	$totaleFattura = 0;
	foreach($righe as $riga){
		list($descrizione, $peso, $um, $prezzo, $iva) = explode(",", $riga);
		$totaleFattura += $peso*$prezzo * ($iva+100)/100;
	}
	return $totaleFattura;
}

//salvo loutput in una varibile
ob_start();
?>
<?xml version="1.0"?>
<p:FatturaElettronica xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" versione="FPR12" xsi:schemaLocation=" http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 https://www.agenziaentrate.gov.it/portale/documents/20143/2931841/Schema_VFPR12.xsd">
  <FatturaElettronicaHeader>
    <DatiTrasmissione>
      <IdTrasmittente>
        <IdPaese>IT</IdPaese>
        <IdCodice>01588530236</IdCodice>
      </IdTrasmittente>
      <ProgressivoInvio><?php echo $fatturaIntegrativa->progressivoSDI; ?></ProgressivoInvio>
      <FormatoTrasmissione>FPR12</FormatoTrasmissione>
      <CodiceDestinatario>0000000</CodiceDestinatario>
    </DatiTrasmissione>
    <CedentePrestatore>
      <DatiAnagrafici>
        <IdFiscaleIVA>
          <IdPaese><?php echo $fornitore->nazione; ?></IdPaese>
          <IdCodice><?php echo $fornitore->piva; ?></IdCodice>
        </IdFiscaleIVA>
        <Anagrafica>
          <Denominazione><?php echo $fornitore->ragione_sociale; ?></Denominazione>
        </Anagrafica>
        <RegimeFiscale>RF01</RegimeFiscale>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo><?php echo $fornitore->indirizzo; ?></Indirizzo>
        <CAP><?php echo $fornitore->cap; ?></CAP>
        <Comune><?php echo $fornitore->paese; ?></Comune>
        <Nazione><?php echo $fornitore->nazione; ?></Nazione>
      </Sede>
    </CedentePrestatore>
    <CessionarioCommittente>
      <DatiAnagrafici>
        <IdFiscaleIVA>
          <IdPaese>IT</IdPaese>
          <IdCodice>01588530236</IdCodice>
        </IdFiscaleIVA>
        <CodiceFiscale>01588530236</CodiceFiscale>
        <Anagrafica>
            <Denominazione>LA FAVORITA DI BRUN G. &amp; G. SRL UNIP.</Denominazione>
        </Anagrafica>
      </DatiAnagrafici>
      <Sede>
        <Indirizzo>Camagre 38/B</Indirizzo>
        <CAP>37063</CAP>
        <Comune>Isola della Scala</Comune>
        <Provincia>VR</Provincia>
        <Nazione>IT</Nazione>
      </Sede>
    </CessionarioCommittente>
  </FatturaElettronicaHeader>
  <FatturaElettronicaBody>
    <DatiGenerali>
      <DatiGeneraliDocumento>
        <TipoDocumento>TD18</TipoDocumento>
        <Divisa>EUR</Divisa>
        <Data><?php echo $fatturaIntegrativa->data;?></Data>
        <Numero><?php echo $fatturaIntegrativa->numero;?>/2024/RCE</Numero>
        <ImportoTotaleDocumento><?php echo number_format(calcolaTotale(), 2,".",""); ?></ImportoTotaleDocumento>
        <Causale>INTEGRAZIONE FATTURA NUMERO <?php echo $fatturaEstera->numero;?> DEL <?php echo $fatturaEstera->data; ?></Causale>
      </DatiGeneraliDocumento>
      <DatiFattureCollegate>
        <IdDocumento><?php echo $fatturaEstera->numero;?></IdDocumento>
        <Data><?php echo $fatturaEstera->data;?></Data>
      </DatiFattureCollegate>
    </DatiGenerali>
    <DatiBeniServizi>
<?php
$contariga = 0;
$aliquote = array();
foreach($righe as $riga){
	list($descrizione, $peso, $um, $prezzo, $iva) = explode(",", $riga);
	$contariga++; 
	$importo = $peso*$prezzo;
echo"
	<DettaglioLinee>
        <NumeroLinea>$contariga</NumeroLinea>
        <Descrizione>".trim($descrizione)."</Descrizione>
        <Quantita>".trim($peso)."</Quantita>
        <UnitaMisura>".trim($um)."</UnitaMisura>
        <PrezzoUnitario>".trim($prezzo)."</PrezzoUnitario>
        <PrezzoTotale>".number_format($importo, 2,".","")."</PrezzoTotale>
        <AliquotaIVA>".trim($iva)."</AliquotaIVA>
     </DettaglioLinee>
";
	@$aliquote[trim($iva)]  += $importo;
}
?>
      <DatiRiepilogo>
<?php
foreach($aliquote as $aliquotaKey => $aliquotaValue ){
	echo "
		<AliquotaIVA>$aliquotaKey</AliquotaIVA>
        <ImponibileImporto>".number_format($aliquotaValue,2,".","")."</ImponibileImporto>
        <Imposta>".number_format($aliquotaKey * $aliquotaValue/100,2,".","")."</Imposta>
        <EsigibilitaIVA>I</EsigibilitaIVA>
";
}
?>
      </DatiRiepilogo>
    </DatiBeniServizi>
  </FatturaElettronicaBody>
</p:FatturaElettronica>
<?php
$nomeFile = 'IT01588530236_'.$fatturaIntegrativa->progressivoSDI.'.xml';

//ottengo l'output dal buffer
$xml = ob_get_contents();
ob_end_clean();


$myfile = fopen("./integrazioneFtIntrastat/".$nomeFile, "w") or die("Unable to open file!");
fwrite($myfile, $xml);
fclose($myfile);
?>
