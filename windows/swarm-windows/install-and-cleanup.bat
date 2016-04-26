REM build swarm and cleanup to minimize image layer size

REM install golang, git, godep
choco install golang -y
if errorlevel 1 exit 1
SET PATH=%PATH%;%GOROOT%\bin;%GOPATH%\bin
choco install git -y
if errorlevel 1 exit 1
SET PATH=%PATH%;c:\program files\git\cmd
go get github.com/tools/godep
if errorlevel 1 exit 1

REM build swarm
mkdir %GOPATH%
mkdir %GOPATH%\src\github.com\docker
cd %GOPATH%\src\github.com\docker
git clone https://github.com/docker/swarm
if errorlevel 1 exit 1
cd swarm
git checkout %SWARM_VERSION%
if errorlevel 1 exit 1

godep go install .
if errorlevel 1 exit 1
cd \
mkdir bin
copy %GOPATH%\bin\swarm.exe bin\swarm.exe

REM cleanup to reduce image layer size
choco uninstall golang -y
if exist %GOROOT% rmdir /s /q %GOROOT%
if exist %GOPATH% rmdir /s /q %GOPATH%
choco uninstall git -y
if exist "c:\program files\git" rmdir /s /q "c:\program files\git"
