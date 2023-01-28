echo off
REM This adds the folder containing php.exe to the path
PATH=%PATH%;C:\xampp\php

REM Change Directory to the folder containing your script
CD C:\xampp\htdocs\lighting.local

REM Execute
php runSchedule.php