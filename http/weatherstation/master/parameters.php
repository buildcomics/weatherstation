<?php 
/* paramters.php containing a list of parameters of different meters versus database names
	Tom van den Berg, 2015 for Event-Engineers
*/
$GLOBALS["livetypes"] = array("kWx63", "ems96"); //Types that are used for live gathering of data using a powergateway

/* format: $p[DATABASE_NAME]["names"] = array of different names of parameters for different meters
 * format: $p[DATABASE_NAME]["desc"][LANGUAGE] = description of parameters in LANGUAGE

 current meters: 
 	- PEL103 by chauvin arnoux
	- U180C&U189A by GMC

*/

$p["date"]["names"] = array("Date", "Datum"); 
$p["date"]["desc"]["en"] = "date of measurement";

$p["time"]["names"] = array("Time", "Tijd"); 
$p["time"]["desc"]["en"] = "time of measurement";


//currents:
$p["I1"]["names"] = array("I1", "A1","AVG A L1 [A] - Average"); 
$p["I1"]["desc"]["en"] = "Average current of phase 1";
$p["I1"]["unit"] = "A";

$p["I2"]["names"] = array("I2", "A2","AVG A L3 [A] - Average"); 
$p["I2"]["desc"]["en"] = "Average current of phase 2";
$p["I2"]["unit"] = "A";

$p["I3"]["names"] = array("I3", "A3","AVG A L2 [A] - Average"); 
$p["I3"]["desc"]["en"] = "Average current of phase 3";
$p["I3"]["unit"] = "A";

$p["IN"]["names"] = array("IN", "AN"); 
$p["IN"]["desc"]["en"] = "Average current of neutral";
$p["IN"]["unit"] = "A";



//phase-to-neutral voltages
$p["V1"]["names"] = array("V1", "V1N", "AVG V L1 [V] - Average"); //Average voltage of Phase 1
$p["V1"]["desc"]["en"] = "Average voltage of Phase 1";
$p["V1"]["unit"] = "V";

$p["V2"]["names"] = array("V2", "V2N", "AVG V L2 [V] - Average"); //Average voltage of Phase 2
$p["V2"]["desc"]["en"] = "Average voltage of Phase 2";
$p["V2"]["unit"] = "V";

$p["V3"]["names"] = array("V3", "V3N", "AVG V L3 [V] - Average"); //Average voltage of Phase 3
$p["V3"]["desc"]["en"] = "Average voltage of Phase 3";
$p["V3"]["unit"] = "V";


//Frequency:
$p["F"]["names"] = array("F");
$p["F"]["desc"]["en"] = "Average frequency";
$p["F"]["unit"] = "Hz";

$p["Fmin"]["names"] = array("F 1s MIN"); 
$p["Fmin"]["desc"]["en"] = "1s average lowest frequency";
$p["Fmin"]["unit"] = "Hz";

$p["Fmax"]["names"] = array("F 1s MAX");
$p["Fmax"]["desc"]["en"] = "1s average highest frequency";
$p["Fmax"]["unit"] = "Hz";

//Max currents
$p["I1max"]["names"] = array("I1 1 s MAX", "I1 1s MAX","AVG A L1 [A] - Max");
$p["I1max"]["desc"]["en"] = "1s average max current of phase 1";
$p["I1max"]["unit"] = "A";

$p["I2max"]["names"] = array("I2 1 s MAX","I2 1s MAX","AVG A L2 [A] - Max");
$p["I2max"]["desc"]["en"] = "1s average max current of phase 2";
$p["I2max"]["unit"] = "A";

$p["I3max"]["names"] = array("I3 1 s MAX","I3 1s MAX","AVG A L3 [A] - Max");
$p["I3max"]["desc"]["en"] = "1s average max current of phase 3";
$p["I3max"]["unit"] = "A";

$p["INmax"]["names"] = array("IN 1 s MAX", "IN 1s MAX");
$p["INmax"]["desc"]["en"] = "1s average max current of neutral";
$p["INmax"]["unit"] = "A";


//vmins & maxes

$p["V1min"]["names"] = array("V1 1 s MIN","V1 1s MIN", "AVG V L1 [V] - Min"); //1s average max voltage of phase 1
$p["V1min"]["desc"]["en"] = "1s average min voltage of phase 1";
$p["V1min"]["unit"] = "V";

$p["V1max"]["names"] = array("V1 1 s MAX", "V1 1s MAX", "AVG V L1 [V] - Max"); //1s average max voltage of phase 1
$p["V1max"]["desc"]["en"] = "1s average max voltage of phase 1";
$p["V1max"]["unit"] = "V";

$p["V2min"]["names"] = array("V2 1 s MIN", "V2 1s MIN", "AVG V L2 [V] - Min"); //1s average max voltage of phase 2
$p["V2min"]["desc"]["en"] = "1s average min voltage of phase 2";
$p["V2min"]["unit"] = "V";

$p["V2max"]["names"] = array("V2 1 s MAX","V2 1s MAX", "AVG V L2 [V] - Max"); //1s average max voltage of phase 2
$p["V2max"]["desc"]["en"] = "1s average max voltage of phase 2";
$p["V2max"]["unit"] = "V";

$p["V3min"]["names"] = array("V3 1 s MIN", "V3 1s MIN", "AVG V L3 [V] - Min"); //1s average max voltage of phase 3
$p["V3min"]["desc"]["en"] = "1s average min voltage of phase 3";
$p["V3min"]["unit"] = "V";

$p["V3max"]["names"] = array("V3 1 s MAX", "V3 1s MAX", "AVG V L3 [V] - Max"); //1s average max voltage of phase 3
$p["V3max"]["desc"]["en"] = "1s average max voltage of phase 3";
$p["V3max"]["unit"] = "V";


//phase-to-phase voltages
$p["V12"]["names"] = array("U12", "V12"); //Voltage between phase 1 and phase 2
$p["V12"]["desc"]["en"] = "Voltage between phase 1 and phase 2";
$p["V12"]["unit"] = "V";

$p["V12max"]["names"] = array("U12 1s MAX","U12 1 s MAX"); //1s average max voltage between phase 1 and phase 2
$p["V12max"]["desc"]["en"] = "1s average max voltage between phase 1 and phase 2";
$p["V12max"]["unit"] = "V";

$p["V23"]["names"] = array("U23", "V23"); //Voltage between phase 2 and phase 3
$p["V23"]["desc"]["en"] = "Voltage between phase 2 and phase 3";
$p["V23"]["unit"] = "V";

$p["V23max"]["names"] = array("U23 1s MAX","U23 1 s MAX"); //1s average max voltage between phase 2 and phase 3
$p["V23max"]["desc"]["en"] = "1s average max voltage between phase 2 and phase 3";
$p["V23max"]["unit"] = "V";

$p["V31"]["names"] = array("U31", "V31"); //Voltage between phase 3 and phase 1
$p["V31"]["desc"]["en"] = "Voltage between phase 3 and phase 1";
$p["V31"]["unit"] = "V";

$p["V31max"]["names"] = array("U31 1s MAX", "U31 1 s MAX"); //1s average max voltage between phase 3 and phase 1
$p["V31max"]["desc"]["en"] = "1s average max voltage between phase 3 and phase 1";
$p["V31max"]["unit"] = "V";




//Powers:
$p["P1"]["names"] = array("P1");
$p["P1"]["desc"]["en"] = "Average energy consumed on phase 1";
$p["P1"]["unit"] = "kW";
$p["P1"]["div"] = 1000; //divide by 1000 to get correct unit

$p["P2"]["names"] = array("P2");
$p["P2"]["desc"]["en"] = "Average energy consumed on phase 2";
$p["P2"]["unit"] = "kW";
$p["P2"]["div"] = 1000; //divide by 1000 to get correct unit

$p["P3"]["names"] = array("P3");
$p["P3"]["desc"]["en"] = "Average energy consumed on phase 3";
$p["P3"]["unit"] = "kW";
$p["P3"]["div"] = 1000; //divide by 1000 to get correct unit

$p["PT"]["names"] = array("PT", "PSYS","W [W] - Average");
$p["PT"]["desc"]["en"] = "Average energy consumed on all phases";
$p["PT"]["unit"] = "kW";
$p["PT"]["div"] = 1000; //divide by 1000 to get correct unit

//VA
$p["S1"]["names"] = array("S1");
$p["S1"]["desc"]["en"] = "Average energy consumed on phase 1";
$p["S1"]["unit"] = "kVA";
$p["S1"]["div"] = 1000; //divide by 1000 to get correct unit

$p["S2"]["names"] = array("S2");
$p["S2"]["desc"]["en"] = "Average energy consumed on phase 2";
$p["S2"]["unit"] = "kVA";
$p["S2"]["div"] = 1000; //divide by 1000 to get correct unit

$p["S3"]["names"] = array("S3");
$p["S3"]["desc"]["en"] = "Average energy consumed on phase 3";
$p["S3"]["unit"] = "kVA";
$p["S3"]["div"] = 1000; //divide by 1000 to get correct unit

$p["ST"]["names"] = array("ST", "SSYS");
$p["ST"]["desc"]["en"] = "Average energy consumed on all phases";
$p["ST"]["unit"] = "kVA";
$p["ST"]["div"] = 1000; //divide by 1000 to get correct unit


//Watt hours:
$p["Wh_imp"]["names"] = array("Ep+","kWhSYS_imp");
$p["Wh_imp"]["desc"]["en"] = "Cumulative imported amount of energy";
$p["Wh_imp"]["unit"] = "kWh";
$p["Wh_imp"]["div"] = 1000; //divide by 1000 to get correct unit

$p["Wh_exp"]["names"] = array("Ep-","kWh SYS_exp");
$p["Wh_exp"]["desc"]["en"] = "Cumulative exported amount of energy";
$p["Wh_exp"]["unit"] = "kWh";
$p["Wh_exp"]["div"] = 1000; //divide by 1000 to get correct unit

$GLOBALS["parameters"] = $p;
?>
