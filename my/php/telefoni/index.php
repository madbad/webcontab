<html><head>
<meta http-equiv="content-type" content="text/html; charset=windows-1252">
<link rel="stylesheet" type="text/css" href="./style.css" />


</head>
<body>

<?php

$handle = fopen('./RUBRICATELEFONICA.csv','r');
while ( ($data = fgetcsv($handle) ) !== FALSE ) {
	//process
	$riga = explode("\t",$data[0]);
	$nominativo = $riga[0];
@	$note = $riga[1];
@	$tipo = $riga[2];
@	$telefono = $riga[3];
@	$noteextra = $riga[4];

//@	$righeRubrica [$nominativo][] = $riga;
	$righe[] = $riga;
}

$nome = array();
foreach ($righe as $key => $row)
{
    $nome[$key] = $row[0];
}
array_multisort($nome, SORT_ASC, $righe);


//ordino l'array alfabeticamente
//$txt = natksort($txt);
echo "\n".'<div><table>';
foreach ($righe as $riga){

		//controllo se cambio nome
		if ($riga[0] == $prevName){
			//$riga[0] = '';
			$class = 'class="riga"';
		}else{
			$prevName = $riga[0];
			$class = ' class="nuovoSoggetto" ';
			
			//controllo se cambio lettera
			if ($riga[0][0] != $prevLetter){
				$prevLetter = $riga[0][0];
				echo '</table></div>'."\n";
				echo '<div class="nuovaPagina"><table>';
				echo '<tr class="lettera">';
				echo '<td colspan="4">'.$riga[0][0].'</td>';
				echo '</tr>';
			}
		}


if ($riga[2]==''){$riga[2]='vuota';}
		
		echo "<tr $class >";
		echo '<td>'.$riga[0].'</td>';
		echo '<td width="30" class="descrizione">'.substr($riga[1],0,50).'</td>';
		echo '<td width="2">'.'<img src="./img/'.$riga[2].'.svg" height="15">'.'</td>';
		echo '<td class="numero" width="10">'.substr($riga[3],0,18).'</td>';
		echo '</tr>';
}
echo '</table></div>';
echo '</body></html>';
?>