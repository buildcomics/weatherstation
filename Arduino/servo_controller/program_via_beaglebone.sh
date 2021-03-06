#!/bin/bash
scp servo_controller.ino.standard.hex tombonewifi:/home/tom/servo_controller.ino.standard.hex
ssh -t tom@tombonewifi "avrdude -v -p atmega328p -c arduino -P /dev/servo_controller -b 115200 -D -U flash:w:/home/tom/servo_controller.ino.standard.hex:i"
