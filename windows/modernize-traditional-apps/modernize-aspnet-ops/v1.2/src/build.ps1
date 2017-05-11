$nuGetPath = "C:\Chocolatey\bin\nuget.bat"
$msBuildPath = "C:\Program Files (x86)\MSBuild\14.0\Bin\MSBuild.exe"

cd c:\src
& $nuGetPath restore .\UpgradeSample.sln
& $msBuildPath .\UpgradeSample.Web\UpgradeSample.Web.csproj /p:OutputPath=c:\out\web\UpgradeSample /p:DeployOnBuild=true /p:VSToolsPath=C:\MSBuild.Microsoft.VisualStudio.Web.targets.14.0.0.3\tools\VSToolsPath