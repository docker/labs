
# Modernize ASP.NET Apps - Ops Lab

You'll already have a process for deploying ASP.NET apps, but it probably involves a lot of manual steps. Work like copying application content between servers, running interactive setup programs, modifying configuration items and manual smoke tests all add time and risk to deployments. 

In Docker, the process of packaging applications is completely automated, and the platform supports automatic update and rollback for application deployments. You can build Docker images from your existing application artifacts, and run ASP.NET apps in containers without changing code.

This lab is aimed at ops and system admins. It steps through packaging an ASP.NET WebForms app to run in a Docker container on Windows 10 or Windows Server 2016. It starts with an MSI and ends by showing you how to package the application from source. You'll see how easy it is to start running applications in Docker, and the benefits you get from a modern application platform.

## What You Will Learn

In this self-paced lab, you'll learn how to:

- Package an existing ASP.NET MSI so the app runs in Docker, without any application changes.

- Create an upgraded package with application updates and Windows patches.

- Update and rollback the running application in a production environment with zero downtime.

- Package an ASP.NET application from the source code without needing Visual Studio or MSBuild.

## Prerequisites

You'll need Docker running on Windows. You can follow the [Windows Container Lab Setup](https://github.com/docker/labs/blob/master/windows/windows-containers/Setup.md) to install Docker on Windows 10, or Windows 2016 - locally, or on AWS or Azure.

You should be familiar with IIS and PowerShell, and with the key [Docker concepts](https://docs.docker.com/engine/understanding-docker/)

## The Lab

- [Part 1 - Packaging ASP.NET Apps as Docker Images](part-1.md)
- [Part 2 - Upgrading Application Images](part-2.md)
- [Part 3 - Zero-Downtime Update and Rollback](part-3.md)
- [Part 4 - Packaging Applications From Source](part-4.md)