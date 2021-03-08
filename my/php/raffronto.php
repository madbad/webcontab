<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<figure class="highcharts-figure">
  <div id="container"></div>
</figure>
<?php
include ('./core/config.inc.php');


function printChart($articolo, $serieString){
	echo "
	
<figure class=\"highcharts-figure\">
  <div id=\"$articolo\"></div>
</figure>
<script>
Highcharts.chart('$articolo', {
    chart: {
        type: 'column'
    },
    title: {
        text: '$articolo'
    },
    subtitle: {
        text: '-'
    },
    xAxis: {
        categories: [
            '01',
            '02',
            '03',
            '04',
            '05',
            '06',
            '07',
            '08',
            '09',
            '10',
            '11',
            '12'
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Peso (kg)'
        }
    },
    tooltip: {
        headerFormat: '<span style=\"font-size:10px\">{point.key}</span><table>',
        pointFormat: '<tr><td style=\"color:{series.color};padding:0\">{series.name}: </td>' +
            '<td style=\"padding:0\"><b>{point.y:.1f} kg</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: $serieString
});

</script>";
}

/*======================================================================

======================================================================*/
function getArticoli($descArticolo){
	global $codiciArticolo;
	$listaArticoli=new MyList(
		array(
			'_type'=>'Articolo',
			'descrizione'=>array('LIKE','%'.$descArticolo.'%'),
		)
	);

	$codiciArticolo="'='";
	$arrayCodiciArticolo=array();

	$listaArticoli->iterate(function ($obj){
		global $codiciArticolo;
		global $arrayCodiciArticolo;

		$codiciArticolo.=",'".$obj->codice->getVal()."'";
		$arrayCodiciArticolo[] = $obj->codice->getVal();

	});
}
/*======================================================================

======================================================================*/
function getData($startDate, $endDate, $codiciArticolo){
	//print_r($codiciArticolo);
	$query = "
		\$test=new MyList(
			array(
				'_type'=>'Riga',
				'ddt_data'=>array('<>','".$startDate."','".$endDate."'),
				//'cod_articolo'=>array(".$codiciArticolo."),
				'cod_cliente'=>array('=','SEVEN'),
				//'cod_articolo'=>array('!=','BSEVEN','631FLOW','31FLOW','631FLOW6'),
				//'cod_articolo'=>array('=','631FLOW','31FLOW','631FLOW6'),
				//'colli'=>array('!=','0'),
				//'prezzo'=>array('!=','0.001','0.000')
			)
		);
	";
	//echo $query;
	eval ($query);
	/*
	var_dump($test->_params['cod_articolo']);
	var_dump($test->_params['ddt_data']);

	echo "\n<br>".$test->sum('colli');
	echo "\n<br>".$test->sum('peso_netto');
	echo "\n<br>".$test->sum('imponibile');	
	*/
	return $test;
}

//$articoli = array('SCAROLA','RICCIA','ROSSO TONDO','ROSSO LUNGO','SEMILUNGO','ZUCCHERO','RAD.BIANCO','CAPUCCI','MELONI','ZUCCHINE','ZUCCA','PORRI','MELANZANE');
$articoli = array(' ');

$anni = array('2017','2018','2019','2020');
$mesi = array('01','02','03','04','05','06','07','08','09','10','11','12');

$outData=array();

foreach ($articoli as $articolo){
	getArticoli($articolo);
	$outData[$articolo]=array();
	foreach ($anni as $anno){
		$outData[$articolo][$anno]=array();
		foreach ($mesi as $mese){
			$giorniMese = cal_days_in_month(CAL_GREGORIAN,$mese,$anno);
			$startDate='01/'.$mese.'/'.$anno;
			$endDate=$giorniMese.'/'.$mese.'/'.$anno;
			
			$dati = getData($startDate, $endDate, $codiciArticolo);
			$outData[$articolo][$anno][$mese]=array();
			$outData[$articolo][$anno][$mese]['coli']=$dati->sum('colli');
			$outData[$articolo][$anno][$mese]['peso_netto']=$dati->sum('peso_netto');
			$outData[$articolo][$anno][$mese]['imponibile']=$dati->sum('imponibile');		
		}
	}
	//build the series string:
	/*
	[{
        name: 'Tokyo',
        data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4]

    }, {
        name: 'New York',
        data: [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3]

    }, {
        name: 'London',
        data: [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2]

    }, {
        name: 'Berlin',
        data: [42.4, 33.2, 34.5, 39.7, 52.6, 75.5, 57.4, 60.4, 47.6, 39.1, 46.8, 51.1]

    }]
	*/
	$serieString='[';
	foreach ($anni as $anno){
		$serieString.='{';
		$serieString.="name: '$anno',";
		$serieString.="data: [";
		
		foreach ($mesi as $mese){
			//$serieString.=$outData[$articolo][$anno][$mese]['peso_netto'].',';
			$serieString.=$outData[$articolo][$anno][$mese]['imponibile'].',';
		}
		$serieString = substr($serieString, 0, -1); //remove the last ,
		$serieString.="]";
		$serieString.='},';		
	}
	$serieString = substr($serieString, 0, -1); //remove the last ,
	$serieString.=']';	
	
	printChart($articolo, $serieString);
}
//print_r($outData);
	
?>