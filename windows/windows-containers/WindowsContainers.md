## Getting Started with Windows Containers

This chapter will cover the basics of using Windows Containers with Docker.

##Running Windows containers

First, make sure the Docker installation is working:

```
> docker version
Client:
 Version:      1.12.2
 API version:  1.24
 Go version:   go1.6.3
 Git commit:   bb80604
 Built:        Tue Oct 11 05:27:08 2016
 OS/Arch:      windows/amd64
 Experimental: true

Server:
 Version:      1.12.2-cs2-ws-beta
 API version:  1.25
 Go version:   go1.7.1
 Git commit:   050b611
 Built:        Tue Oct 11 02:35:40 2016
 OS/Arch:      windows/amd64
```

Next, pull a base image that’s compatible with the evaluation build, re-tag it and do a test-run:

```
docker pull microsoft/windowsservercore:10.0.14393.321
docker tag microsoft/windowsservercore:10.0.14393.321 microsoft/windowsservercore
docker run microsoft/windowsservercore hostname
69c7de26ea48
```

## Building and pushing Windows container images

Pushing images to Docker Cloud requires a [free Docker ID](https://cloud.docker.com/ "Click to create a Docker ID"). Storing images on Docker Cloud is a great way to save build artifacts for later user, to share base images with co-workers or to create build-pipelines that move apps from development to production with Docker.

Docker images are typically built with [docker build](https://docs.docker.com/engine/reference/commandline/build/ "docker build reference") from a [Dockerfile](https://docs.docker.com/engine/reference/builder/ "Dockerfile reference") recipe, but for this example, we’re going to just create an image on the fly in PowerShell.

```
"FROM microsoft/windowsservercore `n CMD echo Hello World!" | docker build -t <docker-id>/windows-test-image -
```

Test the image:

```
docker run <docker-id>/windows-test-image
Hello World!
```

Login with `docker login` and then push the image:

```
docker push <docker-id>/windows-test-image
```

Images stored on Docker Cloud are available in the web interface and public images can be pulled by other Docker users.

### Next Steps
Continue to Step 3: [Multi-Container Applications](MultiContainerApp.md "Multi-Container Applications")




