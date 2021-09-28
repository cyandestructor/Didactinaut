@echo off 
set /p _db-user= Enter database user:
set /p _db-password= Enter database password:
setx DB-USER %_db-user%
setx DB-PASSWORD %_db-password%
echo Credentials were set correctly
pause