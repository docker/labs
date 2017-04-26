
docker build `
 -t dockersamples/modernize-aspnet-builder `
 $pwd\docker\builder

docker run --rm `
 -v $pwd\ProductLaunch:c:\src `
 -v $pwd\docker:c:\out `
 dockersamples/modernize-aspnet-builder `
 C:\src\build.ps1 

docker build `
 -t dockersamples/modernize-aspnet-web:v3 `
 $pwd\docker\web

 docker build `
 -t dockersamples/modernize-aspnet-handler:v3 `
 $pwd\docker\save-prospect

 docker build `
  -t dockersamples/modernize-aspnet-homepage:v3 `
  $pwd\docker\homepage