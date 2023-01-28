# TCPLightingWebInterface - Can be used with IFTTT!
Creating a local web interface to work with the 'Connected by TCP' bulbs.

Let me place this note at the top of the readme. Using this project in conjunction with IFTTT, webhooks and say a Google Home will allow you to control your lights again using voice commands. I will document the procedure step by step.

See the fleshed out Wiki: https://github.com/bren1818/TCPLightingWebInterface/wiki

The goal of this project is to re-create and improve upon the web interface which was used to control the Connected by TCP bulbs. The web interface was removed and the 'Connected by TCP' line was discontinued, leaving people with the bulbs out of luck, myself included.

After doing some reading, de-compiling the Android source code, reverse engineering and some time, I have put together this simple PHP web interface with a GUI that can be used to control the Connected by TCP bulbs. This interface should work with the latest bridge firmware, and the previous version of the firmware  as well. All that needs to be done is modifying some strings in the include.php file. This can also be used with IFTTT via web hook commands issued to the API.php

The web application will walk you through creating a token (if required) and then present you with an interface showing you your currently setup rooms, devices and bulbs.

Devices, individual lights, or rooms can be turned on or off by the web interface or dimmed.

Please see Wiki for Bridge commands and setup on a Raspberry Pi

#Scheduler

I have created an interface where you can create a schedule for devices to turn on or off or dim depending on day of week and time of day. If you plan on using this, I recommend setting up an event which runs every minute and executes the runSchedule.bat file. You may need to edit this file to match your setup. - documentation to come in the wiki

I really hope you enjoy this project and it works well for you!



