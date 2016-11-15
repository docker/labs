# Part 1 - Building the Dacpac

In the source folder there is a Visual Studio solution which contains a single SQL Server Data Tools Project - [Assets.Database](./src/Assets.Databae/Assets.Database.sln'). The database project has a simple schema which represents a store for company assets.

## The Database Schema

The sample schema is deliberately basic, so we can focus on the process. This model scales well though - I have used SQL projects and Dacpacs to manage databases with thousands of objects and hundreds of gigabytes of production data. 

Version 1 contains three tables for storing assets, along with the asset type and location:

![V1 Schema](./img/schema-v1.png)

In the SSDT project, each table is defined as a `CREATE` statement in a SQL script file, and there are post-deployment scripts to insert static data for known asset types and locations.

You can publish the project from Visual Studio to create the Dacpac, but using Docker we can build the project using a container without needing Visual Studio installed.

## The Docker Builder Pattern

The builder pattern is common in Docker projects - essentially you have one Docker image to build your application, and another image to run your application. This means you can keep your run-time image as lightweight as possible, because it doesn't need any of the tooling to build from source. And you don't need a dedicated build server with all the tooling installed, because that's packaged in the builder - your build server just needs to be running Docker.

For a more detailed look at the pattern, see:

- [Docker Pattern: The Build Container](http://blog.terranillius.com/post/docker_builder_pattern/)
- [Building Docker Images for Static Go Binaries](https://medium.com/@kelseyhightower/optimizing-docker-images-for-static-binaries-b5696e26eb07#.2lqwqddjp)

In this lab, we'll use the builder pattern with an image that has MSBuild and the SSDT MSBuild targets installed, so it can publish a Dacpac from a SQL project file.

## Dockerfile for the SQL Server Builder

We have a separate Dockerfile for the builder: [Dockerfile.builder](Dockerfile.builder). It uses the Windows Server Core image as the base, and the setup includes an `escape` directive and a `SHELL` instruction so we can use PowerShell in the `RUN` instructions:

```Dockerfile
# escape=`
FROM microsoft/windowsservercore
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]
``` 

(See [Windows, Dockerfiles and the Backtick Backslash Backlash](https://blog.sixeyed.com/windows-dockerfiles-and-the-backtick-backslash-backlash/) for more information on those lines).

Next we install all the tools we need to build the project. As this is Windows Server Core, we have the full functionality of Windows and we can install MSIs and use 32-bit software. In this case, the majority of what we need is available as Chocolatey packages, so in the image we install Chocolatey, the MSBuild tools, and the .NET 4.6 target package:

```Dockerfile
RUN Install-PackageProvider -Name chocolatey -Force; ` 
    Install-Package -Name microsoft-build-tools -RequiredVersion 14.0.25420.1 -Force; `
    Install-Package dotnet4.6-targetpack -Force
``` 

That gives us a build agent for basic .NET projects, but for SSDT we also need to install [Microsoft.Data.Tools.Msbuild](https://blogs.msdn.microsoft.com/ssdt/2016/08/22/releasing-ssdt-with-visual-studio-15-preview-4-and-introducing-ssdt-msbuild-nuget-package/), which comes as a NuGet package:

```Dockerfile
RUN Install-Package nuget.commandline -Force; `
    & C:\Chocolatey\bin\nuget install Microsoft.Data.Tools.Msbuild
```

That's all the installation we need to do. The final instruction in the builder Dockerfile sets up the command to run when the container starts. When the builder runs we kcik off MSBuild, to build the SQL Project from a known location, and copy the generated Dacpac to another known location:

```Dockerfile
CMD cd 'C:\Program Files (x86)\MSBuild\14.0\Bin'; `
    .\msbuild C:\src\Assets.Database\Assets.Database.sqlproj `
    /p:SQLDBExtensionsRefPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61026\lib\net40" `
    /p:SqlServerRedistPath="C:\Microsoft.Data.Tools.Msbuild.10.0.61026\lib\net40"; `
    cp 'C:\src\Assets.Database\bin\Debug\Assets.Database.dacpac' 'c:\bin'
```

> Note: we don't copy the source code into the builder image. The `MSBuild` command uses explicit paths for the source, which we will provide at runtime using Docker volumes. Keeping the source out of the builder means we don't need to rebuild the builder image for every code change - and it also means this image could easily be extended to be a generic SQL Server project builder.


## Generating the Dacpac with the Builder

First we need to build the builder. Open PowerShell, navigate to the root folder for the lab and run: 

```Docker
docker build -t assets-db-builder -f Dockerfile.builder .
``` 

Now we can use the builder to build the SQL project. We'll create an `out` folder where the builder will copy the generated Dacpac, and map the `src` and `bin` folders in the container to volume mounts on the host machine. When Docker runs the container, it will be reading from the `src` directory and writing to the `out` directory on the host machine, even though MSBuild thinks it's using the local filesystem:

```PowerShell
mkdir out
docker run --rm -v $pwd\out:c:\bin -v $pwd\src:c:\src assets-db-builder
```

That generates the deployment package in `out\Assets.Database.dacpac` which we will use in the image for our SQL Server database.


## Next

- [Part 2 - Building the SQL Server Image](part-2.md)