docker build `
 -t dockersamples/modernize-aspnet-ops-builder `
 $pwd\docker\builder

docker run --rm `
 -v $pwd\src:c:\src `
 -v $pwd\docker:c:\out `
 dockersamples/modernize-aspnet-ops-builder `
 C:\src\build.ps1 

Remove-Item -Recurse -Force $pwd\docker\web\UpgradeSample.Web
Move-Item -Force $pwd\docker\web\UpgradeSample\_PublishedWebsites\UpgradeSample.Web $pwd\docker\web
Remove-Item -Recurse -Force $pwd\docker\web\UpgradeSample

docker build `
 -t dockersamples/modernize-aspnet-ops:1.2 `
 --build-arg RELEASENAME=2017.04 `
$pwd\docker\web