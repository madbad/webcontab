<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

$stampaRighe= function ($obj){

	echo '<tr> ';
	echo '<td>'.$obj->codice->getVal().'</td>';
	echo '<td>'.$obj->descrizione->getVal().'</td>';
	echo '<td>'.$obj->descrizione2->getVal().'</td>';
	echo '<td><pre>'.$obj->descrizionelunga->getVal().'</pre></td>';
//	echo '<td><pre>'.str_replace("�","",$obj->descrizionelunga->getVal()).'</pre></td>';
	echo '</tr>';
};

$test=new MyList(
	array(
		'_type'=>'Articolo',
		'codice'=>array('!=','niente'),
	)
);
echo '<table>';
$test->iterate($stampaRighe);
echo '</table>';
page_end();
?>