#!/usr/bin/env python3

import paho.mqtt.client as mqtt
import requests

# This is the Subscriber

def on_connect(client, userdata, flags, rc):
    print("Connected with result code "+str(rc)) 
    client.subscribe("light/#")
    client.subscribe("control")

### topic message
def on_message(mosq, obj, msg):
    print(msg.topic+" "+str(msg.qos)+" "+str(msg.payload))

def on_message_control(client, userdata, msg):
    if (msg.payload.decode() == 'QUIT'):
        print ('Exiting')
    client.disconnect()


### RearEntrance-BackDoor 
def on_message_RearEntrance_BackDoor(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("RearEntrance BackDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733040739&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("RearEntrance BackDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733040739&val=0')
        r.json()

def on_message_RearEntrance_BackDoor_Bright(client, userdata, msg):
    print ("RearEntrance BackDoor Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733040739&val=' + msg.payload.decode())
    r.json()


### Hallway-Hallway 
def on_message_Hallway_Hallway(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("Hallway Hallway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=360123189510580692&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("Hallway Hallway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=360123189510580692&val=0')
        r.json()

def on_message_Hallway_Hallway_Bright(client, userdata, msg):
    print ("Hallway Hallway Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=360123189510580692&val=' + msg.payload.decode())
    r.json()


### MasterBedroom-Ceiling1 
def on_message_MasterBedroom_Ceiling1(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("MasterBedroom Ceiling1" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733448185&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("MasterBedroom Ceiling1" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733448185&val=0')
        r.json()

def on_message_MasterBedroom_Ceiling1_Bright(client, userdata, msg):
    print ("MasterBedroom Ceiling1 Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733448185&val=' + msg.payload.decode())
    r.json()


### MasterBedroom-Ceiling2 
def on_message_MasterBedroom_Ceiling2(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("MasterBedroom Ceiling2" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733769850&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("MasterBedroom Ceiling2" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733769850&val=0')
        r.json()

def on_message_MasterBedroom_Ceiling2_Bright(client, userdata, msg):
    print ("MasterBedroom Ceiling2 Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733769850&val=' + msg.payload.decode())
    r.json()


### MasterBedroom-Lamp 
def on_message_MasterBedroom_Lamp(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("MasterBedroom Lamp" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733536747&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("MasterBedroom Lamp" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733536747&val=0')
        r.json()

def on_message_MasterBedroom_Lamp_Bright(client, userdata, msg):
    print ("MasterBedroom Lamp Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733536747&val=' + msg.payload.decode())
    r.json()


### Exterior-FrontDoor 
def on_message_Exterior_FrontDoor(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("Exterior FrontDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734112971&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("Exterior FrontDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734112971&val=0')
        r.json()

def on_message_Exterior_FrontDoor_Bright(client, userdata, msg):
    print ("Exterior FrontDoor Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570734112971&val=' + msg.payload.decode())
    r.json()


### Exterior-NWBackDoor 
def on_message_Exterior_NWBackDoor(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("Exterior NWBackDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734219143&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("Exterior NWBackDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734219143&val=0')
        r.json()

def on_message_Exterior_NWBackDoor_Bright(client, userdata, msg):
    print ("Exterior NWBackDoor Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570734219143&val=' + msg.payload.decode())
    r.json()


### Exterior-NEDriveway 
def on_message_Exterior_NEDriveway(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("Exterior NEDriveway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733998142&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("Exterior NEDriveway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733998142&val=0')
        r.json()

def on_message_Exterior_NEDriveway_Bright(client, userdata, msg):
    print ("Exterior NEDriveway Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733998142&val=' + msg.payload.decode())
    r.json()


### Exterior-SEDriveway 
def on_message_Exterior_SEDriveway(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("Exterior SEDriveway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733525707&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("Exterior SEDriveway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733525707&val=0')
        r.json()

def on_message_Exterior_SEDriveway_Bright(client, userdata, msg):
    print ("Exterior SEDriveway Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733525707&val=' + msg.payload.decode())
    r.json()


### LivingRoom-Fan 
def on_message_LivingRoom_Fan(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("LivingRoom Fan" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=359841714533870036&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("LivingRoom Fan" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=359841714533870036&val=0')
        r.json()

def on_message_LivingRoom_Fan_Bright(client, userdata, msg):
    print ("LivingRoom Fan Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=359841714533870036&val=' + msg.payload.decode())
    r.json()


### Bathroom-LED 
def on_message_Bathroom_LED(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("Bathroom LED" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733505864&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("Bathroom LED" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733505864&val=0')
        r.json()

def on_message_Bathroom_LED_Bright(client, userdata, msg):
    print ("Bathroom LED Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733505864&val=' + msg.payload.decode())
    r.json()


### FutureUse-LED 
def on_message_FutureUse_LED(client, userdata, msg):
    if (msg.payload.decode() == '1'):
        print ("FutureUse LED" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734215492&val=1')
        r.json()
    elif (msg.payload.decode() == '0'):
        print ("FutureUse LED" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734215492&val=0')
        r.json()

def on_message_FutureUse_LED_Bright(client, userdata, msg):
    print ("FutureUse LED Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570734215492&val=' + msg.payload.decode())
    r.json()



client = mqtt.Client('tcp-subscriber')               #create new instance
client.username_pw_set('admin', password='zhgcfn')    #set username and password

#Callbacks that trigger on a specific subscription match
client.message_callback_add('control', on_message_control)

### RearEntrance-BackDoor Begin
client.message_callback_add('light/RearEntrance/BackDoor/216773570733040739/switch', on_message_RearEntrance_BackDoor)
client.message_callback_add('light/RearEntrance/BackDoor/216773570733040739/brightness/set', on_message_RearEntrance_BackDoor_Bright)

### Hallway-Hallway Begin
client.message_callback_add('light/Hallway/Hallway/360123189510580692/switch', on_message_Hallway_Hallway)
client.message_callback_add('light/Hallway/Hallway/360123189510580692/brightness/set', on_message_Hallway_Hallway_Bright)

### MasterBedroom-Ceiling1 Begin
client.message_callback_add('light/MasterBedroom/Ceiling1/216773570733448185/switch', on_message_MasterBedroom_Ceiling1)
client.message_callback_add('light/MasterBedroom/Ceiling1/216773570733448185/brightness/set', on_message_MasterBedroom_Ceiling1_Bright)

### MasterBedroom-Ceiling2 Begin
client.message_callback_add('light/MasterBedroom/Ceiling2/216773570733769850/switch', on_message_MasterBedroom_Ceiling2)
client.message_callback_add('light/MasterBedroom/Ceiling2/216773570733769850/brightness/set', on_message_MasterBedroom_Ceiling2_Bright)

### MasterBedroom-Lamp Begin
client.message_callback_add('light/MasterBedroom/Lamp/216773570733536747/switch', on_message_MasterBedroom_Lamp)
client.message_callback_add('light/MasterBedroom/Lamp/216773570733536747/brightness/set', on_message_MasterBedroom_Lamp_Bright)

### Exterior-FrontDoor Begin
client.message_callback_add('light/Exterior/FrontDoor/216773570734112971/switch', on_message_Exterior_FrontDoor)
client.message_callback_add('light/Exterior/FrontDoor/216773570734112971/brightness/set', on_message_Exterior_FrontDoor_Bright)

### Exterior-NWBackDoor Begin
client.message_callback_add('light/Exterior/NWBackDoor/216773570734219143/switch', on_message_Exterior_NWBackDoor)
client.message_callback_add('light/Exterior/NWBackDoor/216773570734219143/brightness/set', on_message_Exterior_NWBackDoor_Bright)

### Exterior-NEDriveway Begin
client.message_callback_add('light/Exterior/NEDriveway/216773570733998142/switch', on_message_Exterior_NEDriveway)
client.message_callback_add('light/Exterior/NEDriveway/216773570733998142/brightness/set', on_message_Exterior_NEDriveway_Bright)

### Exterior-SEDriveway Begin
client.message_callback_add('light/Exterior/SEDriveway/216773570733525707/switch', on_message_Exterior_SEDriveway)
client.message_callback_add('light/Exterior/SEDriveway/216773570733525707/brightness/set', on_message_Exterior_SEDriveway_Bright)

### LivingRoom-Fan Begin
client.message_callback_add('light/LivingRoom/Fan/359841714533870036/switch', on_message_LivingRoom_Fan)
client.message_callback_add('light/LivingRoom/Fan/359841714533870036/brightness/set', on_message_LivingRoom_Fan_Bright)

### Bathroom-LED Begin
client.message_callback_add('light/Bathroom/LED/216773570733505864/switch', on_message_Bathroom_LED)
client.message_callback_add('light/Bathroom/LED/216773570733505864/brightness/set', on_message_Bathroom_LED_Bright)

### FutureUse-LED Begin
client.message_callback_add('light/FutureUse/LED/216773570734215492/switch', on_message_FutureUse_LED)
client.message_callback_add('light/FutureUse/LED/216773570734215492/brightness/set', on_message_FutureUse_LED_Bright)
client.connect('172.16.33.8', 1883,60)

client.on_connect = on_connect
client.on_message = on_message

client.loop_forever()
