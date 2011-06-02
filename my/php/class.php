<?php
class script
{
	var $startTime;
	var $endTime;
	var $duration;
   function script()
   {
      $this->queryCount=0;
   }

   function start()
   {
   	$this->startTime=microtime();
   }
   function stop()
   {
   	$this->endTime=microtime();
   }
   function print_stats()
   {
   	echo '<br>'.$this->endTime-$this->startTime;
   	echo ' ** Numero di query:'.$this->queryCount;
   }
   function queryLogger()
   {
   	$this->queryCount++;
   }
}

class database
{
   function database($test)
   {
      $this->host='';      $this->user='';      $this->password='';      $this->name='';
   }
   function connect($query)
   {
      $connection = mysql_connect($this->host, $this->user, $this->password)
      or die('Connessione al database non riuscita: ' . mysql_error());

      mysql_select_db($this->name, $connection)
      or die('Selezione del database non riuscita.');

      $result=mysql_query($query)
      or die("Query non valida: " . mysql_error(). "<br><br>LA QUERY DA ESEGUIRE ERA: <br>$query" );

      mysql_close($connection);
      return $result;
   }
}

class oFattura 
{
	var $id;
	var $numero;
	var $data;
	var $tipo;
	var $cod_destinatario;
	var $cod_destinazione;
	var $ddt;
	var $tot_fattura;
	var $banca;
	var $condizioni_pagamento;
	function getFromDb($num, $data){
	
	}	
}
class oDDT
{
	var $id;
	var $numero;
	var $data;
	var $cod_destinatario;
	var $cod_destinazione;
	var $causale;
	var $mezzo;
	var $cod_vettore;
	var $ora_inizio_trasporto;
	var $aspetto_beni;
	var $annotazioni;
	
	var $tot_colli;
	var $tot_peso;
		
}
class oRiga
{
	var $id;
	var $numero;
	var $cod_articolo;
	var $unità_misura;
	var $colli;
	var $cod_imballo;
	var $peso_lordo;
	var $peso_netto;
	var $tara;
	var $origine;
	var $categoria;
	var $lotto;
	var $cod_iva;
	
}
class oArticolo
{
	var $codice;
	var $descrizione;
}
class oImballaggio
{
	var $codice;
	var $descrizione;
	var $tara_acquisto;
	var $tara_vendita;
}
class oClienteFornitore
{
	var $codice;
	var $rag_sociale;
	var $via;
	var $paese;
	var $citta;
	var $cod_pagamento;
	var $banca;
	var $trasporto;
	var $vettore;
	var $p_iva;
	var $cod_fiscale;
	var $cod_iva;
	var $lettera_intento;
	var $provvigione;
}
?>