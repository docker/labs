# escape=`
FROM microsoft/aspnet:windowsservercore-10.0.14393.693

COPY UpgradeSample-1.1.0.0.msi /

RUN msiexec /i c:\UpgradeSample-1.1.0.0.msi RELEASENAME=2017.03 /qn