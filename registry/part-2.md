# Part 2 - Running a Secured Registry Container in Linux

We saw how to run a simple registry container in [Part 1](part-1.md), using the official Docker registry image. The registry server con be configured to serve HTTPS traffic on a known domain, so it's straightforward to run a secure registry for private use with a self-signed SSL certificate.

## Generating the SSL Certificate in Linux

The Docker docs explain how to [generate a self-signed certificate](https://docs.docker.com/registry/insecure/#/using-self-signed-certificates) on Linux using OpenSSL:

```
$ mkdir -p certs 
$ openssl req \ 
  -newkey rsa:4096 -nodes -sha256 -keyout certs/domain.key \  
  -x509 -days 365 -out certs/domain.crt
Generating a 4096 bit RSA private key
........++
............................................................++
writing new private key to 'certs/domain.key'
-----
You are about to be asked to enter information that will be incorporated
into your certificate request.
What you are about to enter is what is called a Distinguished Name or a DN.
There are quite a few fields but you can leave some blank
For some fields there will be a default value,
If you enter '.', the field will be left blank.
-----
Country Name (2 letter code) [AU]:US
State or Province Name (full name) [Some-State]:
Locality Name (eg, city) []:
Organization Name (eg, company) [Internet Widgits Pty Ltd]:Docker
Organizational Unit Name (eg, section) []:
Common Name (e.g. server FQDN or YOUR name) []:localhost
Email Address []:
```
If you are running the registry locally, be sure to use your host name as the CN. 

To get the docker daemon to trust the certificate, copy the domain.crt file.
```
$ sudo su
$ mkdir /etc/docker/certs.d
$ mkdir /etc/docker/certs.d/<localhost>:5000 
$ cp `pwd`/certs/domain.crt /etc/docker/certs.d/<localhost>:5000/ca.crt
```
Make sure to restart the docker daemon.
```
$ sudo service docker restart
```
Now we have an SSL certificate and can run a secure registry.

## Running the Registry Securely

The registry server supports several configuration switches as environment variables, including the details for running securely. We can use the same image we've already used, but configured for HTTPS. 

If you have an insecure registry container still running from [Part 2](part-2.md), remove it:

```
$ docker kill registry
$ docker rm registry
```

For the secure registry, we need to run a container which has the SSL certificate and key files available, which we'll do with an additional volume mount (so we have one volume for registry data, and one for certs). We also need to specify the location of the certificate files, which we'll do with environment variables:

```
$ mkdir registry-data
$ docker run -d -p 5000:5000 --name registry \
  --restart unless-stopped \
  -v $(pwd)/registry-data:/var/lib/registry -v $(pwd)/certs:/certs \
  -e REGISTRY_HTTP_TLS_CERTIFICATE=/certs/domain.crt \
  -e REGISTRY_HTTP_TLS_KEY=/certs/domain.key \
  registry
```

The new parts to this command are:

- `--restart unless-stopped` - restart the container when it exits, unless it has been explicitly stopped. When the host restarts, Docker will start the registry container, so it's always available.
- `-v $pwd\certs:c:\certs` - mount the local `certs` folder into the container, so the registry server can access the certificate and key files;
- `-e REGISTRY_HTTP_TLS_CERTIFICATE` - specify the location of the SSL certificate file;
- `-e REGISTRY_HTTP_TLS_KEY` - specify the location of the SSL key file.

We'll let Docker assign a random IP address to this container, because we'll be accessing it by host name. The registry is running securely now, but we've used a self-signed certificate for an internal domain name.

## Accessing the Secure Registry

We're ready to push an image into our secure registry. 
```
$ docker push localhost:5000/hello-world
$ docker pull localhost:5000/hello-world
```
We can go one step further with the open-source registry server, and add basic authentication - so we can require users to securely log in to push and pull images.

## Next

- [Part 3 - Using Basic Authentication with a Secured Registry](part-3.md)