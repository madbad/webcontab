REM C:\BAR-ONE 6 Lite\
REM C:\CONTAB\
REM C:\Documenti\Favorita\Fornitori\vari non abituali - riscontri.ods
REM C:\Programmi\EasyPHP-5.3.9\www
REM CHIAVETTA USB?


cd d:\BackupManuali
set anno=%date:~6,4%
set mese=%date:~3,2%
set giorno=%date:~0,2%
set dirname=%anno%-%mese%-%giorno%
echo %dirname%
mkdir D:\BackupManuali\%dirname%
mkdir D:\BackupManuali\%dirname%\CONTAB

copy /y C:\CONTAB\ D:\BackupManuali\%dirname%\CONTAB\
copy /y "C:\Documenti\Favorita\Fornitori\vari non abituali - riscontri.ods" "D:\BackupManuali\%dirname%\vari non abituali - riscontri.ods"