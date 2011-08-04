<?php
$sFile='./FILEDBF/CONTAB/03BORIGD.DBF';
//$sFile='./FILEDBF/03ANFORD.DBF';
//$sFile='./FILEDBF/03ANCLID.DBF';
/*  
$db=dbase_open($sFile,2);
if($db)
{
        echo "DBF file $sFile successfully opened<br/>";
		
        $insert=dbase_add_record($db, array(date('Ymd'),'Name1','26','name1@domain.com','T'));   
        if($insert)
                echo 'Record was inserted<br/>';
        else
                echo 'Record could not be inserted<br/>';
		
        $record_numbers = dbase_numrecords($db);
        for ($i = 1; $i <= $record_numbers; $i++) 
        {
        $row = dbase_get_record_with_names($db, $i);
        if ($row['ismember'] == 1) 
               {
                echo "Member #$i: " . trim($row['name']) .trim($row['email']). "<br/>";
        }
		echo '<pre>'.printf($row).'<pre>';
        }
        dbase_close($db);               
}       
else
        echo "DBF file $sFile could not be opened<br/>";
		*/
		
		
		
		

function echo_dbf($dbfname) {
    $fdbf = fopen($dbfname,'r');
    $fields = array();
    $buf = fread($fdbf,32);
    $header=unpack( "VRecordCount/vFirstRecord/vRecordLength", substr($buf,4,8));
    echo 'Header: '.json_encode($header).'<br/>';
    $goon = true;
    $unpackString='';
    while ($goon && !feof($fdbf)) { // read fields:
        $buf = fread($fdbf,32);
        if (substr($buf,0,1)==chr(13)) {$goon=false;} // end of field list
        else {
            $field=unpack( "a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf,0,18));
            echo 'Field: '.json_encode($field).'<br/>';
            $unpackString.="A$field[fieldlen]$field[fieldname]/";
            array_push($fields, $field);}}
	
    fseek($fdbf, $header['FirstRecord']+1); // move back to the start of the first record (after the field definitions)
    for ($i=1; $i<=$header['RecordCount']; $i++) {
        $buf = fread($fdbf,$header['RecordLength']);
        $record=unpack($unpackString,$buf);
        //echo 'record: '.json_encode($record).'<br/>';
        echo $i.'----'.$buf.'<br/>';
		$start=0;
		

		if($i==1){
		echo '<table style="border:1px solid #000000"><tr>';
			for ($h=0; $h<count($fields); $h++){
				$field=$fields[$h];
				echo '<td style="border:1px solid #000000">'.$field['fieldname']."</td>";
			}
		
		}
			if($i>33892 &&$i<33897){		
			echo "</tr><tr>"; 	
		}			
		for ($h=0; $h<count($fields); $h++){
			$field=$fields[$h];
			//echo $field['fieldname'].':'.$field['fieldlen'].'<br>';
			$name=$field['fieldname'];
			$length=$field['fieldlen'];
			$riga["$name"]=substr($buf,$start,$length);
			$start+=$field['fieldlen'];

			if($i>33892 &&$i<33897){
						//echo $field['fieldname'].':"'.$riga["$name"].'"<br>';
				echo '<td style="border:1px solid #000000">'.$riga["$name"]."</td>";
			}
			//ECHO substr($buf,$start,$field['fieldlen']);
		}		

		} //raw record

    fclose($fdbf); 
	return $riga;
	}

	
echo '<pre>';
$riga=echo_dbf($sFile);
echo '</pre>';
echo '<br><br><br><br>Prova:<br>';
?>