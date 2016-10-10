## 3.0 Run a multi-container app with Docker Compose
This portion of the tutorial will guide you through the creation and customization of a voting app. It's important that you follow the steps in order, and make sure to customize the portions that are customizable.

**Important.**
To complete this section, you will need to have Docker and Docker Compose installed on your machine as mentioned in the [Setup](./setup.md) section. You'll also need have git installed. There are many options for installing it. For instance, you can get it from [GitHub](https://help.github.com/articles/set-up-git/).

You'll also need to have a [Docker Id](https://hub.docker.com/register/). Once you do run login from the commandline:

```
$ docker login
```

And follow the login directions. Now you can push images to Docker Hub.


### 3.1 Get the voting-app
You now know how to build your own Docker image, so let's take it to the next level and glue things together. For this app you have to run multiple containers and Docker Compose is the best way to do that.

Start by quickly reading the documentation [here](https://docs.docker.com/compose/overview/).

Clone the voting-app repository already available at [Github Repo](https://github.com/docker/example-voting-app.git).

```
git clone https://github.com/docker/example-voting-app.git
```

### 3.2 Customize the app

#### 3.2.1 Modify app.py

In the folder ```example-voting-app/voting-app``` you need to edit the app.py and change the two options for the programming languages you chose.

Edit the following lines:

```
option_a = os.getenv('OPTION_A', "Cats")
option_b = os.getenv('OPTION_B', "Dogs")
```

substituting two options of your choice. For instance:

```
option_a = os.getenv('OPTION_A', "Java")
option_b = os.getenv('OPTION_B', ".NET")
```
#### 3.2.2 Running your app
Now, run your application. To do that, we'll use [Docker Compose](https://docs.docker.com/compose). Docker Compose is a tool for defining and running multi-container Docker applications. With Compose, you define a `.yml` file that describes all the containers and volumes that you want, and the networks between them. In the example-voting-app directory, you'll see a `docker-compose.yml file`:

```yml
version: "2"

services:
  voting-app:
    build: ./voting-app/.
    volumes:
     - ./voting-app:/app
    ports:
      - "5000:80"
    networks:
      - front-tier
      - back-tier

version: "2"

services:
  vote:
    build: ./vote
    command: python app.py
    volumes:
     - ./vote:/app
    ports:
      - "5000:80"

  redis:
    image: redis:alpine
    ports: ["6379"]

  worker:
    build: ./worker

  db:
    image: postgres:9.4

  result:
    build: ./result
    command: nodemon --debug server.js
    volumes:
      - ./result:/app
    ports:
      - "5001:80"
      - "5858:5858"
```

This Compose file defines

- A voting-app container based on a Python image
- A result-app container based on a Node.js image
- A redis container based on a redis image, to temporarily store the data.
- A .NET based worker app based on a .NET image
- A Postgres container based on a postgres image

Note that three of the containers are built from Dockerfiles, while the other two are images on Docker Hub. To learn more about how they're built, you can examine each of the Dockerfiles in the three directories: `vote`, `result`, `worker`. 

The Compose file also defines two networks, front-tier and back-tier. Each container is placed on one or two networks. Once on those networks, they can access other services on that network in code just by using the name of the service. To learn more about networking check out the [Networking with Compose documentation](https://docs.docker.com/compose/networking/).

To launch your app navigate to the example-voting-app directory and run the following command:

```
$ docker-compose up -d
```

This tells Compose to start all the containers specified in the `docker-compose.yml` file. The `-d` tells it to run them in daemon mode, in the background. Navigate to `http://localhost:5000` in your browser, and you'll see the voting app, something like this:

<img src="../images/vote.png" title="vote">

Click on one to vote. You can check the results at `http://<YOUR_IP_ADDRESS:5001>`.

**NOTE**: If you are running this tutorial in a cloud environment like AWS, Azure, Digital Ocean, or GCE you will not have direct access to localhost or 127.0.0.1 via a browser.  A work around for this is to leverage ssh port forwarding.  Below is an example for Mac OS.  Similarly this can be done for Windows and Putty users.

```
$ ssh -L 5000:localhost:5000 <ssh-user>@<CLOUD_INSTANCE_IP_ADDRESS>
```

#### 3.2.3 Build and tag images

You are all set now. Navigate to each of the directories where you have a Dockerfile to build and tag your images that you want to submit.

In order to build the images, make sure to replace `<YOUR_DOCKER_ID>` with your *Docker Hub username* in the following commands:

```
$ docker build --no-cache -t <YOUR_DOCKER_ID>/votingapp_voting-app .
...
$ docker build --no-cache -t <YOUR_DOCKER_ID>/votingapp_result-app .
...
$ docker build --no-cache -t <YOUR_DOCKER_ID>/votingapp_worker .
```

#### 3.2.4 Push images to Docker Hub

Push the images to Docker hub. Remember, you must have run `docker login` before you can push.

```
$ docker push <YOUR_DOCKER_ID>/votingapp_voting-app
...
$ docker push <YOUR_DOCKER_ID>/votingapp_result-app
...
$ docker push <YOUR_DOCKER_ID>/votingapp_worker
```

Now you can access these images anywhere by running

```
$ docker pull <YOUR_DOCKER_ID>/votingapp_voting-app
$ docker pull <YOUR_DOCKER_ID>/votingapp_result-app
$ docker pull <YOUR_DOCKER_ID>/votingapp_worker
```

### 3.3 Next steps
Now that you've built some images and pushed them to hub, and learned about Docker Compose, you can explore more of Docker by checking out [the documentation](https://docs.docker.com). And if you need any help, check out the [Docker Forums](forums.docker.com) or [StackOverflow](https://stackoverflow.com/tags/docker/).
