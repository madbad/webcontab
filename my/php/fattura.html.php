<link rel="stylesheet" type="text/css" href="culdip_files/style_print.css" media="print">
<style>
html{
	width: 210mm;
	height: 287mm; /*297*/
	margin: 0mm;
	padding:0mm;
}

body {
	width: 210mm;
	height: 287mm;  /*297*/
	/*border:0.3mm solid black;*/
	margin: 0mm;
	padding:0mm;
}
@page {
  size: A4;
  margin: 0mm;
}

@media print {
  .pagebreak {page-break-after: always;}
}

#venditore{
	width:15cm;
	font-size:0.35cm;
	position:absolute;
	top:0.5cm;
	left:0.5cm;
}

.nomeAzienda{
	font-size:0.7cm;
	font-weight:bold;
}

#acquirente{
	border:0.3mm solid black;
	width:9cm;
	padding:0.3cm;
	font-size:0.35cm;

	position:absolute;
	left:9cm;
	top:3cm;

}
#datiFattura{
	width: 190mm;

	position:absolute;
	top:7.5cm;
	left:0.5cm;
}
#datiFattura div{
	font-size:0.35cm;
	color:grey;
	display:inline-block;
	width:6cm;
	border:0.3mm solid black;
}
#datiFattura div span{
	display:block;
	color:black;
	font-size:0.35cm;
	font-weight:bold;
}
#acquirente span{
	display: inline-block;
	width: 3cm;
}
#righeFattura{
	width: 190mm;
	
	position:absolute;
	top:10cm;
	left:0.5cm;
}

#righeFattura table{
	width:100%;
}
#righeFattura th{
	border:0.3mm solid black;
	border-collapse: collapse;
}

#righeFattura table, #righeFattura tr, #righeFattura td{
	border:0.3mm solid black;
	border-collapse: collapse;
	border-top:0mm solid black;
	border-bottom:0mm solid black;
	padding-left:0.1cm;
	padding-right:0.1cm;
	padding-top:0cm;
	padding-bottom:0cm;
	font-size:0.35cm;
}
#riferimentoDDT{
	width: 190mm;
	
	position:absolute;
	top:21cm;	
	left:0.5cm;
	border:0.3mm solid black;
	

}
#ddt{
	columns: 5;
	column-rule: 0.1mm solid grey;
	padding:0.1cm;

	font-size:0.30cm;

}


#riferimentoDDT span{
	/*
	border: 1mm solid gray;
	padding:0.1cm;
	*/
}
#annotazioni{
	width: 11cm;

	position:absolute;
	top:25cm;	
	left:0.5cm;
	
	border:0.3mm solid black;
	font-size:0.35cm;
}
#annotazioni ul{
	padding-left: 0.4cm;
}
#castelletto{
	width: 7.5cm;
	
	position:absolute;
	top:25cm;	
	left:12cm;
	border:0.3mm solid black;
}
#castelletto td{
	font-size:0.40cm;	

}
.textRight{
	text-align:right;
}

.dettaglio{
	padding:1cm;
	
}
.dettaglio table{
	width:16cm;
	
}

.dettaglio table, .dettaglio td{
	border:0.3mm solid black;
	border-collapse: collapse;
	font-size:0.30cm;
	padding:0.1mm;
}
</style>


<div id="venditore">
	<br><span class="nomeAzienda"><?php echo $fornitore['ragsoc'];?>
</span>
	<br><?php echo $fornitore['via'];?>
	<br><?php echo $fornitore['citta'];?>
	<br><span>Codice Fiscale:</span> <?php echo $fornitore['codfisc'];?>
	<br><span>Partita IVA:</span> <?php echo $fornitore['piva'];?>
	<br><!--<span>Codice SDI:</span> 0000000-->
	<br><!--<span>PEC:</span> lafavoria_srl@pec.it-->
</div>


<div id="acquirente">
	Spett.le:
	<br><b>La Favorita di Brun G. &amp; G. SRL Unipersonale</b>
	<br>Via Camagre n.38/b
	<br>37063 Isola della Scala (VR)
	<br><span>Codice Fiscale:</span> 01588530236
	<br><span>Partita IVA:</span> 01588530236
	<br><span>Codice SDI:</span> 0000000
	<br><span>PEC:</span> lafavoria_srl@pec.it
</div>

<div id="datiFattura">
<!--	<div style="width:18%">Tipo Documento<span>Ricavo di Conto Vendita</span></div>-->
	<div style="width:18%">Tipo Documento<span>facsimile fattura</span></div>
	<div style="width:10%">Numero<span></span></div>
	<div style="width:15%">Data<span></span></div>
	<br><br>
	<div style="width:15%">Valuta<span>EUR</span></div>
	<div style="width:20%">Pagamento<span>Bonifico</span></div>
	<div style="width:40%">IBAN<span>-</span></div>
	<div style="width:13%">Pagina<span>1/1</span></div>
</div>

<div id="righeFattura">
<table>
	<tbody><tr>
		<th>Articolo</th>
		<th>Descrizione</th>
		<th>U.M.</th>
		<th>Q.tà</th>
		<th>Prezzo</th>
		<th>Importo</th>
		<th>Iva</th>
	</tr>
	<?php
		foreach ($fattura->righe as $riga) {
			//echo '<span> N. '.$ddt['numero'].' del '.$ddt['data'].'</span><br>';
			?>
			<tr>
				<td>-</td>
				<td><?php echo $riga['ARTICOLO']; ?></td>
				<td><?php echo $riga['UM']; ?></td>
				<td class="textRight"><?php echo number_format($riga['PESO'],1,',','.'); ?></td>
				<td class="textRight"><?php echo number_format($riga['PREZZO']*1,3,',','.'); ?></td>
				<td class="textRight"><?php echo number_format($riga['PESO']*$riga['PREZZO'],2,',','.'); ?></td>
				<td class="textRight">4%</td>
			</tr>
			<?php
		}
	?>
	<!--
	<tr>
		<td>-</td>
		<td>CETRIOLI</td>
		<td>KG</td>
		<td class="textRight">4.122,0</td>
		<td class="textRight">0,400</td>
		<td class="textRight">1.648,80</td>
		<td class="textRight">4%</td>
	</tr>
	-->
		
</tbody></table>

</div>

<div id="riferimentoDDT">
	<center>
		<b>Riferimento DDT</b>
		<hr>
	</center>
	<div id="ddt">
	<?php
		foreach ($fattura->ddt as $ddt) {
			echo '<span> N. '.$ddt['numero'].' del '.$ddt['data'].'</span><br>';
		}
	?></div>
</div>

<div id="annotazioni">
	<ul>
		<li>CESSIONE AVVENUTA CON PREZZO DA DETERMINARSI AI SENSI DEL D.M. 15/11/1975.
			LA PRESENTE VALE QUALE DETERMINAZIONE DEL PREZZO.</li>
		<li>CONTRIBUTO CONAI ASSOLTO OVE DOVUTO.</li>
		<li>ASSOLVE AGLI OBBLIGHI DI CUI ALL'ART.62, COMMA 1,  DEL DECRETO 
LEGGE 24/01/2012, N.1,  CONVERTITO CON MODIFICAZIONI DALLA LEGGE 
24/03/12, N.27.</li>
	</ul>
</div>

<div id="castelletto">
	<table width="100%">
	<tbody><tr>
		<td>Imponibile</td>
		<td class="textRight"><?php echo number_format($fattura->imponibile,2,',','.'); ?></td>
	</tr>
	<tr>
		<td>IVA</td>
		<td class="textRight"><?php echo number_format($fattura->iva,2,',','.'); ?></td>
	</tr>
	<tr>
		<td><b>TOT.Fattura SE&amp;O</b></td>
		<td class="textRight"><b><?php echo number_format($fattura->totale,2,',','.'); ?></b></td>
	</tr>
	</tbody></table>
</div>

<div class="pagebreak"></div>
<div class="dettaglio">
<table>
<tr>
<td>ddt</td>
<td>data</td>
<td>fornitore</td>
<td>articolo</td>
<td>colli</td>
<td>peso</td>
<td>prezzo</td>
</tr>
<?php
		$contarighe= 0;
		foreach ($fattura->righeOriginali as $riga) {
			$contarighe++;
			if($contarighe > 60){
				$contarighe= 0;
				echo '</table></div><div class="pagebreak"></div>';

				echo '<div class="dettaglio">';
				echo '<table>';
				echo '<tr>';
				echo '<td>ddt</td>';
				echo '<td>data</td>';
				echo '<td>fornitore</td>';
				echo '<td>articolo</td>';
				echo '<td>colli</td>';
				echo '<td>peso</td>';
				echo '<td>prezzo</td>';
			
			}
			echo '<tr>';
			echo '<td>'.$riga['ddt'].'</td>';
			echo '<td>'.$riga['data'].'</td>';
			echo '<td>'.$riga['fornitore'].'</td>';
			echo '<td>'.$riga['articolo'].'</td>';
			/*
			echo '<td class="textRight">'.number_format($riga['colli']*1,0,',','.').'</td>';
			echo '<td class="textRight">'.number_format($riga['peso']*1,1,',','.').'</td>';
			echo '<td class="textRight">'.number_format($riga['prezzo']*1,3,',','.').'</td>';
			*/
			echo '<td class="textRight">'.$riga['colli'].'</td>';
			echo '<td class="textRight">'.$riga['peso'].'</td>';
			echo '<td class="textRight">'.$riga['prezzo'].'</td>';

			echo '</tr>';
		}
?>
</table>
</div>
</body></html>
