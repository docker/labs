# Running a Heterogeneous Azure-hosted Swarm Cluster
_with both Linux and Windows nodes_

# Introduction
This tutorial will show you how to build an Azure-hosted Swarm cluster
including both a Linux node and a Windows node, then deploy and run an
application comprising Linux and Windows Containers on the Swarm.

It demonstrates using a Docker client running on Windows to interact with
the Swarm cluster, utilizing the Azure CLI to provision the Swarm manager
and node host VMs. The same approach may be used on Mac OS X or Linux, since
the Azure CLI is available on those platforms as well.

Here is the sequence of steps you'll follow:

1. [Install Prerequisites](#install-prerequisites)

2. [Provision Swarm cluster VM hosts, build and push Images to Registry](#provision-swarm-cluster-vm-hosts-build-and-push-images-to-registry)

3. [Create Swarm cluster comprising Linux and Windows nodes](#create-swarm-cluster-comprising-linux-and-windows-nodes)

4. [Run heterogeneous Linux / Windows application on Swarm cluster](#run-heterogeneous-linux--windows-application-on-swarm-cluster)

# Application Description and Architecture
The Bike Commuter Weather App is intended for bicycle commuters, for whom
wind speed and direction are critical parameters - many weather apps leave this
out or de-emphasize it. The architecture looks like this:

* Web UI: ASP.NET 4.6 application hosted in a Docker Windows Container
* Backend Web Service: Python REST web service, wrapping a weather service,
hosted in a Docker Linux Container

![App Diagram](./docker-swarm-diagram.png)

# Install Prerequisites

## Install Chocolatey package manager for Windows
Install the "chocolatey" installation package manager, which provides
a similar experience to package managers on Linux and Mac OS X.

Open an Admin command prompt:

<pre>
> @powershell -NoProfile -ExecutionPolicy unrestricted -Command "(iex ((new-object net.webclient).DownloadString('https://chocolatey.org/install.ps1'))) >$null 2>&1" && SET PATH=%PATH%;%ALLUSERSPROFILE%\chocolatey\bin
</pre>

## Install Git
<pre>
> choco install git -y
</pre>

## Install npm package manager
Use npm to install Azure CLI (the same approach is used with Mac OS X
and Linux, if you should choose to use one of those as your client platform):

<pre>
> choco install nodejs.install -y
</pre>

## Install openssl and other Unix utilities
Since openssl and other Unix utilities are referenced by Azure CLI,
install Cygwin and its package manager cyg-get:

<pre>
> choco install cygwin -y
> choco install cyg-get -y
> cyg-get openssh
</pre>

## Install Azure CLI
_**Open "Cygwin64 Terminal" from the desktop icon and use it for all
remaining commands.**_

<pre>
$ npm install azure-cli -g
</pre>

## Install Docker client via Docker Toolbox
For Windows or Mac OS X, you'll use Docker Toolbox to install the Docker
client (on Linux, you would instead install the Docker Engine).

Navigate to [Docker Toolbox](https://www.docker.com/products/docker-toolbox)
to download and run the installer.

# Provision Swarm cluster VM hosts, build and push Images to Registry
If you don't have an Azure account, you can get a [30-day trial](https://azure.microsoft.com/en-us/free/) with a $200
credit - this requires a credit card to sign-up, but the card will
not be charged for the first month's usage.

## Login using Azure subscription credentials
_Substitute your Azure Account Id for **yourAzureAccountId** below:_

<pre>
$ azure login <b>yourAzureAccountId</b>
</pre>

You'll see a code in the command line window - as the on-screen instructions
say, go to [aka.ms/devicelogin](https://aka.ms/devicelogin)
and enter the code.

_Optional: If you have more than one Azure subscription associated with the
account, specify the subscription to use in subsequent commands as follows,
substituting the subscription id (from the Azure portal) for
**yourDefaultAzureSubscriptionId** below:_

<pre>
$ azure account set <b>yourDefaultAzureSubscriptionId</b>
</pre>

Change mode to ARM (Azure Resource Manager):

<pre>
$ azure config mode arm
</pre>

Create a resource group which will contain all the resources for
the Swarm cluster:

<pre>
$ azure group create -n "cliEastUsSwarmRG" -l "East US"
</pre>

## Provision VM host for Windows Swarm node using Azure ARM template
You'll provision the Windows Swarm node first using an ARM template,
since Windows is not yet fully supported by the Azure CLI "azure vm docker
create" command. The "cli-win-node_subnet" created by this provisioning step
is subsequently used by the Linux VM provisioning steps.

_You'll need to substitute a unique node name for the "dnsNameForPublicIP"
parameter before running the following command in place of
**uniqueWindowsNodeName**, such as "myname-win-node" - this step can take up
to 20 minutes to complete:_

<pre>
$ azure group deployment create cliEastUsSwarmRG cli-win-node --template-uri https://raw.githubusercontent.com/docker/labs/master/windows/dotnet-aspnet46-webapp/azuredeploy.json -p '{"adminUsername": {"value": "Azure123"}, "adminPassword": {"value": "Azure!23"}, "dnsNameForPublicIP": {"value": "<b>uniqueWindowsNodeName</b>"}, "VMName": {"value": "cli-win-node"},"location": {"value": "East US"}}'
</pre>

## Provision VM host for Swarm Manager and Registry using Azure docker create
Below you will create the Swarm manager VM with its own NIC, Public IP Address
and a new storage account, sharing the Windows VM host subnet created earlier.
Ubuntu is used in this tutorial for the Linux VMs.

_This step can take up to 15 minutes to complete._

<pre>
$ azure vm docker create --resource-group cliEastUsSwarmRG --name cli-swarm-master -l "East US" --os-type linux --nic-name clitestnic --public-ip-name clitestpip --vnet-name cli-win-node_vnet --vnet-subnet-name cli-win-node_subnet --storage-account-name cliswarmstorageacct --vnet-address-prefix 10.0.0/16  --vnet-subnet-address-prefix 10.0.0/24 --image-urn canonical:UbuntuServer:14.04.4-LTS:14.04.201602220 --admin-username Azure123 --admin-password 'Azure!23'
</pre>

_When prompted for "public IP domain name", provide a **uniqueManagerName** that
will be unique in the eastus.cloudapp.azure.com DNS namespace, such as
"myname-manager"_

This will be DNS-accessible as **uniqueManagerName**.eastus.cloudapp.azure.com

### Configure Manager VM host Docker Engine startup options
For the purposes of this tutorial, you will disable TLS for the entire swarm,
since TLS is not working with the Windows Docker Engine as of TP5.

_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
$ ssh <b>uniqueManagerName</b>.eastus.cloudapp.azure.com -l Azure123
</pre>

_Use password **Azure!23** when prompted._

_The first time you connect via ssh, you'll see
a message saying "The authenticity of host... can't be established...
Are you sure you want to continue connecting (yes/no)?" - answer **Yes**_

Remove TLS entries from docker daemon config and specify insecure registry.

_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
$ sudo bash
# sed -i -r 's/(DOCKER_OPTS\s*=\s*)\"([^"]+)\"/DOCKER_OPTS="\-H=unix\:\/\/ -H=0\.0\.0\.0\:2376 --insecure-registry <b>uniqueManagerName</b>.eastus.cloudapp.azure.com\:5000\"/g' /etc/default/docker
# service docker restart
# exit
</pre>

### Set up local Docker Registry
In order to utilize the Label-based deployment described in
[Run heterogeneous Linux / Windows application on Swarm cluster]
(#run-heterogeneous-linux--windows-application-on-swarm-cluster)
below, you'll need a Docker registry.

You'll set up a local Docker Registry, sharing the same host VM as the Swarm Manager.

**Note in many use cases you can just use [Docker Hub](https://hub.docker.com) instead of setting up a registry**

<pre>
$ docker run -d -p 5000:5000 --restart=always --name registry -v `pwd`/registry:/var/lib/registry registry:2
$ exit
</pre>

## Provision VM host for Linux Swarm node, build and push App Linux Image to Registry
As for the Swarm Manager, you'll use Ubuntu:

_This step can take up to 15 minutes to complete._

<pre>
$ azure vm docker create --resource-group cliEastUsSwarmRG --name cli-linux-node -l "East US" --os-type linux --nic-name clitestnic-linux-node --public-ip-name clitestpip-linux-node --vnet-name cli-win-node_vnet --vnet-subnet-name cli-win-node_subnet --storage-account-name cliswarmstorageacct --vnet-address-prefix 10.0.0/16  --vnet-subnet-address-prefix 10.0.0/24 --image-urn canonical:UbuntuServer:14.04.4-LTS:14.04.201602220 --admin-username Azure123 --admin-password 'Azure!23'
</pre>

_When prompted for "public IP domain name", provide a **uniqueLinuxNodeName**
that will be unique in the eastus.cloudapp.net DNS namespace, such as
"myname-linux-node"_

### Configure Linux node VM Docker Engine startup options
_For **uniqueLinuxNodeName** below, substitute the name you supplied above,
such as "myname-linux-node"_:

<pre>
$ ssh <b>uniqueLinuxNodeName</b>.eastus.cloudapp.azure.com -l Azure123
</pre>

_Use password **Azure!23** when prompted._

Remove TLS entries from docker daemon config and specify insecure registry.

_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
$ sudo bash
# sed -i -r 's/(DOCKER_OPTS\s*=\s*)\"([^"]+)\"/DOCKER_OPTS="\-H=unix\:\/\/ -H=0\.0\.0\.0\:2375 --insecure-registry <b>uniqueManagerName</b>.eastus.cloudapp.azure.com\:5000 --label ostypelabel=linuxcompat\"/g' /etc/default/docker
# service docker restart
# exit
</pre>

### Get Linux Swarm node advertise IP

Save the "eth0: inet addr" below for later use as **linuxNodeAdvertiseIP**:

<pre>
$ ifconfig

eth0      Link encap:Ethernet  HWaddr 00:0d:3a:12:16:7f
          inet addr:<b>linuxNodeAdvertiseIP</b>  Bcast:10.0.0.255  Mask:255.255.255.0
          inet6 addr: fe80::20d:3aff:fe12:167f/64 Scope:Link
          UP BROADCAST RUNNING MULTICAST  MTU:1500  Metric:1
          RX packets:267865 errors:0 dropped:0 overruns:0 frame:0
          TX packets:81890 errors:0 dropped:0 overruns:0 carrier:0
          collisions:0 txqueuelen:1000
          RX bytes:321904571 (321.9 MB)  TX bytes:202479327 (202.4 MB)
</pre>

### Build App Linux Image
<pre>
$ git clone https://github.com/docker/labs.git
$ docker build -t rushhourweatherappservice labs/windows/Python-REST-Service
</pre>

### Push App Linux Image to Registry
_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
$ docker tag rushhourweatherappservice <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappservice
$ docker push <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappservice
$ exit
</pre>

## Finish provisioning VM for Windows Swarm node, build and push App Windows Image to Registry

### Configure Windows node VM Docker Engine startup options
Remote Desktop: **uniqueWindowsNodeName**.eastus.cloudapp.azure.com

Username: .\Azure123

Password: Azure!23

_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
   > powershell
PS > (cat c:\programdata\docker\runDockerDaemon.cmd) | % { $_ -replace “-H 0.0.0.0:2375”,”-H 0.0.0.0:2375 --insecure-registry <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000 --label ostypelabel=windowscompat” } | set-content c:\programdata\docker\runDockerDaemon.cmd
PS > restart-service docker
</pre>

### Get Windows Swarm node advertise IP

Save the "Ethernet adapter Ethernet 2: IPv4 Address" below for later use as
**windowsNodeAdvertiseIP**:

<pre>
PS > ipconfig

Ethernet adapter Ethernet 2:

   Connection-specific DNS Suffix  . : wehxhlhuqqvuxh2wpb4ezkbuof.bx.internal.cloudapp.net
   Link-local IPv6 Address . . . . . : fe80::81be:578e:4eb:3e2a%6
   IPv4 Address. . . . . . . . . . . : <b>windowsNodeAdvertiseIP</b>
   Subnet Mask . . . . . . . . . . . : 255.255.255.0
   Default Gateway . . . . . . . . . : 10.0.0.1
</pre>

### Build Windows Swarm Image
<pre>
PS > choco install git -y
PS > $env:PATH = "$env:PATH;c:\program files\git\cmd"
PS > git clone https://github.com/docker/labs.git
PS > cd labs\windows
PS > docker build -t swarm swarm-windows
</pre>

### Build App Windows Image
<pre>
PS > docker build -t rushhourweatherappui dotnet-aspnet46-webapp
</pre>

### Push App Windows Image to Registry
_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
PS > docker tag rushhourweatherappui <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappui
PS > docker push <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappui
</pre>

# Create Swarm cluster comprising Linux and Windows nodes
You'll use the docker client on your workstation to create the Swarm cluster.
The sequence of steps is:

1. Create Swarm cluster id on the Manager host

2. Join Linux node to the Swarm cluster, specifying the cluster id

3. Join Windows node to the Swarm cluster, specifying the cluster id

4. Start Swarm Manager on the manager host

## Create Swarm cluster id
_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
$ docker -H tcp://<b>uniqueManagerName</b>.eastus.cloudapp.azure.com:2376 run --rm swarm create
</pre>

_The cluster id is the guid code which is output at the end of the above
command - save this as you will need it later on, referred to as
**myClusterId**._

## Join Linux node to Swarm cluster
For **uniqueLinuxNodeName** below, substitute the unique Linux host name you recorded
above, for **linuxNodeAdvertiseIP** below, the IP address, and for
**myClusterId**, the cluster id:_

<pre>
$ docker -H tcp://<b>uniqueLinuxNodeName</b>.eastus.cloudapp.azure.com:2375 run -d swarm join --advertise=<b>linuxNodeAdvertiseIP</b>:2375 token://<b>myClusterId</b>
</pre>

## Join Windows node to Swarm cluster
_For **uniqueWindowsNodeName** below, substitute the unique Windows host name you recorded
above, for **windowsNodeAdvertiseIP**, the IP address, and for **myClusterId**,
the cluster id:_

<pre>
$ docker -H tcp://<b>uniqueWindowsNodeName</b>.eastus.cloudapp.azure.com:2375 run -d swarm join --advertise=<b>windowsNodeAdvertiseIP</b>:2375 token://<b>myClusterId</b>
</pre>

## Start Swarm Manager
_For **uniqueManagerName** below, substitute the unique manager name
you recorded above, and for **myClusterId**, the cluster id:_
<pre>
$ docker -H tcp://<b>uniqueManagerName</b>.eastus.cloudapp.azure.com:2376 run -d -p 2375:2375 swarm manage token://<b>myClusterId</b>
</pre>

Set SWARM_HOST environment variable on the workstation so that subsequent
docker client commands default to using the Swarm Manager.

_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager":_

<pre>
$ export DOCKER_HOST=<b>uniqueManagerName</b>.eastus.cloudapp.azure.com:2375
</pre>

List the nodes - you should see something like the excerpt below, showing
both Linux and Windows nodes up. You'll sometimes need to wait up to 1
minute for the service discovery process to complete, and both nodes to appear:

<pre>
$ docker info

Nodes: 2
 <b>cli-linux-node: linuxNodeAdvertiseIP</b>:2375
   Status: Healthy
   Containers: 1
   Reserved CPUs: 0 / 1
   Reserved Memory: 0 B / 1.719 GiB
   Labels: executiondriver=native-0.2, kernelversion=3.19.0-51-generic, operatingsystem=Ubuntu 14.04.4 LTS, <b>ostypelabel=linuxcompat</b>, storagedriver=aufs
   Error: (none)
   UpdatedAt: 2016-03-06T19:16:49Z
 <b>cli-win-node: windowsNodeAdvertiseIP</b>:2375
   Status: Healthy
   Containers: 1
   Reserved CPUs: 0 / 1
   Reserved Memory: 0 B / 3.675 GiB
   Labels: executiondriver=
 Name: Windows 1854
 Build: 1.10.0-dev 18c9fe0
 Default Isolation: process, kernelversion=10.0 10586 (10586.0.amd64fre.th2_release.151029-1700), operatingsystem=Windows Server 2016 Technical Preview 4, <b>ostypelabel=windowscompat</b>, storagedriver=windowsfilter
   Error: (none)
   UpdatedAt: 2016-03-06T19:16:53Z
</pre>

# Run heterogeneous Linux / Windows application on Swarm cluster

### List the available Images from the local Docker Registry

Navigate to the following in a web browser - you'll see a "repository"
for each of the Images -

_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_:

<pre>
http://<b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/v2/_catalog

{"repositories":["rushhourweatherappservice","rushhourweatherappui"]}
</pre>

## Run App REST web service in a Linux Container on the Swarm cluster
_For **uniqueManagerName** below, substitute the name you supplied above,
such as "myname-manager"_; for **yourWundergroundApiKey**, a weatherunderground
api key (note the demo application will run without this key, displaying an error
message that the key is required):

<pre>
$ docker run --name rushhourweatherappservice -d -p 5000:5000 -e constraint:ostypelabel==linuxcompat -e WUNDERGROUND_API_KEY=<b>yourWundergroundApiKey</b> <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappservice
</pre>

## Run App Web UI in a Windows Container on the Swarm cluster
_For **uniqueManagerName** below, substitute the manager host name you supplied above,
for **uniqueLinuxNodeName**, the linux node name:_

<pre>
$ docker run --name rushhourweatherappui -d -p 80:80 -e WeatherServiceUrl=http://<b>uniqueLinuxNodeName</b>.eastus.cloudapp.azure.com:5000 -e constraint:ostypelabel==windowscompat <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappui
</pre>

## Inspect the Swarm
<pre>
$ docker ps

CONTAINER ID        IMAGE                                                                         COMMAND                  CREATED                  STATUS                  PORTS                          NAMES
e6082d708dfa        <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappservice   "python /tmp/public-s"   Less than a second ago   Up Less than a second   40.121.85.244:5000->5000/tcp   cli-linux-node/rushhourweatherappservice
e69ad82ac726        <b>uniqueManagerName</b>.eastus.cloudapp.azure.com:5000/rushhourweatherappui        "cmd /S /C powershell"   About an hour ago        Up About an hour                                       cli-win-node/rushhourweatherappui

$ docker info

<b>Containers: 4</b>
 Running: 4
 Paused: 0
 Stopped: 0
Images: 7
Server Version: swarm/1.1.3
Role: primary
Strategy: spread
Filters: health, port, dependency, affinity, constraint
Nodes: 2
 <b>cli-linux-node: linuxNodeAdvertiseIP</b>:2375
   Status: Healthy
   <b>Containers: 2</b>
   Reserved CPUs: 0 / 1
   Reserved Memory: 0 B / 1.719 GiB
   Labels: executiondriver=native-0.2, kernelversion=3.19.0-51-generic, operatingsystem=Ubuntu 14.04.4 LTS, <b>ostypelabel=linuxcompat</b>, storagedriver=aufs
   Error: (none)
   UpdatedAt: 2016-03-06T21:32:29Z
 <b>cli-windows-node: windowsNodeAdvertiseIP</b>:2375
   Status: Healthy
   <b>Containers: 2</b>
   Reserved CPUs: 0 / 1
   Reserved Memory: 0 B / 3.675 GiB
   Labels: executiondriver=
 Name: Windows 1854
 Build: 1.10.0-dev 18c9fe0
 Default Isolation: process, kernelversion=10.0 10586 (10586.0.amd64fre.th2_release.151029-1700), operatingsystem=Windows Server 2016 Technical Preview 4, <b>ostypelabel=windowscompat</b>, storagedriver=windowsfilter
   Error: (none)
   UpdatedAt: 2016-03-06T21:32:33Z
Plugins:
 Volume:
 Network:
Kernel Version: 3.19.0-51-generic
Operating System: linux
Architecture: amd64
CPUs: 2
Total Memory: 5.394 GiB
Name: d73f6f2cf088
</pre>

## Try out the running application on the Swarm cluster

_For **uniqueWindowsNodeName** and **uniqueLinuxNodeName** below, substitute the
names you supplied above, such as "myname-win-node" and "myname-linux-node"._

Try out the Web UI application from a web browser - navigate to:

<pre>
http://<b>uniqueWindowsNodeName</b>.eastus.cloudapp.azure.com/RushHourWeatherApp
</pre>

The Web UI Windows application consumes the Linux service - you can directly examine
the Linux service by navigating to:

<pre>
http://<b>uniqueLinuxNodeName</b>.eastus.cloudapp.azure.com:5000/today/amrush
</pre>

# Credits
* [Stefan Scherer: How to run a Windows Docker Engine in Azure]
(https://stefanscherer.github.io/how-to-run-windows-docker-engine-in-azure/) - defines an Azure ARM template which improves on the
[Msft Azure quickstart-templates](https://github.com/Azure/azure-quickstart-templates), including enabling public TCP listening
* [Stefan Scherer: Build Docker Swarm binary for Windows the "Docker way"]
(https://stefanscherer.github.io/build-docker-swarm-for-windows-the-docker-way/)- dockerized Swarm Image for Windows
* [Msft: Install the Azure CLI](https://azure.microsoft.com/en-us/documentation/articles/xplat-cli-install/)
* [Koushik Biswas: Set up and use Docker Swarm on Azure](http://blogs.msdn.com/b/opensourcemsft/archive/2015/12/07/set-up-and-use-docker-swarm-on-azure.aspx)
* [Docker: Deploying a registry server](https://docs.docker.com/registry/deploying/)
* [Docker: Insecure Registry](https://docs.docker.com/registry/insecure/)
