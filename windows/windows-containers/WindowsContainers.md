## Getting Started with Windows Containers

This chapter will cover the basics of using Windows Containers with Docker.

## Running Windows containers

First, make sure the Docker installation is working correctly by running `docker version`. The output should tell you the basic details about your Docker environment:

```
> docker version
Client:
 Version:      17.06.1-ee-1
 API version:  1.30
 Go version:   go1.8.3
 Git commit:   4dd6e94
 Built:        Sat Aug 12 01:34:13 2017
 OS/Arch:      windows/amd64

Server:
 Version:      17.06.1-ee-1
 API version:  1.30 (minimum version 1.24)
 Go version:   go1.8.3
 Git commit:   4dd6e94
 Built:        Sat Aug 12 02:14:08 2017
 OS/Arch:      windows/amd64
 Experimental: true
```
> The `OS/Arch` field tells you the operating system you're using. Docker is cross-platform, so you can manage Windows Docker servers from a Linux client and vice-versa, using the same `docker` commands.

Next, pull a Docker image which you can use to run a Windows container:

```
docker image pull microsoft/windowsservercore
```

This downloads Microsoft's [Windows Server Core](https://store.docker.com/images/windowsservercore) Docker image onto your environment. That image is a full deployment of Windows Server 2016 Core edition (with no UI), packaged to run as a Docker container. You can use it as the base for your own apps, or you can run containers from it directly.

Try a simple container, passing a command for the container to run:

```
docker container run microsoft/windowsservercore hostname
69c7de26ea48
```

This runs a new container from the Windows Server Core image, and tells it to run the `hostname` command. The output is the machine name of the container, which is actually a random ID set by Docker. Repeat the command and you'll see a different host name every time.

## Building and pushing Windows container images

You package your own apps in Docker by building a Docker image. You share the app by pushing the image to a registry - it could be a public registry like [Docker Cloud](https://cloud.docker.com), or a private registry running in your own environment like [Docker Trusted Registry](https://docs.docker.com/datacenter/dtr/2.0/). Anyone with access to your image can pull it and run containers - just like you did with Microsoft's public Windows Server Core image.

Pushing images to Docker Cloud requires a [free Docker ID](https://cloud.docker.com/ "Click to create a Docker ID"). Storing images on Docker Cloud is a great way to share applications, or to create build pipelines that move apps from development to production with Docker.

Register for an account, and then save your Docker ID in a variable in your PowerShell session. We will use it in the rest of the lab:

```
$dockerId = '<your-docker-id>'
```

> Be sure to use your own Docker ID here. Mine is `sixeyed`, so the command I run is `$dockerId = 'sixeyed'`.

Docker images are built with the [docker image build](https://docs.docker.com/engine/reference/commandline/image_build/ "docker image build reference") command, using a simple script called a [Dockerfile](https://docs.docker.com/engine/reference/builder/ "Dockerfile reference"). The Dockerfile describes the complete deployment of your application and all its dependencies.

You can generate a very simple Dockerfile with PowerShell:

```
'FROM microsoft/windowsservercore' | Set-Content Dockerfile
'CMD echo Hello World!' | Add-Content Dockerfile
```

And now run `docker image build`, giving the image a tag which identifies it with your Docker ID:

```
docker image build --tag $dockerId/hello-world .
```

Run a container from the image, and you'll see it just executes the instruction from the `CMD` line:

```
docker container run $dockerId/hello-world
Hello World!
```

Now you have a Docker image for a simple Hello World app. The image is the portable unit - you can push the image to Docker Cloud, and anyone can pull it and run your app for themselves. First run `docker login` with your credentials, to authenticate with the registry. Then push the image:

```
docker image push $dockerId/hello-world
```

Images stored on Docker Cloud are available in the web interface and public images can be pulled by other Docker users.

### Next Steps

Continue to Step 3: [Multi-Container Applications](MultiContainerApp.md "Multi-Container Applications"), to see how to build and run a web application which uses a SQL Server database - all using Docker Windows containers.
