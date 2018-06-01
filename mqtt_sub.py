#!/usr/bin/env python3

import paho.mqtt.client as mqtt
import requests

# This is the Subscriber

def on_connect(client, userdata, flags, rc)
	print('Connected with result code '+str(rc))
	client.subscribe('light/#')
	client.subscribe('control')

### topic message
def on_message(mosq, obj, msg):
	print(msg.topic+' '+str(msg.qos)+' '+str(msg.payload))