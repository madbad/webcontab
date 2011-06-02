provaaaaaaaaaaaa

<?php
echo '***********************';

include('config.inc.php');
include('class.php');
include('config.inc.php');
include('funzioni.php');


$MyScript=new script();;
$MyScript->start();
##
$action='';


switch ($action) {
    default:

        break;

    case 'openDDT':
	    $tablename='DDT';
	    $query = "SELECT * FROM $table_prefix$tablename WHERE 1 ORDER BY Numero DESC";
	    $queryresult = TD_connect($query);
	    //creo l'oggetto DDT che contiene tutti i DDT indicizzati per numero
	    while ($row = mysql_fetch_array($queryresult))
	    {
		    $DDT[$row['Numero']]=$row;
		    echo $DDT['0000000001']['Numero'];
	    }
       break;

    case 'memoDDT':
       break;

}


$MyScript->stop();
$MyScript->print_stats();

?>