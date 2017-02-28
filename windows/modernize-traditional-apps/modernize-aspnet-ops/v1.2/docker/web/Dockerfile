# escape=`
FROM microsoft/aspnet:windowsservercore-10.0.14393.693
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop'; $ProgressPreference = 'SilentlyContinue';"]

RUN Add-WindowsFeature Web-server, NET-Framework-45-ASPNET, Web-Asp-Net45

RUN New-Item -Path 'C:\web-app' -Type Directory; `
    New-WebApplication -Name UpgradeSample -Site 'Default Web Site' -PhysicalPath 'C:\web-app'

EXPOSE 80 
ARG RELEASENAME

HEALTHCHECK CMD powershell -command `
    try { `
     $response = iwr http://localhost:80/UpgradeSample -UseBasicParsing; `
     if ($response.StatusCode -eq 200) { return 0} `
     else {return 1}; `
    } catch { return 1 }

COPY ServiceMonitor.exe /
COPY UpgradeSample.Web /web-app

RUN $file = 'c:\web-app\Web.config'; `
    (Get-Content $file) | Foreach-Object { $_ -replace '\{RELEASENAME\}', "$($env:RELEASENAME)" } | Set-Content $file

CMD ["C:\ServiceMonitor.exe", "w3svc"]