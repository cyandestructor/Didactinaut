@echo off 
set /p _abs-account-name= Enter account name:
set /p _abs-account-key= Enter account key:
setx account-name %_abs-account-name%
setx account-key %_abs-account-key%
echo Credentials were set correctly
pause