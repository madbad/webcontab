<style>
body{
	padding:2em;
	font-size:0.7em;
	font-family:Helvetica, sans-serif
	
}
table{
	width:100%;
	
}
table, td{
	font-size:1em;	
}
.finePagina {page-break-after: always;}
.titoloSezione{
	/*background-color:#e3e3e3;*/
	box-shadow:inset 0 0 0 1000px #e3e3e3;
	padding:0.6em;
	font-size:1.2em;
	font-family:Helvetica, sans-serif
}
.dataCorso{
	/*background-color:#e3e3e3;*/
	box-shadow:inset 0 0 0 1000px #e3e3e3;
	padding:1em;
	
}
.titoloCorso{
	/*background-color:#e3e3e3;*/
	box-shadow:inset 0 0 0 1000px #e3e3e3;
	padding:1em;
	
}
.borderTable{
	margin:0px;
	padding:0px;
}
.borderTable thead{
	font-weight:bold;
	box-shadow:inset 0 0 0 1000px #e3e3e3;
}
.borderTable tr{
	margin:0px;
	padding:0px;
}

.borderTable td{
	border:1px solid black;
	margin:0px;
	padding:1em;
}

/*hide page titles and number*/
@page { size: auto;  margin: 0mm; }
</style>

<?php

$datiDipendenti =
"ZAMBOTTO GIUSEPPE 				* 12/02/2008 * IMPIEGATO 					* 31/12/2019
BONIARDI MATTEO 				* 24/11/2015 * CARRELLISTA/MAGAZZINIERE		* 
BRUM DEBORA 					* 03/11/2008 * IMPIEGATo					* 
BRUN GENNI 						* 01/06/2009 * OPERAIA GENERICA CERNITRICE	* 
BRUN GIAMPAOLO 					* 05/09/2017 * CARRELLISTA/AUTISTA			* 
BRUN GIMMI 						* 20/04/2010 * AUTISTA						* 
BRUN LISA 						* 01/06/2009 * OPERAIA GENERICA CERNITRICE 	* 
BRUN MARISA 					* 04/03/2008 * OPERAIA GENERICA CERNITRICE	* 
BRUN TIZIANA 					* 06/10/2009 * OPERAIA GENERICA CERNITRICE	* 
CALMERO SIMONETTA 				* 01/10/2015 * OPERAIA CERNITRICE			* 
CIUMACENCO IGOR 				* 09/08/2004 * AUTISTA						* 
DARDOVA VERONIKA 				* 01/10/2015 * OPERAIA CERNITRICE			* 
EL HAOUZY FATIMA 				* 18/09/2017 * OPERAIA CERNITRICE			* 
FOGGINI RUDY 					* 01/10/2015 * CARRELLISTA/MAGAZZINIERE		* 
GIANELLO FABIANA 				* 04/09/2012 * OPERAIA GENERICA CERNITRIE	* 
NEZHA ONEDA 					* 08/09/2016 * OPERAIA GENERICA CERNITRIE	* 
TAKTOUR FATIMA 					* 03/04/2017 * OPERAIA GENERICA CERNITRIE	* 
TRIF IOANA MARIA 				* 09/06/2016 * OPERAIA GENERICA CERNITRIE	* 
ABOTCHI WOEWOE AMEYO VERONIQUE 	* 11/02/2019 * OPERAIA CERNITRICE			* 
EL GORSI NADIA 					* 12/02/2019 * OPERAIA CERNITRICE			* 
EL GORSI SAMIRA 				* 11/11/2019 * OPERAIA CERNITRICE			* 
TARGA RAIKA 					* 04/11/2019 * OPERAIA CERNITRICE			* 29/02/2020
NIZZARDO DANIELA 				* 05/02/2020 * OPERAIA CERNITRICE			* 
MAROCCHIO LORELLA 				* 05/02/2020 * OPERAIA CERNITRICE			* 
TAKTOUR AICHA 					* 01/10/2015 * OPERAIA CERNITRICE			* 31/01/2020
MURARI LAURA 					* 04/09/2017 * OPERAIA CERNITRICE			* 31/01/2020
IKIKER NAJIA 					* 01/07/2017 * OPERAIA CERNITRICE			* 31/01/2020
GOBBI MIRIA 					* 21/01/2020 * OPERAIA CERNITRICE			* 08/02/2020
EL YAZIDI SAIDA					* 05/11/2018 * OPERAIA CERNITRICE			* 31/05/2019
BRUN GIONNI						* 15/12/2001 * AMMINISTRATORE				* ";

//######################################################################################
//                     	DATI DIPENDENTI
//######################################################################################


$outDipendentiAssunti = array();
$outDipendentiLicenziati = array();
//FILTRA I DIPENDENTI CORRENTEMENTE ASSUNTI
$righeDipendenti = explode ( "\n" , $datiDipendenti);
foreach ($righeDipendenti as $rigaDipedente){
	$infoDipendente = explode ( "* " , $rigaDipedente);
	
	//se il dipendente non è già stato licenziato
	if($infoDipendente[3]==''){
		$outDipendentiAssunti[trim($infoDipendente[0])] = array();
		$outDipendentiAssunti[trim($infoDipendente[0])]['nome'] = trim($infoDipendente[0]);
		$outDipendentiAssunti[trim($infoDipendente[0])]['data assunzione'] = trim($infoDipendente[1]);
		$outDipendentiAssunti[trim($infoDipendente[0])]['mansione'] = trim($infoDipendente[2]);
		$outDipendentiAssunti[trim($infoDipendente[0])]['data licenziamento'] = trim($infoDipendente[3]);
	}else{
		$outDipendentiLicenziati[trim($infoDipendente[0])] = array();
		$outDipendentiLicenziati[trim($infoDipendente[0])]['nome'] = trim($infoDipendente[0]);
		$outDipendentiLicenziati[trim($infoDipendente[0])]['data assunzione'] = trim($infoDipendente[1]);
		$outDipendentiLicenziati[trim($infoDipendente[0])]['mansione'] = trim($infoDipendente[2]);
		$outDipendentiLicenziati[trim($infoDipendente[0])]['data licenziamento'] = trim($infoDipendente[3]);		
	}
}
//print_r($outDipendentiAssunti);
ksort($outDipendentiLicenziati);
ksort($outDipendentiAssunti);

//######################################################################################
//                     	STAMPA
//######################################################################################
$outTxt = '';
//output
//foreach ($outDipendenti as $nome => $daticorsi){
foreach ($outDipendentiAssunti as $nome => $dipendenteAssunto){
	?>
	<!--
	<br>
	<center>
		<h1>CONSEGNA DPI</h1>
		<hr>
		Redatto e mantenuto da <b>La Favorita di Brun G. e G. SRL Unip.</b>
		<br> Via Camagre n.38/B
		- 37063 Isola della Scala (VR) 
		- Cod.Fiscale e P.IVA 01588530236
	</center>
	-->
	<br>
	<table class="borderTable">
	<tr>
		<td width="15%"><center><img src="./img/logo.gif" alt="logo" height="50" width="132"></center></td>
		<td>
			<center>
				<span style="font-size:1.8em;">
					ELENCO INDUMENTI E MEZZI PROTETTIVI
					<BR>INDIVIDUALI PER DIPENDENTI
				</span>
			</center>
		</td>
		<td width="15%">
			Rev. 1
			<br>Del 10/04/2020
		</td>
	</tr>
	</table>
	<br>
	<table style="width:100%;border:1px solid black;" >
		<thead class="titoloSezione">
			<td style="padding:0.7em;width:30%;">Lavoratore</td>
			<td style="padding:0.7em;width:30%;">Mansione</td>
			<td style="padding:0.7em;width:20%;">Data Assunzione</td>
			<td style="padding:0.7em;width:20%;">Data Licenziamento</td>
		</thead>
		<tr>
			<td style="padding:0.7em;width:30%;"><?php echo $nome;?></td>
			<td style="padding:0.7em;width:30%;"><?php echo $outDipendentiAssunti[$nome]['mansione']; ?></td>
			<td style="padding:0.7em;width:20%;"><?php echo $outDipendentiAssunti[$nome]['data assunzione'];?></td>
			<td style="padding:0.7em;width:20%;"><?php echo $outDipendentiAssunti[$nome]['data licenziamento'];?></td>
		</tr>
	</table>
	<br>
	<table class="bordertable">
		<thead>
			<td>N.</td>
			<td>DESCRIZIONE MATERIALE IN CONSEGNA</td>
			<td>MOTIVAZIONE DEL RITIRO</td>
			<td>DATA NUOVA CONSEGNA</td>
			<td>FIRMA PER RICEVUTA</td>
		</thead>
		<?php
		for ($i=0; $i<=15; $i++){
			if($i==0){
				//echo $outDipendentiAssunti[$nome]['mansione'];
				if(strpos('CERNITRICE',$outDipendentiAssunti[$nome]['mansione'])){
					$dpi='Grembiule PVC, Vestaglia da lavoro, cuffia per capelli, guanti in cotone, guanti in gomma;';
					$data=$outDipendentiAssunti[$nome]['data assunzione'];
				}
				if(strpos('CARRELLISTA',$outDipendentiAssunti[$nome]['mansione'])){
					$dpi='Guanti da lavoro, giubotto, scarpe antinfortunistiche';
					$data=$outDipendentiAssunti[$nome]['data assunzione'];
				}
			}			
			
			if($i==1){
				$dpi='Mascherina chirurgica';
				$data='04/03/20';
			}elseif($i==2){
				$dpi='Mascherina chirurgica';
				$data='18/03/20';
			}elseif($i==3){
				$dpi='Mascherina chirurgica';
				$data='06/04/20';
			}else{
				$dpi='';
				$data='';
			}
			?>
			<tr>
				<td><?php echo $i; ?></td>
				<td><?php echo $dpi; ?></td>
				<td></td>
				<td><?php echo $data; ?></td>
				<td></td>
			</tr>
			<?php
		
		}
		
		?>
	</table>

	<span style="font-size:1.5em">
	<br>Il sottoscritto, apponendo la firma nel riquadro "FIRMA PER RICEVUTA", dichiara di essere entrato in possesso dei relativi dispositivi, di averne constatato la qualita' e di approvarne la scelta. 
	<br>Si impegna inoltre a:
	<ul>
	<li>farne un uso corretto e costante durante le lavorazioni che ne richiedono l'adozione, secondo le indicazioni fornite dal datore di lavoro;</li>
	<li>mantenerli in buono stato;</li>
	<li>riporli in luogo idoneo al termine del turno di lavoro;</li>.
	<li>informare tempestivamente il datore di lavoro in caso ne fosse necessaria la sostituzione.</li>.
	</ul>
	</span>
	<br><br>
	<span class="finePagina"></span>';
	<?php
}

?>
