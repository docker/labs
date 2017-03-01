
net user iisadmin "!!Sadmin*" /add
net localgroup "Administrators" "iisadmin" /add

Import-Module servermanager
Add-WindowsFeature web-mgmt-service

Set-ItemProperty -Path HKLM:\SOFTWARE\Microsoft\WebManagement\Server -Name EnableRemoteManagement -Value 1
start-service wmsvc