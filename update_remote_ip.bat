@echo off
:: ip-keeper - update_local_ip.bat
:: Requires: grep, cut, curl 
:: Usage: update_remote_ip.bat name notifyemail [simple]

:: "ip-keeper" environment variable is path to ip-keeper service
if "%ip-keeper%"=="" (
    set ip-keeper=http://onai.net/ip-keeper/
)

:: check requirements
curl --version >NUL 2>&1 && (
    echo >NUL
) || (
    echo Missing requirements: curl is required. Please install first and make sure it is available from the command line.
    exit /b
)

curl -X POST "%ip-keeper%?name=%1&notify=%2&%3"
