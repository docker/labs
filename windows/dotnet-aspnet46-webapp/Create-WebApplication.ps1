#Wrap powershell New-WebApplication to more easily handle escaped quotes

param(
[string]$site="Default Web Site",
[string]$applicationPool="DefaultAppPool",
[Parameter(Mandatory=$true)]
[string]$name,
[Parameter(Mandatory=$true)]
[string]$physicalPath
)

set-strictmode -version latest
$ErrorActionPreference = "Stop"

#create ASP.NET app

New-WebApplication -site $site -ApplicationPool $applicationPool -name $name -PhysicalPath $physicalPath