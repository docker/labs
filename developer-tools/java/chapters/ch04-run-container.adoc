:imagesdir: images

= Run a Docker Container

The first step in running any application using Docker is to run a container. There are plenty of images available at https://store.docker.com[Docker Store]. Docker client can simply run the container by giving the image. The client will check if the image already exists on Docker Host. If it exists then it'll run the containers, otherwise the host will first download the image.

== Pull Image

Let's check if any images are available:

[source, text]
----
docker image ls
----

At first, this list is empty. If you've already downloaded the images as specified in the setup chapter, then all the images will be shown here. 

List of images can be seen again using the `docker image ls` command. This will see the following output:

[source, text]
----
REPOSITORY                            TAG                 IMAGE ID            CREATED             SIZE
hello-java                            latest              d9fb8a701f4c        19 minutes ago      643 MB
ubuntu                                latest              f49eec89601e        21 hours ago        129 MB
mysql                                 latest              f3694c67abdb        4 days ago          400 MB
openjdk                               latest              d23bdf5b1b1b        4 days ago          643 MB
busybox                               latest              7968321274dc        7 days ago          1.11 MB
jboss/wildfly                         latest              27e70d979161        5 weeks ago         583 MB
couchbase                             latest              e110bbaa82ca        5 weeks ago         569 MB
couchbase/server                      sandbox             bcd35334353a        4 months ago        566 MB
arungupta/couchbase                   latest              20e80d627161        6 months ago        575 MB
couchbase/server                      latest              97d69bb5e7f4        7 months ago        566 MB
arungupta/wildfly-couchbase-javaee7   latest              ae3db485e77f        13 months ago       590 MB
arungupta/javaee7-hol                 latest              da5c9d4f85ca        18 months ago       582 MB
----

More details about the image can be obtained using `docker image history jboss/wildfly` command:

[source, text]
----
IMAGE               CREATED             CREATED BY                                      SIZE                COMMENT
27e70d979161        5 weeks ago         /bin/sh -c #(nop)  CMD ["/opt/jboss/wildfl...   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  EXPOSE 8080/tcp              0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  ENV LAUNCH_JBOSS_IN_BAC...   0 B                 
<missing>           5 weeks ago         /bin/sh -c cd $HOME     && curl -O https:/...   163 MB              
<missing>           5 weeks ago         /bin/sh -c #(nop)  ENV JBOSS_HOME=/opt/jbo...   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  ENV WILDFLY_SHA1=9ee3c0...   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  ENV WILDFLY_VERSION=10....   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  ENV JAVA_HOME=/usr/lib/...   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  USER [jboss]                 0 B                 
<missing>           5 weeks ago         /bin/sh -c yum -y install java-1.8.0-openj...   200 MB              
<missing>           5 weeks ago         /bin/sh -c #(nop)  USER [root]                  0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  MAINTAINER Marek Goldma...   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  USER [jboss]                 0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  WORKDIR /opt/jboss           0 B                 
<missing>           5 weeks ago         /bin/sh -c groupadd -r jboss -g 1000 && us...   296 kB              
<missing>           5 weeks ago         /bin/sh -c yum update -y && yum -y install...   27.5 MB             
<missing>           5 weeks ago         /bin/sh -c #(nop)  MAINTAINER Marek Goldma...   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  CMD ["/bin/bash"]            0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop)  LABEL name=CentOS Base ...   0 B                 
<missing>           5 weeks ago         /bin/sh -c #(nop) ADD file:940c77b6724c00d...   192 MB              
<missing>           4 months ago        /bin/sh -c #(nop)  MAINTAINER https://gith...   0 B            
----

== Run Container Interactively

Run WildFly container in an interactive mode.

[source, text]
----
docker run -it jboss/wildfly
----

This will show the output as:

[source, text]
----
=========================================================================

  JBoss Bootstrap Environment

  JBOSS_HOME: /opt/jboss/wildfly

  JAVA: /usr/lib/jvm/java/bin/java

. . .

19:44:39,258 INFO  [org.jboss.as] (Controller Boot Thread) WFLYSRV0060: Http management interface listening on http://127.0.0.1:9990/management
19:44:39,259 INFO  [org.jboss.as] (Controller Boot Thread) WFLYSRV0051: Admin console listening on http://127.0.0.1:9990
19:44:39,259 INFO  [org.jboss.as] (Controller Boot Thread) WFLYSRV0025: WildFly Full 10.1.0.Final (WildFly Core 2.2.0.Final) started in 4125ms - Started 331 of 577 services (393 services are lazy, passive or on-demand)
----

This shows that the server started correctly, congratulations!

By default, Docker runs in the foreground. `-i` allows to interact with the STDIN and `-t` attach a TTY to the process. Switches can be combined together and used as `-it`.

Hit Ctrl+C to stop the container.

== Run a Detached Container

Restart the container in detached mode:

[source, text]
----
docker run -d jboss/wildfly
6f8e21487058ac0805b672162a7d106e630485b63221347f5a1afd3abee0536d
----

`-d`, instead of `-it`, runs the container in detached mode.

The output is the unique id assigned to the container. Logs of the container can be seen using the command `docker container logs <CONTAINER_ID>`, where `<CONTAINER_ID>` is the id of the container.

Status of the container can be checked using the `docker container ps` command:

[source, text]
----
CONTAINER ID        IMAGE               COMMAND                  CREATED              STATUS              PORTS               NAMES
6f8e21487058        jboss/wildfly       "/opt/jboss/wildfl..."   About a minute ago   Up About a minute   8080/tcp            mystifying_edison
----

Also try `docker container ps -a` to see all the containers on this machine.

== Run Container with Default Port

If you want the container to accept incoming connections, you will need to provide special options when invoking `docker run`. The container, we just started, can't be accessed by our browser. We need to stop it again and restart with different options.

[source, text]
----
docker container stop `docker container ps | grep wildfly | awk '{print $1}'`
----

Restart the container as:

[source, text]
----
docker container run -d -P --name wildfly jboss/wildfly
----

`-P` map any exposed ports inside the image to a random port on Docker host. In addition, `--name` option is used to give this container a name. This name can then later be used to get more details about the container or stop it. This can be verified using `docker container ps` command:

[source, text]
----
CONTAINER ID        IMAGE               COMMAND                  CREATED             STATUS              PORTS                     NAMES
4f61cb726f63        jboss/wildfly       "/opt/jboss/wildfl..."   5 seconds ago       Up 4 seconds        0.0.0.0:32770->8080/tcp   wildfly
----

The port mapping is shown in the `PORTS` column. Access WildFly server at http://localhost:32768. Make sure to use the correct port number as shown in your case.

NOTE: Exact port number may be different in your case.

The page would look like:

image::wildfly-first-run-default-page.png[]

== Run Container with Specified Port

Stop and remove the previously running container as:

[source, text]
----
docker container stop wildfly
docker container rm wildfly
----

Alternatively, `docker container rm -f wildfly` can be used to stop and remove the container in one command. Be careful with this command because `-f` uses `SIGKILL` to kill the container.

Restart the container as:

[source, text]
----
docker container run -d -p 8080:8080 --name wildfly jboss/wildfly
----

The format is `-p hostPort:containerPort`. This option maps a port on the host to a port in the container. This allows us to access the container on the specified port on the host.

Now we're ready to test http://localhost:8080 again. This works with the exposed port, as expected.

Let's stop the container as:

[source, text]
----
docker container stop wildfly
----

== Deploy a WAR file to Application Server

Now that your application server is running, lets see how to deploy a WAR file to it.

Create a new directory `hellojavaee`. Create a new text file and name it `Dockerfile`. Use the following contents:

[source, text]
----
FROM jboss/wildfly:latest

RUN curl -L https://github.com/javaee-samples/javaee7-simple-sample/releases/download/v1.10/javaee7-simple-sample-1.10.war -o /opt/jboss/wildfly/standalone/deployments/javaee-simple-sample.war
----

Create an image:

[source, text]
----
docker image build -t javaee-sample .
----

Start the container:

[source, text]
----
docker container run -d -p 8080:8080 --name wildfly javaee-sample
----

Access the endpoint:

[source, text]
----
curl http://localhost:8080/javaee-simple-sample/resources/persons
----

See the output:

[source, text]
----
<persons>
	<person>
		<name>
		Penny
		</name>
	</person>
	<person>
		<name>
		Leonard
		</name>
	</person>
	<person>
		<name>
		Sheldon
		</name>
	</person>
	<person>
		<name>
		Amy
		</name>
	</person>
	<person>
		<name>
		Howard
		</name>
	</person>
	<person>
		<name>
		Bernadette
		</name>
	</person>
	<person>
		<name>
		Raj
		</name>
	</person>
	<person>
		<name>
		Priya
		</name>
	</person>
</persons>
----

Optional: `brew install XML-Coreutils` will install XML formatting utility on Mac. This output can then be piped to `xml-fmt` to display a formatted result.

== Stop Container

Stop a specific container by id or name:

[source, text]
----
docker container stop <CONTAINER ID>
docker container stop <NAME>
----

Stop all running containers:

[source, text]
----
docker container stop $(docker container ps -q)
----

Stop only the exited containers:

[source, text]
----
docker container ps -a -f "exited=-1"
----

== Remove Container

Remove a specific container by id or name:

[source, text]
----
docker container rm <CONTAINER_ID>
docker container rm <NAME>
----

Remove containers meeting a regular expression

[source, text]
----
docker container ps -a | grep wildfly | awk '{print $1}' | xargs docker container rm
----

Remove all containers, without any criteria

[source, text]
----
docker container rm $(docker container ps -aq)
----

== Additional Ways To Find Port Mapping

The exact mapped port can also be found using `docker port` command:

[source, text]
----
docker container port <CONTAINER_ID> or <NAME>
----

This shows the output as:

[source, text]
----
8080/tcp -> 0.0.0.0:8080
----

Port mapping can be also be found using `docker inspect` command:

[source, text]
----
docker container inspect --format='{{(index (index .NetworkSettings.Ports "8080/tcp") 0).HostPort}}' <CONTAINER ID>
----
