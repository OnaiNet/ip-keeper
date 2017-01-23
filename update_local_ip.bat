@echo off
:: ip-keeper - update_local_ip.bat
:: Requires: grep, cut, curl 
:: Usage: update_local_ip.bat name notifyemail [simple]

:: "ip-keeper" environment variable is path to ip-keeper service
if "%ip-keeper%"=="" (
    set ip-keeper=http://onai.net/ip-keeper/
)

:: check requirements
cut --version >NUL 2>&1 && grep --version >NUL 2>&1 && curl --version >NUL 2>&1 && (
    echo >NUL
) || (
    echo Missing requirements: grep, cut, and curl are required. Please install first and make sure they are available from the command line.
    exit /b
)

:: Get the local IP from ipconfig
ipconfig | grep "IPv4 Address" | cut -f 2 -d ":" | cut -f 2 -d " " > ip
set /p ip=<ip
del ip
curl -X POST "%ip-keeper%?name=%1&ip=%ip%&notify=%2&%3"
