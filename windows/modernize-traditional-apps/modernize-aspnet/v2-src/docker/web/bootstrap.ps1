Write-Output 'Bootstrap starting'

# copy process-level environment variables (from `docker run`) machine-wide
foreach($key in [System.Environment]::GetEnvironmentVariables('Process').Keys) {
    if ([System.Environment]::GetEnvironmentVariable($key, 'Machine') -eq $null) {
        $value = [System.Environment]::GetEnvironmentVariable($key, 'Process')
        [System.Environment]::SetEnvironmentVariable($key, $value, 'Machine')
        Write-Output "Set environment variable: $key"
    }
}

Write-Output 'Running ServiceMonitor'
& C:\ServiceMonitor.exe w3svc