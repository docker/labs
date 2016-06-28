# DockerCon US 2016 Hands-On Labs (HOL)

![dcus2016](images/dockercon.png)

This repo contains the series of hands-on labs presented at DockerCon 2016. They are designed to help you gain experience in various Docker features, products, and solutions. Depending on your experience, each lab requires between 30-45 minutes to complete. They range in difficulty from easy to advanced.

Some labs will require you to setup virtual machines with Docker installed. You will find specific requirements in each individual lab guide.



## Lab 01. [Docker for Developers](./docker-developer/README.md)

Docker for Mac and Docker for Windows are faster, more reliable alternatives to Docker Toolbox for running Docker locally on your Windows or Mac

Infrastructure requirements: This lab requires you to install either Docker for Mac or Docker for Windows on your local machine

Duration: 30 minutes

In this lab you will:

- Install either Docker for Mac or Docker for Windows
- Deploy a sample Docker application

## Lab 02. [Docker Datacenter](./docker-datacenter/README.md)

Docker Datacenter brings container management and deployment services to enterprises with a production-ready platform supported by Docker and hosted locally behind the firewall.

Infrastructure requirements: This lab requires 3 virtual machines running the latest version of Docker Engine 1.11

Duration: 45 minutes

In this lab you will:

- Install Docker Universal Control Plane
- Deploy a single-container service
- Deploy a multi-container application
- Use users and teams to implement role-based access control

## Lab 03. [Docker Cloud](./docker-cloud/README.md)

Docker Cloud is Docker's cloud platform to build, ship and run your containerized applications. Docker Cloud enables teams to come together to collaborate on their projects and to automate complex continuous delivery flows. So you can focus on working and improving your app, and leave the rest up to Docker Cloud. Docker Cloud offers a set of services that can be used individually or together for an end-to end solution.

Infrastructure requirements: 

- For the management host you may use your local laptop running Docker for Mac or Docker for Windows OR you may use a virtual machine running the latest version of Docker Engine 1.11

- For the managed node you will need one virtual machine running one of the supported Linux distros (RHEL 

Duration: 45 minutes

In this lab you will:

- Install the Docker Cloud CLI
- Bring and existing node under management
- Deploy a single container service
- Build an automated CI/CD pipeline with GitHub and Docker Cloud


## Lab 04. [Docker Native Orchestration](./docker-orchestration/README.md)

In this lab you will try out the new features from Docker engine 1.12 that provide native container orchestration. You will deploy a Dockerized application to a single host and test the application. You will then configure Docker for Swarm Computing and deploy the same app across multiple hosts. You will then see how to scale the application and move the workload across different hosts easily.

Infrastructure requirements: You need three virtual machines each running at least RC2 of Docker Engine 1.12. You can install the latest stable release of Docker Engine 1.12 from http://test.docker.com

Duration: 45 minutes


In this lab you will:

- Deploy a single host application with a Dockerfile
- Configure Docker for Swarm Computing
- Deploy the application across multiple hosts
- Scale the application
- Drain a node and reschedule the containers

