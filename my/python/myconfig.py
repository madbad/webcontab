#! /usr/bin/env python
#-*- coding: utf-8 -*-


def config(mode):
    global HOST
    global USER
    global PASS
    global MAILBOX
    global CARTELLA_ACQUISTI
    global CARTELLA_RICEVUTE_VENDITE
    global MAIL_SDI
    
    if mode=="favo":
        print("favo mode")
        #config FAVORITA
        HOST = 'imaps.pec.aruba.it'         # Il server IMAP, p.es. imap.gmail.com
        USER = ''      # L'indirizzo, p.es. tuoNome@gmail.com
        PASS = ''                 # La password (qui leggibile da tutti !!)
        MAILBOX = 'INBOX'
        CARTELLA_ACQUISTI = 'C:/Programmi/EasyPHP-5.3.9/www/webcontab/my/php/dati/fattureElettronicheAcquisto/'
        CARTELLA_RICEVUTE_VENDITE = 'C:/Programmi/EasyPHP-5.3.9/www/webContab/my/php/core/stampe/RICEVUTE/'
        MAIL_SDI = 'sdi20@pec.fatturapa.it'
    if mode=="gimmi":
        print("gimmi mode")
        #config GIMMI
        HOST = 'imaps.pec.aruba.it'         # Il server IMAP, p.es. imap.gmail.com
        USER = ''      # L'indirizzo, p.es. tuoNome@gmail.com
        PASS = ''                 # La password (qui leggibile da tutti !!)
        MAILBOX = 'INBOX'
        CARTELLA_ACQUISTI = 'C:/Programmi/EasyPHP-5.3.9/www/webstore/dati/brungimmi/fattureElettronicheAcquisto/'
        CARTELLA_RICEVUTE_VENDITE = 'C:/Programmi/EasyPHP-5.3.9/www/webstore/dati/brungimmi/fattureVenditaXML/RICEVUTE/'
        MAIL_SDI = 'sdi28@pec.fatturapa.it'


""" fetch ricevute di consegna
"""
import sys, socket, imaplib, email, os, re, datetime, time


def importa_acquisti(mode):
    config(mode)
    #print(mode)
    #print(USER)
    #
    # connect host
    #
    try:
        imap = imaplib.IMAP4_SSL(HOST)
    except (socket.error, e):
        print ("socket.error: {}\n".format(e))
        sys.exit(1)

    #
    # login
    #
    try:
        result, data = imap.login(USER, PASS)
    except (imaplib.IMAP4.error, e):
        print ("imaplib.IMAP4.error: {}\n".format(e))
        sys.exit(1)
    if result != "OK":
        print ("login fault: {}\n".format(result))
        sys.exit(1)
    print (data)

    #
    # select mailbox folder
    #
    result, data = imap.select(MAILBOX)
    mailcount = int(data[0])
    print ("You have {} message(s) in {}\n".format(mailcount, MAILBOX))

    #
    # searching
    #
    try:
        #result, data = imap.uid('search', None, search)
        
        #$result = imap_search($connection, 'UNSEEN FROM "me@example.com"');
        #sdi20@pec.fatturapa.it
        
        #Ricevuta di consegna 4666095260
        result, data = imap.uid('search', None, 'UNSEEN HEADER Subject "Invio file " FROM "'+MAIL_SDI+'"')
        
        
        #Invio File 4666036467
        #result, data = imap.uid('search', None, 'UNSEEN HEADER Subject "Invio File "')

    except (imaplib.IMAP4.error, e):
        print (search);
        print (e)
        sys.exit(1)
    if result != "OK":
        print ("Search return something wrong. Sorry, exit with error\n")
        sys.exit(1)

    #
    # fetching
    #
    mail_list = data[0].split()
    mailcount = len(mail_list)
    print ( "Your search returned {} message(s)".format(mailcount) )

    readmails = [] # inizialize the list of mail to mark as `seen'


    #cicle trought the email that matched our search
    for uid in mail_list:
        print('Processin email with uid : {}'.format(uid))
        result, data = imap.uid('fetch', uid, '(BODY.PEEK[])')
        if result != "OK":
            continue
        #mail = email.message_from_string(data[0][1])
        mail = email.message_from_bytes(data[0][1])


        #locale.setlocale(locale.LC_TIME, 'it_IT.UTF-8')
        mailDate = mail['Date']
        #Sat, 6 Mar 2021 11:48:22 +0100
        mailDate = time.strptime(mailDate,'%a, %d %b %Y %H:%M:%S %z')
        month = str(mailDate.tm_mon)
        if len(month) < 2 :
            month = '0'+month

        year = str(mailDate.tm_year)

        #print('Email date:{} / {} '.format(month, year))

        attachmentFound = False

        for part in mail.walk():
            part_type = part.get_content_type()
            
            """
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print('part {}'.format(part_type))
            print(part)
            """
            """
            we can usually find 3 parts
            - application/pkcs-7signature => the signature of the email
            - text/plain                  => the text of the body of the email
            - application/octet-stream    => the atachments
            """
            """
            #retrieve the body of the email
            if part_type == 'text/plain':
                body = part.get_payload(decode=True)
                #if CONTENT in body:
                #print ("--------------------------------------")
                #print ( body )
                readmails.append(uid)
            """

            #print(part.get('Content-Disposition').find('attachment'))
            #print(part_type)
            #print(part.get('Content-Disposition'))
            if part_type == 'application/octet-stream':
                #print ("Success 1")
                if part.get('Content-Disposition') is not None:
                    #print ("Success 2")
                    #print (part.get('Content-Disposition').find('attachment'))
                    if part.get('Content-Disposition').find('attachment') > -1:
                        #print ("Success 3")
                        attachmentFound = True
                        fileName = part.get_filename()
                        #print ( "Found attachment with name: {}".format(fileName))
                        if bool(fileName):
                            #where we want to save the file
                            #filePath = os.path.join('C:/pec/', fileName)
                            
                            #if the folder does not exist create it
                            folderPath = CARTELLA_ACQUISTI+year+'/'+month+'/';
                            if not os.path.exists(folderPath) :
                                os.makedirs(folderPath, 0o777)
                            
                            filePath = os.path.join(folderPath, fileName)
                            #if this fine does not exist save it
                            if not os.path.isfile(filePath) :
                                print ("    Saving this file {}".format(fileName))
                                fp = open(filePath, 'wb')
                                fp.write(part.get_payload(decode=True))
                                fp.close()
                                
                                #salvo la data di ricezione
                                print ("    Salvo la data di ricezione".format(fileName))
                                dirPath2 = folderPath+'/.cache/dataricezione/';
                                filePath2 = os.path.join(dirPath2, fileName+'.txt')
                                if not os.path.exists(dirPath2) :
                                    os.makedirs(dirPath2, 0o777)
                                #now.strftime("%m/%d/%Y, %H:%M:%S")
                                #mailDateString = time.strftime('%Y-%m-%dT%H:%M:%SZ', mailDate)
                                mailDateString = time.strftime("%d/%m/%Y, %H:%M:%S %z", mailDate)
                                #mailDateString = time.strftime("%c", mailDate)
                                fp2 = open(filePath2, 'wt')
                                fp2.write(mailDateString)
                                fp2.close()
                                
                                #remember this email was successfull so that we can mark them as read afterward
                                readmails.append(uid)
                            else:
                                print ("    NOT saving this file because it already exist in the destination folder {}".format(fileName))
            #else:
            #    print ("    No attachment found for this part of the e-mail email")
        if not attachmentFound:
            print ("    Could not find an attachment for this e-mail")
                    #retrieve the subject of the email
                    #subject = str(mail).split("Subject: ", 1)[1].split("\nTo:", 1)[0]
                #print('Downloaded "{file}" from email titled "{subject}" with UID {uid}.'.format(file=fileName, subject=subject, uid=latest_email_uid.decode('utf-8')))

    # extra: just in case the xml file turn out to be required as receipt
    #        else:
    #            filename = part.get_filename()
    #            if filename == 'daticert.xml':
    #                print (part.get_payload(decode=True))

    # End for uid in mail_list

    #mark the given emails as read
    for uid in readmails:
        print ("Marking email with UID as read {}".format(uid))
        imap.uid('STORE', uid, '+FLAGS.SILENT', '(\seen)')

    imap.close()
    imap.logout()

  
def importa_vendite(mode):
    config(mode)
    #print(mode)
    #print(USER)
    #
    # connect host
    #
    try:
        imap = imaplib.IMAP4_SSL(HOST)
    except (socket.error, e):
        print ("socket.error: {}\n".format(e))
        sys.exit(1)

    #
    # login
    #
    try:
        result, data = imap.login(USER, PASS)
    except (imaplib.IMAP4.error, e):
        print ("imaplib.IMAP4.error: {}\n".format(e))
        sys.exit(1)
    if result != "OK":
        print ("login fault: {}\n".format(result))
        sys.exit(1)
    print (data)

    #
    # select mailbox folder
    #
    result, data = imap.select(MAILBOX)
    mailcount = int(data[0])
    print ("You have {} message(s) in {}\n".format(mailcount, MAILBOX))

    #
    # searching
    #
    try:
        #result, data = imap.uid('search', None, search)
        
        #$result = imap_search($connection, 'UNSEEN FROM "me@example.com"');
        #sdi20@pec.fatturapa.it
        
        #Ricevuta di consegna 4666095260
        result, data = imap.uid('search', None, 'UNSEEN HEADER Subject "Ricevuta di consegna " FROM "sdi20@pec.fatturapa.it"')
        
        
        #Invio File 4666036467
        #result, data = imap.uid('search', None, 'UNSEEN HEADER Subject "Invio File "')

    except (imaplib.IMAP4.error, e):
        print (search);
        print (e)
        sys.exit(1)
    if result != "OK":
        print ("Search return something wrong. Sorry, exit with error\n")
        sys.exit(1)

    #
    # fetching
    #
    mail_list = data[0].split()
    mailcount = len(mail_list)
    print ( "Your search returned {} message(s)".format(mailcount) )

    readmails = [] # inizialize the list of mail to mark as `seen'


    #cicle trought the email that matched our search
    for uid in mail_list:
        print('Processin email with uid : {}'.format(uid))
        result, data = imap.uid('fetch', uid, '(BODY.PEEK[])')
        if result != "OK":
            continue
        #mail = email.message_from_string(data[0][1])
        mail = email.message_from_bytes(data[0][1])

        attachmentFound = False

        for part in mail.walk():
            part_type = part.get_content_type()
            
            """
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print ("--------------------------------------")
            print('part {}'.format(part_type))
            print(part)
            """
            """
            we can usually find 3 parts
            - application/pkcs-7signature => the signature of the email
            - text/plain                  => the text of the body of the email
            - application/octet-stream    => the atachments
            """
            """
            #retrieve the body of the email
            if part_type == 'text/plain':
                body = part.get_payload(decode=True)
                #if CONTENT in body:
                #print ("--------------------------------------")
                #print ( body )
                readmails.append(uid)
            """

            #print(part.get('Content-Disposition').find('attachment'))
            #print(part_type)
            #print(part.get('Content-Disposition'))        
            if part_type == 'application/octet-stream':
                #print ("Success 1")
                if part.get('Content-Disposition') is not None:
                    #print ("Success 2")
                    #print (part.get('Content-Disposition').find('attachment'))
                    if part.get('Content-Disposition').find('attachment') > -1:
                        #print ("Success 3")
                        attachmentFound = True
                        fileName = part.get_filename()
                        #print ( "Found attachment with name: {}".format(fileName))
                        if bool(fileName):
                            #where we want to save the file
                            #filePath = os.path.join('C:/pec/', fileName)
                            
                            #if the folder does not exist create it
                            folderPath = CARTELLA_RICEVUTE_VENDITE
                            if not os.path.exist(folderPath):
                                os.makedirs(folderPath)
                            
                            filePath = os.path.join(folderPath, fileName)
                            #if this fine does not exist save it
                            if not os.path.isfile(filePath) :
                                print ("    Saving this file {}".format(fileName))
                                fp = open(filePath, 'wb')
                                fp.write(part.get_payload(decode=True))
                                fp.close()
                                
                                #remember this email was successfull so that we can mark them as read afterward
                                readmails.append(uid)
                            else:
                                print ("    NOT saving this file because it already exist in the destination folder {}".format(fileName))
            #else:
            #    print ("    No attachment found for this part of the e-mail email")
        if not attachmentFound:
            print ("    Could not find an attachment for this e-mail")
                    #retrieve the subject of the email
                    #subject = str(mail).split("Subject: ", 1)[1].split("\nTo:", 1)[0]
                #print('Downloaded "{file}" from email titled "{subject}" with UID {uid}.'.format(file=fileName, subject=subject, uid=latest_email_uid.decode('utf-8')))

    # extra: just in case the xml file turn out to be required as receipt
    #        else:
    #            filename = part.get_filename()
    #            if filename == 'daticert.xml':
    #                print (part.get_payload(decode=True))

    # End for uid in mail_list

    #mark the given emails as read
    for uid in readmails:
        print ("Marking email with UID as read {}".format(uid))
        imap.uid('STORE', uid, '+FLAGS.SILENT', '(\seen)')

    imap.close()
    imap.logout()
