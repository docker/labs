# Beginning ASP.NET Web application

A simple example using asp.net to serve a web page using kestrel. First `clone` this repository, and then you can use either docker-compose or docker run to start the image.

```
$ git clone https://github.com/docker/labs
$ cd labs/windows/aspnet-web
$ docker-compose up
```

or

```
$ git clone https://github.com/docker/labs
$ cd labs/windows/aspnet-web/webserver
$ docker build -t myaspnet .
$ docker run myaspnet
```
Then open up [http://localhost:5000](http://localhost:5000)