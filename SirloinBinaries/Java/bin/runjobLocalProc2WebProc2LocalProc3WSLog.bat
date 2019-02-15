::@echo Iniciado el proceso 2 del componente local a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompLocalProc2.log

cd C:\SirloinDesarrollo\Java\bin\

java.exe -jar Sirloin_local_v2.2.jar 2

ping -n 6 127.0.0.1 > nul

::@echo Iniciado el proceso 2 del componente web a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompWebProc2.log
java.exe -jar Sirloin_web_v8.4.jar 2

ping -n 6 127.0.0.1 > nul

::@echo Iniciado el proceso 3 del componente local a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompLocalProc3.log
java.exe -jar Sirloin_local_v2.2.jar 3
