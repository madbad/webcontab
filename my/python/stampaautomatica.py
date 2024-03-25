# import the modules
import sys
import os
import time
from datetime import datetime
import urllib.request

#the file to check for changes
monitoredFile = 'C:/CONTAB/03BOTESD.DBF'
#monitoredFile = './03BOTESD.DBF'

#store the current modification date
lastCheckTime =  os.path.getmtime(monitoredFile)

while True:
	time.sleep(10)
	currCheckTime =  os.path.getmtime(monitoredFile)
	
	if (currCheckTime > lastCheckTime):
		#call php print to check if there are new ddt to print
		print(current_time, ' Checking if there are new ddt to print...')
		lastCheckTime = currCheckTime
		link = "http://192.168.1.110/webContab/my/php/stampaautomatica.php"
		f = urllib.request.urlopen(link)
		myfile = f.read()
		print(myfile)

	else:
		#nothing to do
		now = datetime.now()
		current_time = now.strftime("%H:%M:%S")
		print(current_time, ' Nothing to do')
