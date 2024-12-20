<?php
include ('./core/config.inc.php');

function elenca(){
	?>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="stylesheet" type="text/css" href="style_print.css" media="print">
	<style>
		.searchinput {
			padding: 0.4em;
			width: 20em;
			font-size:1.5em;	
		}
		
		/**/
		.spacedTable td {
			padding: 0.1em;
			margin:0em;
			padding-left:0.8em;
			padding-right:0.8em;
		}
		
		tbody tr:nth-child(odd) {
		  background-color: #afc5e3;
		}

		tbody tr:nth-child(odd) input {
		  background-color: #afc5e3;
		}
		.topbar{
			position:fixed;
			top:0px;
			left:0px;
			background-color: orange;
			width: 100%;
			padding: 0.3em;
			box-shadow: 0px 5px 5px gray;
		}
		.topbar form{
			margin: 0px;	
		}
		.topbar input[type='submit']{
			font-size: 1.6em;
			padding: 0.3em;
			padding-left: 0.8em;
			padding-right: 0.8em;
		}
		
	</style>
	<script>
		function saveNote(ddt_data, ddt_numero, riga, note){
			console.log('Salvato: ',note);
			//alert(note);
			var xhttp = new XMLHttpRequest();
			xhttp.open("POST", "./annotaddt.php.", true);
			xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xhttp.send("ddt_data="+ddt_data+"&ddt_numero="+ddt_numero+"&numero="+riga+"&note="+note); 
		}

		//deffered saving of notes
		document.addEventListener("keyup", (event) => {
			console.log(event);
			console.log()
			var rowSaveButton = event.target.parentNode.parentNode.querySelectorAll('input')[1]
			rowSaveButton.click()
			console.log('saved');
		});

	</script>
	<div class="topbar">
		<form action="./annotaddt.php" method="POST">
			<input type="text" name="articolo" class="searchinput" placeholder="Articolo Descrizione." value="<?php echo $_POST["articolo"];?>" autofocus>
			<input type="text" name="cliente" class="searchinput" placeholder="Cliente (codice)" value="<?php echo $_POST["cliente"];?>">
			<input type="submit" value="Invia">
		</form> 
	</div>
	<br><br><br>
	<?php
	if(!array_key_exists("articolo", $_POST)){
		return;
	}
	//////////
	//  1   //
	//quale � l'ultimo ddt (data e numero) che ho salvato nel mio database?
	$table="BACKUPRIGHEDDT";
	//echo $GLOBALS['config']->sqlite->dir.'/myDb.sqlite3'; 
	//select the max date from this year = 2018
	//$query="SELECT * FROM '".$table."' WHERE descrizione LIKE '%GENTILE%' AND ddt_data LIKE'12-12-2020'"; 
	$query ="SELECT * FROM '".$table."'";
	$query.=" WHERE descrizione LIKE '%".$_POST["articolo"]."%'";
	$query.=" AND cod_cliente LIKE '%".$_POST["cliente"]."%'";
	$query.=" AND ddt_data LIKE '%-2024%'"; //MOSTRA SOLO L'ANNO 2024
	#$query.=" AND ddt_data LIKE '%".$_POST["cliente"]."%'";

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
		echo '<td>'.$row['colli'].'</td>';
		echo '<td>'.$row['peso_netto'].'</td>';
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
