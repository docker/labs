# escape=`
FROM microsoft/windowsservercore
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop'; $ProgressPreference = 'SilentlyContinue';"]

RUN Set-ItemProperty -path 'HKLM:\SYSTEM\CurrentControlSet\Services\Dnscache\Parameters' -Name ServerPriorityTimeLimit -Value 0 -Type DWord

WORKDIR /save-prospect-handler
ENV MESSAGE_QUEUE_URL="nats://message-queue:4222"
ENTRYPOINT ["C:\\save-prospect-handler\\ProductLaunch.MessageHandlers.SaveProspect.exe"]

COPY SaveProspectHandler .