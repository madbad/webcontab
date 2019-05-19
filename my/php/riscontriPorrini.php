<?php
include ('./core/config.inc.php');

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
	
	echo "<tr><td>$data</td><td>$ddt</td><td>$colli</td><td>$pnetto</td></tr>";
};



//echo '<table>';
//echo "<tr><td>data</td><td>ddt</td><td>colli</td><td>pnetto</td></tr>";

$vendite=new MyList(
	array(
		'_type'=>'Riga',
		'ddt_data'=>array('<>','01/01/2019','17/05/2019'),
		'cod_articolo'=>array('640'),
		'cod_cliente'=>'SEVEN',
	)
);
$vendite->iterate($gestisciVendite);
//echo '</table>';

//print_r($vendite);




//gentile
$entrateGentile=" 0 0
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
 0 0";
$righe = explode("\n", $entrateGentile);
$entrate= array();
foreach ($righe as $key => $value){
	$temp = explode (' ', $value);
	$index= count($entrate);

	$entrate[$index]= array();
	$entrate[$index]['data']=$temp[0];
	$entrate[$index]['colli']=$temp[1];
	$entrate[$index]['peso']=$temp[2];
	$entrate[$index]['riscontri']=array();
}
//print_r($entrate);



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

$inusoUscite = 0;
foreach ($entrate as $key => $entrata){
	
	//mi ricordo quanti colli devo usare per i riscontri
	$colliMancanti = $entrata['colli'];
	echo "\n\n\n".$colliMancanti;
	echo "\n".count($uscite);
	
	//finche mi mancano riscontri e ho ancora vendite da utilizzare
	while ($colliMancanti > 0 && (count($uscite) > 0)){
		
		//se mi serve utilizzo tutta la vendita
		if ($colliMancanti >= $uscite[0]['colli']){
			$riscontro = $uscite[0];
			array_shift($uscite);
			
			$colliMancanti -= $uscite[0]['colli'];
			
		//altrimenti (se luscita è maggiore di quello che mi serve) ne uso solo una parte
		}else{
			//mi ricavo il riscontro
			$riscontro= array();
			$riscontro['colli']=$colliMancanti;
			$riscontro['peso']=$colliMancanti*($uscite[0]['peso']);				
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

print_r($entrate);

?>