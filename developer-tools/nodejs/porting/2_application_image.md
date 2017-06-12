# Create the application's image

* We will use 2 images to package the application
  * one image for the database
  * one image for the application

## The application

* There are several possibilities to create the image
  * extend an official Linux distribution image (Ubuntu, CentOS, ...) and install Node.js runtime
  * use the official Node.js image (https://store.docker.com/images/node)

We'll go for the second option as it offers an optimized image.

## Database

* Usage of the official [MongoDB image](https://store.docker.com/images/mongo)

## Dockerfile

We'll use the following Dockerfile to build our application's image:

```
# Use node 4.4.5 LTS
FROM node:4.4.5
ENV LAST_UPDATED 20160605T165400

# Copy source code
COPY . /app

# Change working directory
WORKDIR /app

# Install dependencies
RUN npm install

# Expose API port to the outside
EXPOSE 80

# Launch application
CMD ["npm","start"]
````

Basically, the Dockerfile performs the following actions
* use the official node:4.4.5 (LTS) image
* copy application sources
* install dependencies
* expose port to the outside from the Docker host
* define default command ran when instantiating the image

## Image creation

* Create the image ```docker build -t message-app .```

* List all images available on the Docker host ```docker images```

## Let's instantiate a container

```
$ docker run message-app
npm info it worked if it ends with ok
...
error: A hook (`orm`) failed to load!
error: Error: Failed to connect to MongoDB.  Are you sure your configured Mongo instance is running?
 Error details:
{ [MongoError: connect ECONNREFUSED 127.0.0.1:27017]
  name: 'MongoError',
  message: 'connect ECONNREFUSED 127.0.0.1:27017' }]
  originalError:
   { [MongoError: connect ECONNREFUSED 127.0.0.1:27017]
     name: 'MongoError',
     message: 'connect ECONNREFUSED 127.0.0.1:27017' } }
```

**The application cannot connect to a database as we did not provide external db information nor container running mongodb**


