#Run asp.net 4.6 version of msbuild

set-strictmode -version latest
$ErrorActionPreference = "Stop"

#run msbuild

iex "& 'C:\Program Files (x86)\MSBuild\14.0\Bin\MsBuild.exe' /p:VisualStudioVersion=12.0 /p:VSToolsPath=c:\windows\system32\msbuild.microsoft.visualstudio.web.targets.14.0.0\tools\vstoolspath"
if ($LASTEXITCODE -ne 0) {exit 1}
