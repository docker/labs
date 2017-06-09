# Part 2 - Packaging ASP.NET Applications as Docker Images

Now you have a repeatable way to publish the ASP.NET application. With the build agent from [Part 1](part-1.md), you can build a Docker image which packages the compiled app with all its dependencies. That's the first step towards modernizing the app. Just by running the app in the Docker platform you get plenty of benefits - increased compute utilization, improved security, and centralized management are a few. But it's also an enabler for adding new features to the app, making use of the great software that runs on Docker, and using the platform to integrate components together.

Packaging an ASP.NET application to run in Docker means writing a Dockerfile that does the following:

- starts with a clean, up-to-date installation of Windows Server Core
- installs IIS as the application host and ASP.NET as the app framework
- copies the application content from the published build
- configures the application as an IIS website

To get the most out of Docker, you should package your applications individually, with a single ASP.NET app in each image. That lets you deploy, scale and upgrade your applications separately. If you have many ASP.NET apps with similar configurations, all your Dockerfiles will be broadly the same. The content for each app will change, and some may need additional setup, but there will be a lot of commonality. 

> If you already have a build process which packages your app - into an MSI or a ZIP file - you can use the output from the existing build. Instead of copying the published website as we do in this lab, you would copy in the ZIP file and extract it, or copy in the MSI and run it in unattended mode.

## Writing Dockerfiles for ASP.NET web apps

A [Dockerfile](v1-src/docker/web/Dockerfile) to package ASP.NET websites will start with the same boilerplate code you use for other Windows images:

```
# escape=`
FROM microsoft/windowsservercore:10.0.14393.693
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop'; $ProgressPreference = 'SilentlyContinue';"]
```

In the `FROM` instruction you use the same version of the Windows Server Core base image that you used for the build agent. You could use [microsoft/iis](https://store.docker.com/images/iis) instead, which builds on Server Core and adds IIS, or [microsoft/aspnet](https://store.docker.com/images/aspnet) which builds on the IIS image and adds ASP.NET. Those images are also owned by Microsoft and follow the same release cadence as Windows Server Core, so when there's an OS update all those images have a new release. But it's easy to configure IIS and ASP.NET in the Dockerfile, so you can do it yourself and control what gets installed:

```
RUN Add-WindowsFeature Web-server, NET-Framework-45-ASPNET, Web-Asp-Net45; `
    Remove-Website -Name 'Default Web Site'
```

The `RUN` instruction executes PowerShell cmdlets to install the IIS and ASP.NET Windows features, and remove the default website which IIS creates. You don't need to install the .NET framework, because that's already there in the base image. So far it's conceptually the same as a manual deployment, where you follow the steps in a document to start from Windows Server and configure IIS and ASP.NET. Using the Dockerfile, these steps are automated and repeatable.

Next you need to make a tweak to the Windows setup which is specific to Docker. The Docker platform has a built-in DNS server, so applications running in containers can use DNS host names to reach other containers in the same Docker network, or other servers on the same physical network. It's all seamless to the application, but Windows uses a DNS cache which can be too aggressive - all DNS lookups should be handled by Docker, so the request is answered with the latest information. With a registry tweak you can turn off the Windows DNS cache:

```
RUN Set-ItemProperty -path 'HKLM:\SYSTEM\CurrentControlSet\Services\Dnscache\Parameters' -Name ServerPriorityTimeLimit -Value 0 -Type DWord
```

So far it's all generic setup which applies for any ASP.NET app, and now you can configure the sample application. You'll create an empty directory where the website content will live, and configure a new website in IIS using more PowerShell:

```
RUN New-Item -Path 'C:\web-app' -Type Directory; `
    New-Website -Name 'web-app' -PhysicalPath 'C:\web-app' -Port 80 -Force
```

> There's no specific application pool, so the new website will use the default app pool. If you're used to running multiple web apps on a server, you would typically have one app pool per site - to get isolation between sites at the process level. With Docker you don't need to do that. Only one web app will be running in this container, which gives you a much higher degree of isolation. To run many websites on our server, you'll be running many Docker containers, and inside each container the site can be using the default app pool.

By default, applications running in containers are locked down so there's no integration point between them and the host. When the host gets a request on port 80, Docker should route the traffic into the container. To do that you need to explicitly open a port with the `EXPOSE` instruction:

```
EXPOSE 80
```

The Dockerfile for packaging the ASP.NET WebForms app is nearly complete, and you've only written 10 lines of simple instructions. The next thing you need to do is tell Docker how to run the application when a container is started from the image. The Docker platform needs to know the entry point for the application, and it will monitor the process it starts to ensure the container is running. That doesn't work well with ASP.NET apps, where the host is actually a background Windows Service. 

To take advantage of Docker's entry point monitoring without changing the IIS runtime model, Microsoft have a utility called [ServiceMonitor.exe](https://github.com/Microsoft/iis-docker/issues/1) which is used in the IIS Docker image, and you can use in your image:

```
ADD https://github.com/Microsoft/iis-docker/raw/master/windowsservercore/ServiceMonitor.exe C:/ServiceMonitor.exe
ENTRYPOINT ["C:\\ServiceMonitor.exe", "w3svc"]
```

The `ADD` instruction downloads the binary file from GitHub and copies it into the image. The `ENTRYPOINT` instruction tells Docker to run `ServiceMonitor.exe` when a container starts, passing `w3svc` as the startup argument. The utility will start the IIS Windows Service and monitor the background process. If IIS stops, `ServiceMonitor` can flag the failure up to Docker, for the platform to take action.

The Dockerfile is nearly complete - you just need to copy in and configure the sample application.

## Configuring ASP.NET Applications in Docker

For version 1 you're going to package the application as-is, without any code changes, to run it in Docker. But you will need some configuration changes. The existing configuration expects to use [SQL Server Express LocalDB](https://blogs.msdn.microsoft.com/sqlexpress/2011/07/12/introducing-localdb-an-improved-sql-express/) for the database:

```
<connectionStrings>
  <add name="ProductLaunchDb" 
       providerName="System.Data.SqlClient" 
       connectionString="Server=(localdb)\MSSQLLocalDB;Integrated Security=true;AttachDbFilename=|DataDirectory|\ProductLaunch.mdf"/>
</connectionStrings>
```

That may be OK for developers, as LocalDB comes installed with Visual Studio. You could even install LocalDB in our Docker image and use the same configuration, but that's not good practice. That would mean running one container which hosts both the web app and the database, tightly coupling them and making it difficult to scale or upgrade the components separately. Instead you'll run SQL Server Express in a separate container, and change the connection string in the config file.

There is a separate [Web.config](v1-src/docker/web/Web.config) file, which you'll use in your deployment process - copying it over the existing `Web.config` file from the published application. The connection string in the new file uses `sql-server` as the database server name, and specifies user credentials:

```
<connectionStrings>
  <add name="ProductLaunchDb" 
       providerName="System.Data.SqlClient" 
       connectionString="Server=sql-server;Database=ProductLaunch;User Id=sa;Password=d0ck3r_Labs!;"/>
</connectionStrings>
```

When you run the web application, the DNS server in the Docker platform will resolve the `sql-server` host name to the correct address of the container called `sql-server`.

> There are separate `Web.config` files at this stage to clearly show that you can take an existing app and run it in Docker without any changes. In the next stage of the lab you'll use a different approach to configuration so you can use the Docker platform to manage configuration.

All you need to do to finish the Dockerfile is copy in the published website, which is the output from running the build agent container, and then copy over the replacement `Web.config` file:

```
COPY ProductLaunchWeb/_PublishedWebsites/ProductLaunch.Web /web-app
COPY Web.config /web-app/Web.config
```

You'll be scripting the whole build process, so you can use hard-coded paths for the file locations in the source and the target, knowing that they will exist.

> The order of the instructions in the Dockerfile is important. Each instruction creates a new read-only [image layer](https://docs.docker.com/engine/userguide/storagedriver/imagesandcontainers/), which can be cached and used in future builds or in other images. You start with the most generic instructions and get more specific. The final instruction copies in the website content - if you change code and rebuild the image, Docker will re-use all the existing layers and just run the final `COPY` instruction to copy in the new content, which makes for fast, efficient builds.


## Packaging the ASP.NET App as a Docker Image

To create the image you run `docker build` using the new Dockerfile, assuming you have already packaged and run the builder. Or you can run every step with a simple PowerShell script - this is the [build.ps1](v1-src/build.ps1) script for compiling version 1 of the app and packaging it as a Docker image:

```
docker build `
 -t dockersamples/modernize-aspnet-builder `
 $pwd\docker\builder

docker run --rm `
 -v $pwd\ProductLaunch:c:\src `
 -v $pwd\docker:c:\out `
 dockersamples/modernize-aspnet-builder `
 C:\src\build.ps1 

docker build `
 -t dockersamples/modernize-aspnet-web:v1 `
 $pwd\docker\web
```

The first full build will take a while, as the image are set up with everything they need. But because of the smart way Docker caches and re-uses image layers, subsequent full builds will take very little time. Packaging a new version should take under a minute, and most of that time will be spent in MSBuild. 

If you run `docker images` now, you'll see the Windows Server Core base image, the build agent image, and the application image. `dockersamples/modernize-aspnet-web:v1` is the packaged version of the ASP.NET sample app, configured and ready to run as a Docker container.

## Part 2 - Recap

You put together a Dockerfile which uses Windows Server Core as the base, installs IIS and ASP.NET and configures a web application. Up to that point, the Dockerfile is suitable for any app, and to customize it you just copied in the published WebForms application.

To keep the configuration of the app in the Docker image separate from the development configuration, you used a different `Web.config` file for the image, but you'll see a better way of managing configuration later in the lab.

Now you're ready for [Part 3 - Running ASP.NET applications as Docker containers](part-3.md).
