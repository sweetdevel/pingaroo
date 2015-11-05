// async_exec.cpp : Defines the entry point for the console application.
//

#include "stdafx.h"
#include <stdio.h>
#include <windows.h>

// basic file operations
#include <iostream>
#include <fstream>

#include <string>
#include <time.h>


// Get current date/time, format is YYYY-MM-DD.HH:mm:ss
const std::string currentDateTime() {
    time_t     now = time(0);
    struct tm  tstruct;
    char       buf[80];
    tstruct = *localtime(&now);
    // Visit http://en.cppreference.com/w/cpp/chrono/c/strftime
    // for more information about date/time format
    strftime(buf, sizeof(buf), "%Y-%m-%d %X", &tstruct);

    return buf;
}

using namespace std;

int main(int argc, char* argv[])
{
	/*
	printf("The number of arguments is %d\n", argc);

	for(int index=0; index < argc; index++) {
		printf("Argument %d is %s\n", index, argv[index]);		
	}
	*/

	char allArguments[1000];
	strcpy (allArguments,"");

	if(argc > 2) {
		bool withLogging = (strcmp(argv[1], "/log") == 0);

		int programIndex = 1;
		int argumentStartIndex = 2;
		
		
		if(withLogging) {
			programIndex++;
			argumentStartIndex++;
		}

		for(int index=argumentStartIndex; index < argc; index++) {
			strcat(allArguments, argv[index]);
			strcat(allArguments, " ");
		}


		// argv[0] is the application's name
		if(withLogging) {
			char logFile[300];
			GetCurrentDirectory(300, logFile);
			strcat(logFile, "\\async_exec.log");

			ofstream myfile;
			myfile.open (logFile, ios::out | ios::app | ios::binary);
		
			// the date
			myfile << currentDateTime();
			myfile << ' ';
		

			//actual data
			myfile << argv[programIndex];
			myfile << ' ';
			myfile << allArguments;
			myfile << "\n";

			myfile.close();
		}


		ShellExecute(0, "open", argv[programIndex], allArguments, NULL, SW_SHOW);
	} else {
		 printf("Wrong number of arguments...\n");
		 printf("Ex:\n");
		 printf("php -r \"some code\"\n");
	     printf("/log php -r \"some code\"\n");
	}
	
	return 0;
}
