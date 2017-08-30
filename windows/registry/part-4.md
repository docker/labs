# Part 4 - Using Basic Authentication with a Secured Registry

From [Part 3](part-3.md) we have a registry running in a Docker container, which we can securely access over HTTPS from any machine in our network. We used a self-signed certificate, which has security implications, but you could buy an SSL from a CA instead, and use that for your registry. With secure communication in place, we can set up user authentication.

## Usernames and Passwords

The registry server and the Docker client support [basic authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) over HTTPS. The server uses a file with a collection of usernames and encrypted passwords. The file uses a common standard from the Linux world - [Apache htpasswd](https://httpd.apache.org/docs/current/programs/htpasswd.html), but as usual we don't want to install Apache on our local machine to use one tool.

On the Docker Store, the repository [sixeyed/httpd](https://store.docker.com/community/images/sixeyed/httpd) ([Dockerfile](https://github.com/sixeyed/dockers-windows/blob/master/httpd/Dockerfile)) is configured with Apache and the associated tools already installed. We can use that image to run `htpasswd` and generate the encrypted strings. These commands create a new `auth` subdirectory and a new `registry.htpasswd` file with one set of credentials:

```PowerShell
mkdir auth
$creds = docker run --rm sixeyed/httpd:windowsservercore htpasswd -b -n -B elton d0cker
Add-Content -Path .\auth\registry.htpasswd $creds[0]
```

> Note. The output from `htpasswd` contains additional whitespace which we need to remove - we only write the first line of output in the text file.

The [htpasswd options](https://httpd.apache.org/docs/current/programs/htpasswd.html) will create the encrypted password in a suitable format for the registry server:

- `-b` - use bcrypt encryption
- `-n` - read username and password from the command line
- `elton` - username
- `d0cker` - password.

To add new users, repeat the command to append a new entry to the existing file. This adds a new user `francis`, with password `r3gL4b`:

```PowerShell
$creds = docker run --rm sixeyed/httpd:windowsservercore htpasswd -nb -B francis r3gL4b 
Add-Content -Path .\auth\registry.htpasswd $creds[0]
``` 

We can verify the entries have been written by checking the file contents - which should show the usernames in plain text and a cipher text password:

```PowerShell
> cat .\auth\registry.htpasswd
elton:$2y$05$saIVQ.pV.9EPOsLpNP04puj0Mf9r2wMHeGg/XViGhPfdoxb1oaCPO
francis:$2y$05$CWobimW8aoKSCLf4cp9lGulzAXxPfUIqc452PdArxPfyK8zPEOG9a
```

## Running an Authenticated Secure Registry

Adding authentication to the registry is a similar process to adding SSL - we need to run the registry with access to the `htpasswd` file on the host, and configure authentication using environment variables.

As before, we'll remove the existing container and run a new one with authentication configured:

```PowerShell
docker kill registry
docker rm registry

docker run -d -p 5000:5000 --name registry `
  --ip $ip --restart unless-stopped `
  -v c:\registry-data:c:\data -v $pwd\certs:c:\certs -v $pwd\auth:c:\auth `
  -e REGISTRY_HTTP_TLS_CERTIFICATE=c:\certs\registry.local.crt `
  -e REGISTRY_HTTP_TLS_KEY=c:\certs\registry.local.key `
  -e REGISTRY_AUTH=htpasswd `
  -e REGISTRY_AUTH_HTPASSWD_REALM='Registry Realm' `
  -e REGISTRY_AUTH_HTPASSWD_PATH=c:\auth\registry.htpasswd `
  registry
```

The new options for this container are:

- `-v $pwd\auth:c:\auth` - mount the local `auth` folder into the container, so the registry server can access `htpasswd` file;
- `-e REGISTRY_AUTH=htpasswd` - use the registry's `htpasswd` authentication method;
- `-e REGISTRY_AUTH_HTPASSWD_REALM='Registry Realm'` - specify the authentication realm;
- `-e REGISTRY_AUTH_HTPASSWD_PATH=c:\auth\registry.htpasswd` - specify the location of the `htpasswd` file.

Now the registry is using secure transport and user authentication.

## Authenticating with the Registry

With basic authentication, users cannot push or pull from the registry unless they are authenticated. If you try and pull an image without authenticating, you will get an error:

```PowerShell
> docker pull registry.local:5000/labs/hello-world
Using default tag: latest
Error response from daemon: Get https://registry.local:5000/v2/labs/hello-world/manifests/latest: no basic auth credentials
```

The result is the same for valid and invalid image names, so you can't even check a repository exists without authenticating. Logging in to the registry is the same `docker login` command you use for Docker Store, specifying the registry hostname:

```PowerShell
> docker login registry.local:5000
Username: elton
Password:
Login Succeeded
```

If you use the wrong password or a username that doesn't exist, you get a `401` error message:

```
Error response from daemon: login attempt to https://registry.local:5000/v2/ failed with status: 401 Unauthorized
```

Now you're authenticated, you can push and pull as before:

```PowerShell
> docker pull registry.local:5000/labs/hello-world
Using default tag: latest
latest: Pulling from labs/hello-world
Digest: sha256:961497c5ca49dc217a6275d4d64b5e4681dd3b2712d94974b8ce4762675720b4
Status: Image is up to date for registry.local:5000/labs/hello-world:latest
```

> Note. The open-source registry does not support the same authorization model as Docker Store or Docker Trusted Registry. Once you are logged in to the registry, you can push and pull from any repository, there is no restriction to limit specific users to specific repositories.

## Conclusion

[Docker Registry](https://docs.docker.com/registry/) is a free, open-source application for storing and accessing Docker images. You can run the registry in a container on your own network, or in a virtual network in the cloud, to host private images with secure access. For Linux hosts, there is an [official registry image](https://store.docker.com/images/registry) on Docker Store, but in this lab we saw how to build and run the registry from the lastest source code, in a Windows container.

We've covered all the options, from running an insecure registry, through adding SSL to encrypt traffic, and finally adding basic authentication to restrict access. By now you know how to set up a usable registry in your own environment, and you've also used some key Docker patterns - using containers as build agents and to run basic commands, without having to install software on your host machines. 

There is still more you can do with Docker Registry - using a different [storage driver](https://docs.docker.com/registry/storage-drivers/) so the image data is saved to reliable share storage, and setting up your registry as a [caching proxy for Docker Store](https://docs.docker.com/registry/recipes/mirror/) are good next steps.