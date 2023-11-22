#prerequisito
#pip install getmac

#pip install --upgrade certifi


#from ping3 import ping, verbose_ping
#ping('google.com')  # Returns delay in seconds.

import platform    # For getting the operating system name
import subprocess  # For executing a shell command
import os
import re
import json
import time
from datetime import datetime
from logcelle import checkCelle

runCount = 0

class Device:
  def __init__(self, name, mac):
    self.name = name
    self.mac = mac
    self.ip = ''
    self.isOnline = False

# creating list        
list = {}

# appending instances to list  
list['Inverter1'] = Device('Inverter1', '48-0b-b2-51-9d-a4');
list['Inverter2'] = Device('Inverter2', '48-0b-b2-51-b7-6d');
list['Inverter3'] = Device('Inverter3', '48-0b-b2-51-49-46');
list['Inverter4'] = Device('Inverter4', '48-0b-b2-51-9d-9d');

#list['PcServer'] = Device('PcServer', 'e0-cb-4e-eb-14-92');
#list['PcPosto2'] = Device('PcPosto2', 'e0-cb-4e-e9-4e-2d');
#list['PcPosto3'] = Device('PcPosto3', '00000000000000000');
#list['PcFrigov'] = Device('PcFrigov', '00-24-21-b6-c9-c6'); # this is me

#list['Stampante'] = Device('Stampante', 'c4-65-16-db-36-aa');
#list['Rooter'] = Device('Rooter', '00-d0-d6-54-62-e6');


def emptyArpTable():
	command = ['netsh','interface','ip','delete','arpcache']
	result = subprocess.getoutput(command)

def readArpTable():
	command = ['arp','-a']
	result = subprocess.getoutput(command)
	#print(result)
	return result
	
def parseArpTable():
	print ("Trying to find the MACs of our devices in the arp table...")
	arpTable = readArpTable();
	#print (arpTable)
	
	for device in list:
		#mac = 'e0-cb-4e-eb-14-92'
		mac = list[device].mac
		match = re.search(r".*"+mac, arpTable);
		if match :
			ip = match.group().replace(mac,'').strip()
			list[device].ip = ip
			list[device].isOnline = True
			#print (match.group())
			print (device.ljust(15,' '), ip.ljust(15,' '), mac.ljust(15,' '))
		else:
			print (device.ljust(15,' '), 'mac/ip not found')

def ping(host):
	"""
	Returns True if host (str) responds to a ping request.
	Remember that a host may not respond to a ping (ICMP) request even if the host name is valid.
	"""

	# Option for the number of packets as a function of
	param = '-n' if platform.system().lower()=='windows' else '-c'

	# Building the command. Ex: "ping -c 1 google.com"
	#command = ['ping', param, '1', host]
	command = ['Ping', '-n', '1', host, '-w', '10']
	#print (' '.join(command))
	
	result = subprocess.getoutput(command)
	#print (' '.join(result))
	
	foundPingReply = result.find('TTL')

	if foundPingReply > 0:
		#print ('True ')
		return True
	#print ('False ')
	return False
	
def testPreviouslyKnowIPs():
	print ("Testing our previously know IPs...")
	result = True
	try:
		file = open('./status.json')
	except:
		print("Failed to open the file")
		return False
	with file as json_file:
		try:
			data = json.load(json_file)
		except:
			print("There is no json data to read")  
			return False
		
		for device in list:
			#print(device)
			#this device is not present on our past log, set as if it was not found and exit the loop
			if device not in data['devicesData']:
				pingResult = False
				result = pingResult
				break;
			ip = data['devicesData'][device]['ip'];
			if ip != "" :
				pingResult = ping(ip)
			else:
				pingResult = False
				result = pingResult
				break;
			print (device.ljust(15,' '), ip.ljust(15,' '), pingResult)
	#some of the ip failed
	return result

def writeLog():
	print ('Final result that will be uploaded...')
	for obj in list:
		#pingMacAdress(obj.mac);
		print (list[obj].name.ljust(15,' '), list[obj].ip.ljust(15,' '),str(list[obj].mac).ljust(15,' '), str(list[obj].isOnline).ljust(15,' '));
		
	# datetime object containing current date and time
	now = datetime.now()
	# dd/mm/YY H:M:S
	dateStr = now.strftime("%d/%m/%Y %H:%M:%S")
	
	outData ={
		  "time": time.time(),
		  "timeStr": dateStr,
		  "overallStatus": "ok",
		  "devicesData": list
	}
	#jsonStr = json.dumps(list.__dict__)

	
	outData["celle"] = checkCelle();
	
	def obj_dict(obj):
		return obj.__dict__

	jsonStr = json.dumps(outData, default=obj_dict, sort_keys=True)

	#print (jsonStr)

	#EMPTY THE FILE
	file = open("status.json", 'w').close()
	#REOPEN IT IN APPEND MODE
	file = open("status.json", "a")
	file.write(jsonStr)
	file.close()

"""
def checkDevices():
	#
	#file = open('z:/documenti/testiplog.txt', mode = 'r', encoding = 'utf-8-sig')
	file = open('./testiplog.txt', mode = 'r', encoding = 'utf-8-sig')
	lines = file.readlines()
	file.close()
	linecount=0

	for line in lines:
		linecount= linecount+1
		if linecount > 2:
			ip=line[2:15]
			mac=line[24:41]
			for obj in list: 
				if list[obj].mac== mac:
					list[obj].isOnline = True
					list[obj].ip = ip.strip()
					

	print ("Python code here:");

	#EMPTY THE FILE
	file = open("status.json", 'w').close()
	#REOPEN IT IN APPEND MODE
	file = open("status.json", "a")

	for obj in list:
		#pingMacAdress(obj.mac);
		print (list[obj].name, ' ', list[obj].isOnline, ' ', list[obj].ip);
		
	outData ={
		  "time": time.time(),
		  "overallStatus": "ok",
		  "devicesData": list
	}
	#jsonStr = json.dumps(list.__dict__)

	
	outData["celle"] = checkCelle();
	
	def obj_dict(obj):
		return obj.__dict__

	jsonStr = json.dumps(outData, default=obj_dict)


	#jsonStr = json.dumps(list)
	file.write(jsonStr)
	file.close()
"""

def sendMail():
	#send myself an email if something seems to be wrong
	import smtplib, ssl
	from socket import gaierror

	# now you can play with your code. Let’s define the SMTP server separately here:
	port = 587
	smtp_server = "smtp.gmail.com"
	login = "favoritasrl@gmail.com" # paste your login generated by Mailtrap
	password = "dagliitaliani" # paste your password generated by Mailtrap

	# specify the sender’s and receiver’s email addresses
	sender = "favoritasrl@gmail.com"
	receiver = "madbad82@gmail.com"

	# type your message: use two newlines (\n) to separate the subject from the message body, and use 'f' to  automatically insert variables in the text
	message = """\
	Subject: Allarme fotovoltaico
	To: {receiver}
	From: {sender}

	Si e verificato un problema col fotovoltaico, verifcare prego."""

	#disabilito la verifica SSL in quanto mi da problemi con google (meno sicuro)
	#context=ssl.create_default_context()
	context=ssl._create_unverified_context()

	try:
		#send your message with credentials specified above
		with smtplib.SMTP(smtp_server, port) as server:
			server.starttls(context=context)
			server.login(login, password)
			server.sendmail(sender, receiver, message)

		# tell the script to report if your message was sent or which errors need to be fixed 
		print('Inviata e-mail di allerta!')
	except (gaierror, ConnectionRefusedError):
		print('Failed to connect to the server. Bad connection settings?')
	except smtplib.SMTPServerDisconnected:
		print('Failed to connect to the server. Wrong user/password?')
	except smtplib.SMTPException as e:
		print('SMTP error occurred: ' + str(e))


#def pingMacAdress(mac):
#	from scapy.all import srp, Ether, ARP 
#	#ans,unans=srp(Ether(dst="ff:ff:ff:ff:ff:ff")/ARP(pdst="192.168.1.0/24"),timeout=2)
#	ans,unans=srp(Ether(dst=mac)/ARP(pdst=mac),timeout=2)

#	ip = pkt[ARP].psrc
#	print ("Mac",mac," was found at ip ",ip);

def updateArpTable():
	#import subprocess
	#subprocess.call([r'Z:\Documenti\testip.bat'])
	for number in range(250):
		ip = '192.168.1.'+str(number)
		print(ip, ping(ip))

def uploadStatusFile():
	import ftplib
	session = ftplib.FTP('ftp.madbad.altervista.org','madbad','faggod50')
	file = open('status.json','rb')                  # file to send
	session.storbinary('STOR status.json', file)     # send the file
	file.close()                                    # close file and FTP
	session.quit()


while True:
	runCount = runCount+1
	print ('This script has run for these times: ',str(runCount))
	#readArpTable()
	'''
	emptyArpTable()
	for number in range(250):
		ip = '192.168.1.'+str(number)
		ping (ip)
		print(ip)
	readArpTable()
	'''
	#print (list)
	#break;

	#check if all the IP we remember are still reachable
	if testPreviouslyKnowIPs():
		#we found all dont need to do nothing to update our arpTable
		print ("The previous saved IPs are still valid go on!")
	else:
		#some were not found we will need to scan the whole network to re-fill our arp table
		print ("One or more of the previous saved IPs are no more valid we will need to rescan the whole network!")
		updateArpTable();

	parseArpTable()
	writeLog()
	print('Uploading...');
	uploadStatusFile();
	print('Uploaded!');
	
	waitMinutes = 5
	
	while waitMinutes > 1:
		print ('Waiting for another ', waitMinutes, ' minutes')
		waitMinutes = waitMinutes - 1
		#sleep for 1 minutes
		time.sleep(60)

