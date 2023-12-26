<link rel="stylesheet" type="text/css" href="./../style.css">


<table class="borderTable">
<?php
echo '1.5'+'1.5';
$totaleKgGenerale = 0.0;
$out='<table class="borderTable">';
$out.='<tr><td>Ft.Num</td><td>Ft.Data</td><td>Ft.File</td><td>UM</td><td>Peso</td></tr>';
$files = scandir('./dati/');
foreach($files as $file) {
	if (file_exists('./dati/'.$file)) {
		if($file=='.') continue;
		if($file=='..') continue;
		$totalekg = 0.0;

		$xml = simplexml_load_file('./dati/'.$file);
		
		for ($i=0; $i < count($xml->FatturaElettronicaBody->DatiBeniServizi->DettaglioLinee); $i++){
			$riga = $xml->FatturaElettronicaBody->DatiBeniServizi->DettaglioLinee[$i];
			echo "\n<tr>";
			echo "\n<td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Numero.'</td>';
			echo "\n<td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Data.'</td>';

$kg = str_replace('.','',$riga->Quantita);
$kg=$kg/100;

			$totalekg = 1*$kg + $totalekg;
			$totaleKgGenerale= 1*$kg + $totaleKgGenerale;

			echo "\n<td>".$riga->Descrizione.'</td>';
			echo "\n<td>".$riga->UnitaMisura.'</td>';
			echo "\n<td>".$riga->Quantita." ".$totaleKgGenerale.'</td>';
			echo "\n</tr>";	 
		}

		$out.= "<tr><td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Numero."</td>";
		$out.= "<td>".$xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Data."</td>";

		$out.= "<td>".basename("\n<br>".'./dati/'.$file)."</td>";
		$out.= "<td>kg</td>";
		$out.= "<td>".number_format($totalekg, 2, ",",".")."</td></tr>";
	} else {
		exit('Failed to open '. $file);
	}
}
?>
</table>
<br><br>
<?php
$out.='</table>';
echo "".$out;
echo "Totale generale kg. ".number_format($totaleKgGenerale, 2, ",",".");

/*
 number_format(
    float $num,
    int $decimals = 0,
    ?string $decimal_separator = ".",
    ?string $thousands_separator = ","
): string
 * */
?>
