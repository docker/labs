param(
    [string] $sa_password = $env:sa_password,
    [string] $data_path = $env:data_path,
    [string] $TargetServerName = '.\SQLEXPRESS',
    [string] $TargetDatabaseName = 'AssetsDB',
    [string] $TargetUser = 'sa',
    [string] $TargetPassword = $env:sa_password
)

if ($TargetDatabaseName -eq '.\SQLEXPRESS') {

    # start the service
    Write-Verbose 'Starting SQL Server'
    Start-Service MSSQL`$SQLEXPRESS

    if ($sa_password -ne "_") {
        Write-Verbose 'Changing SA login credentials'
        $sqlcmd = "ALTER LOGIN sa with password='$sa_password'; ALTER LOGIN sa ENABLE;"
        Invoke-SqlCmd -Query $sqlcmd -ServerInstance ".\SQLEXPRESS" 
    }

    $mdfPath = "$data_path\AssetsDB_Primary.mdf"
    $ldfPath = "$data_path\AssetsDB_Primary.ldf"

    # attach data files if they exist: 
    if ((Test-Path $mdfPath) -eq $true) {
        $sqlcmd = "IF DB_ID('AssetsDB') IS NULL BEGIN CREATE DATABASE AssetsDB ON (FILENAME = N'$mdfPath')"
        if ((Test-Path $ldfPath) -eq $true) {
            $sqlcmd = "$sqlcmd, (FILENAME = N'$ldfPath')"
        }
        $sqlcmd = "$sqlcmd FOR ATTACH; END"
        Write-Verbose 'Data files exist - will attach and upgrade database'
        Invoke-Sqlcmd -Query $sqlcmd -ServerInstance ".\SQLEXPRESS"
    }
    else {
        Write-Verbose 'No data files - will create new database'
    }
}

# deploy or upgrade the database:
$SqlPackagePath = 'C:\Program Files\Microsoft SQL Server\140\DAC\bin\SqlPackage.exe'
& $SqlPackagePath  `
    /sf:Assets.Database.dacpac `
    /a:Script /op:deploy.sql /p:CommentOutSetVarDeclarations=true `
    /TargetServerName:$TargetServerName /TargetDatabaseName:$TargetDatabaseName `
    /TargetUser:$TargetUser /TargetPassword:$TargetPassword 

if ($TargetServerName -eq '.\SQLEXPRESS') {
    $SqlCmdVars = "DatabaseName=$TargetDatabaseName", "DefaultFilePrefix=$TargetDatabaseName", "DefaultDataPath=$data_path\", "DefaultLogPath=$data_path\"  
    Invoke-Sqlcmd -InputFile deploy.sql -Variable $SqlCmdVars -Verbose

    Write-Verbose "Deployed AssetsDB database, data files at: $data_path"

    $lastCheck = (Get-Date).AddSeconds(-2) 
    while ($true) { 
        Get-EventLog -LogName Application -Source "MSSQL*" -After $lastCheck | Select-Object TimeGenerated, EntryType, Message	 
        $lastCheck = Get-Date 
        Start-Sleep -Seconds 2 
    }
}
else {
    $SqlCmdVars = "DatabaseName=$TargetDatabaseName", "DefaultFilePrefix=$TargetDatabaseName", "DefaultDataPath=$data_path\", "DefaultLogPath=$data_path\"  
    Invoke-Sqlcmd -ServerInstance $TargetServerName -Database $TargetDatabaseName -User $TargetUser -Password $TargetPassword -InputFile deploy.sql -Variable $SqlCmdVars -Verbose

    Write-Verbose "Deployed AssetsDB database, data files at: $data_path"
}
