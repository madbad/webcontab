<?php
/*
include(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/php-validatore-fattura-elettronica/lib/ValidatorInterface.php');
include(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/php-validatore-fattura-elettronica/lib/Validator.php');
include(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/php-validatore-fattura-elettronica/lib/Exception/ExceptionInterface.php');
include(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/php-validatore-fattura-elettronica/lib/Exception/InvalidXmlStructureException.php');
include(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/libs/php-validatore-fattura-elettronica/lib/Exception/InvalidXsdStructureComplianceException.php');
*/
//da verificare
//http://127.0.0.1:8887/webcontab/my/php/core/gestioneFatture.php?numero=%20%20%20%20%20%2075&data=02-28-2018&tipo=F&do=generaXml
//tutte le note di acrredito
/*
RIF. NS. DDT.N.3268-26.12.17
VEDI NS. FATT.N.547-30.12.2017


RESO CON DDT.N.15/B-09.02.18
VEDI NS. FATT.N.61-28.02.18
..............................
RIF. NS.DDT N.293-03.02.18
            N.284-02.2.18
            N.293-03.02.18
            N.284-02.02.18

			
RESO CON DDT.N.40028-11.01.18
RESO CON DDT.N.40013-05.01.18			
NS. DDT.N.119-13.01.18
******************************
VEDI NS. FATT.N.13-26.01.18			



NOTA DI ACCREDITO CLIENTE
PER DIFFERENZA PESI RIF.NS.
NS. FATTURE EMESSE N.
  88 DEL 22.03.2018
 111 DEL 31.03.2018

 
NOTA DI ACCREDITO CLIENTE
PER DIFFERENZA PESO E PREZZO
RIF.NS.FATT.N.107-27.03.1

 
RESO CON DDT.N.654-18.04.18
03
IND.SCAROLA IT
0,570
KG
624,00
355,68
4
******************************
RIF.NS.DDT.N.879-05.04.18
RIF.NS.FATT.N.158-30.04.18 
*/
/* -------------------------------------------------------------------------------------------------------
	Questa libreria esegue la stampa di 
	e lei ne prepara la stampa
----------------------------------------------------------------------------------------------------------
*/

/*============================================================
UTILITY FUNCTIONS
============================================================*/
function formatDate($from,$to, $date){
	if ($from=='mm-dd-yyyy'){
		preg_match('/^(..)-(..)-(....)$/', $date, $match);
		$month = $match[1]; 
		$day = $match[2];
		$year = $match[3];
	}
	if ($from=='dd.mm.yyyy'){
		preg_match('/^(..).(..).(....)$/', $date, $match);
		$day = $match[1];
		$month = $match[2]; 
		$year = $match[3];
	}	
	if ($from=='dd/mm/yyyy'){
		preg_match('/^(..)\/(..)\/(....)$/', $date, $match);
		$day = $match[1];
		$month = $match[2];
		$year = $match[3];
	}
	
	if($to=='yyyy-mm-dd'){
		
		return $year.'-'.$month.'-'.$day;
	}
}

function formatImporto($importo){
	$posizionevirgola=strpos($importo, '.'); 
	$lunghezza= strlen($importo);
	//echo $importo.'//////'.$posizionevirgola."***".$lunghezza.'==='.($lunghezza*1-$posizionevirgola*1)."\n";
	if($posizionevirgola===false){
		return $importo=$importo.'.00';
	}

	if(($lunghezza*1-$posizionevirgola*1)<3){
		return $importo=$importo.'0';
	}
	return $importo;
}






/*============================================================
MAIN FUNCTION THAT GENERATE THE XML
============================================================*/
function generaXmlFt($myFt){
	global $config;
	$dati =  new stdClass();
	/*
	$dati->emittente = new stdClass();
	$dati->emittente->partitaIvaNazione = 'IT';
	$dati->emittente->partitaIvaCodice = '01588530236';
	$dati->emittente->codiceFiscale= '01588530236';
	$dati->emittente->ragioneSociale = 'La Favorita di Brun G. E G. Srl Unip.';
	$dati->emittente->regimeFiscale = 'RF01'; //(RF01 = regime ordinario)
	$dati->emittente->sede = new stdClass();
	$dati->emittente->sede->via = "Camagre, 38/B";
	$dati->emittente->sede->paese = "Isola della Scala";
	$dati->emittente->sede->citta = "VR";
	$dati->emittente->sede->cap = "37063";
	$dati->emittente->sede->nazione = "IT";

	$dati->emittente->datiREA->Ufficio = "VR";
	$dati->emittente->datiREA->NumeroREA = "185024";
	$dati->emittente->datiREA->CapitaleSociale = "41600.00";
	$dati->emittente->datiREA->SocioUnico = "SU";
	$dati->emittente->datiREA->StatoLiquidazione = "LN";
	*/

	//$dati->ProgressivoInvio = '00001';/*todo: massimo 10 caratteri:: ma il nome file ne contiene massimo 5*/
	$dati->ProgressivoInvio = $myFt->getProgressivoInvioSDI();
	$dati->FormatoTrasmissione = 'FPR12'; //(FPR12 = tra privati  || FPA12 = pubblica amministrazione)

	/*
	$dati->destinatario =  new stdClass();
	$dati->destinatario->codiceSDI =  '0000000';
	$dati->destinatario->pec = 'lafavorita_sr@pec.it';
	$dati->destinatario->partitaIvaNazione = 'IT';
	$dati->destinatario->partitaIvaCodice = '01588530236';
	$dati->destinatario->codiceFiscale = '01588530236';
	$dati->destinatario->ragioneSociale = 'La Favorita di Brun G. E G. Srl Unip.';
	$dati->destinatario->regimeFiscale = 'RF01';
	$dati->destinatario->sede = new stdClass();
	$dati->destinatario->sede->via = "Camagre, 38/B";
	$dati->destinatario->sede->paese = "Isola della Scala";
	$dati->destinatario->sede->citta = "VR";
	$dati->destinatario->sede->cap = "37063";
	$dati->destinatario->sede->nazione = "IT";
	*/

	$dati->fattura = new stdClass();
	/*
	$dati->fattura->tipo = 'TD01';
	$dati->fattura->divisa = 'EUR';
	$dati->fattura->numero = '130';
	$dati->fattura->data = '2018-12-23';
	$dati->fattura->importo = '100,20';
	$dati->fattura->causale = 'vendita';
	*/
	$dati->fattura->righe = array();
	/*
	$dati->fattura->righe[0] = new stdClass();
	$dati->fattura->righe[0]->numero ='';
	$dati->fattura->righe[0]->cod_articolo = '';
	$dati->fattura->righe[0]->descrizione = '';
	$dati->fattura->righe[0]->unita_misura = '';
	$dati->fattura->righe[0]->prezzo = '';
	$dati->fattura->righe[0]->imponibile = '';
	$dati->fattura->righe[0]->importo_iva = '';
	$dati->fattura->righe[0]->importo_totale = '';
	$dati->fattura->righe[0]->colli = '';
	$dati->fattura->righe[0]->peso_lordo = '';
	$dati->fattura->righe[0]->cod_iva = '';
	*/
	$dati->fattura->pagamento = new stdClass();

	if(	$myFt->cod_pagamento->getVal()=='30'){//bonifico 30 gg data fattura fine mese
		$dati->fattura->pagamento->modalita='MP05';
	}else if($myFt->cod_pagamento->getVal()=='BO'){//bonifico
		$dati->fattura->pagamento->modalita='MP05';
	}else if($myFt->cod_pagamento->getVal()=='12'){ //BON.BAN.30 GG D.F.
		$dati->fattura->pagamento->modalita='MP05';
	}else if($myFt->cod_pagamento->getVal()=='RD'){ //RIMESSA DIRETTA A VISTA
		$dati->fattura->pagamento->modalita='MP05';
		//todo:ho forzato questa a bonifico bancario
		//exit("Condizione di pagamento non valida 'RD'");
	}else if($myFt->cod_pagamento->getVal()=='01'){ //RIMESSA DIRETTA 30 GG DF
		$dati->fattura->pagamento->modalita='MP05';
		//todo:ho forzato questa a bonifico bancario
		//exit("Condizione di pagamento non valida '01'");
	}
		

	/*==============================================================================

	==============================================================================*/

	/*
	//ottieni i dati della fattura
	$params=array(
		'numero' => '569',
		'data'   => '15/12/2018',
		'tipo'  => 'F'
	);
	//genero il mio oggetto fattura
	$myFt= new Fattura($params);
	*/
	//print_r($myFt);

	//================DATI EMITTENTE FATTURA
	$dati->emittente = new stdClass();
	$dati->emittente->partitaIvaNazione = 'IT';
	$dati->emittente->partitaIvaCodice = $config->azienda->p_iva->getVal();
	$dati->emittente->codiceFiscale= $config->azienda->cod_fiscale->getVal();
	$dati->emittente->ragioneSociale = htmlentities($config->azienda->ragionesociale->getVal());
	$dati->emittente->regimeFiscale = 'RF01'; //(RF01 = regime ordinario)
	$dati->emittente->sede = new stdClass();
	$dati->emittente->sede->via = htmlentities($config->azienda->via->getVal());
	$dati->emittente->sede->paese = htmlentities($config->azienda->paese->getVal());
	$dati->emittente->sede->citta = htmlentities($config->azienda->citta->getVal());
	$dati->emittente->sede->cap = $config->azienda->cap->getVal();
	$dati->emittente->sede->nazione = "IT";

	$dati->emittente->datiREA->Ufficio = "VR";
	$dati->emittente->datiREA->NumeroREA = "185024";
	$dati->emittente->datiREA->CapitaleSociale = "41600.00";
	$dati->emittente->datiREA->SocioUnico = "SU";
	$dati->emittente->datiREA->StatoLiquidazione = "LN";


	//================DATI DESTINATARIO FATTURA
	$cliente=$myFt->cod_cliente->extend();

	/*
	$dati->destinatario =  new stdClass();
	$dati->destinatario->codiceSDI =  '0000000';
	$dati->destinatario->pec = $cliente->__pec->getVal();
	$dati->destinatario->partitaIvaNazione = $cliente->sigla_paese->getVal();
	$dati->destinatario->partitaIvaCodice = $cliente->p_iva->getVal(); //$cliente->p_iva_cee->getVal()
	$dati->destinatario->codiceFiscale = $cliente->cod_fiscale->getVal();
	$dati->destinatario->ragioneSociale = $cliente->ragionesociale->getVal();
	//$dati->destinatario->regimeFiscale = 'RF01';
	$dati->destinatario->sede = new stdClass();
	$dati->destinatario->sede->via = $cliente->via->getVal();
	$dati->destinatario->sede->paese = $cliente->paese->getVal();
	$dati->destinatario->sede->citta = $cliente->citta->getVal();
	$dati->destinatario->sede->cap = $cliente->cap->getVal();
	$dati->destinatario->sede->nazione = "IT";
	*/
	$dati->destinatario =  new stdClass();
	if($cliente->__SDIcodice->getVal()!=''){
		$dati->destinatario->codiceSDI = $cliente->__SDIcodice->getVal();
	}
	//se disponibile usa la pec comunicata per SDI, altrimenti la PEC ordinaria (utilizzata in precedenza col vecchio metodo), altrimenti niente
	if($cliente->__SDIpec->getVal()!=''){
		$dati->destinatario->pec = $cliente->__SDIpec->getVal();
	}else if($cliente->__pec->getVal()!=''){
		$dati->destinatario->pec = $cliente->__pec->getVal();
	}else{
		$dati->destinatario->pec = '';
	}
	$dati->destinatario->partitaIvaNazione = $cliente->__nazione->getVal();
	$dati->destinatario->partitaIvaCodice = $cliente->__p_iva->getVal(); //$cliente->p_iva_cee->getVal()
	$dati->destinatario->codiceFiscale = $cliente->__cod_fiscale->getVal();
	$dati->destinatario->ragioneSociale = htmlentities($cliente->__ragionesociale->getVal());
	//$dati->destinatario->regimeFiscale = 'RF01';
	$dati->destinatario->sede = new stdClass();
	$dati->destinatario->sede->via = htmlentities($cliente->__via->getVal());
	$dati->destinatario->sede->paese = htmlentities($cliente->__paese->getVal());
	$dati->destinatario->sede->citta = htmlentities($cliente->__citta->getVal());
	$dati->destinatario->sede->cap = $cliente->__cap->getVal();
	$dati->destinatario->sede->nazione =  $cliente->__nazione->getVal();


	//================DATI DESTINATARIO FATTURA
//	$dati->fattura = new stdClass();
	/*todo: controlla per altriu tipi: acconto, nota debito e simili*/
	/* todo:altri tipi di fattura
	TD01  Fattura 
	TD02  Acconto/Anticipo su fattura 
	TD03  Acconto/Anticipo su parcella 
	TD04  Nota di Credito 
	TD05  Nota di Debito 
	TD06  Parcella 
 	*/
	
	if($myFt->tipo->getVal()=='F'){//se fattura
		$dati->fattura->tipo = 'TD01';		
	}
	if($myFt->tipo->getVal()=='N'){//se nota di credito
		$dati->fattura->tipo = 'TD04';		
	}
	/*fine:todo*/
	//================DATI GENERALI FATTURA
	$dati->fattura->divisa = $myFt->valuta->getVal();

	$anno=explode('/',$myFt->data->getFormatted());	
	$dati->fattura->numero = trim($myFt->numero->getVal()).'/'.$anno[2]; //aggiungo la stringa "/anno" se dopo il 2013
	$dati->fattura->data = formatDate('mm-dd-yyyy','yyyy-mm-dd',$myFt->data->getVal());
	$dati->fattura->importo = formatImporto($myFt->importo->valore);
	//$dati->fattura->causale = 'vendita';/*todo:al momento non la usiamo in quanto opzionale*/

	//================DATI RIGHE FATTURA
	$dati->fattura->righe = array();
	/*
	$dati->fattura->righe[0] = new stdClass();
	$dati->fattura->righe[0]->numero ='';
	$dati->fattura->righe[0]->cod_articolo = '';
	$dati->fattura->righe[0]->descrizione = '';
	$dati->fattura->righe[0]->unita_misura = '';
	$dati->fattura->righe[0]->prezzo = '';
	$dati->fattura->righe[0]->imponibile = '';
	$dati->fattura->righe[0]->importo_iva = '';
	$dati->fattura->righe[0]->importo_totale = '';
	$dati->fattura->righe[0]->colli = '';
	$dati->fattura->righe[0]->peso_lordo = '';
	$dati->fattura->righe[0]->cod_iva = '';
	*/
	$contaRighe=1;
	$currentDdt;
	$dati->riferimentoDdt = array();

	//print_r($myFt->righe);
	foreach ($myFt->righe as $key => $value) {
		$riga=$myFt->righe[$key];
	//echo $riga->numero->getVal().':'.$riga->numero->getVal()."/n";
		//tralascio qualche riga che è solo descrittiva
		
		if($riga->cod_articolo->getVal() == 'BSEVEN'){
			$currentDdt->righeDelDDT--;
			continue;
		}
		
		if($riga->cod_articolo->getVal() == 'ASSOLVE'){
			$currentDdt->righeDelDDT--;
			continue;
		}
/*
		echo $riga->descrizione->getVal().'<BR>';
		echo !(substr($riga->descrizione->getVal(),0,9)=='D.d.T. N.');
		echo (substr($riga->descrizione->getVal(),0,3)=='DDT');
		ECHO '<BR><BR>';
*/
		if((!(substr($riga->descrizione->getVal(),0,9)=='D.d.T. N.') && ($riga->peso_lordo->getVal() == 0))){
			if((!(substr($riga->descrizione->getVal(),0,3)=='DDT') && ($riga->peso_lordo->getVal() == 0))){
				$currentDdt->righeDelDDT--;
				continue;
			}
		}


		/*
		if($riga->imponibile->getVal()*1 < 0.001){
			//$currentDdt->righeDelDDT--;
			continue;				
		}
		*/
		
		


		//si tratta di una riga di descrizione ddt
		//D.d.T. N.3160 - 01.12.2018
		if(substr($riga->descrizione->getVal(),0,9)=='D.d.T. N.' || substr($riga->descrizione->getVal(),0,3)=='DDT'){
			if($currentDdt->righeDelDDT>0){
				/*todo: potrei controllare il ddt e vedere se ci sono righe di puro testo: magari lasciando un warning in un log*/
				exit("Stiamo cambiando ddt anche se le righe del ddt precedente non sono ancora finite. Vecchio ddt:".$currentDdt->numero." ne restano ".$currentDdt->righeDelDDT);
			}
			
			if(substr($riga->descrizione->getVal(),0,9)=='D.d.T. N.'){
				//echo $riga->descrizione->getVal()."\n";
				preg_match('/D.d.T. N.(.*?) - (.*?)$/', $riga->descrizione->getVal(), $match);
				$stoFatturendoDdtNonMiei=false;
			}
			if(substr($riga->descrizione->getVal(),0,3)=='DDT'){
				//echo $riga->descrizione->getVal()."\n";
				preg_match('/DDT(.*?) DEL (.*?)$/', $riga->descrizione->getVal(), $match);
				$stoFatturendoDdtNonMiei=true;
			}


			//print_r($match);
			$currentDdt = $dati->riferimentoDdt[] = new stdClass();
			$currentDdt->numero = $match[1];
			$currentDdt->data = $match[2];
			$currentDdt->riferimentoRighe = array();
			
			
			$params=array(
				'numero' => $currentDdt->numero,
				'data'   => str_replace('.','/',$currentDdt->data),
				'cod_causale'  => 'V'//deve essere per forza di vendita se è fatturato
			);
		
			$currentDdt->obj = new Ddt($params);
			$currentDdt->righeDelDDT = count($currentDdt->obj->righe);
			
			//go to the net iteration
			continue;
		}


		
		
		/*todo:verifica descrizione lunga di un articolo*/
		/*
		//se c'è un codice articolo
		if ($riga->cod_articolo->getVal()!=''){
			//seconda descrizione
			$descrizione2=$riga->cod_articolo->extend()->descrizione2->getVal();

			if($descrizione2!=''){
				$ft->html.= MyOwnRow($pdf,'',$descrizione2,'','','','','','','' );
			}

			//descrizione lunga
			$descrizioneL=$riga->cod_articolo->extend()->descrizionelunga->getVal();
			if($descrizioneL!=' '){
				$righeL=explode("\n",$descrizioneL);
				foreach ($righeL as $rigaL){
					if(strlen($rigaL)>1){
						//var_dump($rigaL);
						$ft->html.= MyOwnRow($pdf,'',$rigaL,'','','','','','','' );
					}
				}
			}
		}
		*/
		/*fine:todo*/
		
		
		/*todo: rimuovere il rifierimento ddt se la riga è di questo tipo
		MENO PROVVIGIONE DEL
		MENO PROVVIGIONE 12%
		
		******************************
		************SCONTO************ (codice sconto2)
		
		*/
		

		
		
		//si tratta di una normale riga appartenente ad un ddt
		$dati->fattura->righe[$contaRighe] = new stdClass();
		$dati->fattura->righe[$contaRighe]->numero =$contaRighe;
		$dati->fattura->righe[$contaRighe]->cod_articolo = $riga->cod_articolo->getVal();
		$dati->fattura->righe[$contaRighe]->descrizione = htmlentities($riga->descrizione->getVal());
		$dati->fattura->righe[$contaRighe]->unita_misura = $riga->unita_misura->getVal();
		$dati->fattura->righe[$contaRighe]->prezzo = formatImporto($riga->prezzo->valore);
		$dati->fattura->righe[$contaRighe]->imponibile = formatImporto($riga->imponibile->valore);
		$dati->fattura->righe[$contaRighe]->importo_iva = '';
		$dati->fattura->righe[$contaRighe]->importo_totale = formatImporto($riga->imponibile->valore);
		$dati->fattura->righe[$contaRighe]->colli = ($riga->colli->getVal()*1>0 ? $riga->colli->getFormatted(0) : '');
		$dati->fattura->righe[$contaRighe]->peso_lordo = formatImporto($riga->peso_lordo->valore);
		$dati->fattura->righe[$contaRighe]->cod_iva = formatImporto($riga->cod_iva->getVal());
		

		//se si tratta di una riga di sconto
		if($riga->cod_articolo->getVal()=='SCONTO2'){
			//questa riga (di sconto, non si riferisce ad alcun ddt)
			/*
			SC = sconto
			PR = premio
			AB = abbuono
			AC = spesa accessori
			*/
			$dati->fattura->righe[$contaRighe]->tipocessioneprestazione ='SC';
		}else if (strpos($riga->descrizione->getVal(), 'PROVVIGIONE') !== false) {
			//ho trovato una riga di provvigione, non si riferisce ad alcun ddt
			$dati->fattura->righe[$contaRighe]->tipocessioneprestazione ='AC';
		}else if (strpos($riga->descrizione->getVal(), 'COMMISSIONE') !== false) {
			//ho trovato una riga di commissione, non si riferisce ad alcun ddt
			$dati->fattura->righe[$contaRighe]->tipocessioneprestazione ='AC';
		}else{
			//non è ne uno sconto ne una provvigione... dovrebbe quindi avere riferimento in un ddt
			$currentDdt->riferimentoRighe[]= $contaRighe;
		}	
		
		//SE HO FINITO LE RIGHE DEL DDT E NON E' UNA RIGA DI PROVVIGIONE ALLORA MI BLOCCO PERCHE' QUALCOSA NON VA
		if($currentDdt->righeDelDDT==0 && !strpos($riga->descrizione->getVal(), 'PROVVIGIONE') && ($riga->cod_articolo->getVal()!='SCONTO2')&& ($riga->cod_articolo->getVal()!='COMMISSIONE') && !$stoFatturendoDdtNonMiei){
			exit("Stiamo utilizzando piu righe di quelle del ddt.Riga: ".$contaRighe." del ddt ".$currentDdt->numero." ---->".$riga->descrizione->getVal());
		}
		$currentDdt->righeDelDDT--;
		
		$contaRighe++;
	}


	/**/
	/*==============================================================================

	==============================================================================*/
	$xml = new SimpleXMLElement('<p:p:FatturaElettronica/>');
	$xml->addAttribute('xmlns:xmlns:ds',"http://www.w3.org/2000/09/xmldsig#");
	$xml->addAttribute('xmlns:xmlns:p',"http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2");
	$xml->addAttribute('xmlns:xmlns:xsi',"http://www.w3.org/2001/XMLSchema-instance");
	$xml->addAttribute('versione',"FPR12");
	$xml->addAttribute('xsi:xsi:schemaLocation',"http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd");

	/*
	$xml->addAttribute('version', '1.0');
	$xml->addAttribute('encoding', 'UTF-8');
	*/
	$last = $xml->addChild('FatturaElettronicaHeader');
		$last = $last->addChild('DatiTrasmissione');
			$last = $last->addChild('IdTrasmittente');
				$last->addChild('IdPaese',$dati->emittente->partitaIvaNazione);
				$last->addChild('IdCodice',$dati->emittente->codiceFiscale);

	$last = $xml->FatturaElettronicaHeader->DatiTrasmissione;
		$last->addChild('ProgressivoInvio',$dati->ProgressivoInvio);
		$last->addChild('FormatoTrasmissione',$dati->FormatoTrasmissione);
		
		//mi sa che è un controllo duplicato con quello del blocco "if/elseif" qua sotto
		if($dati->destinatario->codiceSDI == '' && $dati->destinatario->pec ==''){
			exit("Non trovo ne un codice ne un INDIRIZZO PEC da utilizzare");
		}
		
		if($dati->destinatario->codiceSDI != ''){
			//se ho specificato un codice destinatario lo uso
			$last->addChild('CodiceDestinatario',$dati->destinatario->codiceSDI);
		}else if($dati->destinatario->pec!=''){
			//se non ho specificato un codice destinatario ma ho un indirizzo pec uso il codice destinatario "0000000"
			$last->addChild('CodiceDestinatario','0000000');
			$last->addChild('PECDestinatario',$dati->destinatario->pec);
		}else{
			exit("Non trovo ne un codice ne un INDIRIZZO PEC da utilizzare");			
		}
		
		/*
		if($dati->destinatario->pec!=''){
			if($dati->destinatario->codiceSDI==''){		
			}
		}
		*/

		
		
	$last = $xml->FatturaElettronicaHeader->addChild('CedentePrestatore');
		$last = $last->addChild('DatiAnagrafici');
			$last = $last->addChild('IdFiscaleIVA');
					$last->addChild('IdPaese',$dati->emittente->partitaIvaNazione);
					$last->addChild('IdCodice',$dati->emittente->partitaIvaCodice);			
			
			$xml->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->addChild('CodiceFiscale', $dati->emittente->codiceFiscale);
			
			$last = $xml->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->addChild('Anagrafica');
				$last->addChild('Denominazione',  $dati->emittente->ragioneSociale); 
			$last = $xml->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->addChild('RegimeFiscale', $dati->emittente->regimeFiscale);
		$last = $xml->FatturaElettronicaHeader->CedentePrestatore->addChild('Sede');
			$last->addChild('Indirizzo', $dati->emittente->sede->via);
			$last->addChild('CAP', $dati->emittente->sede->cap);
			$last->addChild('Comune', $dati->emittente->sede->paese);
			$last->addChild('Provincia', $dati->emittente->sede->citta);
			$last->addChild('Nazione', $dati->emittente->sede->nazione);

			$last = $xml->FatturaElettronicaHeader->CedentePrestatore->addChild('IscrizioneREA');
				$last->addChild('Ufficio',$dati->emittente->datiREA->Ufficio);
				$last->addChild('NumeroREA',$dati->emittente->datiREA->NumeroREA);
				$last->addChild('CapitaleSociale',$dati->emittente->datiREA->CapitaleSociale);
				$last->addChild('SocioUnico',$dati->emittente->datiREA->SocioUnico);
				$last->addChild('StatoLiquidazione',$dati->emittente->datiREA->StatoLiquidazione);
			
			
	$last = $xml->FatturaElettronicaHeader->addChild('CessionarioCommittente');
		$last = $last->addChild('DatiAnagrafici');
		
		//se azienda
			$last = $last->addChild('IdFiscaleIVA');
				$last->addChild('IdPaese',$dati->destinatario->partitaIvaNazione);
				$last->addChild('IdCodice',$dati->destinatario->partitaIvaCodice);
			if($dati->destinatario->codiceFiscale!=''){
				$xml->FatturaElettronicaHeader->CessionarioCommittente->DatiAnagrafici->addChild('CodiceFiscale', $dati->destinatario->codiceFiscale);
			}

			$last = $xml->FatturaElettronicaHeader->CessionarioCommittente->DatiAnagrafici->addChild('Anagrafica');
				$last->addChild('Denominazione',  $dati->destinatario->ragioneSociale); 
		$last = $xml->FatturaElettronicaHeader->CessionarioCommittente->addChild('Sede');
			$last->addChild('Indirizzo', $dati->destinatario->sede->via);
			$last->addChild('CAP', $dati->destinatario->sede->cap);
			$last->addChild('Comune', $dati->destinatario->sede->paese);
			$last->addChild('Provincia', $dati->destinatario->sede->citta);
			$last->addChild('Nazione', $dati->destinatario->sede->nazione);


	$last = $xml->addChild('FatturaElettronicaBody');
		$last = $last->addChild('DatiGenerali')->addChild('DatiGeneraliDocumento');
			$last->addChild('TipoDocumento',$dati->fattura->tipo);
			$last->addChild('Divisa',$dati->fattura->divisa);
			$last->addChild('Data',$dati->fattura->data);
			$last->addChild('Numero',$dati->fattura->numero);
			$last->addChild('ImportoTotaleDocumento',$dati->fattura->importo);
			$last->addChild('Causale','Contributo CONAI assolto ove dovuto.');
			$last->addChild('Causale',"Assolve gli obblighi di cui all'articolo 62, comma 1, del decreto legge 24 gennaio 2012, n. 1, convertito, con modificazioni, dalla legge 24 marzo 2012, n. 27");
			/* non abbligatorio, lasciamo stare?
			$last->addChild('Causale',$dati->fattura->causale);
			*/
			
			/*va aggiunto il riferimento ad altre fatture*/
			/*DatiFattureCollegate*/

		//da ripetere per ogni ddt
		foreach ($dati->riferimentoDdt as $key => $ddt){
			$last =  $xml->FatturaElettronicaBody->DatiGenerali->addChild('DatiDDT');
					$last->addChild('NumeroDDT', $ddt->numero);
					$last->addChild('DataDDT', formatDate('dd.mm.yyyy','yyyy-mm-dd',$ddt->data));
					foreach ($ddt->riferimentoRighe as $key2 => $riferimentoRiga){
						$last->addChild('RiferimentoNumeroLinea', $riferimentoRiga); /*non va valorizato se c'è solo 1 ddt*/
					}
		}
		
		$last = $xml->FatturaElettronicaBody->addChild('DatiBeniServizi');
		
		
		//da ripetere per ogni riga
		foreach ($dati->fattura->righe as $key => $riga){
				$last = $xml->FatturaElettronicaBody->DatiBeniServizi->addChild('DettaglioLinee');
				$last->addChild('NumeroLinea',		$riga->numero);

				if(property_exists ($riga,'tipocessioneprestazione')){
					$last->addChild('TipoCessionePrestazione',	$riga->tipocessioneprestazione);
				}

				$last->addChild('Descrizione',		$riga->descrizione);
				$last->addChild('Quantita',			$riga->peso_lordo);
				$last->addChild('UnitaMisura',		$riga->unita_misura);
				$last->addChild('PrezzoUnitario',	$riga->prezzo);
				$last->addChild('PrezzoTotale',		$riga->importo_totale);
				$last->addChild('AliquotaIVA',		$riga->cod_iva);

		}
		
		


		//riepilogo dati fattura (per ogni aliquota)

		$imponibili=$myFt->calcolaTotaliImponibiliIva();
	//print_r($imponibili);
		
		foreach ($imponibili as $codIva =>$val){
			$iva=new CausaleIva(array('codice'=>(string)$codIva));
			//$codIva
			$descrizioneIva=$iva->descrizione->getVal();
			$imponibileIva=$val['imponibile'];
			$importoIva=$val['importo_iva'];

			$last = $xml->FatturaElettronicaBody->DatiBeniServizi->addChild('DatiRiepilogo');		
			$last->addChild('AliquotaIVA',formatImporto($codIva));
			$last->addChild('ImponibileImporto',formatImporto($imponibileIva));
			$last->addChild('Imposta',formatImporto($importoIva));
			$last->addChild('EsigibilitaIVA','I');//immediata... potrebbe essere differita
			
		}
		
		//
		$last = $xml->FatturaElettronicaBody->addChild('DatiPagamento');
			/*
			TP01   pagamento a rate 
			TP02   pagamento completo 
			TP03   anticipo 
			*/
			$last->addChild('CondizioniPagamento','TP02');
			$last = $last->addChild('DettaglioPagamento');
				/*
				$ft->cod_pagamento->extend()->descrizione->getVal()
				
				MP01   contanti 
				MP02   assegno 
				MP03   assegno circolare 
				MP04   contanti presso Tesoreria 
				MP05   bonifico 
				MP06   vaglia cambiario 
				MP07   bollettino bancario 
				MP08   carta di pagamento 
				MP09   RID 
				MP10   RID utenze 
				MP11   RID veloce 
				MP12   Riba 
				MP13   MAV 
				MP14   quietanza erario stato 
				MP15   giroconto su conti di contabilità speciale 
				MP16   domiciliazione bancaria 
				MP17   domiciliazione postale  
				MP18   bollettino di c/c postale  
				MP19   SEPA Direct Debit 
				MP20   SEPA Direct Debit CORE 
				MP21   SEPA Direct Debit B2B 
				MP22   Trattenuta su somme già riscosse 
				*/
				if($dati->fattura->pagamento->modalita !=''){
					$last->addChild('ModalitaPagamento',$dati->fattura->pagamento->modalita);				
//todo					$last->addChild('DataScadenzaPagamento',formatDate('dd/mm/yyyy','yyyy-mm-dd',$myFt->getScadenzaPagamento())); /*todo : siamo sicuri di volerla inserire?*/
					$last->addChild('ImportoPagamento',formatImporto($myFt->importo->valore));
					//se bonifico mostro le coordinate
					if($dati->fattura->pagamento->modalita=='MP05'){
						if($myFt->cod_banca->getVal()==''){
							exit("Modalita di pagamento 'bonifico' ma nessuna coordinata specificata");
						}
						$last->addChild('IBAN',$myFt->cod_banca->extend()->__iban->getVal());
					}
				}else{
					//$log->warn("Nessuna modalita di pagamento indicata");	
				}

				
	//$filename= $dati->emittente->partitaIvaNazione.$dati->emittente->partitaIvaCodice.'_'.$dati->ProgressivoInvio.'.xml';

	$xmlDocument = new DOMDocument('1.0');
	$xmlDocument->preserveWhiteSpace = false;
	$xmlDocument->formatOutput = true;
	$xmlDocument->loadXML($xml->asXML());




//validate the XML file before showin it

	if (!$xmlDocument->schemaValidate(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/core/stampe/Schema_del_file_xml_FatturaPA_versione_1.2.xsd')) {
		error_reporting(-1);
		$xmlDocument->schemaValidate(realpath($_SERVER["DOCUMENT_ROOT"]).'/webContab/my/php/core/stampe/Schema_del_file_xml_FatturaPA_versione_1.2.xsd');
		print '<b>DOMDocument::schemaValidate() Generated Errors!</b>';
	}else{
		//save the file
		//$xmlDocument->save($myFt->getXmlFileUrl().'.bozza');
		$xmlDocument->save($myFt->getXmlFileUrl());
		//display it
		$filename=$myFt->getXmlFileName();
		/*
		header("Content-disposition: inline; filename=".$filename);
		header('Content-type: text/xml');
		echo $xmlDocument->saveXML();
		*/
	}


}
?>