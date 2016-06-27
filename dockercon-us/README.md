# DockerCon US 2016 Hands-On Labs (HOL)

![dcus2016](images/dockercon.png)

This repo contains the series of hands-on labs presented at DockerCon 2016. They are designed to help you gain experience in various Docker features, products, and solutions. Depending on your experience, each lab requires between 30-45 minutes to complete. They range in difficulty from easy to advanced.

In order to complete the following labs you'll need to create at least 3 Ubuntu 14.04 virtual machines and install the Docker 1.12 engine.

You can then choose one or more of the following lab tutorials to go through.

---

## Lab 01. [Docker for Developers](https://github.com/docker/dcus-hol-2016/tree/master/docker-developer)

Docker for Mac and Docker for Windows are faster, more reliable alternatives to Docker Toolbox for running Docker locally on your Windows or Mac

Duration: 30 minutes

In this lab you will:

- Install either Docker for Mac or Docker for Windows
- Deploy a sample Docker application

## Lab 02. [Docker Datacenter](https://github.com/docker/dcus-hol-2016/tree/master/docker-datacenter)

Docker Datacenter brings container management and deployment services to enterprises with a production-ready platform supported by Docker and hosted locally behind the firewall.

Duration: 45 minutes

In this lab you will:

- Install Docker Universal Control Plane
- Deploy a single-container service
- Deploy a multi-container application
- Use users and teams to implement role-based access control

## Lab 03. [Docker Cloud](https://github.com/docker/dcus-hol-2016/tree/master/docker-cloud)

Docker Cloud is Docker's cloud platform to build, ship and run your containerized applications. Docker Cloud enables teams to come together to collaborate on their projects and to automate complex continuous delivery flows. So you can focus on working and improving your app, and leave the rest up to Docker Cloud. Docker Cloud offers a set of services that can be used individually or together for an end-to end solution.

Duration: 45 minutes

In this lab you will:

- Install the Docker Cloud CLI
- Bring and existing node under management
- Deploy a single container service
- Build an automated CI/CD pipeline with GitHub and Docker Cloud


## Lab 04. [Windows Server Containers and Docker](https://github.com/docker/dcus-hol-2016/tree/master/windows-containers)

Windows-based Docker containers will debut with the release of Windows Server 2016 later this year. Using the same CLI and APIs that Docker uses today on Linux, Windows users will be able to build, ship, and run software faster than ever before. This lab uses a technical preview of Windows Server 2016 to give you a core introduction to Windows Server Containers and the Docker Engine on Windows. 

Duration: 15 minutes

In this lab you will:

- Pull Docker images for Windows
- Build an application, 
- Dockerize it and iterate on it.


## Lab 05. [Docker Native Orchestration](https://github.com/docker/dcus-hol-2016/tree/master/docker-orchestration)

In this lab you will try out the new features from Docker engine 1.12 that provide native container orchestration. You will deploy a Dockerized application to a single host and test the application. You will then configure Docker for Swarm Computing and deploy the same app across multiple hosts. You will then see how to scale the application and move the workload across different hosts easily.

Duration: 45 minutes

In this lab you will:

- Deploy a single host application with a Dockerfile
- Configure Docker for Swarm Computing
- Deploy the application across multiple hosts
- Scale the application
- Drain a node and reschedule the containers




---

## Contribute Your Own Labs

If you have an awesome tutorial/lab and would like to add it here. Please open a PR. We would love to add more exciting tutorials to the list!
