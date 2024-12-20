@echo off
SETLOCAL EnableDelayedExpansion

REM FIND ALL IP IN THE NETWORK
REM ping 192.168.0.255


SET MACINVERTER[1]=48-0b-b2-51-9d-a4
SET MACINVERTER[2]=48-0b-b2-51-b7-6d
SET MACINVERTER[3]=48-0b-b2-51-49-46
SET MACINVERTER[4]=48-0b-b2-51-9d-9d



SET MACPCSERVER[1]=00000000000000000
SET MACPCPOSTO2[2]=e0-cb-4e-e9-4e-2d
SET MACPCPOSTO3[3]=00000000000000000
SET MACPCFRIGOV[4]=000-24-21-b6-c9-c6

SET MACSTAMPANTE[1]=c4-65-16-db-36-aa
SET MACROOTER[2]=00-d0-d6-54-62-e6

ECHO Svuotola tabella ARP
netsh interface ip delete arpcache


ECHO Pingo tutti gli indirizzi IP della mia rete
rem devo farlo per popolare la tavola che mi restituisce il comando arp con tutti gli ip e mac dei device nella rete 
for /l %%x in (1, 1, 250) do (
	rem do the ping by only waiting 10 microseconds before considering it failed
	Ping -n 1 192.168.1.%%x -w 10> nul
	cls
	echo Cheching all the IP's on our network...%%x/254
)
ECHO Done!


rem AGGIORNO IL MIO FILE DI LOG CON GLI IP CHE HO TROVATO
arp -a >testiplog.txt

rem leggo il file appena salvato e cerco coincidenze con i mac dei miei device
for /F "tokens=*" %%A in (testiplog.txt) do call :ProcessLine %%A
goto End


rem @echo off
rem for /f "tokens=1 delims= " %%i in ('arp -a ^| find /i "48:0B:B2:51:9D:A4"') do set ip=%%i
rem echo IP inverter1: %ip%



rem CALL :PingTest 192.168.1.130,"PCFRIGOVENETA"
rem CALL :PingTest 192.168.1.6  ,"INVERTER-1   "
rem CALL :PingTest 192.168.1.2  ,"INVERTER-2   "
rem CALL :PingTest 192.168.1.11 ,"INVERTER-3   "
rem CALL :PingTest 192.168.1.5  ,"INVERTER-4   "
rem CALL :PingTest 192.168.1.180,"FAKEPC       "

SET/P username=Premi un tasto per continuare: 

REM Ping 192.168.10.1 | find "durata" > nul
REM set PINGRESULT=True
REM If errorlevel 1 set PINGRESULT=False

REM echo %PINGRESULT%
REM IF %PINGRESULT%==False (
REM     ECHO Modem is down
REM ) ELSE (
REM     ECHO Modem is up
REM )

:PingTest
Ping -n 1 %~1 | find "durata" > nul
set PINGRESULT=True
If errorlevel 1 set PINGRESULT=False

IF %PINGRESULT%==False (
    ECHO %~2 %~1 is down 
) ELSE (
    ECHO %~2 %~1 is up 
)
EXIT /B 0 


:ProcessLine
rem ECHO IP:  %1
rem ECHO MAC: %2 
rem ECHO --
if %MACINVERTER[1]%==%2 echo Found inverter_1: %1 & SET IPINVERTER[1]=%1
if %MACINVERTER[2]%==%2 echo Found inverter_2: %1 & SET IPINVERTER[2]=%1
if %MACINVERTER[3]%==%2 echo Found inverter_3: %1 & SET IPINVERTER[3]=%1
if %MACINVERTER[4]%==%2 echo Found inverter_4: %1 & SET IPINVERTER[4]=%1

if %MACPCSERVER[1]%==%2 echo Found POSTO_1:    %1 & SET IPPCSERVER[1]=%1
if %MACPCPOSTO2[2]%==%2 echo Found POSTO_2:    %1 & SET IPPCPOSTO2[2]=%1
if %MACPCPOSTO3[3]%==%2 echo Found POSTO_3:    %1 & SET IPPCPOSTO3[3]=%1
if %MACPCFRIGOV[4]%==%2 echo Found PCFRIGO:    %1 & SET IPPCFRIGOV[4]=%1

if %MACSTAMPANTE[1]%==%2 echo Found STAMPANTE:  %1 & SET IPSTAMPANTE[1]=%1
if %MACROOTER[2]%==%2   echo Found ROOTER:   %1 & SET IPROOTER[2]=%1
goto :eof

:End
ECHO ------------------------------
ECHO INVERTER_1 !IPINVERTER[1]!
ECHO INVERTER_2 !IPINVERTER[2]!
ECHO INVERTER_3 !IPINVERTER[3]!
ECHO INVERTER_4 !IPINVERTER[4]!
ECHO ------------------------------
ECHO PCSERVER_1 !IPPCSERVER[1]!
ECHO PCPOSTO2_2 !IPPCPOSTO2[2]!
ECHO PCPOSTO3_3 !IPPCPOSTO3[3]!
ECHO PCFRIGOV_4 !IPPCFRIGOV[4]!
ECHO ------------------------------
ECHO STAMPANTE  !IPSTAMPANTE[1]!
ECHO ROOTER     !IPROOTER[2]!

if "%IPINVERTER[1]%"==""  goto :missinginverter Inverter1
if "%IPINVERTER[2]%"==""  goto :missinginverter Inverter2
if "%IPINVERTER[3]%"==""  goto :missinginverter Inverter3
if "%IPINVERTER[4]%"==""  goto :missinginverter Inverter4
goto :skip
:missinginverter
ECHO ----------------------------------------------
echo ### Qualche inverter non raggiungibile: %1 ###
ECHO ----------------------------------------------
:skip