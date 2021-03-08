<?php
include ('./core/config.inc.php');

function elenca(){
	?>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
	<script>
		function saveNote(ddt_data, ddt_numero, riga, note){
			console.log(note);
			alert(note);
			var xhttp = new XMLHttpRequest();
			xhttp.open("POST", "./annotaddt.php.", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("ddt_data="+ddt_data+"&ddt_numero="+ddt_numero+"&numero="+riga+"&note="+note); 
		}

	</script>
	<form action="./annotaddt.php" method="POST">
		<br><input type="text" name="articolo"><label>Articolo</label>
		<input type="submit" value="Invia">
	</form> 

	<?php
	if(!array_key_exists("articolo", $_POST)){
		return;
	}
	//////////
	//  1   //
	//quale è l'ultimo ddt (data e numero) che ho salvato nel mio database?
	$table="BACKUPRIGHEDDT";
	//echo $GLOBALS['config']->sqlite->dir.'/myDb.sqlite3'; 
	//select the max date from this year = 2018
	//$query="SELECT * FROM '".$table."' WHERE descrizione LIKE '%GENTILE%' AND ddt_data LIKE'12-12-2020'"; 
	$query="SELECT * FROM '".$table."' WHERE descrizione LIKE '%".$_POST["articolo"]."%'"; 

	$db = new SQLite3($GLOBALS['config']->sqlite->dir.'/myDb.sqlite3');
	$result = $db->query($query);
	$ultimoDdtData = '';
	$ultimoDdtNumero = '';
	echo '<table class="spacedtable bordertable>"';
	while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
		echo '<tr>';
		$ultimoDdtData = $row['DataUltimoDdt'];
		echo '<td>'.$row['ddt_data'].'</td>';
		echo '<td>'.$row['ddt_numero'].'</td>';
		echo '<td>'.$row['numero'].'</td>';
		echo '<td>'.$row['cod_cliente'].'</td>';
		echo '<td>'.$row['descrizione'].'</td>';
		echo '<td><input type="text" value="'.$row['note'].'"></td>';
		echo '<td><input type="button" value="Save" onclick="javascript:saveNote(';
		echo "'".$row['ddt_data']."',";
		echo "'".$row['ddt_numero']."',";
		echo "'".$row['numero']."',";
		#echo "'".$row['note']."'";
		//echo "this.parentElement.getElementsByTagName('input')[1].value";
		echo "this.parentElement.previousSibling.getElementsByTagName('input')[0].value";
		echo ')"></td>';
		echo '</tr>';
	}
	echo '</table>';
	?>
	</body>
<?php
}
function salva(){
	$query="UPDATE BACKUPRIGHEDDT SET note ='".$_POST["note"]."' WHERE ddt_data='".$_POST["ddt_data"]."' AND ddt_numero='".$_POST["ddt_numero"]."' AND numero='".$_POST["numero"]."'";
	echo $query;
	$db = new SQLite3($GLOBALS['config']->sqlite->dir.'/myDb.sqlite3');
	$result = $db->query($query);
}

if (array_key_exists("note", $_POST)){
	salva();
	echo '<br>salvataggio eseguito';
}else{
	elenca();
}
?>