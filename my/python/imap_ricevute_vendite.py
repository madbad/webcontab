#! /usr/bin/env python
#-*- coding: utf-8 -*-
""" fetch ricevute di consegna
"""
import sys, socket, imaplib, email, os, re, datetime, time
import myconfig

myconfig.importa_vendite("favo")
myconfig.importa_vendite("gimmi")