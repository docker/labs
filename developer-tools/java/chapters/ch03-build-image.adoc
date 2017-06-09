:imagesdir: images

== Build a Docker Image

*PURPOSE*: This chapter explains how to create a Docker image.

As explained in <<Docker_Basics>>, Docker image is the *build component* of Docker and a read-only template of application operating system.

=== Dockerfile

Docker build images by reading instructions from a _Dockerfile_. A _Dockerfile_ is a text document that contains all the commands a user could call on the command line to assemble an image. `docker build` command uses this file and executes all the commands in succession to create an image.

`build` command is also passed a context that is used during image creation. This context can be a path on your local filesystem or a URL to a Git repository.

_Dockerfile_ is usually called _Dockerfile_. The complete list of commands that can be specified in this file are explained at https://docs.docker.com/reference/builder/. The common commands are listed below:

.Common commands for Dockerfile
[width="100%", options="header", cols="1,4,4"]
|==================
| Command | Purpose | Example
| FROM | First non-comment instruction in _Dockerfile_ | `FROM ubuntu`
| COPY | Copies mulitple source files from the context to the file system of the container at the specified path | `COPY .bash_profile /home`
| ENV | Sets the environment variable | `ENV HOSTNAME=test`
| RUN | Executes a command | `RUN apt-get update`
| CMD | Defaults for an executing container | `CMD ["/bin/echo", "hello world"]`
| EXPOSE | Informs the network ports that the container will listen on | `EXPOSE 8093`
|==================

=== Create your first Docker image

Create a new directory `hellodocker`.

In that directory, create a new text file `Dockerfile`. Use the following contents:

[source, text]
----
FROM ubuntu:latest

CMD ["/bin/echo", "hello world"]
----

This image uses `ubuntu` as the base image. `CMD` command defines the command that needs to run. It provides a different entry point of `/bin/echo` and gives the argument "`hello world`".

Build the image using the command:

[source, text]
----
  docker image build . -t helloworld
----

`.` in this command is the context for `docker image build`. `-t` adds a tag to the image.


The following output is shown:

[source, text]
----
Sending build context to Docker daemon 2.048 kB
Step 1/2 : FROM ubuntu:latest
latest: Pulling from library/ubuntu
8aec416115fd: Pull complete 
695f074e24e3: Pull complete 
946d6c48c2a7: Pull complete 
bc7277e579f0: Pull complete 
2508cbcde94b: Pull complete 
Digest: sha256:71cd81252a3563a03ad8daee81047b62ab5d892ebbfbf71cf53415f29c130950
Status: Downloaded newer image for ubuntu:latest
 ---> f49eec89601e
Step 2/2 : CMD /bin/echo hello world
 ---> Running in 7615d69d04ec
 ---> 61edf15e4cec
Removing intermediate container 7615d69d04ec
Successfully built 61edf15e4cec
----


List the images available using `docker image ls`:

[source, text]
----
REPOSITORY            TAG                 IMAGE ID            CREATED              SIZE
helloworld            latest              61edf15e4cec        About a minute ago   129 MB
ubuntu                latest              f49eec89601e        21 hours ago         129 MB
----

Other images may be seen as well but we are interested in these images for now.

Run the container using the command:

  docker container run helloworld

to see the output:

  hello world

If you do not see the expected output, check your Dockerfile that the content exactly matches as shown above. build the image again and now run it!

Change the base image from `ubuntu` to `busybox` in `Dockerfile`. Build the image again:

  docker build -t helloworld:2 .

and view the images using `docker image ls` command:

[source, text]
----
REPOSITORY            TAG                 IMAGE ID            CREATED             SIZE
helloworld            2                   fd5297466a74        43 seconds ago      1.11 MB
helloworld            latest              61edf15e4cec        3 minutes ago       129 MB
ubuntu                latest              f49eec89601e        21 hours ago        129 MB
busybox               latest              7968321274dc        7 days ago          1.11 MB
----

`helloworld:2` is the format that allows to specify the image name and assign a tag/version to it separated by `:`.

=== Create your first Docker image using Java

==== Create a simple Java application

Create a new Java project:

[source, text]
----
mvn archetype:generate -DgroupId=org.examples.java -DartifactId=helloworld -DinteractiveMode=false
----

Build the project:

[source, text]
----
cd helloworld
mvn package
----

Run the Java class:

[source, text]
----
java -cp target/helloworld-1.0-SNAPSHOT.jar org.examples.java.App
----

This shows the output:

[source, text]
----
Hello World!
----

Let's package this application as a Docker image.

==== Java Docker image

Run the OpenJDK container in an interactive manner:

[source, text]
----
docker run -it openjdk
----

This will open a terminal in the container. Check the version of Java:

[source, text]
----
root@84904fb904da:/# java -version
openjdk version "1.8.0_111"
OpenJDK Runtime Environment (build 1.8.0_111-8u111-b14-2~bpo8+1-b14)
OpenJDK 64-Bit Server VM (build 25.111-b14, mixed mode)
----

A different version may be seen in your case. Exit out of the container by typing `exit` in the shell.

==== Package and Run Java application as Docker image

Create a new Dockerfile in `helloworld` directory and use the following content:

[source, text]
----
FROM openjdk:latest

COPY target/helloworld-1.0-SNAPSHOT.jar /usr/src/helloworld-1.0-SNAPSHOT.jar

CMD java -cp /usr/src/helloworld-1.0-SNAPSHOT.jar org.examples.java.App
----

Build the image:

    docker build -t hello-java .

Run the image:

    docker run hello-java

This displays the output:

    Hello World!

This shows the exactly same output that was printed when the Java class was invoked using Java CLI.

==== Package and Run Java Application using Docker Maven Plugin

https://github.com/fabric8io/docker-maven-plugin[Docker Maven Plugin] allows you to manage Docker images and containers using Maven. It comes with predefined goals:

[options="header"]
|====
|Goal | Description
| `docker:build` | Build images
| `docker:start` | Create and start containers
| `docker:stop` | Stop and destroy containers
| `docker:push` | Push images to a registry
| `docker:remove` | Remove images from local docker host
| `docker:logs` | Show container logs
|====

Complete set of goals are listed at https://github.com/fabric8io/docker-maven-plugin.

Clone the sample code from https://github.com/arun-gupta/docker-java-sample/.

Create the Docker image:

[source, text]
----
mvn -f docker-java-sample/pom.xml package -Pdocker
----

This will show an output like:

[source, text]
----
[INFO] DOCKER> docker-build.tar: Created [hello-java] in 116 milliseconds
[INFO] DOCKER> [hello-java]: Built image sha256:ea64a
[INFO] DOCKER> [hello-java]: Removed old image sha256:954b1
----

The list of images can be checked using the command `docker image ls | grep hello-java`:

[source, text]
----
hello-java                            latest              ea64a9f5011e        5 seconds ago       643 MB
----

Run the Docker container:

[source, text]
----
mvn -f docker-java-sample/pom.xml install -Pdocker
----

This will show an output like:

[source, text]
----
[INFO] DOCKER> [hello-java] : Start container b3b0e4b63174
[INFO] DOCKER> [hello-java] : Waited on log out 'Hello' 504 ms
[INFO] 
[INFO] --- docker-maven-plugin:0.14.2:logs (docker:start) @ helloworld ---
b3b0e4> Hello World!
----

This is similar output when running the Java application using `java` CLI or the Docker container using `docker run` command.

The container is running in the foreground. Use `Ctrl` + `C` to interrupt the container and return back to terminal.

Only one change was required in the project to enable Docker packaging and running. A Maven profile is added in `pom.xml`:

[source, text]
----
<profiles>
    <profile>
        <id>docker</id>
        <build>
            <plugins>
                <plugin>
                    <groupId>io.fabric8</groupId>
                    <artifactId>docker-maven-plugin</artifactId>
                    <version>0.19.0</version>
                    <configuration>
                        <images>
                            <image>
                                <name>hello-java</name>
                                <build>
                                    <from>openjdk:latest</from>
                                    <assembly>
                                        <descriptorRef>artifact</descriptorRef>
                                    </assembly>
                                    <cmd>java -cp maven/${project.name}-${project.version}.jar org.examples.java.App</cmd>
                                </build>
                                <run>
                                    <wait>
                                        <log>Hello World!</log>
                                    </wait>
                                </run>
                            </image>
                        </images>
                    </configuration>
                    <executions>
                        <execution>
                            <id>docker:build</id>
                            <phase>package</phase>
                            <goals>
                                <goal>build</goal>
                            </goals>
                        </execution>
                        <execution>
                            <id>docker:start</id>
                            <phase>install</phase>
                            <goals>
                                <goal>start</goal>
                                <goal>logs</goal>
                            </goals>
                        </execution>
                    </executions>
                </plugin>
            </plugins>
        </build>
    </profile>
</profiles>
----

=== Dockerfile Command Design Patterns

==== Difference between CMD and ENTRYPOINT

*TL;DR* `CMD` will work for most of the cases.

Default entry point for a container is `/bin/sh`, the default shell.

Running a container as `docker run -it ubuntu` uses that command and starts the default shell. The output is shown as:

```console
> docker run -it ubuntu
root@88976ddee107:/#
```

`ENTRYPOINT` allows to override the entry point to some other command, and even customize it. For example, a container can be started as:

```console
> docker run -it --entrypoint=/bin/cat ubuntu /etc/passwd
root:x:0:0:root:/root:/bin/bash
daemon:x:1:1:daemon:/usr/sbin:/usr/sbin/nologin
bin:x:2:2:bin:/bin:/usr/sbin/nologin
sys:x:3:3:sys:/dev:/usr/sbin/nologin
. . .
```

This command overrides the entry point to the container to `/bin/cat`. The argument(s) passed to the CLI are used by the entry point.

==== Difference between ADD and COPY

*TL;DR* `COPY` will work for most of the cases.

`ADD` has all capabilities of `COPY` and has the following additional features:

. Allows tar file auto-extraction in the image, for example, `ADD app.tar.gz /opt/var/myapp`.
. Allows files to be downloaded from a remote URL. However, the downloaded files will become part of the image. This causes the image size to bloat. So its recommended to use `curl` or `wget` to download the archive explicitly, extract, and remove the archive.

==== Import and export images

Docker images can be saved using `image save` command to a `.tar` file:

  docker image save helloworld > helloworld.tar

These tar files can then be imported using `load` command:

  docker image load -i helloworld.tar

