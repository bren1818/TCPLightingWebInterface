# TCPLightingWebInterface - Can be used with IFTTT!
Creating a local web interface to work with the 'Connected by TCP' bulbs.

Let me place this note at the top of the readme. Using this project in conjunction with IFTTT, webhooks and say a Google Home will allow you to control your lights again using voice commands. I will document the procedure step by step.

The goal of this project is to re-create and improve upon the web interface which was used to control the Connected by TCP bulbs. The web interface was removed and the 'Connected by TCP' line was discontinued, leaving people with the bulbs out of luck, myself included.

After doing some reading, de-compiling the Android source code, reverse engineering and some time, I have put together this simple PHP web interface with a GUI that can be used to control the Connected by TCP bulbs. This interface should work with the latest bridge firmware, and the previous version of the firmware  as well. All that needs to be done is modifying some strings in the include.php file. This can also be used with IFTTT via web hook commands issued to the API.php

The web application will walk you through creating a token (if required) and then present you with an interface showing you your currently setup rooms, devices and bulbs.

Devices, individual lights, or rooms can be turned on or off by the web interface or dimmed.


#Abstracted API calls for this project. 
See 'API Notes.txt' for information on api call details, IE if you want to use this with IFTTT.
See also the 'Using this project with IFTTT.docx' for detailed instructions.



#RAW Bridge API Commands (for the developers)

Get List of Rooms, devices, info etc:

`cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>RoomGetCarousel</gcmd><gdata><gip><version>1</version><token>{TOKEN_STRING}</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml`

Turning a device (bulb or appliance) on/off:

`cmd=DeviceSendCommand&data=<gip><version>1</version><token>{TOKEN_STRING}</token><did>{DEVICE_ID}</did><value>{VALUE 1 | 0}</value></gip>`
  
Dimming a device (bulb or appliance):

`cmd=DeviceSendCommand&data=<gip><version>1</version><token>{TOKEN_STRING}</token><did>{DEVICE_ID}</did><value>{VALUE 0 - 100}</value><type>level</type></gip>`

Turning a room (group of bulbs or appliances) on/off:

`cmd=RoomSendCommand&data=<gip><version>1</version><token>{TOKEN_STRING}</token><rid>{ROOM_ID}</rid><value>{VALUE 1 | 0}</value></gip>`

Dimming a room (group of bulbs or appliances):
`cmd=RoomSendCommand&data=<gip><version>1</version><token>{TOKEN_STRING}</token><rid>{ROOM_ID}</rid><value>{VALUE 0 - 100}</value><type>level</type></gip>`

These commands must be issued to your Connected by TCP Bridge. For example, perform a curl POST to:

https://{BRIDGE_IP}:443/gwr/gop.php the data posted to this url would be the string data of the commands outlined above.

#Other API Commands (see apitest.php)
`cmd=AccountGetExtras&data=<gip><version>1</version><token>{TOKEN_STRING}</token></gip>`

`cmd=GatewayGetInfo&data=<gip><version>1</version><token>{TOKEN_STRING}</token></gip>`

`cmd=RoomGetList&data=<gip><version>1</version><token>{TOKEN_STRING}</token></gip>`

`cmd=SceneGetList&data=<gip><version>1</version><token>{TOKEN_STRING}</token><bigicon>1</bigicon></gip>`

`cmd=SceneGetListDetails&data=<gip><version>1</version><token>{TOKEN_STRING}</token><bigicon>1</bigicon></gip>`

`cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>UserGetListDefaultRooms</gcmd><gdata><gip><version>1</version><token>{TOKEN_STRING}</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml`

`cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>UserGetListDefaultColors</gcmd><gdata><gip><version>1</version><token>{TOKEN_STRING}</token><fields>name,image,imageurl,control,power,product,class,realtype,status</fields></gip></gdata></gwrcmd></gwrcmds>&fmt=xml`

`cmd=GWRBatch&data=<gwrcmds><gwrcmd><gcmd>SceneGetList</gcmd><gdata><gip><version>1</version><token>{TOKEN_STRING}</token><fields>bigicon,detail,imageurl</fields><islocal>1</islocal></gip></gdata></gwrcmd></gwrcmds>&fmt=xml`

#Scheduler

I have created an interface where you can create a schedule for devices to turn on or off or dim depending on day of week and time of day. If you plan on using this, I recommend setting up an event which runs every minute and executes the runSchedule.bat file. You may need to edit this file to match your setup.

I really hope you enjoy this project and it works well for you!



