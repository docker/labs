# Container networking on a single Docker host

## Default networks

* 3 default networks on node1 Docker host

```
$ docker network ls
NETWORK ID          NAME            DRIVER
d87b8fc4c466        bridge          bridge
efaf610f57a5        host            host
f7d0de539edd        none            null
```

**By default, Docker engine attaches each container to the bridge network (network id is d87b8fc4c466)**

## Default bridge network

Let's run 2 container using the default bridge network

```
$ docker run --name mongo -d mongo:3.2
$ docker run --name box -d busybox top
```

Make sure the containers are listed in the bridge network

```
$ docker network inspect --format='{{json .Containers}}' d87b8fc4c466 | python -m json.tool
{
    "0b8fedf4613c7275d89861037ea1b23ad4d65ab10f16df67bf976d9cb5652311": {
        "EndpointID": "0cf0cd3b2e0438c6f68c6a1e2f7587b63c48bda74911af55d1040f0d2fb117d2",
        "IPv4Address": "172.17.0.3/16",
        "IPv6Address": "",
        "MacAddress": "02:42:ac:11:00:03",
        "Name": "mongo"
    },
    "6cb5e5f4a1bcc37925407b39f2dde41f2b370fc48a21f8289da91d17b3763a4c": {
        "EndpointID": "2a6412d3c3c25545a59ea148e317b2046965c0fe5c1eeae2c51f4f882aaa6b36",
        "IPv4Address": "172.17.0.2/16",
        "IPv6Address": "",
        "MacAddress": "02:42:ac:11:00:02",
        "Name": "box"
    }
}
```

**A container cannot be addressed by its name :(**

```
$ docker run -ti busybox /bin/sh
/ # ping mongo
ping: bad address 'mongo'
/ # ping box
ping: bad address 'box'
```

## User defined bridge network

Create a bridge network with Docker network commands

````
$ docker network create mongonet
ce9ea3b69d6ee2ecf56b40bd35b8a43f8505c8ca0473bc37bdede3711ecf60c1

$ docker network ls
NETWORK ID          NAME            DRIVER
d87b8fc4c466        bridge          bridge
efaf610f57a5        host            host
ce9ea3b69d6e        mongonet        bridge
f7d0de539edd        none            null
````

Run container in the newly defined network

````
$ docker run --name mongo --net mongonet -d mongo:3.2

$ docker run --net mongonet -ti busybox /bin/sh
/ # / # ping -c 3 mongo
PING mongo (172.18.0.2): 56 data bytes
64 bytes from 172.18.0.2: seq=0 ttl=64 time=0.058 ms
64 bytes from 172.18.0.2: seq=1 ttl=64 time=0.085 ms
64 bytes from 172.18.0.2: seq=2 ttl=64 time=0.072 ms

--- mongo ping statistics ---
3 packets transmitted, 3 packets received, 0% packet loss
round-trip min/avg/max = 0.058/0.071/0.085 ms
````

Containers can be address by their name through the DNS name server embedded in Docker 1.10+

## Test our application

Run db and application containers in the new bridge network

```
$ docker run --name mongo --net mongonet -d mongo:3.2
$ docker run --name app --net mongonet -p “1337:1337” -d -e “MONGO_URL=mongodb://mongo/messageApp” message-app:v1
```

(Use mongocontainer’s name in environment variable)

Test HTTP Rest API

```
$ curl -XPOST http://192.168.99.100:1337/message?text=hello
{
  "text": "hello",
  "createdAt": "2016-06-06T14:01:05.764Z",
  "updatedAt": "2016-06-06T14:01:05.764Z",
  "id": "57558221a4461312009ce88c"
}
$ curl -XGET http://192.168.99.100:1337/message
[
  {
    "text": "hello",
    "createdAt": "2016-06-06T14:01:05.764Z",
    "updatedAt": "2016-06-06T14:01:05.764Z",
    "id": "57558221a4461312009ce88c"
  }
]
```

Application container is connected to mongo container using container name

# Packaging of the application with Docker Compose

* docker-compose file enables to easily package a multi containers application

```
version: '2'
services:
  mongo:
    image: mongo:3.2
    volumes:
      - mongo-data:/data/db
    expose:
      - "27017"
  app:
    image: message-app:v1
    ports:
      - "80"
    links:
      - mongo
    depends_on:
      - mongo
    environment:
      - MONGO_URL=mongodb://mongo/messageApp
volumes:
  mongo-data:
```

* define one database container and one api container
* internal port of app container is mapped to a random port on the host
* application container is connected to mongo container using container name
* volume used to mount mongodb data folder

# Lifecycle and scalability

* lifecycle
  * ```docker-compose  up```  (-d option enables the application to run in background)
  * ```docker-compose ps```
  * ```docker-compose stop```
* scalability
  * ```docker-compose scale app=3```

But how are the new containers found ? need to add a load balancer that will be updated each time a container is created or removed








