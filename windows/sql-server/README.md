# SQL Server Lab

Microsoft have an image on Docker Store which lets you run SQL Server Express 2016 as a container: [microsoft/mssql-server-windows-express](https://store.docker.com/images/mssql-server-windows-express). That gives you a vanilla SQL Server instance where you can attach existing databases, but you need to have created the database outside of the container first. In this lab we'll build a Docker image which packages up a whole database schema, so when you run the container you have a fully-deployed database ready to use from your applications, or from SQL Server Management Studio. 

## What You Will Learn

You'll learn how to:

- use the Docker Builder pattern with SQL Server, running a Docker container as an agent to build a database project and generate a database deployment [Dacpac](https://www.simple-talk.com/sql/database-delivery/microsoft-and-database-lifecycle-management-dlm-the-dacpac/) file;

- build a Docker image which packages up SQL Server Express together with your Dacpac, and configured to deploy the database when you run a container;

- run the database container with a Docker volume, so the data files are stored outside the container;

- upgrade the database by building a new image with an updated schema, then replacing the existing container, using the same Docker volume to preserve data.

### Prerequisites

You'll need Docker running on Windows. You can follow the [Windows Container Lab Setup](https://github.com/docker/labs/blob/master/windows/windows-containers/Setup.md) to install  Docker on Windows 10, or Windows 2016 - locally, on AWS and Azure.

You should be familiar with the key Docker concepts, and with Docker volumes:

- [Docker concepts](https://docs.docker.com/engine/understanding-docker/)
- [Docker volumes](https://docs.docker.com/engine/tutorials/dockervolumes/)

We'll be using SQL Server Data Tools ("SSDT") to build the database schema into a deployable package. An understanding of SSDT and Dacpacs will be useful:

- [SQL Server Data Tools - SSDT](https://msdn.microsoft.com/en-us/library/mt204009.aspx)
- [Data Tier Applications - Dacpac](https://msdn.microsoft.com/en-us/library/ee210546.aspx)

### Optional

The build process using Docker **does not use** Visual Studio, but if you want to view or edit the SQL Database project yourself, you'll need Visual Studio 2015. The free [Visual Studio Community Edition](https://www.visualstudio.com/vs/community/) comes with SQL Server Data Tools.

## The Lab

- [Part 1 - Building the Dacpac](part-1.md)
- [Part 2 - Building the SQL Server Image](part-2.md)
- [Part 3 - Running the SQL Server Container](part-3.md)
- [Part 4 - Upgrading the SQL Server Database](part-4.md)
