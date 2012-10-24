<?php
//include ('./config.inc.php');
require_once('./phpmailer/class.phpmailer.php');
//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP(); // telling the class to use SMTP

try {
  $mail->Host       = "ssl://smtps.pec.aruba.it"; // SMTP server //ricordarsi di decommentare "extension=php_openssl.dll" nel file php.ini !!! per abilitare l'autenticazione SSL
  $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
  $mail->SMTPAuth   = true;                  // enable SMTP authentication
  $mail->Port       = 465;                    // set the SMTP port
  $mail->Username   = ""; // SMTP account username
  $mail->Password   = "";        // SMTP account password
  $mail->AddAddress('', ''); //destinatario
  $mail->SetFrom('', '');     //chi invia
  $mail->AddReplyTo('', '');  //a chi rispondere
  $mail->Subject = 'Invio mail di prova'; //oggetto
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
//  $mail->MsgHTML(file_get_contents('contents.html'));
  $mail->MsgHTML('<h1>Testo</h1> della mail in <b>html</b>');

//  $mail->AddAttachment('images/phpmailer.gif');      // allegato
//  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
//  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
  
//  $mail->Send();
  echo "Mail inviata: OK<p></p>\n";
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>
