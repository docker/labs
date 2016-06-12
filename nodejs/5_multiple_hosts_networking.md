# Container networking on multiple Docker hosts

## Prerequisite

* Docker 1.9+
  * multihost networking available out the box with libnetwork
* need to setup a Key Value store	
  * eg: etcd / consul / zookeeper
  * keeps all the information regarding
    * networks / subnetworks 
    * IP addresses of Docker hosts / containers
    * …

## Creation of a key-value store

* creation of a Docker host
  * ```docker-machine create -d virtualbox consul```
* switch to context of newly created machine
  * ```eval "$(docker-machine env consul)"```
* run container based on Consul image
  * ```docker run -d -p "8500:8500" -h "consul" progrium/consul -server -bootstrap```
  
## Creation of Docker hosts

### Host 1

```
$ docker-machine create \
-d virtualbox \
--engine-opt="cluster-store=consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-advertise=eth1:2376" \
host1

$ docker $(docker-machine config host1) network ls
NETWORK ID          NAME                DRIVER
14753b15c63e              bridge                 bridge
2cc7d35a48e3             none                   null
ad05eeca763a             host                    host
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
b7765c98adbf             bridge                 bridge
48244d2fca3b             none                   null
36a3858b68c8             host                    host
```

**default networks available on each host: bridge / none / host**

## Creation of an overlay network

* creation of a network from host1
  * docker $(docker-machine config host1) network create -d overlay appnet
* new network also visible from host2

```
$ docker $(docker-machine config host1) network ls
NETWORK ID          NAME                DRIVER
acd47b4c062d            appnet                 overlay
14753b15c63e              bridge                 bridge
2cc7d35a48e3             none                   null
ad05eeca763a             host                    host

$ docker $(docker-machine config host2) network ls
NETWORK ID          NAME                DRIVER
acd47b4c062d            appnet                overlay
b7765c98adbf             bridge                 bridge
48244d2fca3b             none                   null
36a3858b68c8             host                    host
```

## Creation of the containers

* run mongo container on appnet network from host1
  * ```docker $(docker-machine config host1) run -d --name mongo --net=appnet mongo:3.0```
* run busybox container on appnet network from host2
  * ```docker $(docker-machine config host2) run -ti --name box --net=appnet busybox sh```
* “box” container can communicate with “mongo” container using its name through the DNS name server embedded in Docker 1.10+

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




