# Getting Started with Windows Containers

Docker containers run natively in Windows Server 2016, Windows Server 2019 and Windows 10. These labs are based on the latest releases of Windows and Docker which provide the best experience for containerized Windows applications.

The minimum requirements are: 

* Windows 10 Professional or Enterprise, with Windows update 1809 *or*
* Windows Server 2019 

## Step 1 - Setup

You can run Windows containers on Windows 10, Windows Server 2016 and Windows Server 2019:

+ [Install Docker Desktop on Windows 10](https://hub.docker.com/editions/community/docker-ce-desktop-windows "Windows 10 Setup")
+ [Install Docker Enterprise Engine on Windows Server](https://hub.docker.com/editions/enterprise/docker-ee-server-windows "Setup on Windows Server")

> Most public cloud providers also have a VM image with Docker already installed. You can use Microsoft's **Windows Server 2019 Datacenter with Containers** VM image on Azure, and Amazon's **Microsoft Windows Server 2019 Base with Containers** AMI on AWS.

## Verification

Run `docker version` to check the basic details of your deployment. You should see "Windows" listed as the operating system for the Docker client and the Docker Engine:

```
PS>docker version
Client: Docker Engine - Community
 Version:           18.09.1
 API version:       1.39
 Go version:        go1.10.6
 Git commit:        4c52b90
 Built:             Wed Jan  9 19:34:26 2019
 OS/Arch:           windows/amd64
 Experimental:      false

Server: Docker Engine - Community
 Engine:
  Version:          18.09.1
  API version:      1.39 (minimum version 1.24)
  Go version:       go1.10.6
  Git commit:       4c52b90
  Built:            Wed Jan  9 19:50:10 2019
  OS/Arch:          windows/amd64
  Experimental:     true
```

> The `OS/Arch` field tells you the operating system and CPU architecture you're using. Docker is cross-platform, so you can manage Windows Docker servers from a Linux client and vice-versa, using the same `docker` commands.

## Windows Versions

The latest release of Windows to support Docker containers is Windows Server 2019, and Windows 10 with the 1809 update. There are many enhancements from the original Windows containers release in Server 2016.

> [Read about the new container features with Docker on Windows Server 2019](https://blog.docker.com/2019/01/announcing-support-for-windows-server-2019-within-docker-enterprise/)

Windows containers need to match the version of the OS where the container is running with the version of the OS inside the container. Container images flagged as `ltsc2019` or `1809` work with the latest Windows versions.


## Next Steps

Continue to Step 2: [Getting Started with Windows Containers](WindowsContainers.md "Getting Started with Windows Containers")
