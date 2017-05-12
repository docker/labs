
Write-Output 'Starting w3svc'
Start-Service W3SVC
    
Write-Output 'Making HTTP GET call'
Invoke-WebRequest http://localhost -UseBasicParsing | Out-Null

Write-Output 'Flushing log file'
netsh http flush logbuffer | Out-Null

Write-Output 'Tailing log file'
Get-Content -path 'c:\iislog\W3SVC\u_extend1.log' -Tail 1 -Wait