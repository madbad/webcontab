<?php
/* -------------------------------------------------------------------------------------------------------
Questo script legge la stampa delle fatture di CONTAB e raccoglie tutti i relativi dati in array/oggetti

----------------------------------------------------------------------------------------------------------
*/


//aumento la ram a disposizione di php sennò lo script non gira
ini_set("memory_limit","50M");
/*
TO DO
[done] le note di accredito vengono considerate come fatture e quindi inserite nella fattura con stesso numero della nota di accredito
[done] nelle fatture multipagina - sulla pagina n°2 o successive non riesco a identificare a quale ddt appartengono le prime righe (il ddt è dichiarato nella pagina precedente ma non su quella che sto elaborando attualmente)
- arotondare riga per riga
- sistemaRE IL RICONOSCIMENTO DELLE fatture con lettera di intento
- fixare il riconoscimento del numero di alcune fatture decommenta riga 91 per rivelare il bug
- fixare articolo E26
- fixare articolo E15

*/
function parseInt($string) {
// return intval($string);
if(preg_match('/(\d+)/', $string, $array)) {
return $array[1];
} else {
return 0;
}
}
function toNumber($string){
	$string=str_replace(".","",$string); 
	$string=str_replace(",",".",$string);
	return $string;
}
function sommaValorePerArticolo($dbFt){
	$out='';
	foreach ($dbFt as $key=> $value){

		$ft=$value;
		$modifier=1;
		if($ft['isNotaCredito']){$modifier=-1;}else{$modifier=1;}
		$valoreFt=0;
		foreach ($ft['ddt'] as $key=> $value){
			$ddt=$value;
			$valoreDdt=0;
			foreach ($ddt['righe'] as $key=> $value){
				$riga=$value;
				$articolo=$riga['descrArticolo'];
				$valoreRiga=$riga['totRiga'];
				if( parseInt(toNumber($valoreRiga))>0 ){
					//$valore=Math.round(parseFloat(this)*100)/100

					$out[$articolo]= $out[$articolo]+(toNumber($valoreRiga)*$modifier);
					$valoreDdt=$valoreDdt+toNumber($valoreRiga)*1;
					$somma=$somma+(toNumber($valoreRiga)*$modifier);		
				}

			}
			$valoreFt=$valoreFt+$valoreDdt;
		}
		echo "\n<br>Valore della fattura: ".$ft['numero'].' *** ';
		$a=(int)toNumber($ft['imponibile']);
		$b=(int)$valoreFt*1;
		echo ($a."==".$b);
		$test=$a-$b;
		//echo '**'.$test.'**';
		if($test==0){
			echo '<b> OK</b>';
		}else{
			echo '<b style="color:red"> BAD</b>';			
		}
	}
	echo '<br><br><b>Totale fatturato: </b>'.$somma.'<br>';
	return $out;
}


function leggiFattura($pagina){
	$ft['isNotaCredito']=preg_match('/ NOTA DI ACCREDITO /',$pagina);
	$ft['isLetteraIntento']=preg_match('/VS. Dichiarazione di intento N/',$pagina);
	

	
	//$ft;
	$righe=explode("\r",$pagina);
	$ft['ragSoc']=trim($righe[7]);
	$ft['indirizzo']=trim($righe[9]);
	$ft['capPaeseProvincia']=trim($righe[11]);
	$ft['data']=trim(substr($righe[15],4,10));
	$ft['numero']=trim(substr($righe[15],16,22));
	if($ft['isNotaCredito']){$ft['numero']='NC. '.$ft['numero'];}
	$ft['codiceCliente']=substr($righe[15],43,5);
	$ft['partitaIvaCliente']=substr($righe[17],90,15);
	
	
	
	if($ft['numero']==''){//sembra che fatture troppo lunche spostino l'impaginazione di 1 riga
									//quindi proviamo a cercare anche una riga più sotto se non abbiamo trovato niente prima
		$ft['ragSoc']=trim($righe[8]);
		$ft['indirizzo']=trim($righe[10]);
		$ft['capPaeseProvincia']=trim($righe[12]);
		$ft['data']=trim(substr($righe[16],4,10));
		$ft['numero']=trim(substr($righe[16],16,22));
		if($ft['isNotaCredito']){$ft['numero']='NC. '.$ft['numero'];}
		$ft['codiceCliente']=substr($righe[16],43,5);
		$ft['partitaIvaCliente']=substr($righe[18],90,15);		
	}

/*
	$ft['imponibile']=0;
	$ft['iva']=0;
	$ft['totaleft']=0;
	*/	
	//controllo se esiste già una fattura con questo numero
	if($GLOBALS['fatture'][$ft['numero']]){
			$ftAlreadyExist=true;
		if($GLOBALS['fatture'][$ft['numero']]['codiceCliente']!=$ft['codiceCliente']){
			$ft['numero']=$ft['numero'].' BIS';
			$ftAlreadyExist=false;
		}

			//echo 'found another ft like this:'.$ft['numero']."\n<br>";
	}

//se è una lettera di intento rilevo 2 righe in meno
if($ft['isLetteraIntento']){ $intentmodifier=2;}else{$intentmodifier=0;}

	foreach ($righe as $key=> $value){
		if($key>18+$intentmodifier && $key<53){//corpo della fattura
			$isDdt=substr($value,19,6);
			if ($isDdt=='D.d.T.'){
				//e una riga che indica il numero del ddt quindi me lo ricavo
				$numeroDDT=substr($value,28,4);
				$ft['ddt']["$numeroDDT"]['numero']=$numeroDDT;

				$modificatore=strlen(parseInt($numeroDDT));
				$ft['ddt']["$numeroDDT"]['data']=trim(substr($value,31+$modificatore,12));
				if($ftAlreadyExist){	
					$GLOBALS['fatture'][$ft['numero']]['lastddt']=$numeroDDT;	
				}	
				$ft['lastddt']=$numeroDDT;
			}else {
				if($ftAlreadyExist && $numeroDDT==''){
					$numeroDDT=$GLOBALS['fatture'][$ft['numero']]['lastddt'];							
				}
				$test=trim(substr($value,19,32));
				//se ha una descrizione valida vuol dire che e effettivamente una riga della fattura
				if($test!=''){
					$i++;
					$ddt[$numeroDDT][$i];
					//e una riga del corpo della fattura quindi mi ricavo articolo pesi colli etc
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['codArticolo']=substr($value,3,16);
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['descrArticolo']=substr($value,19,32);
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['um']=substr($value,51,2);
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['peso']=substr($value,53,20);
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['colli']=substr($value,73,6);
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['prezzo']=substr($value,101,12);
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['totRiga']=substr($value,113,12);
					$ft['ddt']["$numeroDDT"]['righe']["$i"]['ivaRiga']=substr($value,125,7);
					
					$ivaRiga=$ft['ddt']["$numeroDDT"]['righe']["$i"]['ivaRiga'];
					if(parseInt($ivaRiga)==4 || parseInt($ivaRiga)==10 || parseInt($ivaRiga)==20){
						$ft['hasIva']=true;
					}
					//ci sarebbe altro ma mi fermo qua	
				}	
			}
		}else{
				$rigaTotali=preg_match('/[0-9]           EUR/',$value);
				if(!$rigaTotali){
					//se non ho azzeccato al primo tentativo provo con questo altro metodo di riconoscimento che mi serve per fatture senza iva o con codici iva strani E15 E26 etc
					$rigaTotali=preg_match('/                                                                                                           EUR/',$value);					
				}
				//$rigaTotali=preg_match('/EUR/',$value);

				//potrebbe anche essere una riga totali di una fattura senza iva, in tal caso
				if(!$rigaTotali && $ft['isLetteraIntento']){$rigaTotali=preg_match('/                                                                                                           EUR/',$value);}
				if($rigaTotali){
					//echo "\n<br>Trovato riga dei totali ft: ".$ft['numero']." a riga: $key";
					$ft['imponibile']=substr($value,59,12);
					$ft['iva']=substr($value,84,12);
					$ft['totaleft']=substr($value,113,12);
					
					//se le pagine precedenti hanno iva allora ce l'ha anche questa pagina della fattura
					if($ft['hasIva'] || $GLOBALS['fatture'][$ft['numero']]['hasIva']){
						$ft['hasIva']=true;
					}
					
					if(!$ft['hasIva']){$ft['imponibile']=$ft['totaleft'];}							
					
					if($ftAlreadyExist){
						$GLOBALS['fatture'][$ft['numero']]['imponibile']=$ft['imponibile'];							
						$GLOBALS['fatture'][$ft['numero']]['iva']=$ft['iva'];
						$GLOBALS['fatture'][$ft['numero']]['totaleft']=$ft['totaleft'];
						if(!$ft['hasIva']){$GLOBALS['fatture'][$ft['numero']]['imponibile']=$ft['totaleft'];}	
					}
				}
		}
	}
	//if(!$ft['hasIva']){echo '<br>ft senza iva: '.$ft['numero'];}
	//if($ft['isLetteraIntento']){echo "".$ft['numero'].' = '.$ft['imponibile'];}
	return $ft;
}
function stampaFattura($ft){
	if($ft['isNotaCredito']){
		$out.="NOTA CREDITO";	
	}else{
		$out.="FATTURA";		
	}

	$out.="\nNumero: ".$ft['numero'];
	$out.="\nData:".$ft['data'];
	$out.="\nCliente:".$ft['ragSoc'];
	$out.="\nTot. Imponibile:".$ft['imponibile'];
	$out.="\nTot. Iva:".$ft['iva'];
	$out.="\nTot. Ft.:".$ft['totaleft'];	
	$out.="\n************************************";
		foreach ($ft['ddt'] as $key=> $value){
		$out.="\n    ddt: ".$value['numero'];
		$out.="\n    data: ".$value['data'];
			foreach ($value['righe'] as $key2=> $value2){
				$out.="\n        art:".$value2['descrArticolo'].'|colli:'.$value2['colli'].'|peso:'.$value2['peso'];
				//converto la stringa in un numero
				//$value2['totRiga']=str_replace(".","",$value2['totRiga']); 
				//$value2['totRiga']=str_replace(",",".",$value2['totRiga']);
				$out.='|prezzo:'.$value2['prezzo'];
				$out.='|totRiga:'.toNumber($value2['totRiga'])*1;
				
		}
	}
	return $out;
}

function order_array_num ($array, $key, $order = "ASC")
    {
        $tmp = array();
        foreach($array as $akey => $array2)
        {
            $tmp[$akey] = $array2[$key];
        }
       
        if($order == "DESC")
        {arsort($tmp , SORT_NUMERIC );}
        else
        {asort($tmp , SORT_NUMERIC );}

        $tmp2 = array();       
        foreach($tmp as $key => $value)
        {
            $tmp2[$key] = $array[$key];
        }       
       
        return $tmp2;
    } 
/*------------------------------------------------------------------------------------
  ------------------------------------------------------------------------------------
  ------------------------------------------------------------------------------------
*/

$fileUrl='./2009.ft.txt';
$file = fopen ("$fileUrl", "r");
$text;
//apro il file
if (!$file){
   echo "<p>Impossibile aprire il file remoto.\n";
   exit;
}
//inserisco il contenuto del file in una variabile
while (!feof ($file)){
   $text.= fread ($file, filesize($fileUrl));
}
//suddivido in pagine
$pagine=explode("CB",$text);

//leggo tutte le fatture dal file
foreach ($pagine as $key=> $value){
	$fattura=leggiFattura($value);



	if($fatture[$fattura['numero']]!=''){
		//echo "\n<br>ft già presente ".$fattura['numero'];
		$fatture[$fattura['numero']]['ddt']=array_merge_recursive($fatture[$fattura['numero']]['ddt'], $fattura['ddt']);
	}else{
		$fatture[$fattura['numero']]=$fattura;	
	}



}
//stampo una delle fatture
//echo "<pre>".stampaFattura($fatture['NC. 7'])."</pre>";
echo "<pre>".stampaFattura($fatture[''])."</pre>";


//unisco i ddt di tutte le fatture
$listaDdt=array();
foreach ($fatture as $key=> $value){
	//echo "\n<br>Ft.: ".$value['numero'].' ddt: '.count($value['ddt']);
	$ft=$value;
	$listaDdt=array_merge_recursive($ft['ddt'],$listaDdt);
}

echo "\n<br>Fatture ".count($fatture);
echo "\n<br>DDT ".count($listaDdt);


$listaDdt=order_array_num($listaDdt, 'numero');
/*
foreach ($listaDdt as $key=> $value){
	$diff=$value['numero']-$old-1;
	$missing=$missing+$diff;
	if($diff>0){
		for ($i = $diff; $i > 0; $i--) {
			echo "\n".'<br><b style="color:red">'.($value['numero']-$i).'</b>';
		}
	}
	echo "<br>".$value['data']."-<b style='color:green'>".$value['numero'].'</b>';
	$old=$value['numero'];
}
*/
/*
echo "<br>".count($listaDdt)."ddt trovati";
echo "<br>".$missing."ddt mancanti";
*/


/*
echo '<table>';
$somma=sommaValorePerArticolo($fatture);
	foreach ($somma as $key=> $value){
		$somma[$key]=str_replace(".",",",$value);
		echo "<tr><td>$key</td><td>$somma[$key]</td></tr>";
		}
echo '</table>';
*/

	foreach ($fatture as $key=> $value){
		$ft=$fatture[$key];
		$out[$ft['ragSoc']]=$out[$ft['ragSoc']]['imponibile']+toNumber($ft['imponibile']);
		$totale=$totale+1;//toNumber($ft['imponibile']);
	}
	echo '<pre>';
print_r($out);
	echo '</pre>';
echo 'Totale fatturato: '.$totale;
?>