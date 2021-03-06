TARGET = weather_station_daemon
TARGET_ARM = weather_station_daemon_arm
LIBS = -lm
CFLAGS = -g -Wall
CC = gcc
CC_ARM = arm-linux-gnueabi-gcc


.PHONY: default all clean

default: 
	$(CC) main.c -Wall  -o $(TARGET)
beaglebone:
	$(CC_ARM) main.c -Wall -o $(TARGET_ARM)
transfer:
	scp $(TARGET_ARM) tombonewifi:/home/tom/
all: default beaglebone
bb: beaglebone transfer

clean:
	-rm -f $(TARGET)
	-rm -f $(TARGET_ARM)
