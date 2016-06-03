@echo Copia dei file dbf e dbt in corso...
REM la /d serve a copiare solo i file modificati
xcopy  f:\CONTAB\*.* C:\Programmi\EasyPHP-5.3.9\www\WebContab\my\php\dati\FILEDBF\CONTAB\ /y /d
@echo Finito!
@echo Aggiorno il db sqlite
REM C:\Programmi\"Mozilla Firefox"\firefox.exe -no-remote -p lafavorita http://localhost/webContab/my/php/aggiornadb.php
REM C:\Programmi\"Mozilla Firefox"\firefox.exe -remote openurl("http://localhost/webContab/my/php/aggiornadb.php")
REM C:\Programmi\"Mozilla Firefox"\firefox.exe openurl("http://localhost/webContab/my/php/aggiornadb.php")
call winhttpjs.bat  http://localhost/webContab/my/php/aggiornadb.php -saveTo c:\testaggiornadb.txt
@echo Fatto! Esco
