 <?php
//include ('./config.inc.php');
 
 function import_csv_to_sqlite(&$db, $csv_path, $options = array())
{
/*
    extract($options);

    if (($csv_handle = fopen($csv_path, "r")) === FALSE)
        throw new Exception('Cannot open CSV file');

    if(!$delimiter)
        $delimiter = ',';

    if(!$table)
        $table = preg_replace("/[^A-Z0-9]/i", '', basename($csv_path));

    if(!$fields){
        $fields = array_map(function ($field){
			return $field;
            //return strtolower(preg_replace("/[^A-Z0-9]/i", '', $field));
        }, fgetcsv($csv_handle, 0, $delimiter));
    }

    $create_fields_str = join(', ', array_map(function ($field){
        return "$field TEXT NULL";
    }, $fields));

    //$pdo->beginTransaction();
*/
	//for each row
	$table = "ANAGRAFICACLIENTI";
	$iteration = 0;
	$file = fopen($csv_path,"r");
	while (($data = fgetcsv($file)) !== FALSE) {
		if($iteration==0){
			$fields = $data;	
		}else{
			/*
			foreach ($data as $key => $value) {
				$data[$key]='"'.$value.'"';				
			}
			
			
			UPDATE ANAGRAFICACLIENTI (codice, __SDIcodice, __SDIpec, __ragionesociale, __p_iva, __cod_fiscale, __via, __paese, __citta, __cap, __nazione, __ispersonafisica) VALUES ("FORN2", "", "", "SOCIETA' AGRICOLA FORNARI DI FORNARI GIUSEPPE E C. S.S.", "02210750200", "02210750200", "Localita' Sorbara 115", "Asola", "MN", "46041", "IT", "") WHERE codice = 'FORN2'
			
			$insert_fields_str = join(', ', $fields);
			$insert_values_str = join(', ', $data);

			//echo $query."\n<br>";		
			$query = 'INSERT INTO '.$table.' ('.$insert_fields_str.') VALUES ('.$insert_values_str.') WHERE codice = '.$data[0].'';
			*/
			$query = 'UPDATE ANAGRAFICACLIENTI SET ';
			
			foreach ($data as $key => $value) {
				$query .= $fields[$key].'="'.$value.'", ';				
			}
			//remove the last ,
			$query = substr_replace($query, "", -2);
			$query .=' WHERE codice = "'.$data[0].'"';

			//echo $query."\n<br>";		
			$db->exec($query) or die($query);
		}
		$iteration++;
	}	

	
/*	
    $create_table_sql = "CREATE TABLE IF NOT EXISTS $table ($create_fields_str)";
    $pdo->exec($create_table_sql);
print_r($fields);
    $insert_fields_str = join(', ', $fields);
    $insert_values_str = join(', ', array_fill(0, count($fields),  '?'));
    $insert_sql = "INSERT INTO $table ($insert_fields_str) VALUES ($insert_values_str)";
    //$insert_sth = $pdo->prepare($insert_sql);
	echo $insert_sql;
    ///$pdo->exec($insert_sql);

	
    $inserted_rows = 0;
/*
    while (($data = fgetcsv($csv_handle, 0, $delimiter)) !== FALSE) {
        $insert_sth->execute($data);
        $inserted_rows++;
    }
*/
    //$pdo->commit();
/*
    fclose($csv_handle);
*/
/*
    return array(
            'table' => $table,
            'fields' => $fields,
            'insert' => $insert_sth,
            'inserted_rows' => $inserted_rows
        );
*/
}

$db = new SQLite3('./../dati/sqliteDb/myDb.sqlite3');
import_csv_to_sqlite($db, "./database.csv");
?>