# escape=`

# REMARK - when Go 1.8 is released we can use an official image for the base. 
# There is an issue with Go 1.7 and Windows 2016 SYMLINKD directories which causes Docker volumes and host mounts to fail. 
# This registry build uses a custom build of Go (@a2bd5c5) from the master branch to fix - https://github.com/golang/go/issues/15978

FROM sixeyed/golang:windowsservercore 
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]

CMD .\go get github.com/docker/distribution/cmd/registry ; `    
    cp \"$env:GOPATH\bin\registry.exe\" c:\out\ ; `
    cp \"$env:GOPATH\src\github.com\docker\distribution\cmd\registry\config-example.yml\" c:\out\config.yml

