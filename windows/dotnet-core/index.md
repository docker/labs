<!--- +++
title = "Run .NET Core in a Linux Container "
description = "Sample .NET Core"
keywords = ["beginner, tutorial, Docker"]
[menu.main]
identifier = "dotnetcore_linux"
weight = 1
+++ -->
#Quickstart .NET Core
This quickstart assumes you have a working installation of Docker Engine. To
verify Engine is installed, use the following command:

```
# Check that you have a working install
$ docker info
```

# Get ASP.NET Core Samples
There are 3 samples presently provided in this github repo: A simple console
application, a simple web application with a single static welcome page, and an
MVC web application skeleton.

In the example below, you'll use the MVC web application, "HelloMVC".

```
$ git clone https://github.com/aspnet/Home.git aspnetcore
```

# Modify ASP.NET Core Sample to use CoreClr
As described in [Choosing the Right .NET For You on the Server]
(http://docs.asp.net/en/latest/getting-started/choosing-the-right-dotnet.html),
there are two runtime choices which run on Linux, .NET Core (aka CoreCLR) and Mono.
The CoreCLR is the Microsoft-supported runtime, which you'll use in this example.

The sample Dockerfiles are presently configured to use the mono framework.

To instead use the CoreClr framework, you must modify the Dockerfile to specify
the Docker Hub CoreClr base image instead of the mono base image:

```
$ cd aspnetcore/samples/1.0.0-rc1-final/HelloMvc
$ vi Dockerfile
```

The first line in the Dockerfile from the GitHub repo specifies the mono image:

```
FROM microsoft/aspnet:1.0.0-rc1-final
```

Change this to specify the CoreClr image and save the Dockerfile changes from vi:

```
FROM microsoft/aspnet:1.0.0-rc1-final-coreclr
```

Make a change to the stock application code before building and running it:

```
vi Controllers/HomeController.cs
```

Change Name = "FILL IN YOUR NAME"

Change Address = "FILL IN YOUR ADDRESS"

# Build ASP.NET Core Sample

```
$ docker build -t hellomvc-coreclr .
```

# Run ASP.NET Core Sample

```
$ docker run -d -p 5004:5004 hellomvc-coreclr
```

If using a Linux host with a desktop environment such as Ubuntu desktop or Xfce,
you can open the application via [localhost:5004](http://localhost:5004)

If using docker-machine, you can get the IP as follows, with which you can then
access the site on port 5004 from a browser on the host:
```
$ docker-machine ip default
```
