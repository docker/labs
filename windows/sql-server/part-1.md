# Part 1 - Building the Dacpac

In the source folder there are two Visual Studio solutions each with single SQL Server Data Tools Projects - _Assets.Database-v1_ and _Assets.Database-v2_. They represent two versions of a database schema, used for recording company assets.

## The Database Schema

The sample schema is deliberately basic, so you can focus on the process. Version 1 contains three tables for storing assets, along with the asset type and location:

![V1 Schema](./img/schema-v1.png)

In the SSDT project, each table is defined as a `CREATE` statement in a SQL script file, and there are post-deployment scripts to insert static data for known asset types and locations.

You can publish the project from Visual Studio to create the Dacpac, but using Docker you can build the project using a container without needing Visual Studio installed.

## Docker Multi-stage Builds

You package applications to run in Docker using a Dockerfile. If you use SQL scripts to deploy your schema, you would write a simple Dockerfile which copies the scripts on top of the SQL Server Express image.

Multi-stage Dockerfiles are for more complex tasks, like in this lab. You'll compile the Dacpac in the first stage of the build, and then bundle the Dacpac on top of SQL Server Express in the second stage.

That approach means anyone can build and run your database from the source code, they don't need Visual Studio, MSBuild or SQL Server installed - the only prerequisite is Docker. That's perfect for CI scenarios, where you don't need to configure a build server with all the SSDT tools.

Instead you package all the build tools into a Docker image that can be used to generate the Dacpac.

## Dockerfile for the build toolchain 

The first Dockerfile is used for the build stage: [Dockerfile.builder](Dockerfile.builder). It's based from Microsoft's Windows Server Core image, and the Dockerfile uses a `SHELL` instruction to switch to PowerShell in the `RUN` instructions:

```Dockerfile
FROM microsoft/windowsservercore
SHELL ["powershell"]
``` 

The Dockerfile goes on to install all the tools needed to build SSDT projects. The majority of the tools are available as [Chocolatey](https://chocolatey.org/) packages, so in the Dockerfile the `RUN` instruction installs Chocolatey, the MSBuild tools, and the .NET 4.5.2 target package:

```Dockerfile
RUN Install-PackageProvider -Name chocolatey -RequiredVersion 2.8.5.130 -Force; `
    Install-Package -Name microsoft-build-tools -RequiredVersion 15.0.26228.0 -Force; `
    Install-Package -Name netfx-4.5.2-devpack -RequiredVersion 4.5.5165101 -Force
``` 

> All the packages are installed with specific versions, so when you build the image you will get the exact same versions of the tools, even if newer versions have been released.

At this point the Docker image will have all the tools to build basic .NET projects, but for SSDT you also need to install [Microsoft.Data.Tools.Msbuild](https://blogs.msdn.microsoft.com/ssdt/2016/08/22/releasing-ssdt-with-visual-studio-15-preview-4-and-introducing-ssdt-msbuild-nuget-package/), which comes as a NuGet package:

```Dockerfile
RUN Install-Package nuget.commandline -RequiredVersion 3.5.0 -Force; `
    & C:\Chocolatey\bin\nuget install Microsoft.Data.Tools.Msbuild -Version 10.0.61026
```

Finally the Dockerfile adds the build tools to the path, so users of the image can run `msbuild` without specifying a full path:

```
ENV MSBUILD_PATH="C:\Program Files (x86)\Microsoft Visual Studio\2017\BuildTools\MSBuild\15.0\Bin"

RUN $env:PATH = $env:MSBUILD_PATH + ';' + $env:PATH; `
    [Environment]::SetEnvironmentVariable('PATH', $env:PATH, [EnvironmentVariableTarget]::Machine)
```

That's all the installation you need to do. When you build this, you'll have a Docker image you can use to compile any SSDT project.

## Building the build agent

First you need to build the builder. Open PowerShell, navigate to the root folder for this lab and run: 

```Docker
docker image build --tag dockersamples/assets-db-builder --file Dockerfile.builder .
``` 

> You don't have to build the image yourself, you can pull the public version `docker image pull dockersamples/assets-db-builder`.

Now you can use the builder in a multi-stage Dockerfile to publish the database schema, and package it in a custom SQL Server image.

## Next

- [Part 2 - Building the SQL Server Image](part-2.md)