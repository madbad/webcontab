#main program
import os
import urllib.request
from datetime import datetime

#ui
import tkinter as tk
from tkinter import messagebox



#tasklist
#return a list of running processes
serverProcessName = 'EasyPHP-5.3.9.exe'
serverExeUrl ='C:\Programmi\EasyPHP-5.3.9\EasyPHP-5.3.9.exe'
urlServer = '192.168.1.110'

#chiedi ristampa
def ristampaDdt():
	ddtNumero =ddtNumeroText.get()
	ddtData =ddtDataText.get()
	url='http://192.168.1.110/webContab/my/php/core/gestioneDdt.php?numero='+ddtNumero+'&data='+ddtData+'&do=stampa'
	urllib.request.urlopen(url).read()
	#logText.insert(tk.INSERT, url)
	logText.insert(tk.INSERT, '\nRichiesta ristampa del ddt '+ddtNumero+' del '+ddtData)
	print(url)
#======================== main program loop ========================#
def pingServer():
	try:
		urllib.request.urlopen("http://192.168.1.110/webContab/my/php/stampaautomatica.php").read()
		print ("ping: RIUSCITO")
		return 1
	except: 
		print ("ping: FALLITO")
		return 0


def checkServerProcess():
	processi = os.popen('tasklist').read()
	if processi.find(serverProcessName) > 0:
		print ("processo server: ATTIVO")
		return 1
	else:
		print ("processo server: NON ATTIVO")
		return 0

def startServer():
	print('Starting the server...')
	process_id = os.spawnv(os.P_NOWAIT , serverExeUrl , ["-someFlag" , "someOtherFlag"])
	print(process_id)
	print('Done!')
	#re-check the sever status (and update the UI)
	#pianifico un ricontrollo del server tra 15 secondi
	window.after(10*1000, checkServerStatus)
	
	
def killServer():
	print('Killing the server...')
	os.popen('taskkill /F /IM '+serverProcessName)
	os.popen('taskkill /F /IM apache.exe')
	os.popen('taskkill /F /IM mysqld.exe')
	print('Done!')

def forcedCheckServerStatus():
	currentDateAndTime = datetime.now()
	currentTime = currentDateAndTime.strftime("%H:%M:%S")
	logText.insert(tk.INSERT, '\n'+currentTime + ' Controllo forzato dello stato del server!')
	checkServerStatus()
	
def checkServerStatus():
	# controllo lo stato del server
	#se riesco a pingalo non faccio niente (e' gia' a posto)
	if (pingServer() == 0):
		serverPingText.config(bg=color_red)
		if checkServerProcess()==0:
			#non riesco a pingare il server e non risulta attivo, lo faccio partire
			print('Server not started!')
			currentDateAndTime = datetime.now()
			currentTime = currentDateAndTime.strftime("%H:%M:%S")
			logText.insert(tk.INSERT, '\n'+currentTime + ' Il server non era attivo, lo avvio!')
			startServer()
		else:
			#non riesco a pingare il server ma risulta attivo, lo uccido e lo faccio ripartire
			print('Server active but not working!')
			currentDateAndTime = datetime.now()
			currentTime = currentDateAndTime.strftime("%H:%M:%S")
			logText.insert(tk.INSERT, '\n'+currentTime + ' Il server era bloccato, lo ri-avvio!')
			serverProcessText.config(bg=color_red)
			killServer()
			startServer()
	else:
		serverPingText.config(bg=color_green)
		serverProcessText.config(bg=color_green)

#======================== ui ========================#
def first_print():
    #confirm before printing
    answer = messagebox.askyesno(title='conferma ristampa', message='Vuoi veramente ristampare?')
    if(answer):
        print("ciao a tutti")

def disable_event():
    pass
    
def startUi():
	print("Starting the UI...")
	#button
	#myText = tk.Text(window)
	timeText.insert(tk.INSERT, "--:--:--")
	timeText.config(width=20, height=1,)
	timeText.pack(padx=3, pady=3)
	
	serverProcessText.insert(tk.INSERT, "Processo server")
	serverProcessText.config(width=20, height=1, bg=color_grey)
	serverProcessText.pack(padx=3, pady=3)
	
	serverPingText.insert(tk.INSERT, "Ping server")
	serverPingText.config(width=20, height=1, bg=color_grey)
	serverPingText.pack(padx=3, pady=3)
	
	button1 = tk.Button(text="Controlla il server ora!.",command=forcedCheckServerStatus)
	button1.config(width=20, borderwidth=3)
	button1.pack(padx=6, pady=6)
	
	#input
	#text_input = tk.Entry()
	labelNumeroDDT = tk.Label(window,text="Numero DDT:")
	labelNumeroDDT.pack(padx=6, pady=6)
	ddtNumeroText.pack(padx=3, pady=3)
	
	labelDataDDT = tk.Label(window,text="Data DDT:")
	labelDataDDT.pack(padx=6, pady=6)
	ddtDataText.pack(padx=3, pady=3)
	
	currentDateAndTime = datetime.now()
	currentDate = currentDateAndTime.strftime("%d-%m-%Y")
	ddtDataText.insert(0,currentDate)
	
	button2 = tk.Button(text="Ristampa questo ddt.",command=ristampaDdt)
	button2.config(width=20, borderwidth=3)
	button2.pack(padx=6, pady=6)
	
	#log
	logText.insert(tk.INSERT, "Logging...")
	logText.config(width=50, height=10)
	logText.pack(padx=3, pady=3)
	
	#non lascio che venga chiusa
	window.protocol("WM_DELETE_WINDOW", disable_event)
	
	# Start the event loop.
	#time.sleep(20)
	window.after(1000, monitoringLoop);
	window.after(3000, printingLoop)
	window.after(2000, uiLoop)
	window.mainloop()
	
def uiLoop():
	#print("Updating the UI...")
	updateTime()
	#aggiorno la UI ogni 1 secondo
	window.after(1000, uiLoop)
	
def monitoringLoop():
	print("Updating the server status...")
	checkServerStatus()
	#controllo ogni 10 minuti
	window.after(10*60*1000, monitoringLoop)

def printingLoop():
	print("Checking for somethig to print...")
	pingServer()
	#controllo ogni 10 secondi
	window.after(10*1000, printingLoop)
	
def updateTime():
	currentDateAndTime = datetime.now()
	currentTime = currentDateAndTime.strftime("%H:%M:%S")
	timeText.delete('1.0', tk.END)
	timeText.insert('1.0',currentTime)
	
#create the main window
color_green = '#5ee65e'
color_red = '#f05151'
color_grey = '#969696'

window = tk.Tk()
#minimize the window
window.wm_state('iconic')

timeText = tk.Text(window)
serverProcessText = tk.Text(window)
serverPingText = tk.Text(window)
ddtNumeroText = tk.Entry()
ddtDataText = tk.Entry()
logText = tk.Text(window)

window.title("Server di stampa")
startUi()
