<link rel="stylesheet" type="text/css" href="culdip_files/style_print.css" media="print">
<style>
html{
	width: 210mm;
	height: 287mm; /*297*/
	margin: 0mm;
	padding:0mm;
}
.ddt{
  width: 210mm;
  height: 297mm;
  top: 297mm;
  position: relative;
  page-break-after: always;
  /*border: 1px solid red;*/
  margin: 1px;
  padding: 0px;


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
  /*.pagebreak {page-break-after: always;}*/
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
	
	border-radius: 1.5mm;
}
#datiDdt{
	width: 190mm;

	position:absolute;
	top:7.8cm;
	left:0.5cm;
}
#datiDdt div{
	font-size:0.35cm;
	color:grey;
	display:inline-block;
	width:6cm;
	border:0.3mm solid black;
	border-radius: 1.5mm;
	padding: 0.5mm;
	padding-left:1mm;
	margin-bottom: 1mm;
}
#datiDdt div span{
	display:block;
	color:black;
	font-size:0.35cm;
	font-weight:bold;
}
#datiDdt2{
	width: 200mm;

	position:absolute;
	top:25.5cm;
	left:0.5cm;
}
#datiDdt2 div{
	font-size:0.35cm;
	color:grey;
	display:inline-block;
	width:6cm;
	border:0.3mm solid black;
	border-radius: 1.5mm;
	padding: 0.5mm;
	padding-left:1mm;
	margin-bottom: 1mm;
}
#datiDdt2 div span{
	display:block;
	color:black;
	font-size:0.35cm;
	font-weight:bold;
}



#acquirente span{
	display: inline-block;
	width: 3cm;
}
#righeDdt{
	width: 190mm;
	
	position:absolute;
	top:11cm;
	left:0.5cm;
}

#righeDdt table{
	width:100%;
}
#righeDdt th{
	border:0.3mm solid black;
	border-collapse: collapse;
}

#righeDdt table, #righeDdt tr, #righeDdt td{
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
#annotazioni{
	width: 19.7cm;

	position:absolute;
	top:22cm;	
	left:0.5cm;
	
	border:0.3mm solid black;
	font-size:0.35cm;
	border-radius: 1.5mm;
	padding: 0.5mm;

	font-size:0.35cm;
	color:grey;

}
#annotazioni span{
	font-size:0.30cm;
	color:black;
	font-weight: bold;
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

.doppiaDimensione{
	font-size:0.7cm !important;
}
</style>
<?php
$contaDdt=0;
foreach ($elencoDdt as $ddt) {
if ($contaDdt==0){
	echo '<div class="ddt" style="top:0px;">';
}else{
	echo '<div class="ddt">';	
}
?>

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
	Destinatario:
	<br><b>La Favorita di Brun G. &amp; G. SRL Unipersonale</b>
	<br>Via Camagre n.38/b
	<br>37063 Isola della Scala (VR)
	<br><span>Codice Fiscale:</span> 01588530236
	<br><span>Partita IVA:</span> 01588530236
	<br><span>Codice SDI:</span> 0000000
	<br><span>PEC:</span> lafavoria_srl@pec.it
</div>

<div id="datiDdt">
	<div style="width:30%">Tipo Documento<span>DOCUMENTO DI TRASPORTO<BR>(D.P.R. 472 DEL 18/08/96)</span></div>
	<div style="width:15%">Numero<span class="doppiaDimensione"><?php echo $ddt->numero;?></span></div>
	<div style="width:20%">Data<span class="doppiaDimensione"><?php echo $ddt->data;?></span></div>
	<br>
	<div style="width:32%">Causale del Trasporto<span>C/VENDITA</span></div>
	<div style="width:20%">A mezzo<span>MITTENTE</span></div>
	<div style="width:30%">Inizio del trasporto<span>-</span></div>
	<div style="width:10%">Pagina<span>1/1</span></div>
</div>

<div id="righeDdt">
<table>
	<tbody><tr>
		<th>Articolo</th>
		<th>Descrizione</th>
		<th>Prezzo</th>
		<th>Colli</th>
		<th>U.M.</th>
		<th>Peso Lordo</th>
		<th>Peso Netto</th>
		<th>Tara</th>
	</tr>
	<?php
		$contaRighe =0;
		foreach ($ddt->righe as $riga) {
			$contaRighe++;
			//echo '<span> N. '.$ddt['numero'].' del '.$ddt['data'].'</span><br>';
			?>
			<tr>
				<td>-</td>
				<td><?php echo $riga['articolo']; ?></td>
				<td> <?php /*echo number_format($riga['prezzo'],2,',','.');*/ ?></td>
				<td class="textRight"><?php echo number_format($riga['colli'],1,',','.'); ?></td>
				<td class="textRight"><?php echo $riga['um']; ?></td>
				<td class="textRight"><?php echo number_format($riga['pesoLordo'],1,',','.'); ?></td>
				<td class="textRight"><?php echo number_format($riga['pesoNetto'],1,',','.'); ?></td>
				<td class="textRight"><?php echo number_format($riga['pesoLordo']-$riga['pesoNetto'],1,',','.'); ?></td>
			</tr>
			<?php
		}
		for ($i = 0; $i < (21-$contaRighe); $i++) {
			//echo '<span> N. '.$ddt['numero'].' del '.$ddt['data'].'</span><br>';
			?>
			<tr>
				<td>-</td>
				<td></td>
				<td></td>
				<td class="textRight"></td>
				<td class="textRight"></td>
				<td class="textRight"></td>
				<td class="textRight"></td>
				<td class="textRight"></td>
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

<div id="annotazioni">
	Annotazioni
	<br><span>
		OVE NON DIVERSAMENTE INDICATO SI INTENDE: CATEGORIA I; ORIGINE ITALIA; PESO NETTO DA VERIFICARE ALLARRIVO;
		<br>CESSIONE CON PREZZO DA DETERMINAISI Al SENSI DEL D.M. 15/11/1975. || CONTRIBUTO CONAI ASSOLTO OVE DOVUTO,
		<br>ASSOLVE AGLI OBBLIGH DI CUI ALL'ART.62, COMMA 1, DEL DECRETO LEGGE 24/01/2012, N.1, CONVERTETO CON MODIFICAZIONI DALLA LEGGE 24/03/12,N27.
	</span>
</div>

<div id="datiDdt2">
	<div style="width:24%">Aspetto dei Beni<span>VISIBILE</span></div>
	<div style="width:34%">Incaricato del trasporto<span>&nbsp;</span></div>
	<div style="width:37%">Firma del conducente<span>&nbsp;</span></div>
	<br>
	<div style="width:60%">Annotazioni - Variazioni<span>&nbsp;</span></div>
	<div style="width:37%">Firma del destinatario<span>&nbsp;</span></div>

</div>
</div>
<!--<div class="pagebreak"></div>-->
<?php
}
?>

</body></html>
