<?php
//includo il file config
include('config.inc.php');

setcookie ("TD_ck_admin_id", "", time() - 3600, "/NewSite/");
setcookie ("TD_ck_admin_pass", "", time() - 3600, "/NewSite/");

//effettuo il reindirizzamento per tornare alla pagina che mi ha lanciato
header("Location: ".$_SERVER['HTTP_REFERER']);
exit;
?>
