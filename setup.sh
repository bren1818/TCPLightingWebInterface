#!/bin/bash
# Creates writeable files and directories needed program function
printf "Creating needed files and directories for TCPLightingWebInterface\n\n"

printf "Creating file tcp.token...\n\n"
if [ ! -f tcp.token ]; then
	touch tcp.token
	chmod 777 tcp.token
	printf "File tcp.token created\n\n"
else 
	printf "File exists continuing\n\n"
fi

printf "Creating file schedule.sched...\n\n"
if [ ! -f schedule.sched ]; then
	touch schedule.sched
	chmod 777 schedule.sched
	printf "File schedule.sched created\n\n"
else 
	printf "File exists continuing\n\n"
fi

printf "Creating file schedule.actioned...\n\n"
if [ ! -f schedule.actioned ]; then
	touch schedule.actioned
	chmod 777 schedule.actioned
	printf "File schedule.actioned created\n\n"
else 
	printf "File exists continuing\n\n"
fi

printf "Creating file mqtt_sub.py...\n\n"
if [ ! -f mqtt_sub.py ]; then
	touch mqtt_sub.py
	chmod 777 mqtt_sub.py
	printf "File mqtt_sub.py created\n\n"
else 
	printf "File exists continuing\n\n"
fi

printf "Creating directory logs...\n\n"
if [ ! -d logs ]; then
	mkdir logs
	chmod 777 logs
	printf "Directory logs created\n\n"
else 
	printf "Directory exists continuing\n\n"
fi


printf "File creation complete"

