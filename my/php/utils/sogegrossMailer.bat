@echo Mailer automatico sogegross...

@echo Copio i dati da contab...
CALL "C:\Programmi\EasyPHP-5.3.9\www\webcontab\my\php\utils\copy_all.bat"
@echo Copio i dati da contab...FATTO

@echo set /p DUMMY=Hit ENTER to continue...

@echo Mando la mail con PHP...
 START http://localhost/webContab/my/php/sogegrossMailer.php
@echo call winhttpjs.bat  http://localhost/webContab/my/php/sogegrossMailer.php -saveTo c:\mailsogegross.txt
@echo Mando la mail con PHP...FATTO

@echo Fatto tutto! Esco
set /p DUMMY=Hit PREMI INVIO PER USCIRE...