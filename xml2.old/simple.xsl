<?xml version="1.0" encoding="ISO-8859-1"?>
<!-- Edited by XMLSpy® -->
<html xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
  <head>
  		<title><xsl:value-of select="attrezzo/nome"/></title>
 	   <style type="text/css">
		body, table,td,div,p {
				font-family:"Verdana";
 	       }
 	       .titolo {
 	          font-size:1.5em;
 	          width:100%;
 	          background-color:#a1a1a1;
 	          border:0.05em black solid;
 	          -moz-border-radius:7px;
 	          text-align:center;
			  margin-bottom:5px;
 	       }
 	       .tabellaDescr{
 	        	 font-size:0.5em;
 	          width:100%;
 	          background-color:#a1a1a1;
 	          border:0.05em black solid;
 	          -moz-border-radius:7px;
 	          padding:0.5em; 
 
 	       }
 	       .tabellaDescr td{
				border-bottom:1px dashed black;
				padding:0.5em;
 	       }
 	       p{
 	        	 font-size:1em;	
 	        	 font-weight:bold;			 	       
 	       }
 	       .interventi{
				width:100%; 	       
 	       }
 	       table{
  	        	 font-size:0.5em;	       
 	       }
 	       td{
				vertical-align:top; 	       
 	       }
 	       .tabellaTitolo{
		   			margin:0px;
			padding:0px;
 	       }
 	       .tabellaTitolo td{
		   			margin:0px;

 				border-left:1.5px solid black;
 				border-top:1.5px solid black;
 				border-bottom:1.5px solid black;	       
 	       }
			.tabellaRevisione{
			margin:0px;

 				font-size:1em;
 				border:0px;
 				width:100%;

				font-weight:bold;
			}
			.tabellaRevisione td{
			margin:0px;
				padding-top:0.5em;
				padding-bottom:0.5em;
				
 				border:0px;
 				border-bottom:1.5px solid #000000;
 				border-right:1.5px solid #000000;
			}
			.divInterventi{
				margin-left:15px;
				margin-bottom:15px;
				float:left;
				width:44%;
			 	border:0.05em black solid;
				-moz-border-radius:7px;
				padding:0.4em;
			}			

 	    </style>
  </head>
  <body>
<table style="width:100%" class="tabellaTitolo" cellspacing="0">
	<tr>
		<td style="width:15%"><img src="./logo.gif" style="width:100%"></img></td>
		<td style="text-align:center;"><b style="font-size:2.5em;">REGISTRO DI MANUTENZIONE <br/>DELLE ATTREZZATURE</b></td>
		<td style="width:20%">
			<table class="tabellaRevisione" cellspacing="0">
				<tr>
					<td>REVISIONE</td>
					<td></td>
				</tr>			
				<tr>
					<td>DEL</td>
					<td></td>
				</tr>			
				<tr>
					<td style="border-bottom:0px;">MODELLO</td>
					<td style="border-bottom:0px;text-align:right;"><i>MANATT</i></td>
				</tr>			
			</table>
		</td>
	</tr>
</table>  
  

<xsl:for-each select="attrezzo">
    <p>Dati Identificativi</p>
    <p class="titolo"><xsl:value-of select="nome"/></p>
    <table class="tabellaDescr">
		<tr>
			<td><b>Marca:</b></td>
			<td><xsl:value-of select="marca"/></td>
			<td><b>Modello:</b></td>
			<td><xsl:value-of select="modello"/></td>
		</tr>    
		<tr>
			<td><b>Data fabbricazione:</b></td>
			<td><xsl:value-of select="anno_fabbricazione"/></td>
			<td><b>Data acquisto:</b></td>
			<td><xsl:value-of select="anno_acquisto"/></td>
		</tr>    
		<tr>
			<td><b>Matricola:</b></td>
			<td><xsl:value-of select="matricola"/></td>
			<td><b>Numero assegnato:</b></td>
			<td><xsl:value-of select="id"/></td>
		</tr>    
		<tr>
			<td><b>Ditta Fabbricante:</b></td>
			<td>   
			    <xsl:for-each select="ditta_fabbricatrice">
   		    	<xsl:value-of select="ragionesociale" /><br />
      		 	<xsl:value-of select="via"/><br />
             	<xsl:value-of select="citta"/>
             	(<xsl:value-of select="provincia"/>)<br />
             	Tel:<xsl:value-of select="Telefono"/> -
             	Fax:<xsl:value-of select="fax"/> -
             	Mail:<xsl:value-of select="mail"/>
             </xsl:for-each>
			</td>
			<td><b>Ditta Manutentrice:</b></td>
			<td>   
			    <xsl:for-each select="ditta_manutentrice">
   		    	<xsl:value-of select="ragionesociale" /><br />
      		 	<xsl:value-of select="via"/><br />
             	<xsl:value-of select="citta"/>
             	(<xsl:value-of select="provincia"/>)<br />
             	Tel:<xsl:value-of select="Telefono"/> -
             	Fax:<xsl:value-of select="fax"/> -
             	Mail:<xsl:value-of select="mail"/>
             </xsl:for-each>
			</td>
		</tr> 
    </table>



    <p>Elenco interventi:</p>
    <xsl:for-each select="interventi/intervento">
		<div class="divInterventi">
		<table class="interventi">
			<tr>
				<td width="120px">
					<b>Data Intervento: </b> 
					<br/><xsl:value-of select="data"/>

					<br/><b>Manutentore: </b> 
					<br/><xsl:value-of select="manutentore/ragionesociale" />

					<br/><b>Riferimento Ft.</b> 
					<br/>N. <xsl:value-of select="fattura/numero"/> del <xsl:value-of select="fattura/data"/><br />
				
				</td>			
				<td style="border-left:1px dashed #666666;">
					<b>Descrizione intervento:</b><br/> <xsl:value-of select="descrizione"/>
				</td>
			</tr>
		</table>
		</div>

    </xsl:for-each>
</xsl:for-each>
  </body>
</html>