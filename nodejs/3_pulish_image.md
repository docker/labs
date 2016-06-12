# Why image should be published ?

* provide access to the packaged application
  * public or private access
* possible to use tags to handle all the versions of the application
  * format ⇒ username/image:tag (note: official images do not have the username prefix, eg: mongo, redis, ...)
    * mongo:3.2
    * lucj/message-app (same as lucj/message-app:latest)
* GitHub account can be linked to Docker hub
  * build can be automatically triggered on a  git push command
  
# Create repository on Docker Public Registry

* hub.docker.com

![hub.docker.com](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_1.png)

* list of user’s repositories

![User repos](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_2.png)

* repository details

![Repo details](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_3.png)

* repository created

![Repo created](https://dl.dropboxusercontent.com/u/2330187/docker/labs/node/registry_4.png)

**the newly created repository will contain all the version of the application’s image**

# Publish image

* image needs to be created using username of the Docker hub account 
```docker build -t lucj/message-app .```

* identification
```docker login```

* publication
```docker push lucj/message-app```

* the image (public) can now be used from any Docker host
  * ```docker pull lucj/message-app```
  * ```docker run -dP lucj/message-app (will start with an error as no database information is provided)```

