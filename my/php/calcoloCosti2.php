<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>WebContab Calcolo costi</title>
        <meta charset="utf-8">
		
		<!--ExtJs-->
		<script src="./../js/ext.js/ext-all.js" type="text/javascript"></script>
		<link href="./../js/ext.js/resources/css/ext-all.css" rel="stylesheet" type="text/css">
		<script type="text/javascript">
		Ext.require(['*']);
		</script>
		

         
    </head>
  
    <body>
<?php
$today = date("nn-j-Y");
if(@$_GET['startDate']){$startDate=$_GET['startDate'];}else{$startDate=$today;}
if(@$_GET['endDate']){$endDate=$_GET['endDate'];}else{$endDate=$today;}
  
if(@$_GET['startDateR']){$startDateR=$_GET['startDateR'];}else{$startDateR=$today;}
if(@$_GET['endDateR']){$endDateR=$_GET['endDateR'];}else{$endDateR=$today;}
  
?>
<script>

Ext.create('Ext.form.Panel', {
    renderTo: Ext.getBody(),
    width: 400,
    bodyPadding: 10,
	name: 'input',
	method: 'GET',
	url: './calcoloCosti.php?mode=print',
	standardSubmit: true,
    title: 'Dates',
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
        checked   : <?php if(@$_GET['extraProducts']) {echo 'true';}else{echo 'false';}  ?>,

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


<!--
<form name="input" action="./calcoloCosti.php?mode=print" method="get">
    <input type="text" name="mode" value="print" style="display:none"/>
    <label>Start date</label> <input type="text" name="startDate" value="<?php echo $startDate ?>"/>
    <label>End date</label> <input type="text" name="endDate" value="<?php echo $endDate ?>"/>
     
    <label>Start date2</label> <input type="text" name="startDateR" value="<?php echo $startDateR ?>"/>
    <label>End date2</label> <input type="text" name="endDateR" value="<?php echo $endDateR ?>"/>  
    <label>Includi Radicchi</label> <input name="extraProducts" type="CHECKBOX" <?php if(@$_GET['extraProducts']) echo 'checked'  ?>/>
    <button type="submit">Search</button>
</form>
  -->
<?php
require_once('./classes.php');

if (@$_GET['mode']=='print'){
    //log start date for time execution calc
    $start = (float) array_sum(explode(' ',microtime()));
  
    $table='<table id="rimanenze">';
    $table.='<tr><td>Rimanenze</td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td></td><td></td></tr>';
    $table.='<tr><td>Tot.</td><td></td></tr>';
    $table.='</table>';
    $html='';
  
    $html.="<div>";
    $html.="<h1>Riccia</h1>";
//riccia
	// mercato
	$params = array("articles" => array('01'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.3,
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
    $html.="</div><div>";
    $html.="<h1>Scarola</h1>";
	// mercato
	$params = array("articles" => array('03'),
					"startDate" => $startDate,
					"endDate" => $endDate,
					"abbuonoPerCollo" => 0.3,
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

    if(@$_GET['extraProducts']){
        //$html.='<div style="page-break-before: always"></div>';
        $html.="</div><div>";
        $html.="<h1>Tondo</h1>";
//chioggia
		// mercato
		$params = array("articles" => array('08'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 31,
						"colliPedana" => 112,
						"costoCassa" => 0.39);
		$html.=getArticleTable($params);
		// supermercati
		$params = array("articles" => array('708','808','708-','808-','708--'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
  
        $html.="</div><div>";
        $html.="<h1>Lungo</h1>";
//treviso
		// mercato
		$params = array("articles" => array('29'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 31,
						"colliPedana" => 112,
						"costoCassa" => 0.34);
		$html.=getArticleTable($params);		
		// supermercati
		$params = array("articles" => array('729','829'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
  
        //$html.='<div style="page-break-before: always"></div>';
        $html.="</div><div>";
        $html.="<h1>P.di Zucchero</h1>";
//pan di zucchero
		$params = array("articles" => array('31'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 31,
						"colliPedana" => 104,
						"costoCassa" => 0.43);
		$html.=getArticleTable($params);		
		$params = array("articles" => array('731','831'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 60,
						"costoCassa" => 0.70);
		$html.=getArticleTable($params);
        $html.=$table;
        $html.="</div><div>";
        $html.="<h1>Semil.</h1>";
//verona
		// mercato
		$params = array('articles' => array('05'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.3,
						"costoPedana" => 31,
						"colliPedana" => 112,
						"costoCassa" => 0.39);
		$html.=getArticleTable($params);		
		// supermercati
		$params = array("articles" => array('705','805'),
						"startDate" => $startDate,
						"endDate" => $endDate,
						"abbuonoPerCollo" => 0.4,
						"costoPedana" => 31,
						"colliPedana" => 80,
						"costoCassa" => 0.67);
		$html.=getArticleTable($params);
        $html.=$table;
    }
    $html.='</div>';
    echo $html;
  
     //log end date for time execution calc
    $end = (float) array_sum(explode(' ',microtime()));
     //print execution time
    echo "<br>Exec time: ". sprintf("%.4f", ($end-$start))." seconds";
}
?>
</body>