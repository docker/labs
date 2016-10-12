# Hello World Dotnet

A hello world example using Docker with Microsoft dotnet. To run the application:

```
$ docker build -t dotnetbot .
$ docker run -it dotnetbot
```

The application is a console application that is compiled when the Docker image is created. 