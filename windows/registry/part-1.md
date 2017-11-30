# Part 1 - Building the Registry Image

Docker provides an [official registry image](https://store.docker.com/images/registry) on the Hub, but currently it is only available as a Linux image. The application is written in Go so it can be compiled for Windows and run as a container on Windows 10 and Windows Server 2016.

> Note. Expect the official image to have a Windows variant soon, but this part of the lab is still useful if you want to build from the latest source code yourself.

## Building from Source

The Go source code for the registry is on GitHub in Docker's [distribution](https://github.com/docker/distribution) repository. Building Go applications is simple with the [go command](https://golang.org/doc/articles/go_command.html), we just need to install the Go tools and run:

```PowerShell
go get github.com/docker/distribution/cmd/registry
```

On Windows the build will produce a binary file, `registry.exe`, which is a standalone executable. We can package that exe into a Docker image which doesn't need Go installed, so our Windows registry image will be small and focused.

We won't install Go and Git on our local machine though, instead we'll use the [Docker Builder pattern](http://blog.terranillius.com/post/docker_builder_pattern/) and package up the toolset into an image we can use to build the registry on any Windows host running Docker.

## Dockerfile for the Registry Builder

The Dockerfile for the registry builder is in [Dockerfile.builder](Dockerfile.builder), and it's very simple. It begins in the usual way for Windows Dockerfiles - overriding the escape character and specifying PowerShell to use as the command shell:

```Dockerfile
# escape=`
FROM golang:windowsservercore
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]
```

The base image is the [official Go image](https://store.docker.com/images/golang) which is in the Docker Store.

There's a single `CMD` instruction to build the latest version of the registry and copy the built files to a known output location:

```Dockerfile
CMD go get github.com/docker/distribution/cmd/registry ; `
    cp \"$env:GOPATH\bin\registry.exe\" c:\out\ ; `
    cp \"$env:GOPATH\src\github.com\docker\distribution\cmd\registry\config-example.yml\" c:\out\config.yml
```

When we run a container from the builder image, it will compile the latest registry code, and we can map the output location to a directory on the host to get the application files.

## Building the Registry Server

First we build the builder image - all the images in this lab use [microsoft/windowsservercore](https://store.docker.com/images/windowsservercore) or [microsoft/nanoserver](https://store.docker.com/images/nanoserver) as the base images, so you can only use a Windows host to build and run them. From a PowerShell session, navigate to the lab folder and build the builder:

```PowerShell
docker build -t registry-builder -f Dockerfile.builder .
```

When you first run this command, Docker will download the `sixeyed/golang` image, which will take a while. When it's done, you can create a directory for the application, and run a builder container to build the application and copy it to the host:

```PowerShell
mkdir registry
docker run --rm -v $pwd\registry:c:\out registry-builder
```

Now you'll have two files in the `.\registry` folder in the lab root:

- `registry.exe` - the standalone registry server application (around 18MB);
- `config.yml` - a basic configuration file for running a local registry (<1KB).

These are the files we'll package into a registry server image.

## Dockerfile for the Registry Server

The [Dockerfile](Dockerfile) for the registry image is also very simple. It starts in the standard way and uses `microsoft/nanoserver` as the base, because we don't need any of the additional features in `microsoft/windowsservercore`:

```Dockerfile
# escape=`
FROM microsoft/nanoserver
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]
```

Next we set up the integration between the container and the host:

```Dockerfile
EXPOSE 5000
ENV REGISTRY_STORAGE_FILESYSTEM_ROOTDIRECTORY=c:\\data
```

Port 5000 is the default port for the registry, so we make that available from the image, and we specify a value for the `REGISTRY_STORAGE_FILESYSTEM_ROOTDIRECTORY` environment variable. That's where the registry stores all the image layers, and we default to a known location, `c:\data`.

Finally we copy the applcation files into the working directory and set the startup command:

```Dockerfile
WORKDIR c:\\registry
COPY ./registry/ .
CMD ["registry", "serve", "config.yml"]
```

To build the image, we just need to run `docker build`:

```PowerShell
docker build -t registry .
```

The Docker image for the registry running on Nano Server is 310MB compressed and 830MB uncompressed - of which 810MB is the `microsoft/nanoserver` base layer. 

## Next

- [Part 2 - Running a Registry Container](part-2.md)
