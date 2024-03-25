<?PHP
$data= date("Y").'-'.date("m").'-'.date("d");
$urlListino ="https://veronamercato-api.azurewebsites.net/api/v1/Listini?_page=1";
$urlFilediSalvataggio = "C:\Programmi\EasyPHP-5.3.9\www\webcontab\my\php\dati\listiniVR/".$data.".JS";


if (file_put_contents($urlFilediSalvataggio, file_get_contents($urlListino))) { 
	echo "File downloaded successfully"; 
}else{ 
	echo "File downloading failed."; 
} 

?>
