# One image for application, on image for database

avoid to add too many services in a single image

usage of 2 images to package the application
one image for the database
one image for the application

application: several possibilities
extend official Linux distribution image (Ubuntu, CentOS, ...) with Node.js runtime
usage of the official Node.js image (https://hub.docker.com/_/node/)

Database
usage of the official MongoDB image

# Dockerfile

text file describing all the commands needed to create an image
Dockerfile for our application
usage of the official node:4.4.5 (LTS) image
copy application sources
install dependencies
expose port to the outside from the Docker host
default command ran when instantiating the image


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
PORT 80
EXPOSE 80

# Launch application
CMD ["npm","start"]

Create the image
docker build -t message-app .

List all images available on the Docker host
	docker images

⇒ message-app image created

# Let's instantiate a container

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

The application cannot connect to a database as we did not provide external db information nor container running mongodb


