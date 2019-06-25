<style>
table,tr,td {
border: 1px solid;
margin: 0;
padding: 0;
border-collapse: collapse;
text-align:right;
}
</style>

<?php
include ('./core/config.inc.php');

//data //colli //peso //variazione(+o-)
//gentile

$entrateProdotto=" 34 162
28/04/2019 120 516
29/04/2019 50 215
30/04/2019 100 437
01/05/2019 120 574 -2
02/05/2019 90 417
03/05/2019 240 1333 -2
06/05/2019 180 1056
08/05/2019 90 472 -2
09/05/2019 120 584 -2
10/05/2019 120 627 -2
12/05/2019 90 518 -1
13/05/2019 40 190
14/05/2019 40 204
15/05/2019 90 440
16/05/2019 60 302
17/05/2019 120 434
19/05/2019 150 667
20/05/2019 120 490
22/05/2019 30 123
23/05/2019 30 126
24/05/2019 90 375
26/05/2019 60 225
27/05/2019 10 47
28/05/2019 60 285
29/05/2019 60 264
30/05/2019 50 208
31/05/2019 85 436
02/06/2019 30 132
03/06/2019 40 191
04/06/2019 80 396
05/06/2019 90 434
06/06/2019 90 387 -2
07/06/2019 100 475
09/06/2019 100 487
11/06/2019 90 390
12/06/2019 100 519
 0 0";
 $articolo = '640';
 
/*
//lattuga
$entrateProdotto=" 5 18
28/04/2019 60 215
29/04/2019 10 36
30/04/2019 20 75 -1
01/05/2019 30 201 
02/05/2019 30 146 -1
03/05/2019 60 335 
07/05/2019 60 277 -1
08/05/2019 30 152
09/05/2019 60 242
10/05/2019 60 283
12/05/2019 30 158
13/05/2019 60 302
14/05/2019 20 112 -1
15/05/2019 60 300 -1
16/05/2019 60 302 
17/05/2019 30 124
19/05/2019 30 115
22/05/2019 60 198 -35
23/05/2019 30 134 
24/05/2019 30 130 
26/05/2019 40 185 
27/05/2019 50 227
30/05/2019 10 42
31/05/2019 35 157
02/06/2019 30 159
03/06/2019 20 109
04/06/2019 40 190
05/06/2019 30 146
06/06/2019 30 136
07/06/2019 20 86
09/06/2019 20 78
11/06/2019 30 126
12/06/2019 20 87 -1
 0 0";
$articolo = '639';
*/



$uscite = array();
$gestisciVendite= function ($obj){
	global $uscite;
//echo 'test';
	$articolo = $obj->cod_articolo->getVal();
	$ddt = $obj->ddt_numero->getVal();
	$data = $obj->ddt_data->getFormatted();
	$colli = $obj->colli->getVal(0);
	$plordo = $obj->peso_lordo->getVal();
	$pnetto = $obj->peso_netto->getVal();
	
	$index= count($uscite);
	$uscite[$index]['ddt']=$ddt;
	$uscite[$index]['data']=$data;
	$uscite[$index]['colli']=$colli;
	$uscite[$index]['peso']=$pnetto;
	
//	echo "<tr><td>$data</td><td>$ddt</td><td>$colli</td><td>$pnetto</td></tr>";
};



//echo '<table>';
//echo "<tr><td>data</td><td>ddt</td><td>colli</td><td>pnetto</td></tr>";

$vendite=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','28/04/2019','13/06/2019'),
		'cod_articolo'=>array($articolo),
		'cod_cliente'=>'SEVEN',
	)
);
$vendite->iterate($gestisciVendite);
//echo '</table>';

//print_r($vendite);






$righe = explode("\n", $entrateProdotto);
$entrate= array();
foreach ($righe as $key => $value){
	$temp = explode (' ', $value);
	$index= count($entrate);

	$entrate[$index]= array();
	$entrate[$index]['data']=$temp[0];
	$entrate[$index]['colli']=$temp[1];
	$entrate[$index]['peso']=$temp[2];
	$entrate[$index]['variazioniColli']=$temp[3];
	$entrate[$index]['riscontri']=array();
}
//print_r($entrate);


$inusoUscite = 0;
foreach ($entrate as $key => $entrata){
	
	//mi ricordo quanti colli devo usare per i riscontri
	$colliMancanti = $entrata['colli']+$entrata['variazioniColli'];
	//echo "\n\n\n".$colliMancanti;
	//echo "\n".count($uscite);
	
	//finche mi mancano riscontri e ho ancora vendite da utilizzare
	while ($colliMancanti > 0 && (count($uscite) > 0)){
		
		//se la data che sto usando delle uscite è anteriore alla mia entrata merce non la considero e passoa quella dopo
		if(myStrToTime($uscite[0]['data']) < myStrToTime($entrata['data']) ){
			echo "\nHo scartato una vendita";
			array_shift($uscite);
			continue;
		}
		
		//se mi serve utilizzo tutta la vendita
		if ($colliMancanti >= $uscite[0]['colli']){
			$riscontro = $uscite[0];
			
			$colliMancanti -= $uscite[0]['colli'];

			array_shift($uscite);
			
		//altrimenti (se luscita è maggiore di quello che mi serve) ne uso solo una parte
		}else{
			//mi ricavo il riscontro
			$riscontro= array();
			$riscontro['colli']=$colliMancanti;
			$riscontro['peso']=$colliMancanti*($uscite[0]['peso']/$uscite[0]['colli']);
			$riscontro['ddt']=$uscite[0]['ddt'];
			$riscontro['data']=$uscite[0]['data'];
			
			//scalo la parte di riscontro che ho usato dalle vendite
			$uscite[0]['colli'] -= $riscontro['colli'];
			$uscite[0]['peso'] -=$riscontro['peso'];

			//mi ricordo che ho finito con queste entrate
			$colliMancanti = 0;
		}
		
		//associo il riscontro all'entrata
		array_push($entrate[$key]['riscontri'], $riscontro);
		
	}
}

//print_r($entrate);


function myStrToTime($date){
	//$date = '25/05/2010';
	$date = str_replace('/', '-', $date);
	return date('Y-m-d', strtotime($date));
}
?>


<table>
<?php
foreach ($entrate as $key => $entrata){
?>
	<tr>
		<td><?php echo $entrata['data']; ?></td>
		<td><?php echo $entrata['colli']; ?></td>
		<td><?php echo round($entrata['peso']); ?></td>
		<td>
			<table width="100%">
			<?php
			$totaleColliRiscontrato = 0;
			$totalePesoRiscontrato = 0;

			foreach ($entrata['riscontri'] as $subkey => $riscontro){
				$prossimaEntrata = $entrate[($key+1)];
				
				//print_r($prossimaEntrata);
				//echo $prossimaEntrata['ddt'];
				
				$totaleColliRiscontrato += $riscontro['colli'];
				$totalePesoRiscontrato += $riscontro['peso'];
			?>
				
					<tr>
						<td style="width:5em"><?php echo $riscontro['data']; ?></td>
						<td style="width:3em"><?php echo $riscontro['ddt']; ?></td>
						<td style="width:2em"><?php echo round($riscontro['colli']); ?></td>
						<td style="width:3em"><?php echo round($riscontro['peso']); ?></td>
							<?php
								if ($prossimaEntrata['riscontri'][0]['ddt'] != $riscontro['ddt']){
									
									
									$rimanenza = ($entrata['colli'] - $totaleColliRiscontrato);
									
									//se l'arrivo successivo ha una data antecedente alla mia ultima uscita allora ne tengo conto nelle rimanenze.
									if(myStrToTime($prossimaEntrata['data']) < myStrToTime($riscontro['data'])){
										$rimanenza +=$prossimaEntrata['colli'];
										$rimanenza = $rimanenza.' (*)'; 
									}
									
									echo '<td style="width:5.5em">Rimanenza: </td>';
									echo '<td style="width:3em">';
									echo $rimanenza;
									echo '</td>';
								}else{
									echo '<td></td><td></td>';
								}
								
							?>

						
						
					</tr>
			<?php
			}
			?>
				<tr>
					<td style="width:5em"><?php echo round($totalePesoRiscontrato/$entrata['colli'],1); ?></td>
					<td style="width:3em">-</td>
					<td style="width:2em; font-weight:bold"><?php echo $totaleColliRiscontrato; ?></td>
					<td style="width:3em;font-weight:bold"><?php echo round($totalePesoRiscontrato); ?></td>
					<td style="width:5em;font-weight:bold"><?php echo round($entrata['peso']-$totalePesoRiscontrato); ?></td>
					<td style="width:3em;font-weight:bold"><?php echo round(($entrata['peso']-$totalePesoRiscontrato)/$entrata['colli'],1); ?></td>
					
					
				</tr>
			
			</table>
		</td>
	</tr>
<?php
}
?>
</table>