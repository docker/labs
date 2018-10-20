# USAGE NOTES:
# 
# Step 1)
# Docker => Settings => Daemon => Switch from Basic to Advanced
#    Add :
#           "storage-opts": [
#             "size=120GB"
#           ]
#
# Step 2)
#           docker image build -t vs2017/buildtools -m 2GB .
#
# References: 
#            https://docs.microsoft.com/en-us/visualstudio/install/build-tools-container
#            https://docs.microsoft.com/en-us/visualstudio/install/use-command-line-parameters-to-install-visual-studio
#            https://docs.microsoft.com/en-us/visualstudio/install/workload-component-id-vs-build-tools
#
# NOTE: this build takes ~01:21:17.3328007 hrs on a surface book 2 15" with 16 GB RAM
#       and results in an image size of 56.8GB
#


# Use the latest Windows Server Core image 
FROM microsoft/windowsservercore:latest


# Download the tools 
SHELL ["cmd", "/S", "/C"]
ADD "https://aka.ms/vs/15/release/vs_buildtools.exe" "C:\TEMP\vs_buildtools.exe"
ADD "https://dist.nuget.org/win-x86-commandline/v4.7.0/nuget.exe" "C:\TEMP\nuget.exe"



# Install VS 2017 Build Tools
RUN C:\TEMP\vs_buildtools.exe --includeRecommended --includeOptional --quiet --nocache --norestart --wait \
 --installPath C:\BuildTools \
 --all \
 --remove Microsoft.VisualStudio.Component.Windows10SDK.10240 \
 --remove Microsoft.VisualStudio.Component.Windows10SDK.10586 \
 --remove Microsoft.VisualStudio.Component.Windows10SDK.14393 \
 --remove Microsoft.VisualStudio.Component.Windows81SDK \
 || IF "%ERRORLEVEL%"=="3010" EXIT 0



# Install SSDT NuGet
RUN "C:\TEMP\nuget.exe" install Microsoft.Data.Tools.Msbuild -Version 10.0.61804.210


# Install Chocolatey
ENV chocolateyUseWindowsCompression = false

SHELL ["powershell.exe", "-ExecutionPolicy", "Bypass", "-Command"]

RUN Set-ExecutionPolicy Bypass -Scope Process -Force; iex ((New-Object System.Net.WebClient).DownloadString('https://chocolatey.org/install.ps1')); \
    [System.Environment]::SetEnvironmentVariable('PATH', "\"${env:PATH};%ALLUSERSPROFILE%\chocolatey\bin\"", 'Machine'); \
    choco feature enable -n allowGlobalConfirmation;

# Install git tools with chocolatey
RUN choco install git -y \
    git-lfs -y \
    git-credential-manager-for-windows -y


# Launch VS2017 developer command prompt when started
SHELL ["cmd", "/S", "/C"]
ENTRYPOINT [ "CMD", "/k", "C:/BuildTools/Common7/Tools/VsDevCmd.bat" ]