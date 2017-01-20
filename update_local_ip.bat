@echo off
:: Usage: update_local_ip.bat my-local my@email.com
ipconfig | grep "IPv4 Address" | cut -f 2 -d ":" | cut -f 2 -d " " > ip
set /p ip=<ip
del ip
curl -X POST "http://onai.net/ip-keeper/?name=%1&ip=%ip%&notify=%2"
