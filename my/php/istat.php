
<?php
include ('./core/config.inc.php');

$today = date("j/n/Y"); 
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}

$codicimeloni=array();
$codicimeloni[]='=';


$stampaRighe= function ($obj){
$color='';
if ($obj->prezzo->getVal()=='0.001'){
	$color=' style="background-color:red;color:white;" ';
}

	echo '<tr '.$color.'> ';
	
	echo '<td>'.$obj->cod_articolo->getVal().' # '.$obj->ddt_numero->getVal().'</td>';
	echo '<td>'.$obj->ddt_data->getFormatted().'</td>';
//		echo '<td>'.$obj->cod_cliente->getVal().' # '.$obj->cod_cliente->extend()->ragionesociale->getVal().'</td>';
	echo '<td>'.$obj->cod_cliente->getVal().'</td>';
	echo '<td>'.$obj->colli->getFormatted(0).'</td>';
	if($obj->peso_lordo->getVal() != $obj->peso_netto->getVal()){$warningPeso='<span style="color:orange"><b>*****</b></span>';}else{$warningPeso='';};
	echo '<td>'.$obj->peso_lordo->getFormatted(2).'</td>';
	echo '<td>'.$obj->peso_netto->getFormatted(2).$warningPeso.'</td>';

	echo '<td>'.$obj->prezzo->getFormatted(3).'</td>';
	$number = number_format($obj->getPrezzoLordo(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
	echo '<td>'.$number.'</td>';
	$number = number_format($obj->getPrezzoNetto(),3,$separatoreDecimali=',',$separatoreMigliaia='.');
	echo '<td>'.$number.'</td>';
	$number = number_format($obj->peso_netto->getVal()/$obj->colli->getVal(),2,$separatoreDecimali=',',$separatoreMigliaia='.');
	echo '<td>'.$number.'</td>';
	$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
	$number = number_format($impNetto,2,$separatoreDecimali=',',$separatoreMigliaia='.');
	echo '<td>'.$number.'</td>';
	$obj->_totImponibileNetto->setVal($impNetto);
	//echo '<td>'.$obj->imponibile->getVal().'</td>';
	echo '</tr>';
};
$stampaTotali= function ($obj){
	echo '<tr>';
	echo '<td>'.'-'.'</td>';
	echo '<td>'.'-'.'</td>';
	echo '<td>'.'-'.'</td>';
	echo '<td>'.$obj->sum('colli').'</td>';
	echo '<td>'.'-'.'</td>';
	echo '<td>'.$obj->sum('peso_netto').'</td>';
	echo '<td>'.'-'.'</td>';
	echo '<td>'.'-'.'</td>';
	echo '<td>'.'-'.'</td>';
	echo '<td>'.round($obj->sum('peso_netto')/$obj->sum('colli'),2).'</td>';
	echo '<td>'.$obj->sum('_totImponibileNetto').'</td>';
	echo '</tr>';
};

$tabellaH='<table class="spacedTable, borderTable">';
$tabellaH.='<tr><td>Numero</td><td>Data</td><td>Cliente</td><td>Colli</td><td>Peso lordo<td>Peso netto</td><td>Prezzo</td><td>Prezzo L.</td><td>Prezzo N.</td><td>Media peso</td><td>Imponibile Calc.</td></tr>'; //<td>Imponibile Memo.</td>
$tabellaF='</table><br><br>';




$stampaRighe= function ($obj){
	$impNetto=$obj->peso_netto->getVal()*$obj->getPrezzoNetto();
//	$number = number_format($impNetto,2,$separatoreDecimali=',',$separatoreMigliaia='.');
	$obj->_totImponibileNetto->setVal($impNetto);
};










//==============================================================================================================================
echo "<table>";
$dbClienti=getDbClienti();
$mercati[]='=';
foreach ($dbClienti as $cliente){
	if (
			$cliente['__classificazione']=='supermercato' ||
			$cliente['__classificazione']=='mercato'
		){
			$query = "
				\$test=new MyList(
					array(
						'_type'=>'Riga',
						'ddt_data'=>array('<>','12/03/19','12/03/19'),
						'cod_articolo'=>array('=','21','921','821','621','26','926','826','826-','01F','01S','01SE','01','901','801-','701','801','701S','801S','801F-','03S','03SE','03','903','803-','703','803','703S','803S','03F','803F-','731','631','631FLOW','31FLOW','831-','831','31','931','24','900','05PZ12','05PZ15','05PZ8','05G','05P','805','805-','605','05PL','29','929','829','829-','629','VAS29','729','729-','05','905','VAS05','08P','08G','08','908','808','808-','608','608-','08F','08POL','08TRAD','VAS08','705','705-','705--','08B','908B','36','936','836','836-','20','920','820'),
						//'cod_cliente'=>".$strMercati.",
						'cod_cliente'=>'".$cliente['codice']."',
					)
				);
			";
			
			eval ($query);

			$test->iterate($stampaRighe);

			echo "<tr>";
			echo "<td>".$cliente['codice']."</td>";
			echo "<td>".$test->sum('colli')."</td>";
			echo "<td>".$test->sum('peso_netto')."</td>";
			echo "<td>".$test->sum('_totImponibileNetto')."</td>";
			echo "</tr>";
		}
};
echo "</table>";


page_end();

?>
