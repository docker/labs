# escape=`
FROM microsoft/windowsservercore:10.0.14393.693
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop'; $ProgressPreference = 'SilentlyContinue';"]

RUN Set-ItemProperty -path 'HKLM:\SYSTEM\CurrentControlSet\Services\Dnscache\Parameters' -Name ServerPriorityTimeLimit -Value 0 -Type DWord
RUN Add-WindowsFeature Web-server, NET-Framework-45-ASPNET, Web-Asp-Net45; `
    Remove-Website -Name 'Default Web Site'    

RUN New-Item -Path 'C:\web-app' -Type Directory; `
    New-Website -Name 'web-app' -PhysicalPath 'C:\web-app' -Port 80 -Force

EXPOSE 80
ENV MESSAGE_QUEUE_URL="nats://message-queue:4222"

WORKDIR C://
ADD https://github.com/Microsoft/iis-docker/raw/master/windowsservercore/ServiceMonitor.exe ./ServiceMonitor.exe
COPY bootstrap.ps1 .
ENTRYPOINT ["powershell", "./bootstrap.ps1"]

COPY ProductLaunchWeb/_PublishedWebsites/ProductLaunch.Web /web-app

HEALTHCHECK CMD powershell -command `
    try { `
     $response = iwr http://localhost:80 -UseBasicParsing; `
     if ($response.StatusCode -eq 200) { return 0} `
     else {return 1}; `
    } catch { return 1 }