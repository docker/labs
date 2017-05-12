[CmdletBinding()]
Param(
   [Parameter(Mandatory=$True,Position=1)]
   [string] $dockerID
)

docker build `
 -t $dockerID/modernize-aspnet-builder `
 $pwd\docker\builder

docker run --rm `
 -v $pwd\ProductLaunch:c:\src `
 -v $pwd\docker:c:\out `
 $dockerID/modernize-aspnet-builder `
 C:\src\build.ps1 

docker build `
 -t $dockerID/modernize-aspnet-web:v2 `
 $pwd\docker\web

 docker build `
 -t $dockerID/modernize-aspnet-handler:v2 `
 $pwd\docker\save-prospect