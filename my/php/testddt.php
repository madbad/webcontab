		<link rel="stylesheet" type="text/css" href="style.css">
		<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
<?php
include ('./core/config.inc.php');

$ddt=new Ddt(array('_autoExtend'=>'-1'
	));
$ddt->visualizzaPdf($ddt);

page_end();

?>
</body>