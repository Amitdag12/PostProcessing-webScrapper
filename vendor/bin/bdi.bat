@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../dbrekelmans/bdi/bdi
php "%BIN_TARGET%" %*
