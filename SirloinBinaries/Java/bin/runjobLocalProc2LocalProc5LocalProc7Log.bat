::@echo Iniciado el proceso 2 del componente local a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompLocalProc2.log
cd C:\SirloinDesarrollo\Java\bin\
java.exe -jar Sirloin_local_v2.2.jar 2

ping -n 6 127.0.0.1 > nul


::@echo Iniciado el proceso 5 del componente local a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompLocalProc5.log
java.exe -jar Sirloin_local_v2.2.jar 5

ping -n 6 127.0.0.1 > nul

::@echo Iniciado el proceso 7 del componente local a las %date% %time%  >> C:\SirloinDesarrollo\logs\CompLocalProc7.log
java.exe -jar Sirloin_local_v2.2.jar 7
