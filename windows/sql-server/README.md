# SQL Server Lab

Microsoft SQL Server is available to run in Docker containers on Linux and Windows. This lab focuses on Windows and shows you how to use Docker to modernize your database delivery — bringing modern practices like CI/CD into database management.

The SQL Server Express image — [microsoft/mssql-server-windows-express](https://store.docker.com/images/mssql-server-windows-express) — lets you run a SQL Server database in a Docker container on Windows, without having SQL Server installed. All you need is Docker. 

In this lab we'll build a Docker image which packages up a whole database schema on top of the SQL Server image, so when you run the container you have a fully-deployed database ready to use from your applications, or from SQL Server Management Studio. 

## What You Will Learn

You'll learn how to:

- write a Dockerfile to package SQL Server schemas. You'll use SSDT and compile the schema in a database deployment [Dacpac](https://www.simple-talk.com/sql/database-delivery/microsoft-and-database-lifecycle-management-dlm-the-dacpac/) file;

- build a Docker image which packages up SQL Server Express together with your Dacpac, configured to deploy the database when you run a container;

- run a disposable database container, where the data is not saved when the container is removed, which is ideal for automated testing and dev environments

- run a persistent database container with a Docker volume, so the data files are stored outside the container and are retained when the container is removed;

- upgrade the database by building a new image with an updated schema, then replacing the existing container, using the same Docker volume to preserve data.

## Prerequisites

You'll need Docker running on Windows. You can install [Docker for Windows](https://store.docker.com/editions/community/docker-ce-desktop-windows) on Windows 10, or follow the [Windows Container Lab Setup](https://github.com/docker/labs/blob/master/windows/windows-containers/Setup.md) to install Docker on Windows locally, on AWS and Azure.

You should be familiar with the key Docker concepts, and with Docker volumes:

- [Docker concepts](https://docs.docker.com/engine/understanding-docker/)
- [Docker volumes](https://docs.docker.com/engine/tutorials/dockervolumes/)

### Optional

You'll be using SQL Server Data Tools ("SSDT") to build the database schema into a deployable package. An understanding of SSDT and Dacpacs will be useful, but is not required:

- [SQL Server Data Tools - SSDT](https://msdn.microsoft.com/en-us/library/mt204009.aspx)
- [Data Tier Applications - Dacpac](https://msdn.microsoft.com/en-us/library/ee210546.aspx)

The build process using Docker **does not use** Visual Studio, but if you want to view or edit the SQL Database project yourself, you'll need Visual Studio 2017. The free [Visual Studio Community Edition](https://www.visualstudio.com/vs/community/) comes with SQL Server Data Tools.

## The Lab

- [Part 1 - Building the Dacpac](part-1.md)
- [Part 2 - Building the SQL Server Image](part-2.md)
- [Part 3 - Running the SQL Server Container](part-3.md)
- [Part 4 - Upgrading the SQL Server Database](part-4.md)
