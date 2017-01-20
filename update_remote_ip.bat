@echo off
:: Usage: update_remote_ip.bat my-remote my@email.com
curl -X POST "http://onai.net/ip-keeper/?name=%1&notify=%2"
