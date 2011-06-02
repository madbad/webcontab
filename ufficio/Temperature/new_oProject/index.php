<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- se imposto il docutype firefox mi sballa il posizionamento della finestrella-->
<html>

<head>
  <title>New oProject - OGame tools</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<link rel="stylesheet" type="text/css" href="./../oLibs/ld/style.css">
	<link  rel="stylesheet" type="text/css" href="./../oLibs/MyWindow/style.css">
	<link rel="stylesheet" type="text/css" href="./style.css">
	<link rel="stylesheet" type="text/css" href="./../oLibs/style2.css">
</head>
<body onload="Inizializza()">
<!-- il modo corretto per fare uno script-->
  <script language="javascript" type="text/javascript">
  // <!--
  // -->
  </script>

<!-- PRELOAD: carico tutte le librerie necessarie -->

	<div id="mainLoad">
		<br><br><br><br><br><br>
		<center>
			<h1>
				<strong>oProject</strong>
			</h1>
			<sup>OGame Combar-Report &#038; Spy-Report Converter</sup>
			<br><br><br><br>
			<img alt="Loading" height="14" width="74" src="./../oLibs/ld/img/loading.gif"><br>
			<table cellspacing="0px">
				<tr>
				<td><img alt=":" height="36" width="6" src="./../oLibs/ld/img/bar_start.gif"></td>
				<td><img alt="*" height="36" width="0" src="./../oLibs/ld/img/bar_on.gif" id="loadON"></td>
				<td><img alt="-" height="36" width="400" src="./../oLibs/ld/img/bar_off.gif" id="loadOFF"></td>
				<td><img alt=":" height="36" width="6" src="./../oLibs/ld/img/bar_end.gif"></td>
				</tr>
			</table>
			
			<div id="loadTXT"></div>
		</center>
	</div>
	<!-- preloader -->
	<script type="text/javascript" src="./../oLibs/ld/script.js"></script> 
	<!-- MyWindow -->

	<script type="text/javascript" src="./../oLibs/MyWindow/drag.js"></script>
	<script type="text/javascript" src="./../oLibs/MyWindow/MyWindow.js"></script>
<div id="SpaceCalculator"></div>

	<!--Richiamo i fogli Javascript del converter-->
	<script type="text/javascript">ld(20,'Definizioni Risorse');</script> 
	<script src="./../oLibs/defRISORSE.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(25,'Definizioni Flotte');</script>
	<script src="./../oLibs/defFLOTTA.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(30,'Definizioni Difese');</script>
	<script src="./../oLibs/defDIFESE.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(35,'Definizioni Costruzioni');</script>
	<script src="./../oLibs/defCOSTRUZIONI.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(40,'Definizioni Tecnologie');</script>
	<script src="./../oLibs/defTECNOLOGIE.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(45,'Definizioni Pianeta');</script>
	<script src="./../oLibs/defPIANETA.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(55,'Gestore formattazione e colori');</script>
	<script src="./../oLibs/defALTRICOLORI.js" language="Javascript" type="text/javascript"></script>
	<script src="./../oLibs/formattazione.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(65,'Gestore Spy Report');</script>
	<script src="./../oLibs/leggispyreport.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(75,'Gestore Combat Report');</script>
	<script src="./../oLibs/convertiCR.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(80,'Gestore Globale');</script>
	<script src="./../oLibs/global.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(85,'Gestore Cookies');</script>
	<script src="./../oLibs/cookie.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(90,'Gestore Ajax');</script>
	<script src="./../oLibs/ajax.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(95,'Definizioni Formule');</script>
	<script src="./../oLibs/formule.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(98,'Gestore Roket Report');</script>
	<script src="./../oLibs/rocketReport.js" language="Javascript" type="text/javascript"></script>
	<script type="text/javascript">ld(99,'Widget');</script>
	<script src="./../oLibs/regolatore.js" language="Javascript" type="text/javascript"></script>
	<script src="./../oLibs/div.js" language="Javascript" type="text/javascript"></script>
	<script src="./../oLibs/rocket.js" language="Javascript" type="text/javascript"></script>
	<script language="Javascript" type="text/javascript">
		//inizializzo questa variabile che mi serve per lo SRconverter
		function simulatore(){
			this.pianeta=new pianeta();
			this.risorsa=new risorsa();
			this.difesa=new difesa();
			this.nave=new nave();
			this.costruzione=new costruzione();
			this.tech=new tech();

			var $$listaFlotte=this.nave;
			var $$listaDifese=this.difesa;

			
			//aggiorno la lista colori
		this.AggiornaListaColori=function(){
				alert(document.forms['Color']['Cargo leggero'].value);
	
		
			for ($$arma in $$listaFlotte){if(typeof $$listaFlotte[$$arma]!='function'){
				if($$listaFlotte[$$arma].name){
	//			test=$$listaFlotte[$$arma].name;
	//alert(test);				
	//alert(document.forms['Color'][$$listaFlotte[$$arma].name].value);
					//$$listaFlotte[$$arma].color.Personalizzato=document.forms['Color'][$$listaFlotte[$$arma].name].value;
					$$listaFlotte[$$arma].color.Personalizzato='#000000';
				}}}
			for ($$arma in $$listaDifese){if(typeof $$listaDifese[$$arma]!='function'){
				if($$listaDifese[$$arma].name){
					$$listaDifese[$$arma].color.Personalizzato=document.forms['Color'][$$listaDifese[$$arma].name].value;
				}}}

			}
		}
		//queste due invece mi servono per il missilatore
		difensore=new simulatore;
		attaccante=new simulatore;
		//
		var vAltriColori=new AltriColori();
		opzioni='';
		////////////////////////////////////////////////////////////////
		versione='1.1.6';
		function Inizializza(){
			//Creo i form per i colori personalizzati
			function CreaFormColori(){
				var $$listaFlotte=new nave();
				var $$listaDifese=new difesa();
				var $$t;
				var $$arma;
				var $$html='';
				for ($$arma in $$listaFlotte){if(typeof $$listaFlotte[$$arma]!='function'){
				if($$listaFlotte[$$arma].name){
					$$html+='<input name="'+$$listaFlotte[$$arma].name+'" style="width:7em;" value="#000000"> '+$$listaFlotte[$$arma].name+'<br>';
				}}}
				for ($$arma in $$listaDifese){if(typeof $$listaDifese[$$arma]!='function'){
				if($$listaDifese[$$arma].name){
					$$html+='<input name="'+$$listaDifese[$$arma].name+'" style="width:7em;" value="#000000"> '+$$listaDifese[$$arma].name+'<br>';
				}}}
			return $$html;
			}
			document.getElementById('ColorForm').innerHTML+=CreaFormColori();
difensore.AggiornaListaColori();
attaccante.AggiornaListaColori();
			//CercaAggiungiRegolatori();
			CreaTabella(); //crea la tabella per il rocket simulator
			ListaColori();
			//abilito i calcoli sul deuterio
			if(document.CrConverter.calcolaDeuterio.checked==true){document.CrConverter.percentaleInvioNavi.disabled=false}

			pasteboxStartMsg='Incolla qui il rapporto o i rapporti da convertire (Combat-report o Spy-report) incluse le eventuali relative riciclate.';
			document.getElementById('reportTxt').value=pasteboxStartMsg;

			selectTAB('Converter','Contenuti');
			AggiornaMenuProfili();
			LeggiOpzioni(document.forms.Report.SelezionaProfilo.value);

			AggiungiRichiesta('./count.php','cCR',"0");
			AggiungiRichiesta('./count.php','cSR',"0");
		}
	</script>
	<script type="text/javascript">ld(100,'COMPLETATO');</script>
<!--//
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
Qua inizio la pagina web vera e propria
////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////
//-->
<div>
<table class="midTable" cellspacing="0px">
	<tr>
		<td colspan="3" style="margin:0px;height:45px;">
			<table class="headerTable" cellspacing="0px">
				<tr>
					<td class="headerLogo"></td>
					<td class="headerBg">&#160;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="midLogo" rowspan="2"></td>
		<td class="midAngolo"></td>
		<td><center>
			<table class="minibarTable" cellspacing="0px">
			<tr>
			<td class="minibarSX"></td>
			<td class="minibarBG">
				<div style="float:right;" id="AjaxStatus" class="hide"></div>
				<div style="float:left;">
				 <i>CR convertiti</i>: <span id="cCR"></span>
				| <i>SR convertiti</i>: <span id="cSR"></span>
				| <i>RR convertiti</i>: <span id="cRR"></span>
				</div>

				<div style="float:right;">
				<a href="#" onclick="javascript:$opt=new Array();$opt['name']='MyChangelog';$opt['txt']=document.getElementById('changelog').innerHTML;MyInfo($opt);">Changelog</a>
				</div>
			</td>
			<td class="minibarDX"></td>
			</tr>
			</table>
			</center>
		</td>
	</tr>
	<tr>
		<td class="midVBg">&#160;</td>
		<td></td>
	</tr>
	<tr>
		<td class="menu">
			<a href="javascript:selectTAB('CR','MyOptionsTabs');OpzioniConverter.show();"
			onmouseover="$opt=new Array();$opt['this']=this;$opt['txt']='<b>Impostazioni</b> del profilo corrrente.';$opt['name']='tipImpostazioni';MyTips($opt);"
			><img src="img/impostazioni.png" border="0px" onclick="this.blur();" ></a>

			<a href="javascript:selectTAB('Converter','Contenuti')" id="LinkConverter"
			onmouseover="$opt=new Array();$opt['this']=this;$opt['txt']='<b>Converter</b> di Combat-report, Spy-report e creatore rapporti di missilata.';$opt['name']='tipConverter';MyTips($opt);"
			><img src="img/converter.png" border="0px" onclick="this.blur();"></a>

			<a href="javascript:selectTAB('Missilatore','Contenuti')" id="LinkMissilatore" onclick="this.blur();"
			onmouseover="$opt=new Array();$opt['this']=this;$opt['txt']='<b>Missilatore</b> Simula un attacco missilistico';$opt['name']='tipMissilatore';MyTips($opt);"
			><img src="img/rocket.png" border="0px"></a>

<script>
function apriFalange(){
open("http://www.madbad.altervista.org/OGame/Falange/Falangiatore.html","_blank","toolbar=no,location=0,directories=no,status=no,menubar=0,scrollbars=yes,resizeable=1,copyhistori=0,width=360,height=570");
}
</script>

			<a href="javascript:apriFalange();" onclick="this.blur();"
			onmouseover="$opt=new Array();$opt['this']=this;$opt['txt']='<b>Falange</b> Consente di sincronizzare gli orari di lancio di flotte e recy su attacchi di falange.';$opt['name']='tipFalange';MyTips($opt);"
			><img src="img/orologio.png" border="0px"></a>
		</td>
		<td class="midVBg"></td>
		<td class="midContent" align="center">
			<div id="Contenuti">
				<div id="Converter" class="hide">
				<form name="Report" action="">
				<table valign="top" class="content">
					<tr>
						<td>
<div class="salva"><span style="float:left;padding-right:10px;">Report salvati: </span>
<a href="javascript:alert('salvato')">1</a>
<a href="javascript:alert('salvato')">2</a>
<a href="javascript:alert('salvato')">3</a>
<a href="javascript:alert('salvato')">4</a>
<a href="javascript:alert('salvato')">5</a>
<a href="javascript:alert('salvato')">6</a>
</div>
<div style="background-color:orange;clear:both;">
<b><i>Informazioni dal converter:</i></b>
<div id="avvisi">Prova</div>
</div>

<div style="clear:both;"></div>
							<table valign="top">
								<tr>
									<td class="textareaInfo"  bgcolor="red" valign="top" style="color:#7B0000;" >I<br>N<br>C<br>O<br>L<br>L<br>A</td>
									<td valign="top">
										<textarea rows="4" cols="" style="width: 100%;" wrap="virtual" onfocus="this.rows=12;if(this.value==pasteboxStartMsg){this.value='';}" onblur="this.rows=5;if(this.value==''){this.value=pasteboxStartMsg;}" id="reportTxt">
										</textarea>
									</td>
								</tr>
								<tr>
									<td class="textareaInfo"  bgcolor="orange" valign="top" style="color:#96622E;">C<br>O<br>P<br>I<br>A</td>
									<td valign="top"><textarea rows="4" cols="" style="width: 100%;" wrap="virtual" onfocus="this.rows=12;" onblur="this.rows=5"  name="converted" readonly>Da qui potrai copiare il rapporto convertito pronto per essere incollato sul forum.</textarea></td>
								</tr>
								<tr>
									<td class="textareaInfo" bgcolor="green" valign="top" ><font style="color:#DDFFCF;">A<br>N<br>T<br>E<br>P<br>R<br>I<br>M<br>A</font></td>
									<td valign="top"><div id="anteprima" style="height:100%;">Anteprima del report convertito </div></td>
								</tr>
							</table>
						</td><td valign="top">
							<a href="#" onfocus="blur()"  class="svuota" onclick="javascript:document.forms.Report.reportTxt.value='';return false;" >
								<img alt="X" src="./../oLibs/immagini/vuoto33x33.gif" width="21" height="21" border="0" />
							</a><br><br>
							<input type="button" value="Converti" onclick="javascript:converti();this.blur()">
							
							
							Seleziona profilo:
							<br>
							<select name="SelezionaProfilo" onchange="LeggiOpzioni(this.value)">
							</select>
							<input type="button" value="Cancella prof." onclick="if(confirm('Desideri veramente cancellare il profilo \''+document.forms.Report.SelezionaProfilo.value+' \'?')){CancellaProfilo(document.forms.Report.SelezionaProfilo.value);AggiornaMenuProfili();LeggiOpzioni(document.forms.Report.SelezionaProfilo.value);}">
							<input type="button" value="Nuovo prof." onclick="MemorizzaOpzioni(prompt('Indicare un nome per il nuovo profilo:'))">
							<input type="button" value="Impost. prof." onclick="this.blur();selectTAB('CR','MyOptionsTabs');OpzioniConverter.show();">
						</td>
					</tr>
				</table>
			</form></div>
<!--*****************************************
missilatore
******************************************-->
			<div id="Missilatore" class="hide">
				<table valign="top" class="content">
					<tr>
						<td>
					<form name="oRocket" action="">
						<table id="difensore" width="100%">
							<thead>
								<tr><td colspan="10">Difensore</td></tr>
							</thead>
							<tbody>
								<tr class="titoli">
									<td>Tipo</td>
									<td>Numero</td>
									<td>Richiede</td>
									<td>Rimasti</td>
									<td>Distrutti</td>
									<td>Metallo</td>
									<td>Cristallo</td>
									<td>Deuterio</td>
								</tr>
							</tbody>
							<tfoot class="rigaTOTALI">
								<tr class="riga">
									<td><script>document.write(difensore.tech.tecnologiadegliscudi.name)</script></td>
									<td>
									<script>document.write('<input type="text" id="'+difensore.tech.tecnologiadegliscudi.name+'">')</script></td>
									<td><b>Totali</b></td>
									<td id="TotaliRimasti">-</td>
									<td id="TotaliDistrutte">-</td>
									<td id="TotaliMetallo">-</td>
									<td id="TotaliCristallo">-</td>
									<td id="TotaliDeuterio">-</td>
								</tr>
								<tr>
							</tfoot>
						</table>
						<table id="attaccante" width="100%">
							<thead class="rigaTITOLO">
								<td colspan="8">Attaccante</td>
							</thead>
								<tr class="titoli">
									<td>Tipo</td>
									<td>Numero</td>
									<td>Richiede</td>
									<td>Rimasti</td>
									<td>Distrutti</td>
									<td>Metallo</td>
									<td>Cristallo</td>
									<td>Deuterio</td>
								</tr>
								<tr class="riga">
									<td>Missili da inviare</td>
									<script>
										document.write('<td><input type="text" id="'+attaccante.difesa.missiliinterplanetari.name+'A'+'"></td>');
										document.write('<td id="'+attaccante.difesa.missiliinterplanetari.name+'A'+'Richiede">-</td>');
										document.write('<td id="'+attaccante.difesa.missiliinterplanetari.name+'A'+'Rimasti">-</td>');
										document.write('<td id="'+attaccante.difesa.missiliinterplanetari.name+'A'+'Distrutti">-</td>');
										document.write('<td id="'+attaccante.difesa.missiliinterplanetari.name+'A'+'Metallo">-</td>');
										document.write('<td id="'+attaccante.difesa.missiliinterplanetari.name+'A'+'Cristallo">-</td>');
										document.write('<td id="'+attaccante.difesa.missiliinterplanetari.name+'A'+'Deuterio">-</td>');
									
								</script>
								</tr>
								<tr class="riga">
										<td><b><script>document.write(attaccante.tech.tecnologiadellearmi.name)</script></b></td>
										<td><script>document.write('<input type="text" id="'+attaccante.tech.tecnologiadellearmi.name+'">')</script>
										<td colspan="2"><b>Obbiettivo Primario</b></td>
										<td colspan="4" align="left">
											<select name="ObiettivoPrimario">
											<option selected>Tutti
											<script>
											for ($arma in difensore.difesa){if(typeof difensore.difesa[$arma]!='function'){
											if(difensore.difesa[$arma].name!='Missili anti-balistici' && difensore.difesa[$arma].name!='Missili interplanetari' && difensore.difesa[$arma].name!=undefined){
											document.write('<option>'+difensore.difesa[$arma].name+'</option>')
											}}}
											</script>
											</select>
										</td>
									</tr>
								</table>
							</form>
							<form name="RocketSpyReport" action="">
							<table id="spyREPORT">
								<thead class="rigaTITOLO">
									<td colspan="2">Spy Report</td>
								</thead>
								<tr class="riga">
									<td>
										<button name="spio" type="button" onClick="CompilaCampi()">Leggi lo Spy-Report</button>
										<INPUT type="reset" value="Cancella lo Spy-Report">
									</td>
									<td>
										<textarea name="reportTxt" cols="60" rows="2"></textarea>
									</td>
								</tr>
							</table>
							</form>
						</td><td valign="top">
							<input type="button" name="ll" onClick="esegui('SimulaAttacco')" value="Attacca">
							<input type="button" name="reset" onClick="resetta()" value="Resetta valori">
							<input type="button" name="rak" onClick="esegui('CalcolaMissiliNecessari')" value="Calcola num. missili"><br><br>
						</td>
					</tr>
				</table>
			</form>
			</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="menu"></td>
		<td class="midVBg"></td>
		<td class="midFooter">
			<center><div>
				<b>oProject</b> - Tools for OGame - Orgogliosamente sviluppato da
				<a href="http://madbad.altervista.org/">MADBAD</a> - powered by 
				<a href="http://altera.forumup.it/">AlterA</a>
			</div></center>
		</td>
	</tr>
</table>
</div>

<!--//
////////////////////////////////////////////////////////////////////////////////////
Qui ci metto i div con le opzioni che restano invisibili fino a quando non viene aperta la relativa finestra
////////////////////////////////////////////////////////////////////////////////////
//-->


<div id="FinestraOpzioni" class="hide">
			<input type="button" style="float:right;" value="Salva prof." onclick="if(MemorizzaOpzioni()){$opt=new Array();$opt['txt']='Impostazioni memorizzate';$opt['name']='memoImp';MyInfo($opt);};this.blur()">
				<!--<form name="Profili" action="">
					<br><span>Profili esistenti </span><select name="listaProfili">
							<option></option>
					</select>
					<input type="text" name="nuovoProfilo"> Nascondi Coordinate

					<br><input type="button" name="eliminaProfilo"> Nascondi Costruzioni
					<br><input type="button" name="nuovoProfilo"> Nascondi Tecnologie

				</form>-->


					<div id="Globali" class="hide">
					<h3>Opzioni globali (valide per CR SR RR) </h3><hr>
					<form name="Global" action="">
						<br><span>Codice di conversione: </span><select name="mode">
							<option value="forum">Forum [BBcode]</option>
							<option value="html">Pagina Web [html]</option>
							<option value="forumfree">Forum [ForumFree]</option>
						</select>
						<br><span>Colore: </span><select name="color">
							<option></option>
						</select>
						<br><input type="checkbox" name="DebugMode" checked> Attiva modalita debug 
						<br><input type="checkbox" name="TagQuote" checked> Aggiungi il tag quote
						<br>Tasso di cambio (es: "3:2:1") <input type="text"  maxlength="5" size="5" name="TassoDiCambio">
					</form>
					</div>

<!-- SPy converter-->
					<div id="SR" class="hide">
					<h3>Opzioni Spy-report</h3> <hr>
						<form name="SrConverter" action="">
							<input type="checkbox" name="hidePianeta"> Nascondi Coordinate
							<br><input type="checkbox" name="hideRisorsa"> Nascondi Risorse
							<br><input type="checkbox" name="hideNave"> Nascondi Flotta
							<br><input type="checkbox" name="hideDifesa"> Nascondi Difese
							<br><input type="checkbox" name="hideCostruzione"> Nascondi Costruzioni
							<br><input type="checkbox" name="hideTech"> Nascondi Tecnologie
						</form>
					</div>

<!-- CR converter-->
					<div id="CR" class="hide">
						<h3>Opzioni Combat-report</h3> <hr>
						<h3>Visuali</h3>
						<form name="CrConverter" action="">
							<input type="checkbox" name="TagCenter"> Aggiungi il tag center
							<br><input type="checkbox" name="hideNomeDifensore"> Nascondi nome Difensore
							<br><input type="checkbox" name="hideNomeAttaccante"> Nascondi nome Attaccante
							<br><input type="checkbox" name="hideTecnologie" checked> Nascondi Tecnologie
							<br><input type="checkbox" name="hideCoordinate" checked> Nascondi Coordinate
							<br><input type="checkbox" name="hideDataOra"> Nascondi data e ora
							<br><input type="checkbox" name="showRounds12"> Mostra solo primo e ultimo round
							<br><input type="checkbox" name="showColonna" checked> Visualizza nomi in colonna
							<br>Usa nomi navi <select name="nomiNaviDifese">
								<option value="name">Estesi</option>
								<option value="shortname">Abbreviati</option>
							</select>
							<br>Separatore <select name="separatore">
								<option value="••">••</option>
								<option value="[]">[ ]</option>
								<option value="()">( )</option>
								<option value="{}">{ }</option>
								<option value="~~">~ ~</option>
								<option value="«»">« »</option>
								<option value="‘’">‘ ’</option>
								<option value="“”">“ ”</option>
								<option value="--">- - </option>
								<option value="/\">/ \</option>
								<option value="<>"><></option>
							</select>
							<br><input type="checkbox" name="usaColoreNaviPerNumeroNavi" checked> Usa colore navi anche per il numero navi


							<h3>Avanzate</h3>
							<input type="checkbox" name="showStatisticheAvanzate" checked> Usa statistiche avanzate
							<br>Genera statistiche per <select name="chiConverte">
								<option value="attaccante">Attaccante</option>
								<option value="difensore">Difensore</option>
							</select>
							<br><input type="checkbox" name="calcolaDeuterio" onchange="if(this.checked==true){document.CrConverter.percentaleInvioNavi.disabled=false}else{document.CrConverter.percentaleInvioNavi.disabled=true}"> Calcola costo deuterio

							<select name="percentaleInvioNavi" disabled>
								<option value="100">100%</option>
								<option value="90">90%</option>
								<option value="80">80%</option>
								<option value="70">70%</option>
								<option value="60">60%</option>
								<option value="50">50%</option>
								<option value="40">40%</option>
								<option value="30">30%</option>
								<option value="20">20%</option>
								<option value="10">10%</option>
							</select>
							<a href="javascript:selectTAB('TECH','MyOptionsTabs')">Imposta Tech.Motori</a>
							<br><input type="checkbox" name="showStatisticheAvanzateMulti"> Mostrare le statiche avanzate di ogni CR anche se multipli

						</form>
					</div>
<!-- Tecnologie-->
					<div id="TECH" class="hide">
						<form name="MyTech" action="">
						<h3>Tecnologie (usate da tutto il tool) </h3><hr>
						<br><input type="text" class="addReg" name="motoreCombustione"> &#160;Mot. Combustione
						<br><input type="text" class="addReg"  name="motoreImpulso"> &#160;Mot. Impulso
						<br><input type="text" class="addReg" name="motoreIperspaziale"> &#160;Mot. Iperspaziale
						</form>
					</div>
<!-- Tecnologie-->
					<div id="COLORI" class="hide">
						<form name="Color" id="ColorForm" action="">
						<h3>Colori usati nella conversione</h3><hr>
						</form>
					</div>
</div>
<!--Preparo la finestra con le impostazioni-->
<script>
$opt=new Array();
$opt['name']='OpzioniConverter';
$opt['txt']=document.getElementById('FinestraOpzioni').innerHTML;
bottoni=document.getElementById('FinestraOpzioni').getElementsByTagName('div');

pulsantiera='';
for (var i = 0; i < bottoni.length; i++) {
	//pulsantiera+='<a href="#" onclick="document.getElementById(\''+bottoni[i].id+'\').setAttribute(\'class\',\'show\')">'+bottoni[i].id+'</a>';
pulsantiera+='<a href="javascript:selectTAB(\''+bottoni[i].id+'\',\'MyOptionsTabs\');" onclick="this.blur()" id="Link'+bottoni[i].id+'" id="Link">'+bottoni[i].id+'</a>';

}
document.getElementById('FinestraOpzioni').innerHTML='';
$opt['txt']='<table width="100%""><tr><td valign="top" class="MyDialog" style="border-right:1px #B8B8B8 solid;">'+pulsantiera+'</td><td valign="top" style="width:100%;padding-left:20px;padding-right:20px;"><div id="MyOptionsTabs">'+$opt['txt']+'</div></td></tr></table>';
$opt['isVisible']=false;
$opt['isPermanent']=true;
$opt['width']=600;
$opt['height']=700;

MyDialog($opt);

selectTAB('CR','MyOptionsTabs');
OpzioniConverter.centra();

</script>
<!-- inizio il Changelog -->
	<div class="hide" id="changelog">
		<span class="changelog">
		<br><h3>To Do List</h3>
		<b>Cose che saranno implementate nelle prossime versioni:</b>
		<br> - Restyling dell'output.
		<br> - Personalizzazione di Nomi Navi e Difese.
		<br> - Personalizzazione dei colori.
		<br> - Differenti impostazioni per la visualizzazione delle statistiche.
		<hr>
	
		<h3>Bug's noti</h3>
		<br> - Problemi con Internet Explorer.
		<br> - Problemi con Opera.
		<hr>
	
		<br><br><h3>Changelog</h3>
		
		<p>v. 1.1.6 beta (17/10/08)
		<ul>
		<li>Aggiunto il tipo di conversione per forum ForumFree (grazie a SaRoBoo [UNIONjr] per la richiesta.</li>
		</ul></p>

		<p>v. 1.1.5 beta (05/10/08)
		<ul>
		<li>Aggiornato il riconoscimento degli spy report con l'aggiunta del riconoscimento del nick del giocatore</li>
		<li>Correzzione sul riconoscimento dell'attività sul pianeta che viene mostrata solo quando effettivamente rilevata</li>
		<li>Vengono ora eliminate le stringhe del tipo "Totale: 358.225	Attacco: 10" aggiunte qualche estensione per firefox agli SR</li>
		</ul></p>

		<p>v. 1.1.4 beta (03/02/08)
		<ul>
		<li>Dopo averlo sistemato riabilitata l'opzione per il calcolo deuterio. (attualmente calcola solo quello delle navi)</li>
		<li>Corretta l'inversione nel riconoscimento delle cupole</li>
		<li>Rimosse le parentesi se non ci sono coordinate da mostrare per attaccante e/o difensore</li>
		<li>Il deuterio viene conteggiato solo se a convertire è l'attaccante</li>
		</ul></p>

		<p>v. 1.1.3 beta (30/12/07)
		<ul>
		<li>Correzzione nel calcolo del guadagno convertito in deuterio</li>
		<li>Inserimento della tecnologia delle spedizioni tra le tech</li>
		<li>Disabilitata la scritta sfumata che di punto in bianco ha iniziato a darmi errori strani</li>
		</ul></p>

		<p>v. 1.1.2 beta (26/12/07)
		<ul>
		<li>E'ora possibile definire tra le opzioni globali il tasso di cambio desiderato</li>
		<li>(MyWindows) i tooltip ora scompaiono automaticamente onmouseout senza doverlo impostare manualmente</li>
		<li>Ritocco nel riconoscimento delle coordinate dei pianeti nei CR</li>
		</ul></p>

		<p>v. 1.1.1 beta (25/12/07)
		<ul>
		<li>Modifiche sostanziali alla memorizzazione dei cookies dei profili che non funzionava in presenza di un numero eccessivo di profili</li>
		</ul></p>

		<p>v. 1.1.0 beta (23/12/07)
		<ul>
		<li>Finalmente introdotti i PROFILI per le impostazioni</li>
		<li>Modifiche alla schermata del Converter e alla finestra impostazioni relative alla introduzione dei profili</li>
		<li>Qualche altro piccolo ritocco al menu laterale</li>
		</ul></p>

		<p>v. 1.0.0 beta (22/12/07)
		<ul>
		<li>Aggiunta un'opzione che consente di colorare il numero di navi col colore del nome delle navi</li>
		<li>Aggiunta la possibilità di scere tra una discreta gamma di separatori</li>
		<li>Integrato il tool falange</li>
		<li>Sistamata la visualizzazione dei CR se le navi non sono in colonna</li>
		<li>Sistamato il posizionamento delle finestre (MyWindow)</li>
		<li>Revisione del menu con nuovo set di icone e tooltip per ogni voce</li>
		</ul></p>

		<p>v. 0.9.8 (work in progress)
		<ul>
		<li>Aggiunta la rilevazione dei segni di attività negli spyreport</li>
		<li>Sistemato un bug che impediva di convertire un CR dopo uno SR</li>
		<li>Sistemate le impostazioni per le notifiche (MyWindows)</li>
		<li>More to come...</li>
		</ul></p>

		<p>v. 0.9.7 (08/08/07)
		<ul>
		<li>Aggiunto in fase beta un creatore di rapporti per le missilate</li>
		<li>Risolto il mancato riconoscimento delle risorse catturate quando ci sono numeri negativi</li>
		<li>Risolto un problema relativo alle impostazioni memorizzate per gli spy-report</li>
		<li>Modificata la visualizzazione delle statistiche che ora mostra il dettaglio solo per il guadagno finale netto</li>
		</ul></p>
	
		<p>v. 0.9.6 (21/07/07)
		<ul>
		<li>Aggiunte le opzioni per il calcolo del deuterio (anche se non ancora attivabili)</li>
		<li>Riconoscimento delle difese ricostruite</li>
		</ul></p>
	
		<p>v. 0.9.5 (12/06/07)
		<ul>
		<li>Correzioni sulla visualizzazione delle tech nei CR</li>
		<li>Correzione della possibilità luna che a volte non viene riconosciuta</li>
		<li>Correzione nei combattimenti in equilibrio (la scritta distrutto non viene più mostrata su entrambe le flotte)</li>
		<li>Correzione nelle risorse catturate per conversione di CR multipli che causava tra l'altro una ripetizione di scritte NaN</li>
		</ul></p>
		
		<p>v. 0.9.4 (10/06/07)
		<ul>
		<li>Aggiunta la possibilità di inserire il tag Center per i CR</li>
		<li>Nuovo menù laterale</li>
		<li>Aggiunta di un pulsante per svuotare rapidamente il form dei report</li>
		<li>Resa più comprensibile la scelta dei colori</li>
		</ul></p>
	
		<p>v. 0.9.3 (13/05/07)
		<ul>
		<li>Cambiati il colori della pagna dalla tonalità "rosso omicida" a quella "azzurrino bravo ragazzo" ^^</li>
		<li>Temporaneamente disabilitata la colorazione su sfondi scuri</li>
		<li>Aggiunto un counter in Ajax per tenere il conto dei rapporti convertiti</li>
		<li>Fatto un po' di pulizia nel codice anche se ce nè da fare ancora tanta perchè sono uno zozzone ^^</li>
		</ul></p>
		
		<p>v. 0.9.2 (22/04/07)
		<ul>
		<li>Sistemata la colorazione su sfondi scuri</li>
		<li>Aggiunta una funzione per creare testi sfumati</li>
		<li>Non vengono più usati 2 titoli per mostrare le statistiche avanzate per CR multipli ma tutto viene mostrato in un unico titolo</li>
		</ul></p>
		
		<p>v. 0.9.1 (16/04/07)
		<ul>
		<br>Release contenente alcuni miglioramenti nel rendericng del Combat Report convertito:</li>
		<li>Aggiunte colorazioni del CR dei detriti e delle risorse catturate</li>
		<li>Aggiunte delle statistiche delle reciclate nella parte Statistiche avanzate</li>
		<li>Sistemato un piccolo bug che conteggiava il guadagno reale anche se non c'erano riciclate nel caso di conversione di CR multipli</li>
		</ul></p>
		
		<p>v. 0.9.1 (15/04/07)
		<ul>
		<li>Aggiunta un'opzione che consente di visualizzare solo le statistiche riepilogative se si convertono più CR in una volta sola</li>
		<li>Sistemato un piccolo errore nel conteggio delle riciclate</li>
		</ul></p>
		
		<p>v. 0.9.0 (09/04/07)
		<ul>
		<li>Prima release pubblica comprendente sia un convertitore di Combat report che di Spy report:</li>
		<li>Riconoscimento automatico del tipo di report da convertire</li>
		<li>Scelta tra quattro tipi di palette di colori</li>
		<li>Scelta se utilizzare nomi estesi o abbreviati</li>
		<li>Scelta di conversione HTML o BBCode</li>
		<li>Memorizzazione delle impostazioni</li>
		<li>Possibilità di mostrare solo il primo e l'ultimo round</li>
		<li>Possibilità di utilizzare le statistiche avanzate con conteggio della flotta persa e dei detriti raccolti</li>
		<li>Possibilità di nascondere nome attaccante, nome difensore, coordinate, orario, tecnologie</li>
		<li>Anteprima istantanea del rapporto convertito</li>
		<li>Primitivo controllo su eventuali errori nella lettura dei report (debug-mode)</li>
		</ul></p>
		
		<p>v. 0.5.3 (10/03/07)
		<ul>
		<li>Prima release che conteneva un primitivo convertitore di spy report</li>
		</ul></p>
		</span>
	</div>
<!--Statistiche visite-->

<script language="javascript" src="http://www.madbad.altervista.org/stats/php-stats.js.php"></script>
<noscript><img src="http://www.madbad.altervista.org/stats/php-stats.php" border="0" alt=""></noscript>

</body>
</html>