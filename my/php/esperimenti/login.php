<?php
//includo il file config
include('config.inc.php');
include('funzioni.php');
foreach($_REQUEST as $var_index => $var_value)
  {
    $$var_index=$var_value;       //passo le variabili HTTP_POST_VARS a VARIABILI NORMALI
    $i++;
 }

$check=TD_check_login($TD_admin[id],$TD_admin[pass]);
if ($check>0){
setcookie ("TD_ck_admin_id", $TD_admin['id'], time() + (60*60*24)*5, "/NewSite/");     //resto loggato per 5 giorni 5*24*60*60
setcookie ("TD_ck_admin_pass", $TD_admin['pass'], time() + (60*60*24)*5, "/NewSite/");

//effettuo il reindirizzamento per tornare alla pagina che mi ha lanciato
header("Location: ".$_SERVER['HTTP_REFERER']);
}
else{ 
$TD_html='Errata combinazione User/Password!'; 
$TD_html.='<br> <a href="'.$_SERVER['HTTP_REFERER'].'">Ritenta il login</a>';
echo $TD_html;
//header("Location: ".);
}
?>
