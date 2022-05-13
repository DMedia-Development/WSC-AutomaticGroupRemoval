@ECHO OFF

Rem Set package name based on the current directory
FOR %%I IN (.) DO SET PACKAGE_NAME=%%~nI%%~xI

Rem Extract version number from package.xml
FOR /f "delims=" %%a in ('findstr /i /l /c:"<version>" "package.xml"') DO SET "PACKAGE_VERSION=%%a"
SET "PACKAGE_VERSION=%PACKAGE_VERSION:*<version>=%"
FOR /f "delims=<" %%a in ("%PACKAGE_VERSION%") DO SET "PACKAGE_VERSION=%%a"

Rem Replace spaces with underscores
SET PACKAGE_VERSION=%PACKAGE_VERSION: =_%

Rem Replace uppercase letters to lowercase (supports the words "alpha", "beta", "dev", "pl" & "rc"
FOR %%a IN ("A=a" "B=b" "C=c" "D=d" "E=e" "H=h" "L=l" "M=m" "P=p" "R=r" "T=t" "V=v") DO (
    CALL SET PACKAGE_VERSION=%%PACKAGE_VERSION:%%~a%%
)

Rem Delete files*.tar if present
IF EXIST files*.tar (
  del files*.tar
)

Rem Delete templates*.tar if present
IF EXIST templates*.tar (
  del templates*.tar
)

Rem Delete acptemplates*.tar if present
IF EXIST acptemplates*.tar (
  del acptemplates*.tar
)

Rem Delete PACKAGE_NAME*.tar if present
IF EXIST %PACKAGE_NAME%*.tar (
  del %PACKAGE_NAME%*.tar
)

Rem Delete PACKAGE_NAME*.tar.gz if present
IF EXIST %PACKAGE_NAME%*.tar.gz (
  del %PACKAGE_NAME%*.tar.gz
)

Rem Compress files directory if present
IF EXIST files (
  E:\Programme\7-Zip\7z.exe a -ttar -mx=9 files.tar .\files\*
)

Rem Compress files_update directory if present
IF EXIST files_update (
  E:\Programme\7-Zip\7z.exe a -ttar -mx=9 files_update.tar .\files_update\*
)

Rem Compress templates directory if present
IF EXIST templates (
  E:\Programme\7-Zip\7z.exe a -ttar -mx=9 templates.tar .\templates\*
)

Rem Compress templates_update directory if present
IF EXIST templates_update (
  E:\Programme\7-Zip\7z.exe a -ttar -mx=9 templates_update.tar .\templates_update\*
)

Rem Compress acptemplates directory if present
IF EXIST acptemplates (
  E:\Programme\7-Zip\7z.exe a -ttar -mx=9 acptemplates.tar .\acptemplates\*
)

Rem Compress acptemplates_update directory if present
IF EXIST acptemplates_update (
  E:\Programme\7-Zip\7z.exe a -ttar -mx=9 acptemplates_update.tar .\acptemplates_update\*
)

Rem Create PACKAGE_NAME.tar
E:\Programme\7-Zip\7z.exe a -ttar -mx=9 %PACKAGE_NAME%_v%PACKAGE_VERSION%.tar .\* -x!acptemplates -x!acptemplates_update -x!files -x!files_update -x!templates -x!templates_update -x!%PACKAGE_NAME%.tar -x!.git -x!.github -x!.gitignore -x!make.bat -x!make.sh -x!.vscode -x!.idea -x!constants.php -x!README.md -x!.phpcs.xml -x!.php-cs-fixer.dist.php

timeout 1 >nul

Rem Create PACKAGE_NAME.tar.gz
E:\Programme\7-Zip\7z.exe a -tgzip  %PACKAGE_NAME%_v%PACKAGE_VERSION%.tar.gz %PACKAGE_NAME%_v%PACKAGE_VERSION%.tar

timeout 1 >nul

Rem Delete files*.tar if present
IF EXIST files*.tar (
  del files*.tar
)

Rem Delete templates*.tar if present
IF EXIST templates*.tar (
  del templates*.tar
)

Rem Delete acptemplates*.tar if present
IF EXIST acptemplates*.tar (
  del acptemplates*.tar
)

timeout 1 >nul

Rem Delete PACKAGE_NAME*.tar if present
IF EXIST %PACKAGE_NAME%*.tar (
  del %PACKAGE_NAME%*.tar
)