# Set WeatherServiceUrl parameter in config from environment variable of same name

set-strictmode -version latest
$ErrorActionPreference = "Stop"

$paramName = "WeatherServiceUrl"
$xmlPath = "RushHourWeatherApp\web.config"

Set-Location (Split-Path -parent $MyInvocation.MyCommand.Path)

$fqXmlPath = Join-Path (Split-Path -parent $MyInvocation.MyCommand.Path) $xmlPath
 
$xml = [xml](Get-Content $fqXmlPath)

$xml.SelectSingleNode(".//setting[@name='$paramName']").SelectSingleNode("value").InnerText = $env:WeatherServiceUrl

$xml.Save($fqXmlPath)

#end in idle powershell - works for either detached or interactive mode
powershell