# escape=`
FROM microsoft/mssql-server-2016-express-windows
MAINTAINER Elton Stoneman <elton@docker.com>

SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]

EXPOSE 1433
VOLUME c:\\database
ENV sa_password D0cker!a8s

RUN md c:\init
WORKDIR c:\\init
COPY .\\out\\Assets.Database.dacpac .
COPY Initialize-Database.ps1 .

CMD ./Initialize-Database.ps1 -sa_password $env:sa_password -Verbose