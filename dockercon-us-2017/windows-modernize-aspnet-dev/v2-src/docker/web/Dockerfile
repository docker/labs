# escape=`
FROM microsoft/aspnet:windowsservercore-10.0.14393.693
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop'; $ProgressPreference = 'SilentlyContinue';"]

RUN Set-ItemProperty -path 'HKLM:\SYSTEM\CurrentControlSet\Services\Dnscache\Parameters' -Name ServerPriorityTimeLimit -Value 0 -Type DWord

RUN Remove-Website -Name 'Default Web Site'; `
    New-Item -Path 'C:\web-app' -Type Directory; `
    New-Website -Name 'web-app' -PhysicalPath 'C:\web-app' -Port 80 -Force

ENV MESSAGE_QUEUE_URL="nats://message-queue:4222"
ENTRYPOINT ["powershell", "./bootstrap.ps1"]

COPY bootstrap.ps1 /
COPY ProductLaunchWeb/_PublishedWebsites/ProductLaunch.Web /web-app

HEALTHCHECK CMD powershell -command `
    try { `
     $response = iwr http://localhost:80 -UseBasicParsing; `
     if ($response.StatusCode -eq 200) { return 0} `
     else {return 1}; `
    } catch { return 1 }