<!DOCTYPE html>
<html lang="IT"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
        <!--
		<link rel="stylesheet" type="text/css" href="style.css">
		-->

		<link rel="stylesheet" type="text/css" href="culdip_files/style_print.css" media="print">
		<style>
		#selettoreDati{
			padding:1em;
			position:absolute; 
			top:0px; 
			left:0px; 
			z-index:100;
			opacity:0.1;
			background-color:green;
			font-size:1.5em;
		}
		#selettoreDati:hover{
			opacity:0.9
		}
		#selettoreDati input {
			font-size:1.0em;
		}
		</style>
    </head>
     <body>
<div id="selettoreDati">
<form action="./fattura.ods.php" class="dateform hideOnPrint" method="post"> 
	<span class="dateformtitle">Selezione parametri</span>
	<br> <span class="dateselectordescription" style="width:5em;display:inline-block;">From:</span>
	<input class="dateselector" type="date" name="inizio" value="2023-06-01">
	<br> <span class="dateselectordescription" style="width:5em;display:inline-block;">to:</span>
	<input class="dateselector" type="date" name="fine" value="2023-06-30">
	<br><input list="fornitori" type="text" name="fornitore" value="KULDIP" autofocus="">
	<datalist id="fornitori"><option value="KULDIP
"></option><option value="TUNG
"></option><option value="BUONA TERRA
"></option><option value="GURU NANAK
"></option><option value="ASHA
"></option><option value="PARMINDER
"></option><option value="ZILOCCHI
"></option><option value="NICOLIS
"></option><option value="ORTO DI KAUR
"></option><option value="GURDEEP
"></option><option value="SHANGARA
"></option><option value="VEER
"></option><option value="VEER
"></option><option value="FACCINI
"></option><option value="SANDEEP
"></option><option value="SCHIAVO
"></option><option value="HARBHINDER
"></option><option value="ZOHRA
"></option><option value="MAAN
"></option><option value="NDBK
"></option><option value="ROSSIGNOLI
"></option><option value="SIDHU
"></option><option value="AMNINDER
"></option><option value="ZAPPOLA
"></option><option value="HARJIT
"></option><option value="MUHAMMAD
"></option><option value="SCOLARI
"></option><option value="HARPAL
"></option><option value="TIZIANI
"></option><option value="BRUNO
"></option><option value="MIGLIORINI
"></option><option value="TORRESANI
"></option><option value="GIANELLO
"></option><option value="SUKHWINDER
"></option><option value="SARTAJ
"></option><option value="BALJINDER
"></option><option value="MANJIT
"></option><option value="NANAK
"></option></datalist>	<br><input type="submit" value="Submit" style="padding:1em;width:20em;">
</form>
</div>
<style>
html{
padding-left:2.5em;
padding-right:2.5em;
}
@page { size: auto;  margin: 0mm; }

#venditore{
	/*border:1px solid black;*/
	width:90%;
	padding:1em;
	font-size:1.2em;
}

.nomeAzienda{
	font-size:1.6em;
	font-weight:bold;
}

#acquirente{
	border:1px solid black;
	width:40%;
	position:absolute;
	right:3em;
	padding:1em;
	top:10em;

}
#datiFattura{
	width:100%;
	top:20em;
	position:absolute;
}
#datiFattura div{
	font-size:1em;
	color:grey;
	display:inline-block;
	width:10em;
	border:1px solid black;
}
#datiFattura div span{
	display:block;
	color:black;
	font-size:1.3em;
	font-weight:bold;
}
#acquirente span{
	display: inline-block;
	width: 7em;
}
#righeFattura{
	top:28em;
	position:absolute;
	width:90%;
}

#righeFattura table{
	width:100%;
}
#righeFattura th{
	border:1px solid black;
	border-collapse: collapse;
}

#righeFattura table, tr, td{
	border:1px solid black;
	border-collapse: collapse;
	border-top:0px solid black;
	border-bottom:0px solid black;
	padding-left:0.3em;
	padding-right:0.3em;
	padding-top:0em;
	padding-bottom:0em;
	font-size:0.93em;
}
#riferimentoDDT{
	position:absolute;
	top:70em;
	border:1px solid black;
	width:90%;
}
#ddt{
	columns: 5;
	column-rule: 2px solid grey;
	padding:0.5em;
}


#riferimentoDDT span{
	/*
	border: 1px solid gray;
	padding:0.3em;
	*/
}
#annotazioni{
	position:absolute;
	top:85em;
	border:1px solid black;
	width: 50%;
	left:2em;
}
#castelletto{
	position:absolute;
	top:85em;
	border:1px solid black;
	width: 35%;
	right:2em;
}
.textRight{
text-align:right;
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
	?><div>
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
</body></html>
