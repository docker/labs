# Windows Docker Containers 101

Docker runs natively on Windows 10 and Windows Server 2016. In this lab you'll learn how to package Windows applications as Docker images and run them as Docker containers. You'll learn how to create a cluster of Docker servers in swarm mode, and deploy an application as a highly-available service.

> **Difficulty**: Beginner 

> **Time**: Approximately 30 minutes

> **Tasks**:
>
> * [Prerequisites](#prerequisites)
> * [Task 1: Run some simple Windows Docker containers](#task1)
>   * [Task 1.1: Run a task in a Nano Server container](#task1.1)
>   * [Task 1.2: Run an interactive Windows Server Core container](#task1.2)
>   * [Task 1.3: Run a background IIS web server container](#task1.3)
> * [Task 2: Package and run a custom app using Docker](#task2)
>   * [Task 2.1: Build a custom website image](#task2.1)
>   * [Task 2.2: Push your image to Docker Hub](#task2.2)
>   * [Task 2.3: Run your website in a container](#task2.3)
> * [Task 3: Run your app in a highly-available cluster](#task3)
>   * [Task 3.1: Create a Docker swarm with Windows Server nodes](#task3.1)
>   * [Task 3.2: Deploy your website as a service](#task3.2)
>   * [Task 3.3: Take a node offline for maintenance](#task3.3)

## Document conventions

When you encounter a phrase in between `<` and `>`  you are meant to substitute in a different value. 

For instance if you see `$ip = <ip-address>` you would actually type something like `$ip = '10.0.0.4'`

You will be asked to RDP into various servers. 

## <a name="prerequisites"></a>Prerequisites

You will need a set of Windows Server 2016 virtual machines running in Azure, which are already configured with Docker and the Windows base images. You do not need Docker running on your laptop, but you will need a Remote Desktop client to connect to the VMs. 

- Windows - use the built-in Remote Desktop Connection app.
- Mac - install [Microsoft Remote Desktop](https://itunes.apple.com/us/app/microsoft-remote-desktop/id715768417?mt=12) from the app store.
- Linux - install [Remmina](http://www.remmina.org/wp/), or any RDP client you prefer.

> When you connect to the VM, if you are prompted to run Windows Update, you should cancel out. The labs have been tested with the existing VM state and any changes may cause problems.

You will build images and push them to Docker Hub, so you can pull them on different Docker hosts. You will need a Docker ID.

- Sign up for a free Docker ID on [Docker Hub](https://hub.docker.com)

## Prerequisite Task: Prepare your lab environment

Start by ensuring you have the latest lab source code. RDP into one of your Azure VMs, open a PowerShell prompt from the taskbar shortcut, and clone the lab repo from GitHub:

```
mkdir -p C:\scm\github\docker
cd C:\scm\github\docker
git clone https://github.com/docker/dcus-hol-2017.git
```

Now clear up anything left from a previous lab. You only need to do this if you have used this VM for one of the other Windows labs, but you can run it sefaly to restore Docker to a clean state. 

This stops and removes all running containers, and then leaves the swarm - ignore any error messages you see:

```
docker container rm -f $(docker container ls -a -q)
docker swarm leave -f
```

## <a name="task1"></a>Task 1: Run some simple Windows Docker containers

There are different ways to use containers:

1. In the background for long-running services like websites and databases
2. Interactively for connecting to the container like a remote server
3. To run a single task, which could be a PowerShell script or a custom app

In this section you'll try each of those options and see how Docker manages the workload. Before you start, let's check the Docker service is up and running. RDP into one of your Azure VMs, open a PowerShell prompt from the taskbar shortcut, and run:

```
docker version
```

The Docker CLI connects to the Docker API running as a Windows Service, and you will see two sets of output. The first tells you the version and operating system where the Docker client is running, and the second tells you the version and operating system where the Docker service is running. Docker is cross-platform, so you can manage Linux hosts from Windows clients, and Windows hosts from Linux or Mac clients.

## <a name="task1.1"></a>Task 1.1: Run a task in a Nano Server container

This is the simplest kind of container to start with. In PowerShell run:

```
docker run microsoft/nanoserver powershell Write-Output Hello DockerCon 2017!
```

You'll see the output written from the PowerShell command. Here's what Docker did:

- created a new container from the [microsoft/nanoserver](https://hub.docker.com/r/microsoft/nanoserver/) image, which has a basic Nano Server deployment, maintained by Microsoft
- started the container, running the `powershell` command and passing the `Write-Output...` arguments
- relayed the console output from the container to the PowerShell window

Docker keeps a container running as long as the process it started inside the container is still running. In this case the `powershell` process completes when the `Write-Output` command finishes, so the container stops. The Docker platform is cautious with your data - it doesn't delete resources by default, so the container still exists. You can list all the containers on the system and see the Nano Server container is still there, but is in the `Exited` state:

```
> docker ps --all
CONTAINER ID        IMAGE                  COMMAND                  CREATED             STATUS                      PORTS               NAMES
a525b7fb086b        microsoft/nanoserver   "powershell Write-..."   31 seconds ago      Exited (0) 19 seconds ago                       peaceful_benz
```

Containers which do one task and then exit can be very useful. You could build a Docker image which installs the Azure PowerShell module and bundles a set of custom scripts to create a cloud deployment. Anyone can execute that task just by running the container - they don't need the scripts or the right version of the Azure module, they just need to pull the Docker image.


## <a name="task1.2"></a>Task 1.2: Run an interactive Windows Server Core container

Nano Server is a new operating system from Microsoft which supports a subset of the full Windows APIs. You can use it to package cross-platform applications like Java, Go, Node.js and .NET Core, but not full .NET Framework apps. For that there's the [microsoft/windowsservercore](https://hub.docker.com/r/microsoft/windowsservercore) image which is a full Windows Server 2016 OS, without the UI.

You can explore an image by running an interactive container. Run this to start a Windows Server Core container and connect to it:

```
docker run -it --rm microsoft/windowsservercore powershell
```

- `-it` means run the container interactively, and connect a terminal session
- `--rm` means automatically remove the container when it exits

When the container starts you'll drop into a PowerShell session with the default prompt `PS C:\>`. This is PowerShell running inside a Windows Server Core container. Docker has attached to the console, relaying input and output between your PowerShell window and the PowerShell session in the container.

Run some commands to see how the Windows Server Core image is built:

- `ls c:\` - lists the C drive contents, you'll see this is a minimal installation of Windows 
- `Get-Process` - shows all running processes in the container. There are a number of Windows Services, and the PowerShell exe
- `Get-WindowsFeature` - shows the Windows feature which are available or already installed

You'll see that the base image has .NET 4.6 installed, which is backwards-compatible so you can run .NET 2.0 apps. Almost all Windows Server features are available (.NET 3.5 is an exception, but the [microsoft/aspnet:3.5](https://hub.docker.com/r/microsoft/aspnet/) image comes with that installed). You can install them with the `Add-WindowsFeature` cmdlet, which is how you would start to build up a custom application image from the base image, adding in the features you need.

Interactive containers are useful when you are putting together your own image. You can run a container and verify all the steps you need to deploy your app. Once you have it working, you can [commit](https://docs.docker.com/engine/reference/commandline/commit/) a running container to an image - but it's much better to capture those steps in a repeatable [Dockerfile](https://docs.docker.com/engine/reference/builder/). You'll do that in a later task.

Now run `exit` to leave the PowerShell session. That stops the container process, so the container exits. You ran the container with the `--rm` flag, so Docker will delete it - run `docker ps -a` now and you'll see only the original Nano Server container.


## <a name="task1.3"></a>Run a background IIS web server container

Background containers are how you'll run your application. Here's a simple exmaple using another image from Microsoft - [microsoft/iis](https://hub.docker.com/r/microsoft/iis/) which builds on top of the Windows Server Core image and installs the IIS Web Server feature:

```
docker run -d -p 80:80 --name iis microsoft/iis
```

- `-d` starts in detached mode, Docker runs the container in the background and monitors it
- `-p` publishes a port. The IIS image is built to allow traffic in on port 80. This maps port 80 on the host to port 80 in the container
- `--name` gives the container a name so you can refer to it in management commands

Open a browser on your laptop and go to the address of your VM. You'll see the basic IIS homepage, which is being generated by the container:

![IIS in a Windows Server Core container](images/iis.png)

Publishing the container port means any traffic the VM receives on port 80 gets forwarded to the IIS container, and the responses are forwarded back. Back in your Windows Server VM, run `docker ps` and you'll see the IIS container is still running. Run `Get-Process` and you'll see all the processes running in the VM, which will include a line for the IIS worker process, `w3wp` - something like this:

```
Handles  NPM(K)    PM(K)      WS(K)     CPU(s)     Id  SI ProcessName
-------  ------    -----      -----     ------     --  -- -----------
...
    187      19     4944      13096       0.09   4916   4 w3wp
```

It's important to realize that the container process - `w3wp.exe` in this case - is actually running on the host. That's what makes Docker containers so lightweight and so efficient, all containers are sharing the host's kernel. The container is using the filesystem built in the `microsoft/iis` image, which is where the `w3wp.exe` file lives. The Azure VM doesn't have IIS installed, everything the app needs is packaged in the image.


## <a name="task2"></a>Task 2: Package and run a custom app using Docker

Next you'll learn how to package your own Windows app as a Docker image, using a [Dockerfile](https://docs.docker.com/engine/reference/builder/). 

The Dockerfile is a simple script which contains all the steps to deploy your application. You need to use an existing image as the starting point for your app, but you decide which one. You can use the base Windows Server or Nano Server image, or an [official image](https://docs.docker.com/docker-hub/official_repos/) with the app platform you need already installed - like [openjdk:windowsservercore](https://hub.docker.com/_/openjdk/) which is good for running Java apss in Windows Docker containers.

The Dockerfile syntax is simple. In this task you'll walk through a Dockerfile for a custom website, and see how to package and run it with Docker.

Have a look at the [Dockerfile for the lab](tweet-app/Dockerfile), which builds a simple static website running on IIS. These are the main features:

- it is based [FROM](https://docs.docker.com/engine/reference/builder/#from) `microsoft/windowsservercore`, so the image will start with a clean Windows Server 2016 deployment
- it uses the [SHELL](https://docs.docker.com/engine/reference/builder/#shell) instruction to switch to PowerShell when building the Dockerfile, so the commands to run are all in PowerShell
- it adds the IIS Windows feature and exposes port 80, setting up the web server and allowing traffic into containers on port 80
- it configures IIS to write all log output to a single file, using the `Set-WebConfigurationProperty` cmdlet
- it copies the [start.ps1](tweet-app/start.ps1) startup script and [index.html](tweet-app/index.html) files from the host into the image
- it specifies `start.ps1` as the [CMD](https://docs.docker.com/engine/reference/builder/#cmd) to run when containers start. The script starts the IIS Windows Service and relays the log file entries to the console
- it adds a [HEALTHCHECK](https://docs.docker.com/engine/reference/builder/#healthcheck) which makes an HTTP GET request to the site and returns whether it got a 200 response code


## <a name="task2.1"></a>Task 2.1: Build a custom website image

The Docker platform has the capability to build, ship and run software.

To build the Dockerfile into a Docker image, open a PowerShell prompt on the Windows VM, change to the `tweet-app` directory and run the `docker build` command:

```
cd C:\scm\github\docker\dcus-hol-2017\windows-101\tweet-app
docker build -t <DockerID>/dockercon-tweet-app .
```

-`-t` tags the image, giving it a name you use to push the image or run containers

> Be sure to use your Docker ID in the image tag. You will share it on Docker Hub in the next step, and you can only do that if you use your ID. My Docker ID is `sixeyed`, so I run `docker build -t sixeyed/dockercon-tweet-app` 

You'll see output on the screen as Docker runs each instruction in the Dockerfile, starting like this:

```
Sending build context to Docker daemon 6.144 kB
Step 1/10 : FROM microsoft/windowsservercore
 ---> b4713e4d8bab
Step 2/10 : SHELL powershell -Command $ErrorActionPreference = 'Stop'; $ProgressPreference = 'SilentlyContinue';
...
```

The build command will take a little while to run, as the step to install IIS takes a while in Windows. Once it's built you'll see a `Successfully built...` message. If you repeat the `docker build` command again, it will complete in seconds. That's because Docker caches the [image layers](https://docs.docker.com/engine/userguide/storagedriver/imagesandcontainers/) and only runs instructions if the Dockerfile has changed since the cached version.

Now if you list the images and filter on the `dockercon` name, you'll see your new image:

```
> docker image ls -f reference=*/dockercon*

REPOSITORY                    TAG                 IMAGE ID            CREATED             SIZE
sixeyed/dockercon-tweet-app   latest              a14860778046        11 minutes ago      10.4 GB
```

Docker has built the image but it's only stored on the local machine. Next we'll push it to a public repository.

## <a name="task2.2"></a>Task 2.2: Push your image to Docker Hub

Distribution is built into the Docker platform. You can build images locally and push them to a [registry](https://docs.docker.com/registry/), making them available to other users. Anyone with access can pull that image and run a container from it. The behavior of the app in the container will be the same for everyone, because the image contains the fully-configured app - the only requirements to run it are Windows and Docker.

[Docker Hub](https://hub.docker.com) is the public registry for Docker images. You can push your website image to the Hub, and later in the lab we'll pull it on other servers and run it on a cluster. To push images, you need to log in using the command line and providing your Docker ID credentials:

```
> docker login
Login with your Docker ID to push and pull images from Docker Hub. If you don't have a Docker ID, head over to https://hub.docker.com to create one.
Username: <DockerID>
Password:
Login Succeeded
```

Now upload your image to the Hub:

```
docker push <DockerID>/dockercon-tweet-app
```

You'll see the upload progress for each layer in the Docker image. The IIS layer is almost 300MB so that will take a few seconds. The whole image is over 10GB, but the bulk of that is in the Windows Server Core base image. Those layers are already stored in Docker Hub, so they don't get uploaded - only the new parts of the image get pushed.

You can browse to [https://hub.docker.com/r/&lt;DockerID&gt;/dockercon-tweet-app/](https://hub.docker.com/r/<DockerID>/dockercon-tweet-app/) and see your newly-pushed app image. This is a public repository, so anyone can pull the image - you don't even need a Docker ID to pull public images.

## <a name="task2.3"></a>Task 2.3: Run your website in a container

It's time to run your app and see what it does! First, clear up any containers which are still running by forcing them to stop and then removing them:

```
docker container kill $(docker container ls -a -q)
docker container rm $(docker container ls -a -q)
```

- `container ls -a -q` lists all containers and just returns the ID - which is suitable to feed into the `kill` and `rm` commands

This is a web application, so you'll run it in the background and publish the HTTP port in the same way that you did with the IIS image:

```
docker run -d -p 80:80 <DockerID>/dockercon-tweet-app 
```

When the application starts, browse to the VM address from your laptop browser and you'll see the web app:

![The DockerCon Tweet app in a Windows Server Core container](images/tweet-app.png)

Go ahead and hit the button to Tweet about your lab progress! No data gets stored in the container, so your credentials are safe. 

## <a name="task3"></a>Task 3: Run your app in a highly-available cluster

Your app is packaged in a portable Docker image, and you can rebuild it any time with a simple script. You've shared it on Docker Hub and deployed it on a cloud server, but at the moment you just have a single VM serving your app. That doesn't give you high availability, and for a tier 1 app like this you'll want to avoid downtime if there are any issues with the VM.

Docker supports failover and scaling with a clustering technology built right into the engine - [Docker swarm mode](https://docs.docker.com/engine/swarm/). You don't need anything extra to turn a set of machines into a highly-available cluster, just Windows and Docker. In the rest of this lab you'll set up a swarm of three VMs and deploy the web app on the swarm.

 
## <a name="task3.1"></a> Task 3.1: Create a Docker swarm with Windows Server nodes

There are two server roles in a Docker swarm - managers and workers. You'll make the VM you've use so far the manager as a reward for all the work it's done. Start by clearing down running containers again:

```
docker container kill $(docker container ls -a -q)
docker container rm $(docker container ls -a -q)
```

First you need to get the internal IP address of the VM - that's so the swarm nodes can communicate privately, and the internal traffic doesn't go across the public Internet. Run `ipconfig` and make a note of the IP address - on my Azure VM it's `10.0.0.4`:

```
> ipconfig

Windows IP Configuration

Ethernet adapter vEthernet (HNSTransparent):

   Connection-specific DNS Suffix  . : j02230nt5nmevlqwbqoh5yfxre.zx.internal.cloudapp.net
   Link-local IPv6 Address . . . . . : fe80::75cd:7da9:b793:feed%10
   IPv4 Address. . . . . . . . . . . : 10.0.0.4
   Subnet Mask . . . . . . . . . . . : 255.255.240.0
   Default Gateway . . . . . . . . . : 10.0.0.1

```
Save the internal IP address in a PowerShell variable to use next:

```
$ip = '<ip-address>'
```

Now switch to swarm mode. Run the `swarm init` command to initialize the swarm, specifying the local IP address for the swarm advertise and listen addresses:

```
docker swarm init --listen-addr $ip --advertise-addr $ip
```

> Your RDP session may be interrupted because of a network change when the node switches to swarm mode. It will reconnect in a few seconds.

The output shows you that Docker is now in swarm mode, and it gives you the secret token you need to join other nodes to the swarm. The token is used to stop rogue nodes joining your swarm, so you should keep it secure - like a database connection string. The `swarm init` command output looks like this:

```
Swarm initialized: current node (9o59jpqkuvetmk6wqtvf8ijjx) is now a manager.

To add a worker to this swarm, run the following command:

    docker swarm join \
    --token SWMTKN-1-tfytr54vhhbjb0p8jx0i3hr76hiuhijnty57p0ekp87yy8hjpr5h-0c39fibliuhuh6xlk2ek6t76fbm \
    10.0.0.4:2377

To add a manager to this swarm, run 'docker swarm join-token manager' and follow the instructions.
```

> Make a note of the join token, and the IP address of the manager node. 

Now open an RDP connection to the two other VMs you have access to for this lab. On each VM, open a PowerShell session from the taskbar, run `docker version` to make sure Docker is running, and then join the swarm:

```
docker swarm join --token <swarm-token> <manager-ip-address>:2377
```

The command completes in about a second, and you'll see the output `This node joined a swarm as a worker`. 

You're going to run the web app on every node in the next task, so to reduce startup time it's good to pull the image in advance. Run `docker pull` on the second and third nodes:

```
docker pull <DockerID>/dockercon-tweet-app
```

When you've joined the second and third VMs to the swarm, you can close the RDP sessions. Now you have a fully-featured cluster of Docker servers, and you'll administer the swarm from the manager node.


## <a name="task3.2"></a> Task 3.2: Deploy your website as a service

Swarm mode is an orchestration engine - you use it to deploy highly-available, scalable, distributed solutions. You don't run containers in swarm mode - instead you create services and specify the number of replicas each service should have. Replicas are just containers, but the swarm decides which node to create them on, based on the capacity of the nodes when more containers are needed.

You can run your web app as a highly available service by specifying three replicas:

```
docker service create `
 --publish mode=host,target=80,published=80 `
 --replicas=3 `
 --name tweet-app `
<DockerID>/dockercon-tweet-app
```

- `--publish` publishes the ports, but you use a special syntax in swarm mode
- `--replicas` states how many containers should run the service
- `--name` gives the service a name for administration and discovery

Docker will create three containers from the same image, and schedule them to run across the nodes in the cluster. By default the scheduler will use different nodes for each replica if it can, but in this case it will definitely run one replica on each node, because of the host port publishing. Only one process can listen on a port, so Docker won't schedule multiple replicas on one node, because they can't share the port.

Run `service ps` to see the containers which are running the service replicas:

```
> docker service ps tweet-app
ID                  NAME                IMAGE                                NODE                DESIRED STATE       CURRENT STATE                    ERROR               PORTS
anuh590v4a1b        tweet-app.1         sixeyed/dockercon-tweet-app:latest   win-node00          Running             Running 11 seconds ago                               *:80->80/tcp
gon54xsxp09j        tweet-app.2         sixeyed/dockercon-tweet-app:latest   win-node01          Running             Running 2 seconds ago                                *:80->80/tcp
pjaehhsj8xbp        tweet-app.3         sixeyed/dockercon-tweet-app:latest   win-node02          Running             Running less than a second ago                       *:80->80/tcp
```

You'll see each container is on a different node. In a production environment you would have a load balancer as the entrypoint to your application, distributing traffic among the swarm VMs. Any node can receive a request and respond to it. You would also have a healthcheck in the load balancer so traffic only reached healthy nodes - that lets you do [zero-downtime updates](https://docs.docker.com/engine/swarm/swarm-tutorial/rolling-update/) of your Docker service.

## <a name="task3.3"></a> Task 3.3: Take a node offline for maintenance

Rolling updates make application deployment easy. You can update a distributed app running on many nodes with a single `docker service update` command, which replaces existing containers with new containers from a new image. 

That lets you update the version of your application, and also the version of the Windows base image you're using - Microsoft regularly update the base image with hotfixes. When a Windows update is released, that's when you should rebuild your images from the new Windows base and update your running services.

Your host Windows servers will also need updating, and that can be more invasive - updates may be compute intensive, reducing the workload capacity of the server, and they may require a reboot. Docker swarms have a graceful feature for dealing with that. If you need to do maintenance on a server, you can change its availability and Docker will stop any running containers and take the node out of service.

Try that with the third VM in your swarm. In PowerShell on the manager node, run:

```
docker node update --availability drain <vm-name>
```

Drain mode stops all running containers and ensures the node doesn't have any new containers scheduled. Run `docker node ls` and you'll see the worker is still part of the swarm, but it's in drain mode. Your web app service is still running on the other nodes:

```
> docker service ps tweet-app
ID                  NAME                IMAGE                                NODE                DESIRED STATE       CURRENT STATE                ERROR                              PORTS
anuh590v4a1b        tweet-app.1         sixeyed/dockercon-tweet-app:latest   win-node00          Running             Running about a minute ago                                      *:80->80/tcp
gon54xsxp09j        tweet-app.2         sixeyed/dockercon-tweet-app:latest   win-node01          Running             Running about a minute ago                                      *:80->80/tcp
ofgqn0t9i635        tweet-app.3         sixeyed/dockercon-tweet-app:latest   win-node00          Ready               Ready 2 seconds ago
pjaehhsj8xbp         \_ tweet-app.3     sixeyed/dockercon-tweet-app:latest   win-node02          Shutdown            Shutdown 3 seconds ago
```

You can see here that the replica running on `win-node02` in my cluster is in the `Shutdown` state, because that's the node I've put into drain mode. You'll also see the swarm is trying to create a new replica on another node (`win-node00` in my case, the new replica is in `Ready` state). The swarm tries to maintain the service level of three replicas, but the new container will fail to start because the published port is not available on that node.

While the node is in drain mode, you can run `sconfig` to start a Windows Update, or update the Docker engine. When your maintenance is done, you can put the node back into action by updating its availability from the manager node:

```
docker node update --availability active <vm-name>
```

Now when you check the service replicas, you'll see a new container starting on the node you just made active. When the container is running, the web app will be back up to the full service level.


## Wrap Up

Thank you for taking the time to complete this lab! You now know how to use Docker on Windows, how to package your own apps as Docker images, and how to run multiple Windows servers as a Docker swarm for high-availability and scale.

Do try the other Windows labs at DockerCon, and make a note to check out the full lab suite when you get home - there are plenty more Windows walkthroughs at [docker/labs](https://github.com/docker/labs/tree/master/windows) on GitHub.
