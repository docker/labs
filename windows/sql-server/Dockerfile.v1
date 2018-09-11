# escape=`
FROM dockersamples/assets-db-builder AS builder

WORKDIR C:\src
COPY src\Assets.Database-v1\ .
RUN msbuild Assets.Database.sqlproj `
    /p:SQLDBExtensionsRefPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61804.210\lib\net46" `
    /p:SqlServerRedistPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61804.210\lib\net46"

# update to latest SqlPackage
FROM microsoft/windowsservercore:ltsc2016 AS sqlpackage
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop'; $ProgressPreference = 'SilentlyContinue';"]

ENV download_url="https://download.microsoft.com/download/6/E/4/6E406E38-0A01-4DD1-B85E-6CA7CF79C8F7/EN/x64/DacFramework.msi"

RUN Invoke-WebRequest -Uri $env:download_url -OutFile DacFramework.msi ; `
    Start-Process msiexec.exe -ArgumentList '/i', 'DacFramework.msi', '/quiet', '/norestart' -NoNewWindow -Wait; `
    Remove-Item -Force DacFramework.msi

# db image
FROM microsoft/mssql-server-windows-express:2016-sp1
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]

COPY --from=sqlpackage ["C:\\Program Files\\Microsoft SQL Server\\140\\DAC", "C:\\Program Files\\Microsoft SQL Server\\140\\DAC"]

ENV ACCEPT_EULA="Y" `
    DATA_PATH="C:\database" `
    sa_password="D0cker!a8s"

VOLUME ${DATA_PATH}

WORKDIR C:\init
COPY Initialize-Database.ps1 .
ENTRYPOINT ["powershell", "./Initialize-Database.ps1"]

COPY --from=builder C:\src\bin\Debug\Assets.Database.dacpac .