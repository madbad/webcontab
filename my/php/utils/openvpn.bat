start "" "C:\Programmi\OpenVPN\bin\openvpn-gui.exe" --connect server2.ovpn
ping 192.0.2.2 -n 1 -w 15000 > nul
start "" "C:\Programmi\EasyPHP-5.3.9\EasyPHP-5.3.9.exe"
exit
