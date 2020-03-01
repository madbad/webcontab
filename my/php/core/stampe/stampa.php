<?php
//$handle = printer_open("HP Deskjet 930c");
// AcroRd32.exe /t "C:\\test.pdf" "\\\\servername\\printername"
$status='';
$output='';

//linux way
exec('lpr myfile.pdf',$output, $status);
//windows way
//exec('AcroRd32.exe /t "C:\\test.pdf" "\\\\servername\\printername',$output, $status);

//http://technet.microsoft.com/en-us/library/bb490970.aspx
//print /d:\\copyroom\printer1 c:\accounting\report.txt

//http://technet.microsoft.com/en-us/library/bb490928.aspx

//http://technet.microsoft.com/en-us/library/cc782930%28v=ws.10%29.aspx

//exec('date',$output, $status);
var_dump($output);
var_dump($status);

?>