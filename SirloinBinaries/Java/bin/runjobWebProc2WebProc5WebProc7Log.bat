::@echo Iniciado el proceso 2 del componente web a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompWebProc2.log
cd C:\SirloinDesarrollo\Java\bin\
java.exe -jar Sirloin_web_v8.4.jar 2

ping -n 6 127.0.0.1 > nul

::@echo Iniciado el proceso 5 del componente web a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompWebProc5.log
java.exe -jar Sirloin_web_v8.4.jar 5

ping -n 6 127.0.0.1 > nul

::@echo Iniciado el proceso 7 del componente web a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompWebProc7.log
java.exe -jar Sirloin_web_v8.4.jar 7
