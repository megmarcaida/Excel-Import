if not "%minimized%"=="" goto :minimized
set minimized=true
@echo off

cd "C:\Users\Office6-1\Documents\Migz\Development\fb\nightmare"

start /min cmd /C "nodemon server.js"
goto :EOF
:minimized