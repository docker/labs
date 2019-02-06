## Getting Started with Windows Containers

This chapter will cover the basics of using Windows Containers with Docker.

## Running Windows containers

First pull a Docker image which you can use to run a Windows container:

```
docker image pull mcr.microsoft.com/windows/nanoserver:1809
```

This downloads Microsoft's [Nano Server](https://hub.docker.com/_/microsoft-windows-nanoserver) Docker image onto your environment. That image is a minimal Windows server operating system, packaged to run as a Docker container. You can use it as the base for your own apps, or you can run containers from it directly.

Try a simple container, passing a command for the container to run:

```
PS> docker container run mcr.microsoft.com/windows/nanoserver:1809 hostname
a33758b2dbea
```

This runs a new container from the Windows Nano Server image, and tells it to run the `hostname` command. The output is the machine name of the container, which is actually a random ID set by Docker. Repeat the command and you'll see a different host name every time.

## Building and pushing Windows container images

You package your own apps in Docker by building a Docker image. You share the app by pushing the image to a registry - it could be a public registry like [Docker Hub](https://hub.docker.com), or a private registry running in your own environment like [Docker Trusted Registry](https://docs.docker.com/ee/dtr/). Anyone with access to your image can pull it and run containers - just like you did with Microsoft's public Windows Nano Server image.

Pushing images to Docker Hub requires a [free Docker ID](https://hub.docker.com/ "Click to create a Docker ID"). Storing images on Docker Hub is a great way to share applications, or to create build pipelines that move apps from development to production with Docker.

Register for an account, and then save your Docker ID in a variable in your PowerShell session. We will use it in the rest of the lab:

```
$dockerId = '<your-docker-id>'
```

> Be sure to use your own Docker ID here. Mine is `sixeyed`, so the command I run is `$dockerId = 'sixeyed'`.

Docker images are built with the [docker image build](https://docs.docker.com/engine/reference/commandline/image_build/ "docker image build reference") command, using a simple script called a [Dockerfile](https://docs.docker.com/engine/reference/builder/ "Dockerfile reference"). The Dockerfile describes the complete deployment of your application and all its dependencies.

You can generate a very simple Dockerfile with PowerShell:

```
'FROM mcr.microsoft.com/windows/nanoserver:1809' | Set-Content Dockerfile
'CMD echo Hello World!' | Add-Content Dockerfile
```

And now run `docker image build`, giving the image a tag which identifies it with your Docker ID:

```
docker image build --tag $dockerId/hello-world .
```

Run a container from the image, and you'll see it just executes the instruction from the `CMD` line:

```
> docker container run $dockerId/hello-world
Hello World!
```

Now you have a Docker image for a simple Hello World app. The image is the portable unit - you can push the image to Docker Hub, and anyone can pull it and run your app for themselves. First run `docker login` with your credentials, to authenticate with the registry. Then push the image:

```
docker image push $dockerId/hello-world
```

Images stored on Docker Hub are available in the web interface, and public images can be pulled by other Docker users.

### Next Steps

Continue to Step 3: [Multi-Container Applications](MultiContainerApp.md "Multi-Container Applications"), to see how to build and run a web application which uses an ASP.NET Core web application and a SQL Server database - all using Docker Windows containers.
