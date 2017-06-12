# Part 2 - Running a Registry Container

There are several ways to run a registry container from the image we built in [Part 1](part-1.md). The simplest is to run an insecure registry over HTTP, but for that we need to configure Docker to explicitly allow insecure access to the registry. 

## Testing the Registry Image

First we'll test that the registry image is working correctly, by running it without any special configuration:

```PowerShell
docker run -d -p 5000:5000 --name registry registry
```

To use the registry we'll need the IP address of the container:

```PowerShell
> $ip = docker inspect --format '{{ .NetworkSettings.Networks.nat.IPAddress }}' registry
> $ip
172.24.194.96
```

In my case, the container IP address is `172.24.194.96`, yours will be different. We'll use that as the domain part of the image name for pushing and pulling images.

## Understanding Image Names

Typically we work with images from the Docker Store, which is the default registry for the Docker Engine. Commands using just the image repository name work fine, like this:

```PowerShell
docker pull microsoft/nanoserver
```

`microsoft/nanoserver` is the repository name, which we are using as a short form of the full image name. The full name is `docker.io/microsoft/nanoserver:latest`. That breaks down into three parts:

- `docker.io` - the hostname of the registry which stores the image;
- `microsoft/nanoserver` - the repository name, in this case in `{userName}/{imageName}` format;
- `latest` - the image tag.

If a tag isn't specified, then the default `latest` is used. If a registry hostname isn't specified then the default `docker.io` for Docker Store is used. If you want to use images with any other registry, you need to explicitly specify the hostname - the default is always Docker Store, you can't change to a different default registry.

With our local registry, the hostname is the IP address, and we also need to specify the custom port we're using. The full registry address is `172.24.194.96:5000`.

## Configuring Docker for the Insecure Registry

Docker expects all registries to run on HTTPS. In the next part of this lab we will run a secure version of our registry container, but the current version runs on HTTP. If you try to use it, Docker will give you an error message like this:

```
http: server gave HTTP response to HTTPS client
```

We need to set up the Docker Engine to explictly use HTTP for our insecure registry. The setting is needed for `dockerd.exe`, which is the Windows version of the Docker engine. In Windows it runs as a Windows Service, so first we need to stop and unregister the service.

> Note. This will kill all your running containers.

```PowerShell
Stop-Service docker
dockerd --unregister-service
```

Now we'll re-register the service with custom startup options, adding the IP address of the registry container as an insecure registry, where the engine will use HTTP rather than HTTPS:

```
dockerd --register-service  -G docker -H npipe:// -H 0.0.0.0:2375 --insecure-registry 172.24.194.96:5000
Start-Service docker
```

Docker is running again now, so we can restart the registry container. When we stopped the Docker Windows Service, the container was stopped, but it still exists with the original IP address, so we can just start it again:

```PowerShell
docker start registry
``` 

The registry is running at the expected address, and we've configured Docker to allow access, so we can push and pull images to our local registry, just like we can with Docker Cloud and Docker Store.

## Pushing and Pulling from the Local Registry

Docker uses the hostname from the full image name to determine which registry to use. We can buid images and include the local registry hostname in the image tag, or use the `docker tag` command to add a new tag to an existing image.

These commands pull a public image from Docker Store, tag it for use in the private registry with the full name `172.24.194.96:5000/labs/hello-world:nanoserver`, and then push it to the registry:

```PowerShell
docker pull sixeyed/hello-world:nanoserver
docker tag sixeyed/hello-world:nanoserver 172.24.194.96:5000/labs/hello-world:nanoserver
docker push 172.24.194.96:5000/labs/hello-world:nanoserver
```

When you push the image to your local registry, you'll see similar output to when you push a public image to the Hub:

```
The push refers to a repository [172.24.194.96:5000/labs/hello-world]
d6826c28b1cd: Pushed
2c195a33d84d: Skipped foreign layer
342d4e407550: Skipped foreign layer
nanoserver: digest: sha256:961497c5ca49dc217a6275d4d64b5e4681dd3b2712d94974b8ce4762675720b4 size: 1149
```

> Note. The two layers from Microsoft's base image are skipped - they don't get stored in the local registry, because the image is not freely redistributable. Check [GitHub issue 27580](https://github.com/moby/moby/issues/27580) for more information.

On the local machine, you can remove the new image tag and the original image, and pull it again from the local registry to verify it was correctly stored:

```
docker rmi 172.24.194.96:5000/labs/hello-world:nanoserver
docker rmi sixeyed/hello-world:nanoserver
docker pull 172.24.194.96:5000/labs/hello-world:nanoserver
```

That exercise shows the registry we built works correctly, but at the moment it's not very useful because all the image data is stored in the container's writeable storage area, which will be lost when the container is removed. To store the data outside of the container, we need to mount a host directory when we start the container.

## Running a Registry Container with External Storage

Let's get rid of the existing registry container - removing the container also removes its storage layer, so any images you had pushed will be lost:

```PowerShell
docker kill registry
docker rm registry
```

We'll use a host-mounted Docker volume for the new container. When the registry server in the container writes image layer data, it will think it's writing to a local directory in the container but it will actually be writing to a folder on the host.

We also need to specify the IP address, because we want to use the same IP the previous registry had - that's what our Docker Engine is configured to use, and it's how we've tagged our images. The `-v` option maps the host folder, and the `--ip` option specifies an IP address:


```PowerShell
mkdir c:\registry-data
docker run -d -p 5000:5000 --name registry -v c:\registry-data:c:\data --ip 172.24.194.96 registry
```

Repeat the previous `docker push` command to upload an image to the registry container, and the layers will be stored in the container's `C:\data` directory, which is actually mapped to the `C:\registry-data` directory on you local machine. The `tree` command will show you the directory structure the registry server uses:

```
> &tree c:\registry-data
Folder PATH listing for volume Windows 2016
Volume serial number is A456-8058
C:\REGISTRY-DATA
└───docker
    └───registry
        └───v2
            ├───blobs
            │   └───sha256
            │       ├───06
            │       │   └───06162e188174e2f0b76a2fd507645ca13b3beb43204cccddf82dcb0251e34fb4
            │       ├───96
            │       │   └───961497c5ca49dc217a6275d4d64b5e4681dd3b2712d94974b8ce4762675720b4
...
```

Storing data outside of the container means we can build a new version of the registry image and replace the old container with a new one using the same host mapping - so the new registry container has all the images stored by the previous container.

This container is still limited though. The container is accessible externally using the host's IP address, but that's different from the container's IP address - so you need to use different tags and a different engine configuration for external clients.

Using an insecure registry also isn't practical in multi-user scenarios. Effectively there's no security so anyone can push and pull images if they know the registry hostname. The registry server supports authentication, but only over a secure SSL connection. We'll run a secure version of the registry server in a container next.

## Next

- [Part 3 - Running a Secured Registry Container](part-3.md)