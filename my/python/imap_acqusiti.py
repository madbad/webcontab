#! /usr/bin/env python
#-*- coding: utf-8 -*-
""" fetch ricevute di consegna
"""
import sys, socket, imaplib, email, os, re, datetime, time
import myconfig

myconfig.importa_acquisti("favo")
myconfig.importa_acquisti("gimmi")

myconfig.imap_ricevute_vendite("favo")
myconfig.imap_ricevute_vendite("gimmi")
