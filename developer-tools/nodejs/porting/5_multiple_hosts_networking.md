# Container networking on multiple Docker hosts

## Prerequisite

The multihost networking is available out the box with libnetwork since Docker 1.9

This required to setup a key-value store first
* several are supported: etcd / consul / zookeeper
* keeps all the information regarding (networks / subnetworks, IP addresses of Docker hosts / containers, ...)

## Creation of a key-value store

Several steps are needed to run the key value store

* Create dedicated Docker host with Machine ```docker-machine create -d virtualbox consul```
* Switch to the context of the newly created host ```eval "$(docker-machine env consul)"```
* Run container based on [progirum/consul image](https://store.docker.com/images/consul) ```docker run -d -p "8500:8500" -h "consul" progrium/consul -server -bootstrap```
  
## Creation of Docker hosts that will run application containers

As for consul, we use Docker Machine to create 2 test Docker hosts

### Host 1

```
$ docker-machine create \
-d virtualbox \
--engine-opt="cluster-store=consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-advertise=eth1:2376" \
host1

$ docker $(docker-machine config host1) network ls
NETWORK ID          NAME                DRIVER
14753b15c63e        bridge              bridge
2cc7d35a48e3        none                null
ad05eeca763a        host                host
````

### Host 2

```
$ docker-machine create \
-d virtualbox \
--engine-opt="cluster-store=consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-advertise=eth1:2376" \
host2

$ docker $(docker-machine config host2) network ls
NETWORK ID          NAME                DRIVER
b7765c98adbf        bridge              bridge
48244d2fca3b        none                null
36a3858b68c8        host                host
```

As we've seen in a previous chapter, 3 default networks are available on each host: bridge / none / host.
We will create an overlay user defined network and benefit from the embedded DNS name server that enables container communication across nodes.

## Creation of an overlay network

As seen befire, a user defined network can easily be created. Let's create an overlay network, named **appnet**, from host1.

```docker $(docker-machine config host1) network create -d overlay appnet```

This network is also visible from host2 as we can see below.

```
$ docker $(docker-machine config host1) network ls
NETWORK ID          NAME                DRIVER
acd47b4c062d        appnet              overlay
14753b15c63e        bridge              bridge
2cc7d35a48e3        none                null
ad05eeca763a        host                host

$ docker $(docker-machine config host2) network ls
NETWORK ID          NAME                DRIVER
acd47b4c062d        appnet              overlay
b7765c98adbf        bridge              bridge
48244d2fca3b        none                null
36a3858b68c8        host                host
```

## Check cross host communication


Run the **mongo** container, based on mongo 3.2 offical image, on appnet network from host1

```docker $(docker-machine config host1) run -d --name mongo --net=appnet mongo:3.2```

Run the **box** container, based on busybox) on appnet network from host2

```docker $(docker-machine config host2) run -ti --name box --net=appnet busybox sh```

Even if **box** and **mongo** do not run on the same host, **box** can communicate with **mongo** container using its name through the DNS name server embedded in Docker 1.10+

```
/ # ping mongo
PING mongo (10.0.0.2): 56 data bytes
64 bytes from 10.0.0.2: seq=0 ttl=64 time=0.553 ms
…
/ # ping mongo.appnet
PING mongo.appnet (10.0.0.2): 56 data bytes
64 bytes from 10.0.0.2: seq=0 ttl=64 time=0.474 ms
…
```
