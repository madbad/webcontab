@echo Avvio automatico dei servizi...

rem @echo Avvio VPN
rem start "" "C:\Programmi\OpenVPN\bin\openvpn-gui.exe" --connect server2.ovpn
rem ping 192.0.2.2 -n 1 -w 15000 > nul

@echo Comunico il mio indirizzo IP...
call winhttpjs.bat  http://www.madbad.altervista.org/lavoro/ping.php
@echo Comunico il mio indirizzo IP...FATTO
ping 127.0.0.1 -n 1 > nul

@echo Avvio il monitoraggio stampe...
START pythonw "C:\Programmi\EasyPHP-5.3.9\www\webcontab\my\python\monitor.py"
@echo Avvio il  monitoraggio stampe...FATTO
ping 127.0.0.1 -n 30 > nul

rem @echo Avvio il server php...
rem START "" "C:\Programmi\EasyPHP-5.3.9\EasyPHP-5.3.9.exe"
rem @echo Avvio il server php...FATTO
rem ping 127.0.0.1 -n 40 > nul

rem @echo Avvio Firefox...
rem START ""  "C:\Programmi\Mozilla Firefox\firefox.exe" -p lafavorita
rem @echo Avvio Firefox...FATTO
rem ping 127.0.0.1 -n 31 > nul

rem @echo Avvio Thunderbird...
rem START "" "F:\ThunderbirdPortable_pec\ThunderbirdPortable.exe"
rem @echo Avvio Thunderbird...FATTO
rem ping 127.0.0.1 -n 61 > nul

rem @echo Avvio Libre Office...
rem START "" "C:\Programmi\LibreOffice 5\program\scalc.exe"
rem @echo Avvio Libre Office...FATTO
rem ping 127.0.0.1 -n 31 > nul


@echo Fatto tutto! Esco
exit

