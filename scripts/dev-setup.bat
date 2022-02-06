@ECHO OFF

rem Install Deps
winget install -e --id Git.Git
winget install -e --id Docker.DockerDesktop

rem Get SOURCE
pushd %~dp0
echo %CD% > SOURCE.tmp
set /p SOURCE= < SOURCE.tmp
del SOURCE.tmp
set SOURCE=%SOURCE:~0,-1%
popd

rem GET ROOT_FOLDER
git -C "%SOURCE%" rev-parse --show-toplevel > ROOT_FOLDER.tmp
set /p ROOT_FOLDER= < ROOT_FOLDER.tmp
set ROOT_FOLDER=%ROOT_FOLDER:/=\%
del ROOT_FOLDER.tmp

rem Import environment variables
call "%SOURCE%\dev-vars.bat"

@REM call "C:\Program Files\Docker\Docker\DockerCli.exe" -SwitchDaemon
call "%SOURCE%\helpers\functions.bat" dev_destroy_containers
call "%SOURCE%\helpers\functions.bat" dev_setup_config_files

call "%SOURCE%\helpers\functions.bat" dev_create_containers
call "%SOURCE%\helpers\functions.bat" dev_setup_containers