# escape=`
FROM dockersamples/assets-db-builder AS builder

WORKDIR C:\src
COPY src\Assets.Database-v1\ .
RUN msbuild Assets.Database.sqlproj `
      /p:SQLDBExtensionsRefPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61026\lib\net40" `
      /p:SqlServerRedistPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61026\lib\net40"

# db image
FROM microsoft/mssql-server-windows-express
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]

VOLUME C:\database
ENV sa_password D0cker!a8s

WORKDIR C:\init
COPY Initialize-Database.ps1 .
CMD ./Initialize-Database.ps1 -sa_password $env:sa_password -Verbose

COPY --from=builder C:\src\bin\Debug\Assets.Database.dacpac .