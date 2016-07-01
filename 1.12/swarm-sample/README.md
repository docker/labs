# Service deployment on a swarm

Script that create a swarm cluster and deploy a simple service.
Swarm is created with Swarm mode of Engine 1.12. Can be created on
* Virtualbox
* Digitalocean
* Amazon EC2

Note: currently, if deploying on AWS, only EU (Ireland) region is available. Make sure you use a Key Pairs for this region

# Usage

```
./swarm.sh [--driver provider]
           [--amazonec2-access-key ec2_access_key]
           [--amazonec2-secret-key ec2_secret_key]
           [--amazonec2-security-group ec2_security_group]
           [--digitalocean_token]
           [-m|--manager nbr_manager]
           [-w|--worker nbr_worker]
           [-r|--replica nbr_replica]
           [-p|--port exposed_port]
```

Several parameters can be provided
* driver used ("virtualbox", "digitalocean", "amazonec2") (default: "virtualbox")
* number of manager (default: 3)
* number of worker (default: 5)
* number of replicas for the deployed service (lucj/randomcity:1.1) (default: 5)
* port exposed by the cluster (default: 8080)
* digitalocean token (if digitalocean driver specified)
* amazon access key, secret key, security group (currently only for EU (Ireland) region) (if amazonec2 driver is specified)

# Example

Let's create a swarm cluster with 2 manager and 2 worker nodes locally (with virtualbox)

```
$ ./swarm.sh --manager 2 --worker 2
->  about to create a swarm with 2 manager(s) and 2 workers on virtualbox machines
-> creating Docker host for manager 1 (please wait)
-> creating Docker host for manager 2 (please wait)
-> creating Docker host for worker 1 (please wait)
-> creating Docker host for worker 2 (please wait)
-> init swarm
Swarm initialized: current node (99xi3bzlgobxmeff573qitctg) is now a manager.
-> join manager 2 to the swarm
Node f4wocnel60xwfn2z522a645ba accepted in the swarm.
-> join worker 1 to the swarm
This node joined a Swarm as a worker.
-> join worker 2 to the swarm
This node joined a Swarm as a worker.
-> deploy service with 5 replicas with exposed port 8080
-> waiting for service 5ny5u5pmfw75mnomleb34a3kp to be available
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
-> service available on port 8080 of any node
ID            NAME  REPLICAS  IMAGE                COMMAND
5ny5u5pmfw75  city  5/5       lucj/randomcity:1.1
ID                         NAME    SERVICE  IMAGE                LAST STATE          DESIRED STATE  NODE
1j157qz7nu4kaqmack4zuwibm  city.1  city     lucj/randomcity:1.1  Running 20 seconds  Running        manager1
72y2off8y5f8zp4djzmjdzowg  city.2  city     lucj/randomcity:1.1  Running 20 seconds  Running        worker1
efzaweh8lhj9aalrgdhnx26i0  city.3  city     lucj/randomcity:1.1  Running 20 seconds  Running        manager2
1f5ccot3wn3yhrhfbqf6vj5d5  city.4  city     lucj/randomcity:1.1  Running 20 seconds  Running        worker2
f53ummqn8mba0hzy15w08pxj4  city.5  city     lucj/randomcity:1.1  Running 20 seconds  Running        worker2
```


# Docker hosts

List all Docker host created

```
$ docker-machine ls
NAME          ACTIVE   DRIVER         STATE     URL                         SWARM   DOCKER        ERRORS
manager1      -        virtualbox     Running   tcp://192.168.99.100:2376           v1.12.0-rc2
manager2      -        virtualbox     Running   tcp://192.168.99.101:2376           v1.12.0-rc2
worker1       -        virtualbox     Running   tcp://192.168.99.102:2376           v1.12.0-rc2
worker2       -        virtualbox     Running   tcp://192.168.99.103:2376           v1.12.0-rc2
```

# Service details

The test service deployed is a simple http server that returns a message with
* the ip of the container that handled the request
* a random city of the world

# Test deployed service

Send several requests to the manager1

```
$ curl 192.168.99.100:8080
{"message":"10.255.0.7 suggests to visit Zebunto"}
$ curl 192.168.99.100:8080
{"message":"10.255.0.8 suggests to visit Areugpip"}
$ curl 192.168.99.100:8080
{"message":"10.255.0.10 suggests to visit Fozbovsav"}
$ curl 192.168.99.100:8080
{"message":"10.255.0.9 suggests to visit Kitunweg"}
$ curl 192.168.99.100:8080
{"message":"10.255.0.11 suggests to visit Aviznuk"}
$ curl 192.168.99.100:8080
{"message":"10.255.0.7 suggests to visit Nedhikmu"}
$ curl 192.168.99.100:8080
{"message":"10.255.0.8 suggests to visit Palmenme"}
```

Send several requests to the worker2

```
$ curl http://192.168.99.102:8080
{"message":"10.255.0.8 suggests to visit Wehappap"}
$ curl http://192.168.99.102:8080
{"message":"10.255.0.11 suggests to visit Jocuvdam"}
$ curl http://192.168.99.102:8080
{"message":"10.255.0.12 suggests to visit Suvigenuh"}
$ curl http://192.168.99.102:8080
{"message":"10.255.0.9 suggests to visit Jinonat"}
```

The requests are dispatched in a round robin fashion to the running containers.

# Examples with other drivers

## Run 3 managers and 6 workers on DigitalOcean

```
./swarm.sh --driver digitalocean --digitalocean_token $DO_TOKEN --manager 3 --worker 6
```

## Run 3 managers and 6 workers on AmazonEC2

```
./swarm.sh --driver amazonec2 --amazonec2-access-key $AWS_ACCESS_KEY --amazonec2-secret-key $AWS_SECRET_KEY --amazonec2-security-group default --manager 3 --worker 6
```

note: make sure the security group provided (default in this example) allow communication between hosts and open the exposed port (8080 by default) to the outside

# Status

- [x] Virtualbox deployment
- [x] Digitalocean deployment (Ubuntu 14.04 / 1gb / lon1)
- [x] Amazon deployment (Ubuntu 14.04 / t2.micro / EU (Ireland))
- [ ] DigitalOcean deployment with image / size / region selection
- [ ] Amazon deployment with AMI / instance type / region selection
- [ ] Amazon deployment with automatic opening of exposed port in SecurityGroup
