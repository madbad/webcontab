@echo Mailer automatico sogegross...

@echo Eseguo i task giornalieri...
call winhttpjs.bat  http://localhost/webContab/my/php/dailyTasks.php -saveTo c:\dailyTasks.txt

@echo Eseguo i bacup giornalieri...
call backup.bat
@echo Fatto tutto! Esco
rem set /p DUMMY=Hit PREMI INVIO PER USCIRE...
