<h1>Copio i file aggiornati da CONTAB...</h1>
<?php
$startTime = microtime(true);

error_reporting(-1); //0=spento || -1=acceso
set_time_limit (0); //0=nessun limite di tempo

$command = './utils/copy_all.bat';
$command = "C:\Programmi\EasyPHP-5.3.9\www\webContab\my\php\utils\copy_all.bat";
echo '<br>'.$command;
echo '<pre style="font-size:0.8em;border:1px solid green;">';
exec($command,$output);
//var_dump($output)
foreach ($output as $txtline){
		echo "\n<br># ".$txtline;
}
echo "</pre>";
$tempoImpiegato =(microtime(true) - $startTime);
echo "Tempo impiegato: ".round($tempoImpiegato, 3)." sec.<br><br><br>";
?>


<h1>Aggiorno il database sqlite...</h1>
<?php
include ('./aggiornadb.php');
?>
