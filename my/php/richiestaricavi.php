<?php
//include ('./core/config.inc.php');


function inviaMailRichiestaRicavi($cliente,$mailcliente, $elencoDdt){
	
		//importo i dati di configurazione della pec
		$gmail=$GLOBALS['config']->gmail;

		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

		$mail->IsSMTP(); // telling the class to use SMTP

		try {
			
			$mail->Host       = $gmail->Host;
			$mail->SMTPDebug  = $gmail->SMTPDebug;
			$mail->SMTPAuth   = $gmail->SMTPAuth;
			$mail->Port       = $gmail->Port;
			$mail->Username   = $gmail->Username;
			$mail->Password   = $gmail->Password;
			
			$mail->SMTPSecure = "tls";
			//$mail->SMTPSecure = "SSL";
			
			//qui dovrei avere un elenco di indirizzi email separati da una virgola","
			//invio la mail ad ogni indirizzo
			$mail->AddAddress($mailcliente, $cliente); //destinatario
			
			//ne invio una copia anche a me per conoscenza
			$mail->AddAddress('lafavorita_srl@libero.it', 'La Favorita Srl'); //mia copia per conoscenza

			//mi faccio mandare la ricevuta di lettura
			$mail->ConfirmReadingTo=$gmail->ReplyTo->Mail;
			$mail->SetFrom($gmail->From->Mail, $gmail->From->Name);
			$mail->AddReplyTo($gmail->ReplyTo->Mail, $gmail->ReplyTo->Name);

			$mail->Subject = 'Richiesta ricavi - '.$cliente; //oggetto


			$message="[Messaggio automatizzato]";
			$message="[si prega di utilizzare l'indirizzo lafavorita_srl@libero.it per le risposte ]";

			$message="Buongiorno,";
			$message.="<br><br> abbiamo necessita di ricevere i ricavi relativi ai seguenti ddt per chiudere la fatturazzione del mese precedente.";
			$message.="<br><br>";			
			
			$message.=$elencoDdt;
			/*
			$message="- n. 2337 del 26/08/2020";
			$message="- n. 2341 del 27/08/2020";
			$message="- n. 2363 del 29/08/2020";
			*/

			$message.="<br><br> Grazie in anticipo";
			$message.="<br> ";
			$message.="<br> ";
			$message.="<br> ------------------------";																									
			$message.="<br> La Favorita di Brun G. e G. Srl Unip.";
			$message.="<br> Via Camagre 38/B";
			$message.="<br> 37063 Isola della Scala (VR)";
			$message.="<br> Cod.Fiscale e P.IVA: 01588530236";
			$message.="<br> PEC: lafavorita_srl@pec.it";
			$message.="<br> Mail: lafavorita_srl@libero.it";
			$message.="<br> Tel: 045-6630397";
			$message.="<br> Fax: 045-7302598";
			$message.="<br> Codice SDI: 0000000";

			$mail->MsgHTML($message);
			//$mail->Body($message); 


			if($mail->Send()){
			//	$html= '<h2 style="color:green">Messaggio Inviato</h2>';
			//	$html.= '<br>Il messaggio con oggetto: ';
			//	$html.= '<b>'.$mail->Subject.'</b>';
			//	$html.='<br>E\' stato inviato a: <b>'.$cliente->ragionesociale->getVal().'</b>';
			//	$html.='<br>all\'indirizzo: <b>'.$cliente->__pec->getVal().'</b>';
			//	$html.='<br>con allegato il file: <b>'.$this->getPdfFileUrl().'</b>';
				
				//memorizzo la data di invio
		//				$this->__datainviopec->setVal(date("d/m/Y"));
		//				$this->saveSqlDbData();
				//mostro il messaggio di avvenuto invio
			//	echo $html;
			//	var_dump($message);
				//all seems ok
				return true;
			}
		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
		}
}
?>
