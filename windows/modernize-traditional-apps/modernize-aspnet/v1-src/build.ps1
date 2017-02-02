
docker build `
 -t dockersamples/modernize-aspnet-builder `
 $pwd\docker\builder

docker run --rm `
 -v $pwd\ProductLaunch:c:\src `
 -v $pwd\docker:c:\out `
 dockersamples/modernize-aspnet-builder `
 C:\src\build.ps1 

docker build `
 -t dockersamples/modernize-aspnet-web:v1 `
 $pwd\docker\web