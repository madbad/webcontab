<?php
include ('./core/config.inc.php');

$uscite = array();
$gestisciRiscontro= function ($obj){
echo 'test';
	$articolo = $obj->cod_articolo->getVal();
	$ddt = $obj->ddt_numero->getVal();
	$data = $obj->ddt_data->getFormatted();
	$colli = $obj->colli->getFormatted(0);
	$plordo = $obj->peso_lordo->getFormatted(2);
	$pnetto = $obj->peso_netto->getFormatted(2);
	
	$index= count($uscite);
	$uscite[$index]['ddt']=$ddt;
	$uscite[$index]['data']=$data;
	$uscite[$index]['colli']=$colli;
	uscite[$index]['pnetto']=$pnetto;
	
	echo "<tr><td>$data</td><td>$ddt</td><td>$colli</td><td>$pnetto</td></tr>";
};



echo '<table>';
echo "<tr><td>data</td><td>ddt</td><td>colli</td><td>pnetto</td></tr>";

$riscontri=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','01/01/2019','17/05/2019'),
		'cod_articolo'=>array('640'),
		'cod_cliente'=>'SEVEN',
	)
);
$riscontri->iterate($gestisciRiscontro);
echo '</table>';






//gentile
$entrateGentile="
01/05/2019 120 574
02/05/2019 90 417
03/05/2019 240 1765
06/05/2019 180 1056
08/05/2019 90 472
09/05/2019 120 584
10/05/2019 120 627
12/05/2019 90 518
13/05/2019 40 190
14/05/2019 40 204
15/05/2019 90 440
16/05/2019 60 302
";
$righe = explode("\n", $entrateGentile);
$entrate= array();
foreach ($righe as $key => $value){
	$temp = explode (' ', $value);
	$index= count($dati);

	$entrate[$index]['data']=$temp[0];
	$entrate[$index]['colli']=$temp[1];
	$entrate[$index]['peso']=$temp[2];
}
print_r($dati);



//lattuga
$entrateLattuga="
01/05/19 30 201
02/05/19 30 146
03/05/19 60 335
07/05/19 60 277
08/05/19 30 152
09/05/19 60 242
10/05/19 60 283
12/05/19 30 158
13/05/19 60 302
14/05/19 20 112
15/05/19 60 300
16/05/19 60 302
";


foreach ($entrate as $key => $entrata){



	while ($inusoUscite > 0 && $riscontriEntrata['colli'] < $entrata['colli']){
	
		$colliMancanti = $entrata['colli'] - $riscontriEntrata['colli'];
		
		if ( $colliMancanti > 0){
			$inusoUscite = array_shift($uscite);
			
			if ($colliMancanti <= $inusouscite){
				$riscontriEntrata['colli'] = $uscite['colli'];
				$riscontriEntrata['peso'] = $uscite['peso'];
				$inusouscite['colli'] -= $uscite['colli'];
				$inusouscite['peso'] -= $uscite['peso'];
				
				
				
			}else if ($colliMancanti > $inusouscite){
			
				$riscontriEntrata['colli'] = $colliMancanti;
				$riscontriEntrata['peso'] $colliMancanti * $uscite['peso'];
				
				$inusouscite['colli'] -= $colliMancanti;
				$inusouscite['peso'] -= $colliMancanti * $uscite['peso'];
			}
		}
		
	}
}

?>