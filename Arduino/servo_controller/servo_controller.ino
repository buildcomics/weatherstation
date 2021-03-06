#define DEBUGX

#define MAX_PWM 17

#ifdef DEBUG
 #define DEBUG_PRINT(x)  Serial.print (x)
 #define DEBUG_PRINTLN(x)  Serial.println (x)
#else
 #define DEBUG_PRINT(x)
 #define DEBUG_PRINTLN(x)
#endif

#include <Wire.h>
#include <Servo.h>

bool old_pinstate;
int servoVal = 93;
int cur_angle = 0;
unsigned int setAngle = 33;

const int interrupt_pin = 2;
const int power_pin = 3;

unsigned long int fallVal;


Servo myservo;  // create servo object to control a servo

void setup() {
  Serial.begin(9600);           // set up Serial library at 9600 bps
  DEBUG_PRINTLN("servotest");


  pinMode(power_pin, INPUT_PULLUP);
  pinMode(interrupt_pin, INPUT);
  attachInterrupt(digitalPinToInterrupt(interrupt_pin), feedback_interrupt, CHANGE);

  myservo.attach(11);  

  //setup timer0:
  TCCR0A = 0;
  TCCR0B = _BV(CS02); //256 prescaler

}

void loop() {
  bool pinstate;
  //determine to turn right or left
  int valDiff = setAngle-fallVal; //setpoint minus actual value = error
  if(old_pinstate == LOW) { //if turned on
    if (abs(valDiff) > 1 && abs(valDiff) < 64) { //too big a difference, turn some way
      if (abs(valDiff) < 32) { //easy, no need taking into account zero problems
        if(valDiff > 0) { //below zero, turn left
          servoVal = 89;
        }
        else { //above zero, turn right
          servoVal = 96;
        }
      }
      else {
        if (valDiff > 0 ) {//negative, turn right
          servoVal = 96;
        }
        else { //positive, turn left
          servoVal = 89;
        }
      }
    }
    else {
      servoVal = 93; //stop turning
    }
  }
  else {
    servoVal = 93; //stop turning
  }
  pinstate = digitalRead(power_pin);
  if (pinstate != old_pinstate) {
    delay(1000);
    pinstate = digitalRead(power_pin);
    if (pinstate != old_pinstate) {
      if (pinstate == HIGH) {
        Serial.print('H');
      }
      else {
        Serial.print('L');
      }
      old_pinstate=pinstate;
      delay(1000);
    }
  }
  myservo.write(servoVal);
}
void feedback_interrupt() {
  if (digitalRead(interrupt_pin) == LOW) { //falling
    //store timer value
    fallVal = TCNT0;
  }
  else { //rising
    TCNT0 = 0;
    //store timer value
    //restart timer
  }
}
void serialEvent() {
  /*char buffer[2];
  Serial.readBytes(buffer, 2) ;
  setAngle = atoi(buffer);
  if (setAngle > 65) {
    setAngle = 65;
  }*/
  while (Serial.available()) {
      setAngle = (char)Serial.read(); //angle 0-66
  }
  /*
  while(!Serial.available());
  char var1=Serial.read();
    while(!Serial.available());

  setAngle=var-'0';*/
  /*
  char var1=Serial.read();
  while(!Serial.available());
  char var2=Serial.read();
  char converter[]={var1,var2};
  int setAngle = atoi(converter);*/
}
