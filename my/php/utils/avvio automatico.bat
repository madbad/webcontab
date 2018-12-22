@echo Avvio automatico dei servizi...

@echo Comunico il mio indirizzo IP...
call winhttpjs.bat  http://www.madbad.altervista.org/lavoro/ping.php
@echo Comunico il mio indirizzo IP...FATTO
ping 127.0.0.1 -n 1 > nul

@echo Avvio il server php...
START "" "C:\Programmi\EasyPHP-5.3.9\EasyPHP-5.3.9.exe"
@echo Avvio il server php...FATTO
ping 127.0.0.1 -n 91 > nul


@echo Avvio Firefox...
START ""  "C:\Programmi\Mozilla Firefox\firefox.exe" -p lafavorita
@echo Avvio Firefox...FATTO
ping 127.0.0.1 -n 31 > nul

@echo Avvio Thunderbird...
START "" "F:\ThunderbirdPortable_pec\ThunderbirdPortable.exe"
@echo Avvio Thunderbird...FATTO
ping 127.0.0.1 -n 61 > nul

@echo Avvio Libre Office...
START "" "C:\Programmi\LibreOffice 5\program\scalc.exe"
@echo Avvio Libre Office...FATTO
ping 127.0.0.1 -n 31 > nul


@echo Fatto tutto! Esco
