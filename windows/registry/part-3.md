# Part 3 - Running a Secured Registry Container

We saw how to run a simple registry container in [Part 2](part-2.md), using the image we built in [Part 1](part-1.md). The registry server con be configured to serve HTTPS traffic on a known domain, so it's straightforward to run a secure registry for private use with a self-signed SSL certificate.

## Generating the SSL Certificate

The Docker docs explain how to [generate a self-signed certificate](https://docs.docker.com/registry/insecure/#/using-self-signed-certificates) on Linux using a command like this:

```
openssl req \ 
-newkey rsa:4096 -nodes -sha256 -keyout certs/domain.key \ 
-x509 -days 365 -out certs/domain.crt
```

[OpenSSL](https://www.openssl.org/) is a very popular TLS/SSL toolkit in Linux, but it's less common in Windows. There is a Windows build hosted at [indy.fulgan.com](https://indy.fulgan.com/SSL/), but rather than install it onto our Windows host to run a one-off command, we can use a Docker image with OpenSSL installed.

The [sixeyed/openssl](https://store.docker.com/community/images/sixeyed/openssl) image on Docker Store is built from this [Dockerfile](https://github.com/sixeyed/dockers-windows/blob/master/openssl/Dockerfile), which just installs and configures OpenSSL on top of the Windows Nano Server base image.

We can use it to generate the SSL certificate for the registry with Docker:

```PowerShell
mkdir certs
docker run -it --rm -v $pwd\certs:c:\certs sixeyed/openssl:nanoserver `
       req -newkey rsa:4096 -nodes -sha256 -x509 -days 365 `
       -keyout c:\certs\registry.local.key -out c:\certs\registry.local.crt `
       -subj '/CN=registry.local/O=sixeyed/C=GB' 
```

This will create a certificate file `registry.local.crt`, and a private key file `registry.local.key` in the `certs` subdirectory of the working path. The `-subj` option specifies the details for the domain. This example uses `registry.local` for the common name, which needs to match the hostname address for the registry server. The organization name and country aren't used, but need to be valid values.

Now we have an SSL certificate, we can run a secure registry.

## Running the Registry Securely

The registry server supports several configuration switches as environment variables, including the details for running securely. We can use the same image we've already used, but configured for HTTPS. 

If you have an insecure registry container still running from [Part 2](part-2.md), remove it:

```PowerShell
docker kill registry
docker rm registry
```

For the secure registry, we need to run a container which has the SSL certificate and key files available, which we'll do with an additional volume mount (so we have one volume for registry data, and one for certs). We also need to specify the location of the certificate files, which we'll do with environment variables:

```PowerShell
docker run -d -p 5000:5000 --name registry `
  -v c:\registry-data:c:\data -v $pwd\certs:c:\certs `
  -e REGISTRY_HTTP_TLS_CERTIFICATE=c:\certs\registry.local.crt `
  -e REGISTRY_HTTP_TLS_KEY=c:\certs\registry.local.key `
  registry
```

The new parts to this command are:

- `-v $pwd\certs:c:\certs` - mount the local `certs` folder into the container, so the registry server can access the certificate and key files;
- `-e REGISTRY_HTTP_TLS_CERTIFICATE` - specify the location of the SSL certificate file;
- `-e REGISTRY_HTTP_TLS_KEY` - specify the location of the SSL key file.

We'll let Docker assign a random IP address to this container, because we'll be accessing it by host name. The registry is running securely now, but we've used a self-signed certificate for an internal domain name, so we need to set up Windows to find the host and trust the certificate.

## Configuring Windows to Access the Registry

The Docker client uses the security of the host operating system when it accesses an HTTPS registry. Windows doesn't trust self-signed certificates because they weren't created by a trusted Certificate Authority, so Windows will block access to our secure registry.

In our network we can trust our own certificate, but we'll need to add it to the certificate store in every Windows client machine we want to use with the registry. The `registry.local.crt` file we generated is the public certificate which can be safely distributed - the `registry.local.key` file is the private key which should be kept secure.

These PowerShell commands will install the certificate onto the Windows host:

```PowerShell
$cert = new-object System.Security.Cryptography.X509Certificates.X509Certificate2 `
        $pwd\certs\registry.local.crt
$store = new-object System.Security.Cryptography.X509Certificates.X509Store('Root','localmachine')
$store.Open('ReadWrite')
$store.Add($cert)
$store.Close()
```

> Note. This installs the self-signed certificate as a trusted CA for all users of the machine. If the private key for your cert is compromised then an attacker could exploit the trust you set up here, by signing malicious services with your certificate.

If you want to use the registry from other Windows machines, you'll need to distribute the `.crt` fike (**not** the `.key` file), and install the certificate on all client machines.

The next step is to add a DNS entry for the `registry.local` hostname to point to the container's IP address. The easiest way to do that is by adding an entry to the [hosts](https://en.wikipedia.org/wiki/Hosts_(file)) file:

```PowerShell
Add-Content -Path 'C:\Windows\System32\drivers\etc\hosts' "$ip registry.local"
```

Remote machines will be able to access the registry container from your Docker host, because we publish the port when the container starts - but the hosts entry will be for the IP address of the machine, not the container.

## Accessing the Secure Registry

We're ready to run a secure registry now. We want a reliable service, so we'll remove the existing container and start a new one with some more options:

```PowerShell
docker kill registry
docker rm registry

docker run -d -p 5000:5000 --name registry `
  --ip $ip --restart unless-stopped `
  -v c:\registry-data:c:\data -v $pwd\certs:c:\certs `
  -e REGISTRY_HTTP_TLS_CERTIFICATE=c:\certs\registry.local.crt `
  -e REGISTRY_HTTP_TLS_KEY=c:\certs\registry.local.key `
  registry
```

The new parts here are:

- `--ip $ip` - specify an explicit IP adress. We're using the IP from the previous container, which is what the address we've mapped to the `registry.local` domain name in the `hosts` file;
- `--restart unless-stopped` - restart the container when it exits, unless it has been explicity stopped. When the host restarts, Docker will start the registry container, so it's always available.

Now we have a domain name for our registry, the image tags are a lot more flexible - we don't need a specific IP address in the image name. We still tag, push and pull images in the same way:

```PowerShell
docker tag sixeyed/hello-world:nanoserver registry.local:5000/labs/hello-world
docker push registry.local:5000/labs/hello-world
```

> Note. You can use any valid DNS name for your registry hostname but there must be at least one period in the name - if there's no period then Docker can't distinguish the hostname part from the repository name. The hostname you use has to match the common name you used when you generated the SSL certificate.

The IP address for my Docker host is `192.168.2.196`, so on a *different* machine I can map the registry hostname to that address by writing to the host file, and install the certificate which I've copied locally:

```PowerShell
Add-Content -Path 'C:\Windows\System32\drivers\etc\hosts', "192.168.2.196 registry.local"

$cert = new-object System.Security.Cryptography.X509Certificates.X509Certificate2 `
        c:\drops\registry.local.crt
$store = new-object System.Security.Cryptography.X509Certificates.X509Store('Root','localmachine')
$store.Open('ReadWrite')
$store.Add($cert)
$store.Close()
```

And from that machine (with Docker installed), I can pull the image from the registry container running on the remote machine on my local network:

```PowerShell 
> docker pull registry.local:5000/labs/hello-world
Unable to find image 'registry.local:5000/labs/hello-world:latest' locally
latest: Pulling from labs/hello-world

5496abde368a: Already exists
94b4ce7ac4c7: Pull complete
06162e188174: Pull complete
Digest: sha256:961497c5ca49dc217a6275d4d64b5e4681dd3b2712d94974b8ce4762675720b4
Status: Downloaded newer image for registry.local:5000/labs/hello-world:latest
```

In this case, the client machine already had one of the Windows Nano Server base layers (`5496a`), but it pulled an update layer from Docker Store (`94b4c`), and it pulled the custom layer for my image from my own registry (`06162`).

We can go one step further with the open-source registry server, and add basic authentication - so we can require users to securely log in to push and pull images.

## Next

- [Part 4 - Using Basic Authentication with a Secured Registry](part-4.md)