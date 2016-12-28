# Part 2 - Building the SQL Server Image

The SQL Server image will come packaged with the database schema, in the form of the Dacpac we generated in [Part 1](part-1.md). With the Dacpac in the image, we can run containers to create a new database, or to upgrade an existing one.

## Dockerfile for the SQL Server Image

[SQL Server Express](https://www.microsoft.com/en-us/sql-server/sql-server-editions-express) is a free version of SQL Server which is suitable for non-production workloads (and even for production with small-scale workloads). Microsoft  provide a Dockerized version on the Hub: [microsoft/mssql-server-windows-express](https://hub.docker.com/r/microsoft/mssql-server-windows-express/), which we'll use as the basis for our database.

The Dockerfile starts in the usual way for Windows images:

```Dockerfile
# escape=`
FROM microsoft/mssql-server-windows-express
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]
```
Next we specify some integration points between the container and the host. We want to be able to access SQL Server on port 1433, we want the database files stored outside of the container, and we want to set a default value for the `sa` user password:

```Dockerfile
EXPOSE 1433
VOLUME c:\\database
ENV sa_password D0cker!a8s
```

> Note: this is a simplified approach to securing SQL Server. The Express instance is set up to allow SQL Server authentication, and an environment variable is used in the image for the `sa` password. Users can override the default password when they run a container, but environment variables are not meant for secrets. [Secret management](https://github.com/docker/docker/pull/27794) is coming to Docker soon.

The rest of the Dockerfile is straightforward. We set up a directory for the deployment package and deployment script, and copy them in from the Docker build context:

```Dockerfile
RUN md c:\init
WORKDIR c:\\init
COPY .\\out\\Assets.Database.dacpac .
COPY Initialize-Database.ps1 .
```

Lastly we specify the command to run when Docker starts a container from the image - which is our initialize script:

```Dockerfile
CMD ./Initialize-Database.ps1 -sa_password $env:sa_password -Verbose
```

Usually it's not good practice to call a script in your `CMD` instruction, because it hides the logic and means you can't get a full understanding of the image from the Dockerfile alone. In this case we need to do a few things when a container starts, which is why there's a separate script.

## Initializing the Database

The SQL Server image we're building supports multiple scenarios:

- starting a new container with an empty database
- starting a new container using an existing database
- starting a new container and upgrading an existing database.

When users have an existing database, they will run a container with a volume mount, containing their existing `MDF` (data) and `LDF` (log) files. In the initialize script, we need to check if files exist in the expected location. If the files are there, we build a SQL command to attach the database:

```SQL
CREATE DATABASE AssetsDB ON 
(FILENAME = N'c:\database\AssetsDB_Primary.mdf'), 
(FILENAME = N'c:\database\AssetsDB_Primary.ldf')
FOR ATTACH;
```

The filenames are hard-coded, because they will have been created by another instance of this container, so we know the exact locations. 

For all scenarios, whether we have attached the database or not, we use the [SqlPackage](https://msdn.microsoft.com/en-us/library/hh550080(v=vs.103).aspx) tool to generate a deployment SQL script from the Dacpac in the image:

```PowerShell
SqlPackage.exe `
    /sf:Assets.Database.dacpac `
    /a:Script /op:create.sql /p:CommentOutSetVarDeclarations=true `
    /tsn:.\SQLEXPRESS /tdn:AssetsDB /tu:sa /tp:$sa_password 
```

SqlPackage will compare the existing database to the schema model in the Dacpac and generate DDL instructions to upgrade the database. If the database doesn't exist, SqlPackage generates a full deployment script. In both cases, the post-deployment SQL scripts are appended to the generated script.

The final intialization step is to run the SQL script, specifying the known database name and file locations, which we do with [SQLCMD variables](https://msdn.microsoft.com/en-us/library/ms188714.aspx) and the `Invoke-SqlCmd` cmdlet:

```PowerShell
$SqlCmdVars = "DatabaseName=AssetsDB", "DefaultFilePrefix=AssetsDB", "DefaultDataPath=c:\database\", "DefaultLogPath=c:\database\"  
Invoke-Sqlcmd -InputFile create.sql -Variable $SqlCmdVars -Verbose
```

That's all packaged into the image, so we can run a container and use the database, without needing to get involved with what happens behind the scenes.

## Building the SQL Server Database Image

To build the database image, we just need to build the default Dockerfile - the expected files are already in place in the root directory and the `out` directory, created by the builder:

```PowerShell
docker build -t assets-db .
``` 

## Next

- [Part 3 - Running the SQL Server Container](part-3.md)
