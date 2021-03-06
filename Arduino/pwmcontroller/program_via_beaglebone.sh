#!/bin/bash
scp pwmcontroller.ino.mega.hex tombonewifi:/home/tom/pwmcontroller.ino.mega.hex
ssh -t tom@tombonewifi "avrdude -v -p m2560 -c stk500v2 -P /dev/pwm_controller -b 115200 -D -U flash:w:/home/tom/pwmcontroller.ino.mega.hex:i"
