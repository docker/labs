
# Modernize ASP.NET Apps Lab

You can run full .NET Framework apps in Docker using the [Windows Server Core](https://store.docker.com/images/windowsservercore) base image from Microsoft. That image is a headless version of Windows Server 2016, so it has no UI but it has all the other roles and features available. Building on top of that there are also Microsoft images for [IIS]https://store.docker.com/images/iis) and [ASP.NET](https://store.docker.com/images/aspnet), which are already configured to run ASP.NET and ASP.NET 3.5 apps in IIS.

This lab steps through porting an ASP.NET WebForms app to run in a Docker container on Windows 10 or Windows Server 2016. With the app running in Docker, you can easily modernize it - and in the lab you'll add new features quickly and safely by making use of the Docker platform.

## What You Will Learn

In this self-paced lab, you'll learn how to:

- Package an existing ASP.NET application so it runs in Docker, without any application changes.

- Run SQL Server Express in a Docker container, and use it for the application database.

- Use a feature-driven approach to address problems in the existing application, without an extensive re-write.

- Use the Dockerfile and Docker Compose syntax to replace manual deployment documents.

## Prerequisites

You'll need Docker running on Windows. You can follow the [Windows Container Lab Setup](https://github.com/docker/labs/blob/master/windows/windows-containers/Setup.md) to install Docker on Windows 10, or Windows 2016 - locally, or on AWS or Azure.

You should be familiar with ASP.NET and C#, and with the key [Docker concepts](https://docs.docker.com/engine/understanding-docker/)

### Optional

The build process for the application uses MSBuild in Docker container and **does not use** Visual Studio, but if you want to view or edit the solution yourself, you can use Visual Studio 2015. The free [Visual Studio Community Edition](https://www.visualstudio.com/vs/community/) is fine, or you can use [Visual Studio Code](http://code.visualstudio.com/).

## The Lab

- [Part 1 - Building ASP.NET applications with Docker](part-1.md)
- [Part 2 - Packaging ASP.NET applications as Docker images](part-2.md)
- [Part 3 - Running ASP.NET applications as Docker containers](part-3.md)
- [Part 4 - Improving performance with asynchronous messaging](part-4.md)
- [Part 5 - Enabling fast prototyping with separate UI components](part-5.md)