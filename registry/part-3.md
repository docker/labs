# Part 3 - Using Basic Authentication with a Secured Registry in Linux

From [Part 2](part-2.md) we have a registry running in a Docker container, which we can securely access over HTTPS from any machine in our network. We used a self-signed certificate, which has security implications, but you could buy an SSL from a CA instead, and use that for your registry. With secure communication in place, we can set up user authentication.

## Usernames and Passwords

The registry server and the Docker client support [basic authentication](https://en.wikipedia.org/wiki/Basic_access_authentication) over HTTPS. The server uses a file with a collection of usernames and encrypted passwords. The file uses a common standard from the Linux world - [Apache htpasswd].

Create the password file with an entry for user "moby" with password "DockerRules";
```
> mkdir auth
> docker run --entrypoint htpasswd registry:latest -bn moby DockerRules > auth/htpasswd
```
The options are:

- --entrypoint Overwrite the default ENTRYPOINT of the image
- -b run in batch mode 
- -n display results

We can verify the entries have been written by checking the file contents - which should show the usernames in plain text and a cipher text password:

```PowerShell
> cat auth/htpasswd
moby:$apr1$xnxYmr0O$S4HXd0ACkZkpp40YCw/lW/
```

## Running an Authenticated Secure Registry

Adding authentication to the registry is a similar process to adding SSL - we need to run the registry with access to the `htpasswd` file on the host, and configure authentication using environment variables.

As before, we'll remove the existing container and run a new one with authentication configured:

```
> docker kill registry
> docker rm registry

> docker run -d -p 5000:5000 --name registry \
  --restart unless-stopped \
  -v $(pwd)/registry-data:/var/lib/registry \
  -v $(pwd)/certs:/certs \
  -v $(pwd)/auth:/auth \
  -e REGISTRY_HTTP_TLS_CERTIFICATE=/certs/domain.crt \
  -e REGISTRY_HTTP_TLS_KEY=/certs/domain.key \
  -e REGISTRY_AUTH=htpasswd \
  -e REGISTRY_AUTH_HTPASSWD_REALM='Registry Realm' \
  -e REGISTRY_AUTH_HTPASSWD_PATH=/auth/htpasswd \
  registry
```

The new options for this container are:

- `-v $(pwd)/auth:/auth` - mount the local `auth` folder into the container, so the registry server can access `htpasswd` file;
- `-e REGISTRY_AUTH=htpasswd` - use the registry's `htpasswd` authentication method;
- `-e REGISTRY_AUTH_HTPASSWD_REALM='Registry Realm'` - specify the authentication realm;
- `-e REGISTRY_AUTH_HTPASSWD_PATH=/auth/htpasswd` - specify the location of the `htpasswd` file.

Now the registry is using secure transport and user authentication.

## Authenticating with the Registry

With basic authentication, users cannot push or pull from the registry unless they are authenticated. If you try and pull an image without authenticating, you will get an error:

```PowerShell
> docker pull registry.local:5000/labs/hello-world
Using default tag: latest
Error response from daemon: Get https://registry.local:5000/v2/labs/hello-world/manifests/latest: no basic auth credentials
```

The result is the same for valid and invalid image names, so you can't even check a repository exists without authenticating. Logging in to the registry is the same `docker login` command you use for Docker Hub, specifying the registry hostname:

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

> Note. The open-source registry does not support the same authorization model as Docker Hub or Docker Trusted Registry. Once you are logged in to the registry, you can push and pull from any repository, there is no restriction to limit specific users to specific repositories.

## Conclusion

[Docker Registry](https://docs.docker.com/registry/) is a free, open-source application for storing and accessing Docker images. You can run the registry in a container on your own network, or in a virtual network in the cloud, to host private images with secure access. For Linux hosts, there is an [official registry image](https://hub.docker.com/_/registry/) on Docker Hub.

We've covered all the options, from running an insecure registry, through adding SSL to encrypt traffic, and finally adding basic authentication to restrict access. By now you know how to set up a usable registry in your own environment, and you've also used some key Docker patterns - using containers as build agents and to run basic commands, without having to install software on your host machines. 

There is still more you can do with Docker Registry - using a different [storage driver](https://docs.docker.com/registry/storage-drivers/) so the image data is saved to reliable share storage, and setting up your registry as a [caching proxy for Docker Hub](https://docs.docker.com/registry/recipes/mirror/) are good next steps.