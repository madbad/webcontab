<?php
include ('./config.inc.php');
//require_once('./classes.php');
//page_start();
		
//mi memorizzo il database clienti	(dove ho salvato se sono mercati supermercati o altro)	
$dbClienti=getDbClienti();

	function getArticleTable($params){
		global $dbClienti;
		/*
		$params = array("articles" => "31",
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 31,
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
		$result=dbFrom('RIGHEDDT', 'SELECT *', "WHERE $condizioniProdotti F_DATBOL >= #".$params['startDate']."# AND F_DATBOL <= #".$params['endDate']."# ORDER BY F_DATBOL, F_NUMBOL, F_PROGRE");
		
		$out.="<table class=\"righe\"><tr><th colspan='5'>cod:".join(",", $params['articles'])." <br>( ".$params['startDate']." > ".$params['endDate']." )</th></tr>";	
		$out.='<tr><th>Data</th><th>Cliente</th><th>Colli</th><th>p.Netto</th><th>md</th><th>tara</th></tr>';
		//this will containt table totals
		$sum=array('NETTO'=>0,'F_NUMCOL'=>0);

		foreach ($result as $id => $row){
		//while($row = odbc_fetch_array($result)){
			$codCliente=$row['F_CODCLI'];
			$tipoCliente=$dbClienti["$codCliente"]['__classificazione'];
			if (in_array($row['F_CODPRO'],$params['articles']) && ($tipoCliente=='mercato' || $tipoCliente=='supermercato')){

				$calopeso=round(round($row['F_NUMCOL'])*$params['abbuonoPerCollo']);
				$netto=$row['F_PESNET']-$calopeso;
				$media=round($netto/$row['F_NUMCOL'],1);
				$tara=round(($row['F_QTA']-$row['F_PESNET'])/$row['F_NUMCOL'],3);
				//$tara=$row['F_PESNET'].'::'.$row['F_QTA'];				
				$out.="\n<tr><td>$row[F_DATBOL]</td><td>$row[F_CODCLI]</td><td>".round($row['F_NUMCOL'])."</td><td>$netto</td><td>$media</td><td>$tara</td></tr>";
				$sum['NETTO']+=$netto;
				$sum['F_NUMCOL']+=$row['F_NUMCOL'];
			}	
		}

		$out.="<tr><th>Totali</th><th>-</th><th class='totali'>".round($sum['F_NUMCOL'])."</th><th class='totali' colspan='3'>".$sum['NETTO']."</th></tr>";
		$out.='</table>';
		
		$out.=' Imballo: '.round($params['costoCassa']*$sum['F_NUMCOL']/$sum['NETTO'],3);
		$out.='<br> Trasporto: '.round($params['costoPedana']/(($sum['NETTO']/$sum['F_NUMCOL'])*$params['colliPedana']),3);
		$out.='<br>';

		return $out;
	}
?>
<!DOCTYPE HTML>
<html lang="IT">
    <head>
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
		
		<!--ExtJs-->
		<script src="./../js/ext.js/ext-all.js" type="text/javascript"></script>

		<link href="./../js/ext.js/resources/css/ext-all.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
		Ext.require(['*']);
		</script>
 		<script src="./../js/ext.js/locale/ext-lang-it.js" type="text/javascript"></script>
        <style type="text/css">
			@PAGE landscape {size: landscape;}
			TABLE {PAGE: landscape;}
			@page rotated { size : landscape }
            .totali{
                 font-size:1.5em;
            }
			.righe table, .righe tr, .righe td, .righe th{
                font-size:x-small;
                padding:0px;
                margin:0;
                text-align:right;
                border:1px solid #000000;
                    border-collapse: collapse;
                margin-left:0.5em;
			}
            .righe td, .righe th{
                padding-left:4px;
                padding-right:4px;
            }
            .righe th{
                font-weight:bold;
                text-align:left;
            }
            .righe hr{
                margin-top:150px;
            }
            .rimanenze td{
                height:3.5em;
                width:9em;
                text-align:left;
				padding-left:1em;

            }
			.rimanenze table, .rimanenze tr, .rimanenze td, .rimanenze th{
                font-size:x-small;
                border:1px solid #000000;
                border-collapse: collapse;
								margin:1em;
			}			
            span div {
                float:left;
            }
            .totali{
                 font-size:1.5em;
            }
			.tableContainer{
				padding:0.2em;
			}
			h1{
				font-size:2em;
			}
        </style>
		<style type="text/css" media="print" />      
			.hideOnPrint{
				display:none;
			}
			@PAGE landscape {size: landscape;}
			TABLE {PAGE: landscape;}
			@page rotated { size : landscape }
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
	
	<span class="hideOnPrint" id="myForm">	
	</span>
	<script>
		Ext.create('Ext.form.Panel', {
			//renderTo: Ext.getBody(),
			renderTo: document.getElementById('myForm'),
			name: 'input',
			method: 'POST',
			layout: {
    type: 'vbox',
    align: 'right'
},
width:350,
height:230,
			url: './calcoloCosti.php',
			standardSubmit: true,
			bodyStyle: 'padding:10px',
			title: 'Selezione parametri',
			items: [{
				xtype: 'hiddenfield',
				name: 'mode',
				value: 'print'
			}, {
				xtype: 'datefield',
				format: 'm-d-Y',
				anchor: '100%',
				fieldLabel: 'From',
				name: 'startDate',
				value: '<?php echo $startDate ?>'  // defaults to today
			}, {
				xtype: 'datefield',
				format: 'm-d-Y',
				anchor: '100%',
				fieldLabel: 'To',
				name: 'endDate',
				value: '<?php echo $endDate ?>' // defaults to today
			}, {
				xtype: 'datefield',
				format: 'm-d-Y',
				anchor: '100%',
				fieldLabel: 'From (R)',
				name: 'startDateR',
				value: '<?php echo $startDateR ?>'  // defaults to today
			}, {
				xtype: 'datefield',
				format: 'm-d-Y',
				anchor: '100%',
				fieldLabel: 'To (R)',
				name: 'endDateR',
				value: '<?php echo $endDateR ?>'  // defaults to today
			}, {
				xtype: 'checkboxfield',
				boxLabel  : 'Includi radicchi',
				name: 'extraProducts',
				checked   : <?php if(@$_POST['extraProducts']) {echo 'true';}else{echo 'false';}  ?>,

			}],
			// Reset and Submit buttons
			buttons: [{
				text: 'Submit',
				handler: function() {
					var form = this.up('form').getForm();
					form.submit();
				}
			}],
		});
	</script>

<span>

<?php


if (@$_POST['mode']=='print'){
    $table='<table class="rimanenze">';
    $table.='<tr><td colspan="2">Rimanenze</td></tr>';
    $table.='<tr><td style="width:30%"></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td>Tot.</td><td style="border:4px solid #000000"></td></tr>';
    $table.='</table>';
    $html='';
  
    $html.="<div class='tableContainer'>";
    $html.="<h1>Riccia</h1>";
//riccia
	// mercato
	
	$params = array("articles" => array('01'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.5, //0.3
					"costoPedana" => 31,
					"colliPedana" => 104,
					"costoCassa" => 0.43);	
    $html.=getArticleTable($params);
	
	//supermercati
	$params = array("articles" => array('701','801'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.7,
					"costoPedana" => 31,
					"colliPedana" => 60,
					"costoCassa" => 0.70);
    $html.=getArticleTable($params);					
    $html.=$table;
	
//scarola  
    $html.="</div><div class='tableContainer'>";
    $html.="<h1>Scarola</h1>";
	// mercato
	$params = array("articles" => array('03'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.5, //0.3
					"costoPedana" => 31,
					"colliPedana" => 104,
					"costoCassa" => 0.43);
	$html.=getArticleTable($params);
	// supermercati
	$params = array("articles" => array('703','803'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.7,
					"costoPedana" => 31,
					"colliPedana" => 60,
					"costoCassa" => 0.70);
	$html.=getArticleTable($params);
    $html.=$table;

    if(@$_POST['extraProducts']){
        //$html.='<div style="page-break-before: always"></div>';
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>Tondo</h1>";
//chioggia
		// mercato
		$params = array("articles" => array('08'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.3, //0.3
						"costoPedana" => 31,
						"colliPedana" => 112,
						"costoCassa" => 0.39);
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('708','808','708-','808-','708--','808--'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
  
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>Lungo</h1>";
//treviso
		// mercato
		$params = array("articles" => array('29'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.5, //0.3
						"costoPedana" => 31,
						"colliPedana" => 112,
						"costoCassa" => 0.34);
		$html.=getArticleTable($params);		
		// supermercati
		$params = array("articles" => array('729','829'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
  
        //$html.='<div style="page-break-before: always"></div>';
		$html.="</div><div class='tableContainer'>";
        $html.="<h1>P.di Zucchero</h1>";
//pan di zucchero
		$params = array("articles" => array('31'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.5, //0.3
						"costoPedana" => 31,
						"colliPedana" => 104,
						"costoCassa" => 0.43);
		$html.=getArticleTable($params);		
		$params = array("articles" => array('731','831'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 60,
						"costoCassa" => 0.70);
		$html.=getArticleTable($params);
        $html.=$table;

		$html.="</div><div class='tableContainer'>";
        $html.="<h1>Semil.</h1>";
//verona
		// mercato
		$params = array('articles' => array('05'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.5, //0.3
						"costoPedana" => 31,
						"colliPedana" => 112,
						"costoCassa" => 0.39);
		$html.=getArticleTable($params);		
		// supermercati
		$params = array("articles" => array('705','805'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
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
						"costoPedana" => 31,
						"colliPedana" => 112,
						"costoCassa" => 0.38);
		$html.=getArticleTable($params);		
		// supermercati
		$params = array("articles" => array('726','826'),
						"startDate" => $startDateR,
						"endDate" => $endDateR,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
    }
*/

    $html.='</div>';
    echo $html;
}
page_end();
?>
</span>
</body>