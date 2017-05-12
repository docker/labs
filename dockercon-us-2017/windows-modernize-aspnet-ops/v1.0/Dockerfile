# escape=`
FROM microsoft/aspnet:windowsservercore-10.0.14393.576

COPY UpgradeSample-1.0.0.0.msi /

RUN msiexec /i c:\UpgradeSample-1.0.0.0.msi RELEASENAME=2017.02 /qn