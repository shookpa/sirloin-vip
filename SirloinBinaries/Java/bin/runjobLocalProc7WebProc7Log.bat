::@echo Iniciado el proceso 7 del componente local a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompLocalProc7.log
cd C:\SirloinDesarrollo\Java\bin\
java.exe -jar Sirloin_local_v2.2.jar 7

ping -n 6 127.0.0.1 > nul
::@echo Iniciado el proceso 7 del componente web a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompWebProc7.log

java.exe -jar Sirloin_web_v8.4.jar 7
