#include <stdio.h>
#include <signal.h> //used for periodic update
#include <sys/time.h>
#include <time.h> //Used for time
#include <unistd.h>			//Used for UART
#include <fcntl.h>			//Used for UART
#include <termios.h>		//Used for UART
#include <inttypes.h>
#include <errno.h>
#include <stdlib.h>

 #define max(a,b) \
   ({ __typeof__ (a) _a = (a); \
       __typeof__ (b) _b = (b); \
     _a > _b ? _a : _b; })
     
 #define min(a,b) \
   ({ __typeof__ (a) _a = (a); \
       __typeof__ (b) _b = (b); \
     _a < _b ? _a : _b; })

   //Delay getLux function
    #define LUXDELAY 500
    
int uart0_filestream = -1;
int uart1_filestream = -1;
int fd = 0;
int power = 0;
/* signal process */
void everysecond(int signo) {
	//UART buffers	
	unsigned char tx_buffer[20];
	unsigned char *p_tx_buffer;
	p_tx_buffer = &tx_buffer[0];

	//File variables
	char * fileString = NULL;
	size_t len = 0;
	FILE *file;
    size_t read_line;
	int rx_length;

	file = fopen("/home/tom/pwmvalues", "r");
    if (file == NULL)
	{
		printf("Error - Unable to open pwmvalues\n");
	}
	else {
		*p_tx_buffer++ = 'A';
		*p_tx_buffer++ = 'A';
//		printf("Strings: ");
		if (power == 1) { //if power is on send file values
			while ((read_line = getline(&fileString, &len, file)) != -1) {
				*p_tx_buffer++ = atoi(fileString);
				//printf("%d, ", atoi(fileString));
			}
		}
		else {
			int x = 0;
			for (x = 0; x<=15; x++) { //power is of, send 00
				*p_tx_buffer++ = 0;
			}
		}
		//printf("\n");
	    fclose(file);	
		if (uart0_filestream != -1){
			printf("Sending pwm %d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d,%d \n", tx_buffer[0], tx_buffer[1], tx_buffer[2], tx_buffer[3], tx_buffer[4], tx_buffer[5], tx_buffer[6], tx_buffer[7], tx_buffer[8], tx_buffer[9], tx_buffer[10], tx_buffer[11], tx_buffer[12], tx_buffer[13], tx_buffer[14], tx_buffer[15], tx_buffer[16], tx_buffer[17]);
			int count = write(uart0_filestream, &tx_buffer[0], (p_tx_buffer - &tx_buffer[0]));		//Filestream, bytes to write, number of bytes to write
			if (count < 0){
				printf("UART TX error\n");
			}
				
			unsigned char rx_buffer[512];
			rx_length = read(uart0_filestream, (void*)rx_buffer, 511);		//Filestream, buffer to store in, number of bytes to read (max)
			if (rx_length < 0)	{
				//An error occured (will occur if there are no bytes)
			}
			else if (rx_length == 0)
			{
				//No data waiting
			}
			else
			{
				//Bytes received
				rx_buffer[rx_length] = '\0';
				printf("pwm %i bytes read : %s", rx_length, rx_buffer);
			}
		
		}
	}
	
	char servoString[4];
	p_tx_buffer = &tx_buffer[0];
	file = fopen("/home/tom/servovalues", "r");
    if (file == NULL)
	{
		printf("Error - Unable to open servovalues\n");
	}
	else {
		fgets(servoString, 4, file);
		*p_tx_buffer++ = atoi(servoString);
		printf("Servo read %d,", atoi(servoString));
	    fclose(file);	
		if (uart1_filestream != -1){
			printf("Sending %d ,", tx_buffer[0]);
			int count = write(uart1_filestream, &tx_buffer[0], (p_tx_buffer - &tx_buffer[0]));		//Filestream, bytes to write, number of bytes to write
			if (count < 0){
				printf("UART TX error\n");
			}
				
			unsigned char rx_servo_buffer[32];
			rx_length = read(uart1_filestream, (void*)rx_servo_buffer, 31);		//Filestream, buffer to store in, number of bytes to read (max)
			if (rx_length < 0)	{
				printf("some kind of error \n");
				//An error occured (will occur if there are no bytes)
			}
			else if (rx_length == 0) {//No data waiting
				printf("no data... \n"); 
			}
			else {
				//Bytes received
				rx_servo_buffer[rx_length] = '\0';
				printf("%i bytes read : %s, ", rx_length, rx_servo_buffer);
				if(rx_servo_buffer[0] == 'L') {//power on
					power = 1;
					printf("Power on\n");
				}
				else if(rx_servo_buffer[0] == 'H') { //power off
					printf("Power off\n");
					power = 0;
				}
			}
		
		}
	}}

/* init sigaction */
void init_sigaction(void){
    struct sigaction act;

    act.sa_handler = everysecond;
    act.sa_flags   = 0;
    sigemptyset(&act.sa_mask);
    sigaction(SIGPROF, &act, NULL);
} 

/* init */
void init_time(void){
    struct itimerval val;

    val.it_value.tv_sec = 5;
    val.it_value.tv_usec = 0;
    val.it_interval = val.it_value;
    setitimer(ITIMER_PROF, &val, NULL);
}


int main(int argc, char **argv) {
  	if (argc < 2) {
		printf("missing argument ports (use command pwm_port servo_port");
		return 0;
	}
	else {
		printf("Opening pwm port %s and servo port %s \n", argv[1], argv[2]);
	}

	uart0_filestream = open(argv[1], O_RDWR | O_NOCTTY | O_NDELAY);		//Open in non blocking read/write mode
	if (uart0_filestream == -1) {
		printf("Error - Unable to open pwm port,  Ensure it is not in use by another application\n");
		return 0;
	}
	else {
		printf("Connected to pwm port\n");
	}
	fflush(stdout); // This will flush any pending printf output
	struct termios options;
	tcgetattr(uart0_filestream, &options);
	options.c_cflag = B9600 | CS8 | CLOCAL | CREAD;		//<Set baud rate
	options.c_iflag = IGNPAR;
	options.c_oflag = 0;
	options.c_lflag = 0;
	tcflush(uart0_filestream, TCIFLUSH);
	tcsetattr(uart0_filestream, TCSANOW, &options);
    
	
	uart1_filestream = open(argv[2], O_RDWR | O_NOCTTY | O_NDELAY);		//Open in non blocking read/write mode
	if (uart1_filestream == -1) {
		printf("Error - Unable to open servo port,  Ensure it is not in use by another application\n");
		return 0;
	}
	else {
		printf("Connected to servo port\n");
	}
	fflush(stdout); // This will flush any pending printf output
	struct termios options1;
	tcgetattr(uart1_filestream, &options);
	options1.c_cflag = B9600 | CS8 | CLOCAL | CREAD;		//<Set baud rate
	options1.c_iflag = IGNPAR;
	options1.c_oflag = 0;
	options1.c_lflag = 0;
	tcflush(uart1_filestream, TCIFLUSH);
	tcsetattr(uart1_filestream, TCSANOW, &options1);
    
    init_sigaction();
    init_time();
    while(1);
    return 0;
}

