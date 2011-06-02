<?php

/*********************************************
 *   Funzione per il passaggio POST->NORMAL  *
 *********************************************/
function TD_httpVarTOnormalVar($_REQUEST)
{
  foreach($_REQUEST as $var_index => $var_value)
  {
    if ($$var_index=''){$$var_index=$var_value;}       //passo le variabili HTTP_POST_VARS a VARIABILI NORMALI
  }
}
/*********************************************
 *   Creazione di variabili dopo la query    *
 *********************************************/
function TD_create_var($row,$queryresult,$type){
  $TD_campi_num =mysql_num_fields($queryresult);
  for ($temp=0; $temp < $TD_campi_num; $temp++)           //E PER OGNI CAMPO
  {
    $camponame=mysql_field_name($queryresult, $temp);   //ASSEGNO IL NOME DEL CAMPO NEL DB ALLA VARIABILE
    $varname=ereg_replace($type, "", $camponame);
    $varvalue=$row[$camponame];
    $TD_new_var[$varname]= $varvalue;                   //E INSERISCO NELLA VARIABILE COSI' CREATA IL RELATIVO VALORE
  }
  return $TD_new_var;
}

?>