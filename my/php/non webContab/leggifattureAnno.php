<?php
/* -------------------------------------------------------------------------------------------------------
Questo script legge la stampa delle fatture di CONTAB e raccoglie tutti i relativi dati in array/oggetti

----------------------------------------------------------------------------------------------------------
*/

$fileUrl='./2009.ft.txt';

$file = fopen ("$fileUrl", "r");

$text;
  if (!$file)
  {
    echo "<p>Impossibile aprire il file remoto.\n";
    exit;
  }
  while (!feof ($file))
  {
    $text.= fread ($file, filesize($fileUrl));

	}
	
	
$righe=explode("\n",$text);

for ($i = 0; $i <= count($righe); $i++) {

	//se è una riga di una bolla di vendita
	$bv=substr( $righe[$i], 9,2);
	if($bv=='BV'){

	}
	//se è una riga dove mi danno la data della bolla di vendita
	$databv=substr( $righe[$i], 14,10);
	if(isDate($databv)){
		$data=$databv;
	}

//echo $righe[1];

function isDate($string){
	//controlla se la stringa è una data del formato "01.01.2001"
	return preg_match("([0-9]{2}.[0-9]{2}.[0-9]{4})",$string);	

}

?>
