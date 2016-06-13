# Publish image

The following procedure describes how to create a repository on Docker Hub and publish an image.

## Create repository on Docker Public Registry

* Docker Hub

![hub.docker.com](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_1.png)

* List of user’s repositories

![List of user repository](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_2.png)

* Repository details

![Repository details](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_3.png)

* Repository created

![Repository created](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_4.png)

**the newly created repository will contain all the version of the application’s image**

## Create image

* Image needs to be created using username of the Docker hub account 
```docker build -t lucj/message-app .```

## Push image to Docker Hub

Before publishing an image, authentication must be performed with the following command:
```docker login```

Image can then be published to the user repository
```docker push lucj/message-app```

## Instantiate the image

The image can then be used form any Docker host (the image is public in this example)
  * ```docker pull lucj/message-app```
  * ```docker run -dP lucj/message-app``` (will start with an error as no database information is provided)

