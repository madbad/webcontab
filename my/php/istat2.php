<link rel="stylesheet" type="text/css" href="./../style.css">


<table class="borderTable">
<?php
include ('./core/config.inc.php');
set_time_limit(0);
//error_reporting(-1);
$articoli = array();
$totaleKgGenerale = 0.0;
$out='<table class="borderTable">';
$out.='<tr><td>Ft.Num</td><td>Ft.Data</td><td>Ft.File</td><td>UM</td><td>Peso</td><td>Importo</td></tr>';
$files = scandir('./dati/copiavendite/2023');



foreach($files as $file) {
	if (file_exists('./dati/copiavendite/2023/'.$file)) {
		if($file=='.') continue;
		if($file=='..') continue;
		$totalekg = 0.0;
		$xml = simplexml_load_file('./dati/copiavendite/2023/'.$file);


//cliente
		$partitaIVA = $xml->FatturaElettronicaHeader->CessionarioCommittente->DatiAnagrafici->IdFiscaleIVA->IdCodice;

/*
		$cliente = new ClienteFornitore(Array(
			//'codice'=>'AMAT2',
			//'codice'=>'AMAT2',
			'p_iva'=>(string)$partitaIVA
		));
		//echo '<pre>';
		
		$cliente->getDataFromDb();
*/
		$elencoClienti=new MyList(
			array(
				'_type'=>'ClienteFornitore',
				'p_iva'=>(string)$partitaIVA
				)
		);
//print_r($elencoClienti->arr[0]);
		//print_r($cliente);
		//echo '</pre>';
		//print_r($elencoClienti);
		$cliente = $elencoClienti->arr[0];
		if((string)$partitaIVA == '004062180239'){//indiano
			continue;	
		}
		if((string)$partitaIVA == '09599790962'){//bucaneve
			continue;	
		}
		
		if(count($elencoClienti->arr)<=0 ){
			echo '<br>Impossibile trovare il cliente con piva '.(string)$partitaIVA;
			
		}
		//echo '<br>*'.$cliente->ragionesociale->getVal().'*';
		//echo '<br>*'.$cliente->p_iva->getVal().'*';
		//echo '<br>*'.(string)$partitaIVA.'*';
		//$cliente
		if ($cliente->__classificazione->getVal()!='supermercato' && $cliente->__classificazione->getVal()!='mercato' && $cliente->__classificazione->getVal()!='semilavorato'){
			//skip this one
//			echo '<br>*'.$cliente->ragionesociale->getVal().'* non e un cliente del trasformato ';
//			echo $cliente->__classificazione->getVal();
//			echo $cliente->codice->getVal();
//			continue;
		}
		
		for ($i=0; $i < count($xml->FatturaElettronicaBody->DatiBeniServizi->DettaglioLinee); $i++){
			$riga = $xml->FatturaElettronicaBody->DatiBeniServizi->DettaglioLinee[$i];
//			echo "\n<tr>";
//			echo "\n<td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Numero.'</td>';
//			echo "\n<td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Data.'</td>';


			//skippo i seguenti articoli che non sono nostri e ci arrivano già confezionati
			if (!(strpos((string)$riga->Descrizione, 'RICCIA')  === false) ){
				$riga->Descrizione = 'insalate';
			};
			if (!(strpos((string)$riga->Descrizione, 'SCAROLA')  === false) ){
				$riga->Descrizione = 'insalate';
			};

			if (!(strpos((string)$riga->Descrizione, 'ROSSO TONDO')  === false) ){
				$riga->Descrizione = 'RADICCHI';
			};
			if (!(strpos((string)$riga->Descrizione, 'ROSSO LUNGO')  === false) ){
				$riga->Descrizione = 'RADICCHI';
			};
			if (!(strpos((string)$riga->Descrizione, 'LUNGO')  === false) ){
				$riga->Descrizione = 'RADICCHI';
			};
			if (!(strpos((string)$riga->Descrizione, 'ROSSO SEMILUNGO')  === false) ){
				$riga->Descrizione = 'RADICCHI';
			};
			if (!(strpos((string)$riga->Descrizione, 'SEMILUNGO')  === false) ){
				$riga->Descrizione = 'RADICCHI';
			};
			if (!(strpos((string)$riga->Descrizione, 'PEPERONC')  === false) ){
				$riga->Descrizione = 'PEPERONCINI';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'MELON')  === false) ){
				$riga->Descrizione = 'MELONI';
			};
			if (!(strpos((string)$riga->Descrizione, 'ZUCCHIN')  === false) ){
				$riga->Descrizione = 'ZUCCHINE';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'ZUCCHER')  === false) ){
				$riga->Descrizione = 'PDZ';
			};
			if (!(strpos((string)$riga->Descrizione, 'MELANZA')  === false) ){
				$riga->Descrizione = 'MELANZANE';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'PEPERON')  === false) ){
				$riga->Descrizione = 'PEPERONI';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'GENTILE')  === false) ){
				$riga->Descrizione = 'GENTILE';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'CAP')  === false) ){
				$riga->Descrizione = 'CAPUCCI';
			};

			if (!(strpos((string)$riga->Descrizione, 'PROVVI')  === false) ){
				$riga->Descrizione = 'PROVVIGIONE';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'COMMIS')  === false) ){
				$riga->Descrizione = 'PROVVIGIONE';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'PRIOV')  === false) ){
				$riga->Descrizione = 'PROVVIGIONE';
//				continue;
			};
			if (!(strpos((string)$riga->Descrizione, 'VERZ')  === false) ){
				$riga->Descrizione = 'VERZE';
			};
			if (!(strpos((string)$riga->Descrizione, 'CETR')  === false) ){
				$riga->Descrizione = 'CETRIOLI';
//				continue;
			};
$kg = str_replace('.','',$riga->Quantita);
$kg=$kg/100;


$valore = str_replace('.','',$riga->PrezzoTotale);
$valore=$valore/100;

			$totalekg = 1*$kg + $totalekg;
			$totaleKgGenerale= 1*$kg + $totaleKgGenerale;

			$totaleValoreGenerale += $valore;

//			echo "\n<td>".$riga->Descrizione.'</td>';
//			echo "\n<td>".$riga->UnitaMisura.'</td>';
//			echo "\n<td>".$riga->Quantita." ".$totaleKgGenerale.'</td>';
//			echo "\n</tr>";
			
			//
		
			
			if (array_key_exists((string)$riga->Descrizione, $articoli)){
				$articoli[(string)$riga->Descrizione]['peso'] += $kg;
				$articoli[(string)$riga->Descrizione]['valore'] += $valore;
			}else{
				$articoli[(string)$riga->Descrizione] = array();
				$articoli[(string)$riga->Descrizione]['peso'] = $kg;
				$articoli[(string)$riga->Descrizione]['valore'] = $valore;
			}
			
		}

		$out.= "<tr><td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Numero."</td>";
		$out.= "<td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Data."</td>";
		$out.= "<td>".basename("\n<br>".'./dati/'.$file)."</td>";
		$out.= "<td>kg</td>";
		$out.= "<td>".number_format($totalekg, 2, ",",".")."</td>";
		$out.= "<td>".$xml->FatturaElettronicaBody->DatiPagamento->DettaglioPagamento->ImportoPagamento."</td></tr>";
	} else {
		exit('Failed to open '.'./dati/copiavendite/2023/'. $file);
	}
}
?>
</table>
<br><br>
<?php
$out.='</table>';
echo "".$out;
echo "Totale generale kg. ".number_format($totaleKgGenerale, 2, ",",".");
echo "Totale generale eur. ".number_format($totaleValoreGenerale, 2, ",",".");

/*
 number_format(
    float $num,
    int $decimals = 0,
    ?string $decimal_separator = ".",
    ?string $thousands_separator = ","
): string
 * */
 echo '<table>';
foreach ($articoli as $key => $value){
		echo '<tr>';
		echo '<td>'.$key.'</td>';
		echo '<td>'.$value['peso'].'</td>';
		echo '<td>'.$value['valore'].'</td>';
		echo '</tr>';
}
 //print_r($articoli);
echo '</table>';
?>
