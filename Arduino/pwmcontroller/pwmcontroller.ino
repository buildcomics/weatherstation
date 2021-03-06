#define NO_DEBUG

#define MAX_PWM 16

#ifdef DEBUG
 #define DEBUG_PRINT(x)  Serial.print (x)
 #define DEBUG_PRINTLN(x)  Serial.println (x)
#else
 #define DEBUG_PRINT(x)
 #define DEBUG_PRINTLN(x)
#endif
 /***** PWM Pins *****
   * PG5 (OCOB), pin4
   * PE3 (OC3A), pin5
   * PE4 (OC3B), pin2
   * PE5 (OC3C), pin3
   * PH3 (OC4A), pin6
   * PH3 (OC4B), pin7
   * PH3 (OC4C), pin8
   * PH3 (OC2B), pin9
   * PH3 (OC2A), pin10
   * PH3 (OC1A), pin11
   * PH3 (OC1B), pin12
   * PH3 (OC0A,OC1C), pin13
   * PH3 (OC5A), pin46
   * PH3 (OC5B), pin45
   * PH3 (OC5C), pin44
   */

#include <Wire.h>
#include <LiquidCrystal.h>

unsigned int pwmval;
byte pwmVals[MAX_PWM];
byte softVal = 0;
bool startInit;
bool startVals;
unsigned int valCount = 0;
int i,j;


void setup() {
  Serial.begin(9600);           // set up Serial library at 9600 bps
  Serial.println("test2 hexfet");

// lcd.begin(16, 2);
// lcd.println("wh00p!");

  //setup timer0:
  TCCR0A = _BV(COM0A1)|_BV(COM0B1)|_BV(WGM00); //non-inverting phase correct pwm
  TCCR0B = _BV(CS00); //no prescaler (30khz)

  //setup timer1
  TCCR1A = _BV(COM1A1)|_BV(COM1B1) |_BV(WGM10); //non-inverting phase correct 8bit pwm, enable a/b
  TCCR1B = _BV(CS10); //no prescaler (30khz)

  //setup timer2
  TCCR2A = _BV(COM2A1)|_BV(COM2B1)|_BV(WGM20); //non-inverting phase correct 8bit pwm, enable a/b
  TCCR2B = _BV(CS20); //no prescaler (30khz)

  //setup timer3
  TCCR3A = _BV(COM3A1)|_BV(COM3B1)|_BV(COM3C1)|_BV(WGM30); //non-inverting phase correct 8bit pwm, enable a/b/c
  TCCR3B = _BV(CS30); //no prescaler (30khz)

  //setup timer4
  TCCR4A = _BV(COM4A1)|_BV(COM4B1)|_BV(COM4C1)|_BV(WGM40); //non-inverting phase correct 8bit pwm, enable a/b/c
  TCCR4B = _BV(CS40); //no prescaler (30khz)

  //setup timer5
  TCCR5A = _BV(COM5A1)|_BV(COM5B1)|_BV(COM5C1)|_BV(WGM50); //non-inverting phase correct 8bit pwm, enable a/b/c
  TCCR5B = _BV(CS50); //no prescaler (30khz)

  //set all to 0:
  OCR0A = 0; //pin 13
  OCR0B = 0; //pin 4
   
  OCR1A = 0; //pin 11
  OCR1B = 0; //pin 12
  
  OCR2A = 0; //pin 10
  OCR2B = 0; //pin 9
   
  OCR3AL = 0; //pin 5
  OCR3BL = 0; //pin 2
  OCR3CL = 0; //pin 3
  
  OCR4AL = 0; //pin 6
  OCR4BL = 0; //pin 7
  OCR4CL = 0; //pin 8
   
  OCR5AL = 0; //pin 46
  OCR5BL = 0; //pin 45
  OCR5CL = 0; //pin 44

  softVal = 0;
  
  // All pwm pins to output:
  pinMode(2, OUTPUT); //OC3B
  pinMode(3, OUTPUT); //OC3C
  pinMode(4, OUTPUT); //OCOB
  pinMode(5, OUTPUT); //OC3A
  pinMode(6, OUTPUT); //OC4A
  pinMode(7, OUTPUT); //OC4B
  pinMode(8, OUTPUT); //OC4C
  pinMode(9, OUTPUT); //OC2B
  pinMode(10, OUTPUT); //OC2A
  pinMode(11, OUTPUT); //OC1A
  pinMode(12, OUTPUT); //OC1B
  pinMode(13, OUTPUT); //OC0A,OC1C
  pinMode(44, OUTPUT); //OC5C
  pinMode(45, OUTPUT); //OC5B
  pinMode(46, OUTPUT); //OC5A

  pinMode(43, OUTPUT); //softpwm
  digitalWrite(43, LOW);

}

void loop() {
 //digitalWrite(43,LOW);
 for (j = 0; j<256; j++) {
  delayMicroseconds(10);
  //for(int j = 0; j< 500; j++);
  if (j == softVal) {
    digitalWrite(43, HIGH);
  }
/*  else {
    digitalWrite(43, LOW);
  }*/
 }
 
digitalWrite(43, LOW);
}
void serialEvent() {
  while (Serial.available()) {
    char inChar = (char)Serial.read();
    DEBUG_PRINT(valCount);
    DEBUG_PRINT(", in: ");
    DEBUG_PRINTLN((byte) inChar);
    if (startInit && startVals && valCount < MAX_PWM) { //values are starting, count untill 16
      pwmVals[valCount] = (byte) inChar;
      valCount++;

    }
    else if (inChar == 'A' && !startInit && !startVals) { //first part of AA starter
      startInit = true;
      valCount = 0;
    }
    else if(inChar != 'A' && startInit && !startVals) { //wrong second char, reset
      startInit = false;
      valCount = 0;
      Serial.println("SR");
    }
    else if(inChar == 'A' && startInit && !startVals) { //values part starting
      startVals = true;
      valCount = 0;
    }
    else {
      Serial.print("ER, vc: ");
      Serial.print(valCount);
      Serial.print(" char: ");
      Serial.println((int)inChar);
      valCount = 0;
    }
    if (valCount == MAX_PWM) { //take in last character and stop
      startInit = false;
      startVals = false;
           
      for (int i = 0; i<MAX_PWM; i++) {
        DEBUG_PRINT(i);
        DEBUG_PRINT(" : ");
        DEBUG_PRINTLN(pwmVals[i]);
      }
      Serial.println("OK");
      OCR0A = pwmVals[13]; //pin 13
      OCR0B = pwmVals[4]; //pin 4
       
      OCR1A = pwmVals[11]; //pin 11
      OCR1B = pwmVals[12]; //pin 12
      
      OCR2A = pwmVals[10]; //pin 10
      OCR2B = pwmVals[9]; //pin 9
       
      OCR3AL = pwmVals[5]; //pin 5
      OCR3BL = pwmVals[2]; //pin 2
      OCR3CL = pwmVals[3]; //pin 3
      
      OCR4AL = pwmVals[6]; //pin 6
      OCR4BL = pwmVals[7]; //pin 7
      OCR4CL = pwmVals[8]; //pin 8
       
      OCR5AL = pwmVals[0]; //pin 46
      OCR5BL = pwmVals[1]; //pin 45
      OCR5CL = pwmVals[14]; //pin 44

      softVal = 255-pwmVals[15]; //pim 43, softpwm
      valCount = 0;
    }
  }
}


