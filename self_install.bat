@echo off

git pull

if exist %~dp0composer.phar (
%~dp0php\php.exe %~dp0composer.phar self-update
) else (
%~dp0php\php.exe -r "readfile('https://getcomposer.org/installer');" | %~dp0php\php.exe
)

%~dp0php\php.exe %~dp0composer.phar install -o --no-dev

%~dp0php\php.exe %~dp0PhpUpdate config:init