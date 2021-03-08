<?php
set_time_limit ( 0);
echo '<h1>Importo gli acquisti</h1>';
$command = escapeshellcmd('C:/Programmi/EasyPHP-5.3.9/www/webContab/my/python/imap_acqusiti.py');
$output = shell_exec($command);
echo '<pre>'.$output.'</pre>';

echo '<hr>';
echo '<h1>Importo le ricevute delle fatture di vendita</h1>';
$command = escapeshellcmd('C:/Programmi/EasyPHP-5.3.9/www/webContab/my/python/imap_ricevute_vendite.py');
$output = shell_exec($command);
echo '<pre>'.$output.'</pre>';

?>
<hr>
<a href="./acquisti.php">Vai agli acquisti</a>
<br>
<a href="./verificaSequenzialitaRicevute.php">Vai al controllo sequenzialita ricevute</a>
