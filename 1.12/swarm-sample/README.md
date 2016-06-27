# Service deployment on a swarm

Script that deploys a sample service swarm cluster created on virtualbox or digitalocean with Engine 1.12

# Usage

./swarm.sh [--digitalocean_token] [-m|--manager nbr_manager] [-w|--worker nbr_worker] [-r|--replica nbr_replica] [-p|--port exposed_port]"

Several parameters can be provided
* number of manager (default: 3)
* number of worker (default: 5)
* number of replicas for the deployed service (lucj/randomcity:1.1) (default: 5)
* port exposed by the cluster (default: 8080)
* digitalocean token (if not provided, virtualbox driver used)

# Example

Let's create a swarm cluster with 2 manager and 2 worker nodes

```
$ ./swarm.sh --manager 2 --worker 2
->  about to create a swarm with 2 manager(s) and 2 workers
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

The requests are dispatched to the running containers.
