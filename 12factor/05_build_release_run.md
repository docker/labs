# 5 - Build / Release / Run

Build / Release and Run phases must be kept separated

![Build/Release/Run](https://dl.dropboxusercontent.com/u/2330187/docker/labs/12factor/build_release_run.png)

A release is deployed on the execution environment and must be immutable.

## What does that mean for our application ?

We'll use Docker in the whole development pipeline. We will start by adding a Dockerfile that will help define the build phase (during which the dependencies are compiled in _node-modules_ folder)

```
FROM node:4.4.5
ENV LAST_UPDATED 20160617T185400

# Copy source code
COPY . /app

# Change working directory
WORKDIR /app

# Install dependencies
RUN npm install

# Expose API port to the outside
ENV PORT 80
EXPOSE 80

# Launch application
CMD ["npm","start"]
```

Let's build our application `$ docker build -t message-app:v0.1 .`

And verify the resulting image is in the list of available images

```
$ docker images
REPOSITORY        TAG           IMAGE ID           CREATED             SIZE
message-app       v0.1          f35464cf4b0b       2 seconds ago       769 MB
```

Now the image (build) is available, execution environment must be injected to create a release.

There are several options to inject the configuration in the build, among them
* create a new image based on the build
* define a Compose file

We'll go for the second option and define a docker-compose file where the MONGO_URL will be set with the value of the execution environment

```
version: '3'
services:
  mongo:
    image: mongo:3.2
    volumes:
      - mongo-data:/data/db
    expose:
      - "27017"
  app:
    image: message-app:v0.1
    ports:
      - "8000:80"
    links:
      - mongo
    depends_on:
      - mongo
    environment:
      - MONGO_URL=mongodb://mongo/messageApp
volumes:
  mongo-data:
```

This file defines a release as it considers a given build and inject the execution environment.

The run phase can be done manually with Compose CLI or through an orchestrator (Docker Cloud).

Compose CLI enables to run the global application as simple as `docker-compose up -d`

[Previous](04_external_services.md) - [Next](06_processes.md)
