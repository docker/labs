# Deployment on a Docker Swarm

As for the multi Docker host environment, a Docker Swarm requires a key value store to gather the nodes / containers configurations and states.

## Creation of a key-value store

Note: if you still have the key-value store from the previous chapter do not re-create it and go directly to the creation of the Swarm.

Several steps are needed to run the key-value store

* Create dedicated Docker host with Machine) ```docker-machine create -d virtualbox consul```
* Switch to context of the newly created machine ```eval "$(docker-machine env consul)"```
* Run container based on Consul image ```docker run -d -p "8500:8500" -h "consul" progrium/consul -server -bootstrap```

## Creation of the Swarm

Additional options need to be provided to docker-machine in order to define a Swarm.

### Creation of the Swarm master

```
$ docker-machine create \
-d virtualbox \
--swarm \
--swarm-master \
--swarm-discovery="consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-store=consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-advertise=eth1:2376" \
demo0
```

### Creation of the Swarm agent

```
$ docker-machine create \
 -d virtualbox \
--swarm \
--swarm-discovery="consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-store=consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-advertise=eth1:2376" \
demo1
```

### List the nodes

We have created 3 Docker hosts (key-store, Swarm master, Swarm agent)

```
$ docker-machine ls

NAME     ACTIVE   DRIVER         STATE     URL                         SWARM
consul   *        virtualbox     Running   tcp://192.168.99.100:2376
demo0    -        virtualbox     Running   tcp://192.168.99.101:2376   demo0 (master)
demo1    -        virtualbox     Running   tcp://192.168.99.102:2376   demo1
```

## Create a DNS load balancer

In order to load balance the traffic towards several instances of our **app** service, we will add a new service. This one uses the DNS round-robin capability of Docker engine (version 1.11) for containers with the same network alias.

Note: to present the DNS round-robin feature, we do not use the load balancer of the previous chapter (dockercloud/haproxy).

The following Dockerfile uses nginx:1.9 official image and add a custom nginx.conf configuration file.

```
FROM nginx:1.9

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

The following nginx.conf file define a proxy_pass directive towards **http://apps** for each request received on port 80.

**apps** is the value we will set as the app service network alias.

```
user nginx;
worker_processes 2;
events {
  worker_connections 1024;
}
http {
  access_log /var/log/nginx/access.log;
  error_log /var/log/nginx/error.log;

  # 127.0.0.11 is the address of the Docker embedded DNS server
  resolver 127.0.0.11 valid=1s;
  server {
    listen 80;
    # apps is the name of the network alias in Docker
    set $alias "apps";

    location / {
      proxy_pass http://$alias;
    }
  }
}
```

Let's build and publish the image of this load-balancer to Docker Cloud:

```
# Create image
$ docker build -t lucj/lb-dns .

# Publish image
$ docker push -t lucj/lb-dns
```

The image can now be used in our Docker Compose file.

## Update our docker-compose file

The new version of the docker-compose.yml file is the following one

```
version: '3'
services:
  mongo:
    image: mongo:3.2
    networks:
      - backend
    volumes:
      - mongo-data:/data/db
    expose:
      - "27017"
    environment:
      - "constraint:node==demo0"
  lbapp:
    image: lucj/lb-dns
    networks:
      - backend
    ports:
      - "8000:80"
    environment:
      - "constraint:node==demo0"
  app:
    image: lucj/message-app
    expose:
      - "80"
    environment:
      - MONGO_URL=mongodb://mongo/messageApp
      - "constraint:node==demo1"
    networks:
      backend:
        aliases:
          - apps
    depends_on:
      - lbapp
volumes:
  mongo-data:
networks:
  backend:
    driver: overlay
```

There are several important updates here
* usage of the lb-dns image for the load balancer service
* constraints to choose the nodes on which each service will run (needed in our example to illustrate the DNS round robin)
* creation of a new user-defined overlay network to enable each container to communicate with each other through their name
* for each service, definition of the network used
* definition of network alias for the **app** service (crucial item as this is the one that will enable nginx to proxy requests)

## Deployment and scaling of the application

In order to run the application in this Swarm, we will issue the following commands
* switch to the swarm master context ```eval $(docker-machine env --swarm demo0)```
* run the new compose file ```docker-compose up```
* increase the number of **app** service instances ```docker-compose scale app=5```

Our application is then available through http://192.168.99.101:8000/message

192.168.99.101 is the IP of the Swarm master. 8000 is the port exported by the load balancer to the outside.


