<?php
include ('./core/config.inc.php');
//require_once('./classes.php');
//page_start();

function formatData($data){
	$newData=explode("-", $data);
	return $newData[2].'/'.$newData[1];
}

//mi memorizzo il database clienti	(dove ho salvato se sono mercati supermercati o altro)	
$dbClienti=getDbClienti();

	function getArticleTable($params){
		global $dbClienti;
		/*
		$params = array("articles" => "31",
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 33,
						"colliPedana" => 104,
						"costoCassa" => 0.43);
		*/

		$out=null;
		//preparo la stringa di query con le condizioni di query per i prodotti
		$condizioniProdotti='';
		foreach ($params['articles'] as $value){
			$condizioniProdotti.="OR F_CODPRO='$value'";
		}
		//rimuovo il primo 'OR ' nella parte iniziale della stringa
		$condizioniProdotti=substr($condizioniProdotti, 3);
		$condizioniProdotti="($condizioniProdotti) AND";
		//decommentare la linea seguente per usare il vecchio metodo che selezionava tutti i prodotti (anche quelli che non ci interesavano)
		//$condizioniProdotti='';
		if ($params["cliente"]){
			$condizioniCliente=" AND F_CODCLI='".$params["cliente"]."' ";
		}else{
			$condizioniCliente='';
		}
		
		$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE $condizioniProdotti F_DATBOL >= #".$params['startDate']."# AND F_DATBOL <= #".$params['endDate']."# $condizioniCliente ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE");
		
		//$out.="<table class=\"righe smallFontTable\"><tr><th colspan='6'>cod:".join(",", $params['articles'])." <br>( ".$params['startDate']." > ".$params['endDate']." )</th></tr>";
		$out.="<table class=\"righe smallFontTable\"><tr><th colspan='6'> <br>( ".$params['startDate']." > ".$params['endDate']." )</th></tr>";
		$out.='<tr><th>Data</th><th>Cliente</th><th>Colli</th><th>p.Net</th><th>md</th><th>tara</th></tr>';
		//this will containt table totals
		$sum=array('NETTO'=>0,'F_NUMCOL'=>0);

		foreach ($result as $id => $row){
		//while($row = odbc_fetch_array($result)){
			$codCliente=$row['F_CODCLI'];
			$tipoCliente=$dbClienti["$codCliente"]['__classificazione'];
			
			
			//può essere che un nuovo cliente non sia ancora stato classificato
			//stampo un allert così se necessario vado ad inserirlo
			if(!array_key_exists($codCliente,$dbClienti)){
				echo '<br>!!! Cliente non presente nel nostro database !!! =>'.$codCliente;
				//echo array_keys($dbClienti);
			}

		
			if (in_array($row['F_CODPRO'],$params['articles']) && ($tipoCliente=='mercato' || $tipoCliente=='supermercato') && ($codCliente!="MARTI") /*| $codCliente=='BRUNF'*//*ABILITA UNO SPECIFICO CODICE CLIENTE ANCHE SE NON RIENTRA TRA IL LAVORTO*/){

				$calopeso=round(round($row['F_NUMCOL'])*$params['abbuonoPerCollo']);
				
				//se ho gia ricevuto il riscontro peso non tolgo il calo peso... il peso è già corretto!
				if($row['F_PESNET'] == $row['F_QTA']){
					$netto=$row['F_PESNET'];
				}else{
					$netto=$row['F_PESNET']-$calopeso;
				}
				
				$media=round($netto/$row['F_NUMCOL'],1);
				$tara=round(($row['F_QTA']-$row['F_PESNET'])/$row['F_NUMCOL'],3);
				//$tara=$row['F_PESNET'].'::'.$row['F_QTA'];
				$out.="\n<tr><td>".formatData($row['F_DATBOL'])."</td><td>$row[F_CODCLI]</td><td>".round($row['F_NUMCOL'])."</td><td>$netto</td><td>$media</td><td>$tara</td></tr>";
				$sum['NETTO']+=$netto;
				$sum['F_NUMCOL']+=$row['F_NUMCOL'];
			}
			IF(($row['F_CODCLI']=="MARTI") && ($params['cliente']=="MARTI")){
				//echo $row['F_CODCLI']
				$calopeso=round(round($row['F_NUMCOL'])*$params['abbuonoPerCollo']);
				
				//se ho gia ricevuto il riscontro peso non tolgo il calo peso... il peso è già corretto!
				if($row['F_PESNET'] == $row['F_QTA']){
					$netto=$row['F_PESNET'];
				}else{
					$netto=$row['F_PESNET']-$calopeso;
				}
				
				$media=round($netto/$row['F_NUMCOL'],1);
				$tara=round(($row['F_QTA']-$row['F_PESNET'])/$row['F_NUMCOL'],3);
				//$tara=$row['F_PESNET'].'::'.$row['F_QTA'];
				$out.="\n<tr><td>".formatData($row['F_DATBOL'])."</td><td>$row[F_CODCLI]</td><td>".round($row['F_NUMCOL'])."</td><td>$netto</td><td>$media</td><td>$tara</td></tr>";
				$sum['NETTO']+=$netto;
				$sum['F_NUMCOL']+=$row['F_NUMCOL'];
			}
		}

		$out.="<tr><th>Totali</th><th>-</th><th>".round($sum['F_NUMCOL'])."</th><th colspan='3'><b style='font-size:2em'>".$sum['NETTO']."</b></th></tr>";
		$out.='</table>';
		if ($sum['F_NUMCOL']>0){
			$out.=' Imballo: '.round($params['costoCassa']*$sum['F_NUMCOL']/$sum['NETTO'],3);
			$out.='<br> Trasporto: '.round($params['costoPedana']/(($sum['NETTO']/$sum['F_NUMCOL'])*$params['colliPedana']),3);
		}
		$out.='<br><br>';

		return $out;
	}
?>
<!DOCTYPE HTML>
<html lang="IT">
    <head>
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
		<style>
		body{
			color: black;
			font-size: 12px;
			font-family: tahoma,arial,verdana,sans-serif;
		}
		</style>
    </head>
     <body>
	<?php
	$today = date("m-d-Y");
	if(@$_POST['startDate']){$startDate=$_POST['startDate'];}else{$startDate=$today;}
	if(@$_POST['endDate']){$endDate=$_POST['endDate'];}else{$endDate=$today;}
	  
	if(@$_POST['startDateR']){$startDateR=$_POST['startDateR'];}else{$startDateR=$today;}
	if(@$_POST['endDateR']){$endDateR=$_POST['endDateR'];}else{$endDateR=$today;}
	  
	?>
<style>
.dateform{
	border: 1px solid #00a3f5;
padding: 0.2em;
	
}
.dateformtitle{
	background-color: #00a3f5;
	width:100%;
	padding:0.5em;	
	display:inline-block;
	font-size:1.5em;
}
.dateselector{
	padding:0.3em;	
	font-size:1.2em;	
	
}
.dateselectordescription{
	width:10em;
	display:inline-block;
	padding:0.8em;	
}
dateselectorcheckbox{
	font-size:2em;
	padding:0.3em;
	transform: scale(2);
	
}
</style>
<form action="./calcoloCosti.php" class="dateform hideOnPrint" method="post"> 
<span class="dateformtitle">Selezione parametri</span>
<br> <span class="dateselectordescription">From:</span>
<input class="dateselector" type="date" id="startDate" name="startDate" value="<?php echo $startDate ?>">
<br> <span class="dateselectordescription">to:</span>
<input class="dateselector" type="date" id="endDate" name="endDate" value="<?php echo $endDate ?>">
<br> <span class="dateselectordescription">From (R):</span>
<input class="dateselector" type="date" id="startDateR" name="startDateR" value="<?php echo $startDateR ?>">
<br> <span class="dateselectordescription">to (R):</span>
<input class="dateselector" type="date" id="endDateR" name="endDateR" value="<?php echo $endDateR ?>">
<br>
<input class="dateselectorcheckbox" type="checkbox" id="extraProducts" name="extraProducts" style="padding:2em;"><label for="extraProducts" class="dateselectorcheckbox" <?php if(@$_POST['extraProducts']){echo 'checked';}?>> Includi radicchi</label>
<br>
<input name="mode" value="print" style="display: none;">

<input type="submit" value="Submit" style="padding:1em;width:20em;">
</form>
<script>
if(document.getElementById('startDate').value==""){
	

	var myDate = new Date();

	
	document.getElementById('startDate').valueAsDate = myDate;
	document.getElementById('endDate').valueAsDate = myDate;
	document.getElementById('endDateR').valueAsDate = myDate;
	myDate.setDate(myDate.getDate() -1);	
	document.getElementById('startDateR').valueAsDate = myDate;
}
</script>
<?php
/*
$costoPolistirolo3050
$costoPlastica3050bianca
$costoPolistirolo3050
$costoPolistirolo3050
*/


if (@$_POST['mode']=='print'){
    $table='<table class="rimanenze">';
    $table.='<tr><td colspan="2">Rimanenze</td></tr>';
    $table.='<tr><td class="rimanenzecellone"></td><td class="rimanenzecelltwo"></td></tr>';
    $table.='<tr><td><b>- pl</b></td><td></td></tr>';
    $table.='<tr><td><b>- Poly</b></td><td></td></tr>';
    $table.='<tr><td><b>- Pad.</b></td><td></td></tr>';
    $table.='<tr><td>+ pl</td><td></td></tr>';
    $table.='<tr><td>+ Poly</td><td></td></tr>';
    $table.='<tr><td>+ Pad.</td><td></td></tr>';
    $table.='<tr><td colspan="2" style="border:4px solid #000000">Tot.<br><br><br></td></tr>';
    $table.='</table>';
    $html='';
  
    $html.="<div class='tableContainer'>";
    $html.="<h1>Riccia</h1>";
//riccia
	// mercato
	
	$params = array("articles" => array('01','01S','01F','01SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.5, //0.3
					"costoPedana" => 33,
					"colliPedana" => 104,
					//"costoCassa" => 0.81);//cassa nuova bianca vergine
					"costoCassa" => 0.43);//cassa vecchia
	$html.=getArticleTable($params);
	
	$params = array("articles" => array('01','01S','01F','01SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.5, //0.3
					"costoPedana" => 33,
					"colliPedana" => 104,
					//"costoCassa" => 0.81);//cassa nuova bianca vergine
					"costoCassa" => 0.43,
					"cliente"=>"MARTI");//cassa vecchia
	$html.=getArticleTable($params);
	//supermercati
	$params = array("articles" => array('801','801-','801F-','601'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.3,
					"costoPedana" => 33,
					"colliPedana" => 140,
					"costoCassa" => 0.52);
    $html.=getArticleTable($params);
    $html.=$table;
	
//scarola  
	$html.="</div><div class='tableContainer'>";
	$html.="<h1>Scarola</h1>";
	// mercato
	$params = array("articles" => array('03','03S','03F','03SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.5, //0.3
					"costoPedana" => 33,
					"colliPedana" => 104,
					//"costoCassa" => 0.81);//cassa nuova bianca
					"costoCassa" => 0.43);//cassa vecchia
	$html.=getArticleTable($params);
	$params = array("articles" => array('03','03S','03F','03SE'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.5, //0.3
					"costoPedana" => 33,
					"colliPedana" => 104,
					//"costoCassa" => 0.81);//cassa nuova bianca vergine
					"costoCassa" => 0.43,
					"cliente"=>"MARTI");//cassa vecchia
	$html.=getArticleTable($params);
	// supermercati
	/*  vecchi conteggi sma
	$params = array("articles" => array('703','703S','803','803S'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.7,
					"costoPedana" => 33,
					"colliPedana" => 60,
					"costoCassa" => 0.70);
	$html.=getArticleTable($params);
	*/
	//supermercati
	$params = array("articles" => array('803','803-','803F-','603'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.3,
					"costoPedana" => 33,
					"colliPedana" => 140,
					"costoCassa" => 0.52);
    $html.=getArticleTable($params);
    $html.=$table;

    if(@$_POST['extraProducts']){
        //$html.='<div style="page-break-before: always"></div>';
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>CH</h1>";
//chioggia
		// mercato
		$params = array("articles" => array('08','08B','08F','08G','08P','08POL','08PZ11','08TRAD'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.3, //0.3
						"costoPedana" => 33,
						"colliPedana" => 112,
						"costoCassa" => 0.38); //NERA DA 13
						//"costoCassa" => 0.47); //POLISTIROLO
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('708','808','808+','708-','808-','708--','808--','608','608-','608+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 80,
						"costoCassa" => 0.68);
		$html.=getArticleTable($params);
		/*
		// VASSOI
		$params = array('articles' => array('VAS08'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.2, //0.3//0.5
						"costoPedana" => 33,
						"colliPedana" => 124,
						"costoCassa" => 0.57); //0,37 cassa + 0,05 x4 vassoi
		$html.=getArticleTable($params);
		*/
		// MARTINELLI
		$params = array('articles' => array('08'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0, //0.3//0.5
						"costoPedana" => 33,
						"colliPedana" => 124,
						"costoCassa" => 0.59, //0,40 cassa pl305016 + 0,031 x4 vassoi+ 0,015 x 4conezioni film
						"cliente"=>"MARTI");
		$html.=getArticleTable($params);
		
		
		
        $html.=$table;
  
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>TV</h1>";
//treviso
		// mercato
		$params = array("articles" => array('29'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4, //0.3
						"costoPedana" => 33,					
						"colliPedana" => 112,
						"costoCassa" => 0.38); //NERA DA 13
						//"costoCassa" => 0.56); //cassa blu
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('729','829','729-','829-','629','629-','629+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
		/*
		// VASSOI
		$params = array('articles' => array('VAS29'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.1, //0.3//0.5
						"costoPedana" => 33,
						"colliPedana" => 124,
						"costoCassa" => 0.67); //0,37 cassa + 0,05 x4 vassoi
		$html.=getArticleTable($params);
		*/
		// MARTINELLI
		$params = array('articles' => array('29'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0, //0.3//0.5
						"costoPedana" => 33,
						"colliPedana" => 124,
						"costoCassa" => 0.68, //0,40 cassa pl305016 + 0,031 x6 vassoi+ 0,015 x 6 conezioni film
						"cliente"=>"MARTI");
		$html.=getArticleTable($params);
		
		
        $html.=$table;
  
        //$html.='<div style="page-break-before: always"></div>';
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>PDZ</h1>";
//pan di zucchero
		$params = array("articles" => array('31'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.5, //0.3
						"costoPedana" => 33,
						"colliPedana" => 104,
						"costoCassa" => 0.40);
		$html.=getArticleTable($params);
		$params = array("articles" => array('731','831','831-','631','631+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 60,
						"costoCassa" => 0.70);
		$html.=getArticleTable($params);
		$params = array("articles" => array('31FLOW'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.1,
						"costoPedana" => 33,
						"colliPedana" => 60,
						"costoCassa" => 0.70);
		$html.=getArticleTable($params);
		$params = array("articles" => array('631FLOW','631FLOW6','631FLOW6+'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0,
						"costoPedana" => 4.0,
						"colliPedana" => 40,
						"costoCassa" => 0.68);
		$html.=getArticleTable($params);
		$html.=$table;

		$html.="</div><div class='tableContainer'>";
        $html.="<h1>VR</h1>";
//verona
		// mercato
		$params = array('articles' => array('05','05G','05P','05PL','05PZ12','05PZ1215','05PZ15','05PZ8'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.1, //0.3//0.5
						"costoPedana" => 33,
						"colliPedana" => 112,
						"costoCassa" => 0.47);//POLISTIROLO
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('705','805','705-','805-','705--','805--','605','605+' ),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
		/*
		// VASSOI
		$params = array('articles' => array('VAS05'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.1, //0.3//0.5
						"costoPedana" => 33,
						"colliPedana" => 124,
						"costoCassa" => 0.67); //0,37 cassa + 0,05 x6 vassoi
		$html.=getArticleTable($params);
		*/
		// MARTINELLI
		$params = array('articles' => array('05'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.0, //0.3//0.5
						"costoPedana" => 33,
						"colliPedana" => 124,
						"costoCassa" => 0.68, //0,40 cassa pl305016 + 0,031 x6 vassoi+ 0,015 x 6 conezioni film
						"cliente"=>"MARTI");
		$html.=getArticleTable($params);
        $html.=$table;

//sedano
	if(0){
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>Sedano</h1>";
	// mercato
		$params = array('articles' => array('36'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.3, //0.3
						"costoPedana" => 33,
						"colliPedana" => 104,
						"costoCassa" => 0.40);
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('836'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
	}
		
/*
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>Finocchio</h1>";
//finocchio
		// mercato
		$params = array('articles' => array('26'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.3, //0.3
						"costoPedana" => 33,
						"colliPedana" => 112,
						"costoCassa" => 0.38);
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('726','826'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
*/

/*
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>Iceberg</h1>";
//iceberg
		// mercato
		$params = array('articles' => array('09'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.5, //0.3
						"costoPedana" => 33,
						"colliPedana" => 104,
						"costoCassa" => 0.43);
		$html.=getArticleTable($params);		
		// supermercati
		$params = array("articles" => array('809'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 60,
						"costoCassa" => 0.70);
		$html.=getArticleTable($params);
        $html.=$table;
*/
//VERZE
/*
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>Verze</h1>";
		// mercato
		$params = array('articles' => array('20'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.3, //0.3
						"costoPedana" => 33,
						"colliPedana" => 104,
						"costoCassa" => 0.43);
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('820'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 33,
						"colliPedana" => 60,
						"costoCassa" => 0.70);
		$html.=getArticleTable($params);
        $html.=$table;
*/
	}


    $html.='</div>';
    echo $html;
}
page_end();
?>
</span>
</body>
