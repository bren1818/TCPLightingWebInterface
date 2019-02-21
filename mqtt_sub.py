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


### RearEntrance 
def on_message_RearEntrance(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("RearEntrance" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=room&uid=0&val=' + msg.payload.decode())
        r.json()
        client.publish("light/RearEntrance/0/status", msg.payload.decode(), 0, True)

def on_message_RearEntrance_Bright(client, userdata, msg):
    print ("RearEntrance Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=room&uid=0&val=' + msg.payload.decode())
    r.json()

    client.publish("light/RearEntrance/0/brightness", msg.payload.decode(), 0, True)

### RearEntrance-BackDoor 
def on_message_RearEntrance_BackDoor(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("RearEntrance BackDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733040739&val=' + msg.payload.decode())
        r.json()
        client.publish("light/RearEntrance/BackDoor/216773570733040739/status", msg.payload.decode(), 0, True)

def on_message_RearEntrance_BackDoor_Bright(client, userdata, msg):
    print ("RearEntrance BackDoor Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733040739&val=' + msg.payload.decode())
    r.json()

    client.publish("light/RearEntrance/BackDoor/216773570733040739/brightness", msg.payload.decode(), 0, True)

### Hallway 
def on_message_Hallway(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Hallway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=room&uid=1&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Hallway/1/status", msg.payload.decode(), 0, True)

def on_message_Hallway_Bright(client, userdata, msg):
    print ("Hallway Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=room&uid=1&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Hallway/1/brightness", msg.payload.decode(), 0, True)

### Hallway-Hall2 
def on_message_Hallway_Hall2(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Hallway Hall2" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570738064271&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Hallway/Hall2/216773570738064271/status", msg.payload.decode(), 0, True)

def on_message_Hallway_Hall2_Bright(client, userdata, msg):
    print ("Hallway Hall2 Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570738064271&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Hallway/Hall2/216773570738064271/brightness", msg.payload.decode(), 0, True)

### Hallway-Hall1 
def on_message_Hallway_Hall1(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Hallway Hall1" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570738075960&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Hallway/Hall1/216773570738075960/status", msg.payload.decode(), 0, True)

def on_message_Hallway_Hall1_Bright(client, userdata, msg):
    print ("Hallway Hall1 Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570738075960&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Hallway/Hall1/216773570738075960/brightness", msg.payload.decode(), 0, True)

### MasterBedroom 
def on_message_MasterBedroom(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("MasterBedroom" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=room&uid=2&val=' + msg.payload.decode())
        r.json()
        client.publish("light/MasterBedroom/2/status", msg.payload.decode(), 0, True)

def on_message_MasterBedroom_Bright(client, userdata, msg):
    print ("MasterBedroom Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=room&uid=2&val=' + msg.payload.decode())
    r.json()

    client.publish("light/MasterBedroom/2/brightness", msg.payload.decode(), 0, True)

### MasterBedroom-Ceiling1 
def on_message_MasterBedroom_Ceiling1(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("MasterBedroom Ceiling1" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733448185&val=' + msg.payload.decode())
        r.json()
        client.publish("light/MasterBedroom/Ceiling1/216773570733448185/status", msg.payload.decode(), 0, True)

def on_message_MasterBedroom_Ceiling1_Bright(client, userdata, msg):
    print ("MasterBedroom Ceiling1 Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733448185&val=' + msg.payload.decode())
    r.json()

    client.publish("light/MasterBedroom/Ceiling1/216773570733448185/brightness", msg.payload.decode(), 0, True)

### MasterBedroom-Lamp 
def on_message_MasterBedroom_Lamp(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("MasterBedroom Lamp" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733536747&val=' + msg.payload.decode())
        r.json()
        client.publish("light/MasterBedroom/Lamp/216773570733536747/status", msg.payload.decode(), 0, True)

def on_message_MasterBedroom_Lamp_Bright(client, userdata, msg):
    print ("MasterBedroom Lamp Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733536747&val=' + msg.payload.decode())
    r.json()

    client.publish("light/MasterBedroom/Lamp/216773570733536747/brightness", msg.payload.decode(), 0, True)

### MasterBedroom-Ceiling2 
def on_message_MasterBedroom_Ceiling2(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("MasterBedroom Ceiling2" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733769850&val=' + msg.payload.decode())
        r.json()
        client.publish("light/MasterBedroom/Ceiling2/216773570733769850/status", msg.payload.decode(), 0, True)

def on_message_MasterBedroom_Ceiling2_Bright(client, userdata, msg):
    print ("MasterBedroom Ceiling2 Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733769850&val=' + msg.payload.decode())
    r.json()

    client.publish("light/MasterBedroom/Ceiling2/216773570733769850/brightness", msg.payload.decode(), 0, True)

### Exterior 
def on_message_Exterior(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Exterior" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=room&uid=3&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Exterior/3/status", msg.payload.decode(), 0, True)

def on_message_Exterior_Bright(client, userdata, msg):
    print ("Exterior Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=room&uid=3&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Exterior/3/brightness", msg.payload.decode(), 0, True)

### Exterior-SEDriveway 
def on_message_Exterior_SEDriveway(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Exterior SEDriveway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733525707&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Exterior/SEDriveway/216773570733525707/status", msg.payload.decode(), 0, True)

def on_message_Exterior_SEDriveway_Bright(client, userdata, msg):
    print ("Exterior SEDriveway Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733525707&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Exterior/SEDriveway/216773570733525707/brightness", msg.payload.decode(), 0, True)

### Exterior-NEDriveway 
def on_message_Exterior_NEDriveway(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Exterior NEDriveway" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733998142&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Exterior/NEDriveway/216773570733998142/status", msg.payload.decode(), 0, True)

def on_message_Exterior_NEDriveway_Bright(client, userdata, msg):
    print ("Exterior NEDriveway Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733998142&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Exterior/NEDriveway/216773570733998142/brightness", msg.payload.decode(), 0, True)

### Exterior-FrontDoor 
def on_message_Exterior_FrontDoor(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Exterior FrontDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734112971&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Exterior/FrontDoor/216773570734112971/status", msg.payload.decode(), 0, True)

def on_message_Exterior_FrontDoor_Bright(client, userdata, msg):
    print ("Exterior FrontDoor Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570734112971&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Exterior/FrontDoor/216773570734112971/brightness", msg.payload.decode(), 0, True)

### Exterior-NWBackDoor 
def on_message_Exterior_NWBackDoor(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Exterior NWBackDoor" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734219143&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Exterior/NWBackDoor/216773570734219143/status", msg.payload.decode(), 0, True)

def on_message_Exterior_NWBackDoor_Bright(client, userdata, msg):
    print ("Exterior NWBackDoor Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570734219143&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Exterior/NWBackDoor/216773570734219143/brightness", msg.payload.decode(), 0, True)

### LivingRoom 
def on_message_LivingRoom(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("LivingRoom" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=room&uid=4&val=' + msg.payload.decode())
        r.json()
        client.publish("light/LivingRoom/4/status", msg.payload.decode(), 0, True)

def on_message_LivingRoom_Bright(client, userdata, msg):
    print ("LivingRoom Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=room&uid=4&val=' + msg.payload.decode())
    r.json()

    client.publish("light/LivingRoom/4/brightness", msg.payload.decode(), 0, True)

### LivingRoom-fanwest 
def on_message_LivingRoom_fanwest(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("LivingRoom fanwest" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733998247&val=' + msg.payload.decode())
        r.json()
        client.publish("light/LivingRoom/fanwest/216773570733998247/status", msg.payload.decode(), 0, True)

def on_message_LivingRoom_fanwest_Bright(client, userdata, msg):
    print ("LivingRoom fanwest Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733998247&val=' + msg.payload.decode())
    r.json()

    client.publish("light/LivingRoom/fanwest/216773570733998247/brightness", msg.payload.decode(), 0, True)

### LivingRoom-faneast 
def on_message_LivingRoom_faneast(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("LivingRoom faneast" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733869505&val=' + msg.payload.decode())
        r.json()
        client.publish("light/LivingRoom/faneast/216773570733869505/status", msg.payload.decode(), 0, True)

def on_message_LivingRoom_faneast_Bright(client, userdata, msg):
    print ("LivingRoom faneast Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733869505&val=' + msg.payload.decode())
    r.json()

    client.publish("light/LivingRoom/faneast/216773570733869505/brightness", msg.payload.decode(), 0, True)

### LivingRoom-fannorth 
def on_message_LivingRoom_fannorth(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("LivingRoom fannorth" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733998518&val=' + msg.payload.decode())
        r.json()
        client.publish("light/LivingRoom/fannorth/216773570733998518/status", msg.payload.decode(), 0, True)

def on_message_LivingRoom_fannorth_Bright(client, userdata, msg):
    print ("LivingRoom fannorth Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733998518&val=' + msg.payload.decode())
    r.json()

    client.publish("light/LivingRoom/fannorth/216773570733998518/brightness", msg.payload.decode(), 0, True)

### Bathroom 
def on_message_Bathroom(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Bathroom" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=room&uid=5&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Bathroom/5/status", msg.payload.decode(), 0, True)

def on_message_Bathroom_Bright(client, userdata, msg):
    print ("Bathroom Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=room&uid=5&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Bathroom/5/brightness", msg.payload.decode(), 0, True)

### Bathroom-LED 
def on_message_Bathroom_LED(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Bathroom LED" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570733505864&val=' + msg.payload.decode())
        r.json()
        client.publish("light/Bathroom/LED/216773570733505864/status", msg.payload.decode(), 0, True)

def on_message_Bathroom_LED_Bright(client, userdata, msg):
    print ("Bathroom LED Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570733505864&val=' + msg.payload.decode())
    r.json()

    client.publish("light/Bathroom/LED/216773570733505864/brightness", msg.payload.decode(), 0, True)

### FutureUse 
def on_message_FutureUse(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("FutureUse" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=room&uid=6&val=' + msg.payload.decode())
        r.json()
        client.publish("light/FutureUse/6/status", msg.payload.decode(), 0, True)

def on_message_FutureUse_Bright(client, userdata, msg):
    print ("FutureUse Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=room&uid=6&val=' + msg.payload.decode())
    r.json()

    client.publish("light/FutureUse/6/brightness", msg.payload.decode(), 0, True)

### FutureUse-LED 
def on_message_FutureUse_LED(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("FutureUse LED" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=toggle&type=device&uid=216773570734215492&val=' + msg.payload.decode())
        r.json()
        client.publish("light/FutureUse/LED/216773570734215492/status", msg.payload.decode(), 0, True)

def on_message_FutureUse_LED_Bright(client, userdata, msg):
    print ("FutureUse LED Brightness " + msg.payload.decode())
    r = requests.get('https://lighting.taylortrash.com/api.php?fx=dim&type=device&uid=216773570734215492&val=' + msg.payload.decode())
    r.json()

    client.publish("light/FutureUse/LED/216773570734215492/brightness", msg.payload.decode(), 0, True)

### AllFull 
def on_message_AllFull(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("AllFull" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=scene&uid=1&type=' + msg.payload.decode())
        r.json()
        client.publish("light/AllFull/1/status", msg.payload.decode(), 0, True)

### WeekdayAM 
def on_message_WeekdayAM(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("WeekdayAM" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=scene&uid=2147483642&type=' + msg.payload.decode())
        r.json()
        client.publish("light/WeekdayAM/2147483642/status", msg.payload.decode(), 0, True)

### Afternoon 
def on_message_Afternoon(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Afternoon" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=scene&uid=2147483646&type=' + msg.payload.decode())
        r.json()
        client.publish("light/Afternoon/2147483646/status", msg.payload.decode(), 0, True)

### Exterior 
def on_message_Exterior(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("Exterior" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=scene&uid=2147483645&type=' + msg.payload.decode())
        r.json()
        client.publish("light/Exterior/2147483645/status", msg.payload.decode(), 0, True)

### TruckHome 
def on_message_TruckHome(client, userdata, msg):
    if (msg.payload.decode() == '0' or msg.payload.decode() == '1'):
        print ("TruckHome" + msg.payload.decode())
        r = requests.get('https://lighting.taylortrash.com/api.php?fx=scene&uid=2147483640&type=' + msg.payload.decode())
        r.json()
        client.publish("light/TruckHome/2147483640/status", msg.payload.decode(), 0, True)


client = mqtt.Client('tcp-subscriber')               #create new instance
client.username_pw_set('admin', password='zhgcfn')    #set username and password

#Callbacks that trigger on a specific subscription match
client.message_callback_add('control', on_message_control)

### RearEntrance-BackDoor Begin
client.message_callback_add('light/RearEntrance/BackDoor/216773570733040739/switch', on_message_RearEntrance_BackDoor)
client.message_callback_add('light/RearEntrance/BackDoor/216773570733040739/brightness/set', on_message_RearEntrance_BackDoor_Bright)

### Hallway-Hall2 Begin
client.message_callback_add('light/Hallway/Hall2/216773570738064271/switch', on_message_Hallway_Hall2)
client.message_callback_add('light/Hallway/Hall2/216773570738064271/brightness/set', on_message_Hallway_Hall2_Bright)

### Hallway-Hall1 Begin
client.message_callback_add('light/Hallway/Hall1/216773570738075960/switch', on_message_Hallway_Hall1)
client.message_callback_add('light/Hallway/Hall1/216773570738075960/brightness/set', on_message_Hallway_Hall1_Bright)

### MasterBedroom-Ceiling1 Begin
client.message_callback_add('light/MasterBedroom/Ceiling1/216773570733448185/switch', on_message_MasterBedroom_Ceiling1)
client.message_callback_add('light/MasterBedroom/Ceiling1/216773570733448185/brightness/set', on_message_MasterBedroom_Ceiling1_Bright)

### MasterBedroom-Lamp Begin
client.message_callback_add('light/MasterBedroom/Lamp/216773570733536747/switch', on_message_MasterBedroom_Lamp)
client.message_callback_add('light/MasterBedroom/Lamp/216773570733536747/brightness/set', on_message_MasterBedroom_Lamp_Bright)

### MasterBedroom-Ceiling2 Begin
client.message_callback_add('light/MasterBedroom/Ceiling2/216773570733769850/switch', on_message_MasterBedroom_Ceiling2)
client.message_callback_add('light/MasterBedroom/Ceiling2/216773570733769850/brightness/set', on_message_MasterBedroom_Ceiling2_Bright)

### Exterior-SEDriveway Begin
client.message_callback_add('light/Exterior/SEDriveway/216773570733525707/switch', on_message_Exterior_SEDriveway)
client.message_callback_add('light/Exterior/SEDriveway/216773570733525707/brightness/set', on_message_Exterior_SEDriveway_Bright)

### Exterior-NEDriveway Begin
client.message_callback_add('light/Exterior/NEDriveway/216773570733998142/switch', on_message_Exterior_NEDriveway)
client.message_callback_add('light/Exterior/NEDriveway/216773570733998142/brightness/set', on_message_Exterior_NEDriveway_Bright)

### Exterior-FrontDoor Begin
client.message_callback_add('light/Exterior/FrontDoor/216773570734112971/switch', on_message_Exterior_FrontDoor)
client.message_callback_add('light/Exterior/FrontDoor/216773570734112971/brightness/set', on_message_Exterior_FrontDoor_Bright)

### Exterior-NWBackDoor Begin
client.message_callback_add('light/Exterior/NWBackDoor/216773570734219143/switch', on_message_Exterior_NWBackDoor)
client.message_callback_add('light/Exterior/NWBackDoor/216773570734219143/brightness/set', on_message_Exterior_NWBackDoor_Bright)

### LivingRoom-fanwest Begin
client.message_callback_add('light/LivingRoom/fanwest/216773570733998247/switch', on_message_LivingRoom_fanwest)
client.message_callback_add('light/LivingRoom/fanwest/216773570733998247/brightness/set', on_message_LivingRoom_fanwest_Bright)

### LivingRoom-faneast Begin
client.message_callback_add('light/LivingRoom/faneast/216773570733869505/switch', on_message_LivingRoom_faneast)
client.message_callback_add('light/LivingRoom/faneast/216773570733869505/brightness/set', on_message_LivingRoom_faneast_Bright)

### LivingRoom-fannorth Begin
client.message_callback_add('light/LivingRoom/fannorth/216773570733998518/switch', on_message_LivingRoom_fannorth)
client.message_callback_add('light/LivingRoom/fannorth/216773570733998518/brightness/set', on_message_LivingRoom_fannorth_Bright)

### Bathroom-LED Begin
client.message_callback_add('light/Bathroom/LED/216773570733505864/switch', on_message_Bathroom_LED)
client.message_callback_add('light/Bathroom/LED/216773570733505864/brightness/set', on_message_Bathroom_LED_Bright)

### FutureUse-LED Begin
client.message_callback_add('light/FutureUse/LED/216773570734215492/switch', on_message_FutureUse_LED)
client.message_callback_add('light/FutureUse/LED/216773570734215492/brightness/set', on_message_FutureUse_LED_Bright)

### AllFull Begin
client.message_callback_add('light/AllFull/1/switch', on_message_AllFull)

### WeekdayAM Begin
client.message_callback_add('light/WeekdayAM/2147483642/switch', on_message_WeekdayAM)

### Afternoon Begin
client.message_callback_add('light/Afternoon/2147483646/switch', on_message_Afternoon)

### Exterior Begin
client.message_callback_add('light/Exterior/2147483645/switch', on_message_Exterior)

### TruckHome Begin
client.message_callback_add('light/TruckHome/2147483640/switch', on_message_TruckHome)
client.connect('172.16.33.8', 1883,60)

client.on_connect = on_connect
client.on_message = on_message

client.loop_forever()
