# Part 2 - Building the SQL Server Image

Your database image will come packaged with the schema, by compiling the Dacpac with the builder image from [Part 1](part-1.md). With the Dacpac in the final image, you can run containers to create a new database, or to upgrade an existing one.

## Dockerfile for the SQL Server Image

[SQL Server Express](https://www.microsoft.com/en-us/sql-server/sql-server-editions-express) is the free version of SQL Server which is suitable for dev and test environments, and even for production with smaller workloads. Microsoft provide a Docker image with SQL Server Expres installed on Docker Hub: [microsoft/mssql-server-windows-express](https://hub.docker.com/r/microsoft/mssql-server-windows-express/). You will use that as the basis for your database image.

Version 1 of the schema is packaged in [Dockerfile.v1](Dockerfile.v1), using multi-stage builds. The first stage compiles the Dacpac from the SQL Server Data Tools project. It starts by using the builder from [Part 1](part-1.md), and copying in the V1 source code:

```Dockerfile
FROM dockersamples/assets-db-builder AS builder
WORKDIR C:\src
COPY src\Assets.Database-v1\ .
```

Then it runs MSBuild to compile the SQL Project, specifying the tool paths - which are well-known because of the specific versions installed in the builder:

```
RUN msbuild Assets.Database.sqlproj `
      /p:SQLDBExtensionsRefPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61026\lib\net40" `
      /p:SqlServerRedistPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61026\lib\net40"
```

After this completes, the Dacpac will be stored in a temporary image used by the build process, and it can be accessed later in the build. The second stage of the same Dockerfile starts with a new `FROM` instruction, using the SQL Server Express Docker image:

```Dockerfile
FROM microsoft/mssql-server-windows-express
SHELL ["powershell"]
```

Next it specifies some configuration points between the container and the host. For a persistent database, you want the database files stored outside of the container in a volume, and you also want to set a default value for the `sa` user password:

```Dockerfile
VOLUME C:\database
ENV sa_password D0cker!a8s
```

> This is a simplified approach to securing SQL Server. The Express instance is set up to allow SQL Server authentication, and an environment variable is used in the image for the `sa` password. Users can override the default password when they run a container, but environment variables are not meant for sensitive data. [Docker secrets](https://github.com/dockersamples/newsletter-signup) are a better option.

The rest of the Dockerfile is straightforward. It sets up a directory for the deployment package and deployment script, copies the script in from the Docker build context, and sets that as the command to run when a container starts:

```Dockerfile
WORKDIR C:\init
COPY Initialize-Database.ps1 .
CMD ./Initialize-Database.ps1 -sa_password $env:sa_password -Verbose
```

Lastly it copies in the Dacpac from the `builder` stage, which contains the complete database schema and scripts to insert reference data:

```Dockerfile
COPY --from=builder C:\src\bin\Debug\Assets.Database.dacpac .
```

The script in the `CMD` instruction is what initializes the database using the Dacpac. It does a few things to support running disposable and persistent databases, and enable schema upgrades for containers.

> The script is already written for the lab, the next step just walks you through what it does.

## Initializing the Database

The SQL Server image you're building supports multiple scenarios:

- starting a new container with an empty database
- starting a new container using an existing database
- starting a new container and upgrading an existing database.

When users have an existing database, they will run a container with a volume mount, containing their existing `MDF` (data) and `LDF` (log) files. The initialize script first checks if files exist in the expected location. If the files are there, it builds a SQL command to attach the database:

```SQL
CREATE DATABASE AssetsDB ON 
(FILENAME = N'c:\database\AssetsDB_Primary.mdf'), 
(FILENAME = N'c:\database\AssetsDB_Primary.ldf')
FOR ATTACH;
```

The filenames are hard-coded, because they will have been created by another instance of this container, so it's safe to use the exact locations. 

For all scenarios, whether the user has attached the database or not, the script uses the [SqlPackage](https://msdn.microsoft.com/en-us/library/hh550080(v=vs.103).aspx) tool to generate a deployment SQL script from the Dacpac in the image:

```PowerShell
SqlPackage.exe `
    /sf:Assets.Database.dacpac `
    /a:Script /op:create.sql /p:CommentOutSetVarDeclarations=true `
    /tsn:.\SQLEXPRESS /tdn:AssetsDB /tu:sa /tp:$sa_password 
```

SqlPackage compares the existing database to the schema model in the Dacpac and generates DDL instructions to upgrade the database. If the database doesn't already exist, SqlPackage generates a full deployment script. Otherwise it generates a diff script to bring the schema into line with the Dacpac. In both cases, the post-deployment SQL scripts are appended to the generated script.

The final intialization step is to run the SQL script, specifying the known database name and file locations, which uses [SQLCMD variables](https://msdn.microsoft.com/en-us/library/ms188714.aspx) and the `Invoke-SqlCmd` cmdlet:

```PowerShell
$SqlCmdVars = "DatabaseName=AssetsDB", "DefaultFilePrefix=AssetsDB", "DefaultDataPath=c:\database\", "DefaultLogPath=c:\database\"  
Invoke-Sqlcmd -InputFile create.sql -Variable $SqlCmdVars -Verbose
```

That's all packaged into the image, so you can run a container and use the database, without needing to know what happens behind the scenes.

## Building the SQL Server Database Image

To build the database image, just build the multi-stage Dockerfile:

```PowerShell
docker image build --tag dockersamples/assets-db:v1 --file Dockerfile.v1 .
``` 

When that command completes you have your schema packaged into a Docker image which is a portable unit. You can share it on the public Docker Hub, or on a private registry like [Docker Trusted Registry](https://docs.docker.com/datacenter/dtr/2.0/). Anyone who has access can pull the image and run a copy of your database in a container.

## Next

- [Part 3 - Running the SQL Server Container](part-3.md)
