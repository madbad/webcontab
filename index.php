<?php
phpinfo();

error_reporting(-1); //0=spento || -1=acceso
  $ch = curl_init('https://www.howsmyssl.com/a/check'); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt ($ch, CURLOPT_CAINFO, "C:\Programmi\EasyPHP-5.3.9\www\webcontab\my\php\utils\cacert.pem");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  if(curl_exec($ch) === false)
	{
		echo 'Curl error: ' . curl_error($ch);
	}
	else
	{
		echo 'Operation completed without any errors';
	}
  
  $data = curl_exec($ch); 
  curl_close($ch); 
  $json = json_decode($data); 
print_r($data);
  echo "<h1>Your TLS version is: " . $json->tls_version . "</h1>\n";

?>
