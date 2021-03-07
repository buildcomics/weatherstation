# 3D Printer Control MKII
This is the GitHub repository for the project: https://buildcomics.com/meters/final-weatherstation-quest/ \
You will also find the "instructions"  there!

It contains Arduino code for the uno and the mega, code for the beaglebone controlling the two arduinos, python code to collect bandwidth data and a ton of PHP code to get data and display it and store it.\
The data runs as follows: sensors (online, or hardware) ==> PHP code (processing and storing in database and updating the pwm values files) ==> pwm values are sent to beaglebone ==> Beaglebone sends the PWM values to the arduinos ==> Arduino drives the mosfets that drive the meters.

# Disclaimer #
This is not a real code repository, but more a collection of code for re-use. The code is extremely specific for my use (and possibly a tad messy here and there).

## License
-- with regards to code that does not already fall under a license , the other code falls under: --

MIT License

Copyright (c) 2021 buildcomics

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
