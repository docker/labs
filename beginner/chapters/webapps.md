## 2.0 Webapps with Docker
Great! So you have now looked at `docker run`, played with a docker container and also got the hang of some terminology. Armed with all this knowledge, you are now ready to get to the real-stuff i.e. deploying web applications with Docker.

### 2.1 Static Sites
>**Note:** Code for this section is in this repo in the [static-site directory](https://github.com/docker/labs/tree/master/beginner/static-site).

Let's start by taking baby-steps. First, we'll use Docker to run a dead-simple static website. You're going to pull a Docker image from the Docker Hub, run the container and see how easy it is to set up a web server.

The image that you are going to use is a single-page website that was already created for this demo and is available on the Docker Hub as [`seqvence/static-site`](https://hub.docker.com/r/seqvence/static-site/). You can download and run the image directly in one go using `docker run`.

```
$ docker run seqvence/static-site
```
Since the image doesn't exist on your Docker host, the Docker daemon first fetches the image from the registry and then runs the image.

* Okay, now that the server is running, do you see the website?
* What port is it running on?
* And more importantly, how do you access the container directly from our host machine?

In this case, the client didn't tell the Docker Engine to publish any of the ports, so you need to re-run the `docker run` command. We'll take the oportunity to publish ports and pass your name to the container to customize the message displayed. While we are at it, you should also find a way so that our terminal is not attached to the running container. So that you can happily close your terminal and keep the container running. This is called the **detached** mode.

Before we look at the **detached** mode, we should first find out a way to stop the container that you have just launched.

First up, launch another terminal (command window) and execute the following command. If you're using docker-machine you need to run `eval $(docker-machine env <YOUR_DOCKER_MACHINE_NAME>)` in each new terminal otherwise you'll get the error "Cannot connect to the Docker daemon. Is the docker daemon running on this host?".
```
$ docker ps
CONTAINER ID        IMAGE                  COMMAND                  CREATED             STATUS              PORTS               NAMES
a7a0e504ca3e        seqvence/static-site   "/bin/sh -c 'cd /usr/"   28 seconds ago      Up 26 seconds       80/tcp, 443/tcp     stupefied_mahavira
```

Check out the `CONTAINER ID` column. You will need to use this `CONTAINER ID` value, a long sequence of characters and first stop the running container and then remove the running container as given below. The example below provides the `CONTAINER ID` on our system, you should use the value that you see in your terminal.
```
$ docker stop a7a0e504ca3e
$ docker rm   a7a0e504ca3e
```

Note: A cool feature is that you do not need to specify the entire `CONTAINER ID`. You can just specify a few starting characters and if it is unique among all the containers that you have launched, the Docker client will intelligently pick it up.

Now, let us launch a container in **detached** mode as shown below:

```
$ docker run --name static-site -e AUTHOR="Your Name" -d -P seqvence/static-site
e61d12292d69556eabe2a44c16cbd54486b2527e2ce4f95438e504afb7b02810
```

In the above command, `-d` will create a container with the process detached from our terminal, `-P` will publish all the exposed container ports to random ports on the Docker host, `-e` is how you pass environment variables to the container, and finally `--name` allows you to specify a container name. `AUTHOR` is the environment variable name and `Your Name` is the value that you can pass.

Now you can see the ports by running the `docker port` command.

```
$ docker port static-site
443/tcp -> 0.0.0.0:32772
80/tcp -> 0.0.0.0:32773
```

If you're on Linux, you can open [http://localhost:32773](http://localhost:32773) (replace 32773 with your port for 80/tcp) in your browser. If you're on Windows or a Mac, you need to find the IP of the hostname.

```
$ docker-machine ip default
192.168.99.100
```
You can now open [http://192.168.99.100:32773](http://192.168.99.100:32773) (replace 32773 with your port for 80/tcp) to see your site live!

You can also run a second webserver at the same time, specifying a custom host port mapping to the container's webserver.

```
$ docker run --name static-site-2 -e AUTHOR="Your Name" -d -p 8888:80 seqvence/static-site
```
<img src="../images/static.png" title="static">

I'm sure you agree that was super simple. To deploy this on a real server you would just need to install docker, and run the above docker command.

Now that you've seen how to run a webserver inside a docker image, you must be wondering - how do I create my own docker image? This is the question we'll be exploring in the next section. But first, let's stop and remove the containers since you won't be using them anymore.

```
$ docker stop static-site static-site-2
$ docker rm static-site static-site-2
```

### 2.2 Docker Images

You've looked at images before but in this section we'll dive deeper into what docker images are and build our own image. And, we'll also use that image to run our application locally. Finally, you'll push some of your images to Docker Hub.

Docker images are the basis of containers. In the previous example, you **pulled** the *seqvence/static-site* image from the registry and asked the docker client to run a container **based** on that image. To see the list of images that are available locally, use the `docker images` command.

```
$ docker images
REPOSITORY             TAG                 IMAGE ID            CREATED             SIZE
seqvence/static-site   latest              92a386b6e686        2 hours ago        190.5 MB
nginx                  latest              af4b3d7d5401        3 hours ago        190.5 MB
python                 2.7                 1c32174fd534        14 hours ago        676.8 MB
postgres               9.4                 88d845ac7a88        14 hours ago        263.6 MB
containous/traefik     latest              27b4e0c6b2fd        4 days ago          20.75 MB
node                   0.10                42426a5cba5f        6 days ago          633.7 MB
redis                  latest              4f5f397d4b7c        7 days ago          177.5 MB
mongo                  latest              467eb21035a8        7 days ago          309.7 MB
alpine                 3.3                 70c557e50ed6        8 days ago          4.794 MB
java                   7                   21f6ce84e43c        8 days ago          587.7 MB
```

The above gives a list of images that I've pulled from the registry and the ones that I've created myself (we'll shortly see how). The list will most likely not correspond to the list of images that you have currently on your machine. The `TAG` refers to a particular snapshot of the image and the `ID` is the corresponding unique identifier for that image.

For simplicity, you can think of an image akin to a git repository - images can be [committed](https://docs.docker.com/engine/reference/commandline/commit/) with changes and have multiple versions. When you do not provide a specific version number, the client defaults to `latest`.

For example, you can pull a specific version of `ubuntu` image as follows:

```
$ docker pull ubuntu:12.04
```

**NOTE**: Do not execute the above command. It is only for your reference.

If you do not specify the version number of the image, then as mentioned the Docker client will default to a version named `latest`.

So for example, the `docker pull` command given below will pull an image named `ubuntu:latest`:

```
$ docker pull ubuntu
```

To get a new Docker image you can either get it from a registry (such as the docker hub) or create your own. There are tens of thousands of images available on [Docker hub](https://hub.docker.com). You can also search for images directly from the command line using `docker search`.

An important distinction to be aware of when it comes to images is between base and child images.

- **Base images** are images that has no parent image, usually images with an OS like ubuntu, alpine or debian.

- **Child images** are images that build on base images and add additional functionality.

Then there are two more types of images that can be both base and child images, they are official and user images.

- **Official images** Docker, Inc. sponsors a dedicated team that is responsible for reviewing and publishing all Official Repositories content. This team works in collaboration with upstream software maintainers, security experts, and the broader Docker community. These are not prefixed by an organization or user name. In the list of images above, the `python`, `node`, `alpine` and `nginx` images are official (base) images. To find out more about them, check out the [Official Images Documentation](https://docs.docker.com/docker-hub/official_repos/).

- **User images** are images created and shared by users like you. They build on base images and add additional functionality. Typically these are formatted as `user/image-name`. The `user` value in the image name is your Docker Hub user or organization name.

### 2.3 Our First Image
_The code for this section is in this repository in the [flask-app](https://github.com/docker/labs/tree/master/beginner/flask-app) directory_

Now that you have a better understanding of images, it's time to create our own. Our goal in this section will be to create an image that sandboxes a small [Flask](http://flask.pocoo.org) application.
For the purposes of this workshop, we'll created a fun little Python Flask app that displays a random cat `.gif` every time it is loaded - because you know, who doesn't like cats?

### 2.4 Dockerfile

A [Dockerfile](https://docs.docker.com/engine/reference/builder/) is a text-file that contains a list of commands that the Docker daemon calls while creating an image. It is simple way to automate the image creation process. The best part is that the [commands](https://docs.docker.com/engine/reference/builder/) you write in a Dockerfile are *almost* identical to their equivalent Linux commands. This means you don't really have to learn new syntax to create your own Dockerfiles.

**The goal of this exercise is to create a Docker image which will run a Flask app.**

Start by creating a folder ```flask-app``` where we'll create the following files:

```
- Dockerfile
- app.py
- requirements.txt
- templates/index.html
```

Create the **app.py** with the following content:

```
from flask import Flask, render_template
import random

app = Flask(__name__)

# list of cat images
images = [
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr05/15/9/anigif_enhanced-buzz-26388-1381844103-11.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr01/15/9/anigif_enhanced-buzz-31540-1381844535-8.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr05/15/9/anigif_enhanced-buzz-26390-1381844163-18.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr06/15/10/anigif_enhanced-buzz-1376-1381846217-0.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr03/15/9/anigif_enhanced-buzz-3391-1381844336-26.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr06/15/10/anigif_enhanced-buzz-29111-1381845968-0.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr03/15/9/anigif_enhanced-buzz-3409-1381844582-13.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr02/15/9/anigif_enhanced-buzz-19667-1381844937-10.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr05/15/9/anigif_enhanced-buzz-26358-1381845043-13.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr06/15/9/anigif_enhanced-buzz-18774-1381844645-6.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr06/15/9/anigif_enhanced-buzz-25158-1381844793-0.gif",
    "http://ak-hdl.buzzfed.com/static/2013-10/enhanced/webdr03/15/10/anigif_enhanced-buzz-11980-1381846269-1.gif"
]

@app.route('/')
def index():
    url = random.choice(images)
    return render_template('index.html', url=url)

if __name__ == "__main__":
    app.run(host="0.0.0.0")
```

In order to install Python modules required for our app we need to add to **requirements.txt** file the following line:

```
Flask==0.10.1
```

Create a directory called `templates` and create a **index.html** file in that directory, to have the same content as below:

```
<html>
  <head>
    <style type="text/css">
      body {
        background: black;
        color: white;
      }
      div.container {
        max-width: 500px;
        margin: 100px auto;
        border: 20px solid white;
        padding: 10px;
        text-align: center;
      }
      h4 {
        text-transform: uppercase;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <h4>Cat Gif of the day</h4>
      <img src="{{url}}" />
      <p><small>Courtesy: <a href="http://www.buzzfeed.com/copyranter/the-best-cat-gif-post-in-the-history-of-cat-gifs">Buzzfeed</a></small></p>
    </div>
  </body>
</html>
```

The next step now is to create a Docker image with this web app. As mentioned above, all user images are based off a base image. Since our application is written in Python, we will build our own Python image based on [Alpine](https://hub.docker.com/_/alpine/). We'll do that using a **Dockerfile**.

Create a file **Dockerfile**.
Start by specifying our base image. Use the `FROM` keyword to do that

```
FROM alpine:latest
```

The next step usually is to write the commands of copying the files and installing the dependencies.
But first we will install the Python pip package to the alpine linux distribution. This will not just install the pip package but any other dependencies too, which includes the python interpreter. Add the following [RUN](https://docs.docker.com/engine/reference/builder/#run) command next:
```
RUN apk add --update py-pip
```

Next, let's add the files that make up the Flask Application.


Install all Python requirements for our app to run. This will be accomplished by adding the lines:

```
COPY requirements.txt /usr/src/app/
RUN pip install --no-cache-dir -r /usr/src/app/requirements.txt
```

Copy the files you have created earlier our image by using [COPY](https://docs.docker.com/engine/reference/builder/#copy)  command.

```
COPY app.py /usr/src/app/
COPY templates/index.html /usr/src/app/templates/
```

The next thing you need to specify is the port number which needs to be exposed. Since our flask app is running on `5000` that's what we'll expose.
```
EXPOSE 5000
```

The last step is the command for running the application which is simply - `python ./app.py`. Use the [CMD](https://docs.docker.com/engine/reference/builder/#cmd) command to do that -

```
CMD ["python", "/usr/src/app/app.py"]
```

The primary purpose of `CMD` is to tell the container which command it should run by default when it is started. With that, our `Dockerfile` is now ready. This is how it looks:

```
# our base image
FROM alpine:latest

# Install python and pip
RUN apk add --update py-pip

# install Python modules needed by the Python app
COPY requirements.txt /usr/src/app/
RUN pip install --no-cache-dir -r /usr/src/app/requirements.txt

# copy files required for the app to run
COPY app.py /usr/src/app/
COPY templates/index.html /usr/src/app/templates/

# tell the port number the container should expose
EXPOSE 5000

# run the application
CMD ["python", "/usr/src/app/app.py"]
```

Now that you finally have your `Dockerfile`, you can now build your image. The `docker build` command does the heavy-lifting of creating a docker image from a `Dockerfile`.

While running the `docker build` command given below, make sure to replace `<YOUR_USERNAME>`  with your username. This username should be the same on you created when you registered on [Docker hub](https://hub.docker.com). If you haven't done that yet, please go ahead and create an account. The `docker build` command is quite simple - it takes an optional tag name with `-t` and a location of the directory containing the `Dockerfile` - the `.` indicates the current directory:

```
$ docker build -t <YOUR_USERNAME>/myfirstapp .
Sending build context to Docker daemon 9.728 kB
Step 1 : FROM alpine:latest
 ---> 0d81fc72e790
Step 2 : RUN apk add --update py-pip
 ---> Running in 8abd4091b5f5
fetch http://dl-4.alpinelinux.org/alpine/v3.3/main/x86_64/APKINDEX.tar.gz
fetch http://dl-4.alpinelinux.org/alpine/v3.3/community/x86_64/APKINDEX.tar.gz
(1/12) Installing libbz2 (1.0.6-r4)
(2/12) Installing expat (2.1.0-r2)
(3/12) Installing libffi (3.2.1-r2)
(4/12) Installing gdbm (1.11-r1)
(5/12) Installing ncurses-terminfo-base (6.0-r6)
(6/12) Installing ncurses-terminfo (6.0-r6)
(7/12) Installing ncurses-libs (6.0-r6)
(8/12) Installing readline (6.3.008-r4)
(9/12) Installing sqlite-libs (3.9.2-r0)
(10/12) Installing python (2.7.11-r3)
(11/12) Installing py-setuptools (18.8-r0)
(12/12) Installing py-pip (7.1.2-r0)
Executing busybox-1.24.1-r7.trigger
OK: 59 MiB in 23 packages
 ---> 976a232ac4ad
Removing intermediate container 8abd4091b5f5
Step 3 : COPY requirements.txt /usr/src/app/
 ---> 65b4be05340c
Removing intermediate container 29ef53b58e0f
Step 4 : RUN pip install --no-cache-dir -r /usr/src/app/requirements.txt
 ---> Running in a1f26ded28e7
Collecting Flask==0.10.1 (from -r /usr/src/app/requirements.txt (line 1))
  Downloading Flask-0.10.1.tar.gz (544kB)
Collecting Werkzeug>=0.7 (from Flask==0.10.1->-r /usr/src/app/requirements.txt (line 1))
  Downloading Werkzeug-0.11.4-py2.py3-none-any.whl (305kB)
Collecting Jinja2>=2.4 (from Flask==0.10.1->-r /usr/src/app/requirements.txt (line 1))
  Downloading Jinja2-2.8-py2.py3-none-any.whl (263kB)
Collecting itsdangerous>=0.21 (from Flask==0.10.1->-r /usr/src/app/requirements.txt (line 1))
  Downloading itsdangerous-0.24.tar.gz (46kB)
Collecting MarkupSafe (from Jinja2>=2.4->Flask==0.10.1->-r /usr/src/app/requirements.txt (line 1))
  Downloading MarkupSafe-0.23.tar.gz
Installing collected packages: Werkzeug, MarkupSafe, Jinja2, itsdangerous, Flask
  Running setup.py install for MarkupSafe
  Running setup.py install for itsdangerous
  Running setup.py install for Flask
Successfully installed Flask-0.10.1 Jinja2-2.8 MarkupSafe-0.23 Werkzeug-0.11.4 itsdangerous-0.24
You are using pip version 7.1.2, however version 8.1.1 is available.
You should consider upgrading via the 'pip install --upgrade pip' command.
 ---> 8de73b0730c2
Removing intermediate container a1f26ded28e7
Step 5 : COPY app.py /usr/src/app/
 ---> 6a3436fca83e
Removing intermediate container d51b81a8b698
Step 6 : COPY templates/index.html /usr/src/app/templates/
 ---> 8098386bee99
Removing intermediate container b783d7646f83
Step 7 : EXPOSE 5000
 ---> Running in 31401b7dea40
 ---> 5e9988d87da7
Removing intermediate container 31401b7dea40
Step 8 : CMD python /usr/src/app/app.py
 ---> Running in 78e324d26576
 ---> 2f7357a0805d
Removing intermediate container 78e324d26576
Successfully built 2f7357a0805d
```

If you don't have the `alpine:latest` image, the client will first pull the image and then create your image. Therefore, your output on running the command will look different from mine. If everything went well, your image should be ready! Run `docker images` and see if your image (`<YOUR_USERNAME>/myfirstapp`) shows.

The last step in this section is to run the image and see if it actually works.

```
$ docker run -p 8888:5000 --name myfirstapp YOUR_USERNAME/myfirstapp
 * Running on http://0.0.0.0:5000/ (Press CTRL+C to quit)
```

Head over to `http://<DOCKER_HOST-IP-ADDRESS>:8888` and your app should be live. You may need to open up another terminal and determine the container ip address using `docker-machine ip default`.

<img src="../images/catgif.png" title="static">

Hit the Refresh button in the web browser to see a few more cat images.

OK, now that you are done with the this container, stop and remove it since you won't be using it again.

Open another terminal window and execute the following commands:

```
$ docker stop myfirstapp
$ docker rm myfirstapp
```
## Next Steps
For the next step in the tutorial head over to [3.0 Run a multi-container app with Docker Compose](./votingapp.md)
