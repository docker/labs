# Deployment on a Docker Swarm

* Docker hosts cluster
* one or several swarm master (for HA)
  * orchestrator / scheduler
  * failover
* one Swarm agent per node
* easy to create with Docker Machine
* integration of Docker Machine / Docker Compose / Docker Swarm

## Creation of a key-value store

* creation of a Docker host
  * ```docker-machine create -d virtualbox consul```
* switch to context of newly created machine
  * ```eval "$(docker-machine env consul)"```
* run container based on Consul image
  *  ```docker run -d -p "8500:8500" -h "consul" progrium/consul -server -bootstrap```

## Creation of the swarm

### swarm master

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

### swarm agent

```
$ docker-machine create \
 -d virtualbox \
--swarm \
--swarm-discovery="consul://$(docker-machine ip consul):8500" \ --engine-opt="cluster-store=consul://$(docker-machine ip consul):8500" \
--engine-opt="cluster-advertise=eth1:2376" \
demo1
```

### List the nodes

3 Docker hosts created (key-store, Swarm master, Swarm node)


```
$ docker-machine ls

NAME          ACTIVE   DRIVER         STATE     URL                         SWARM
consul   *        virtualbox     Running   tcp://192.168.99.100:2376
demo0    -        virtualbox     Running   tcp://192.168.99.101:2376   demo0 (master)
demo1    -        virtualbox     Running   tcp://192.168.99.102:2376  demo1
```

## Create a DNS load balancer

**Dockerfile**

```
FROM nginx:1.9

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

**nginx.conf**
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

```
# Create image
$ docker build -t lucj/lb-dns .

# Publish image
$ docker push -t lucj/lb-dns
```

## Update our docker-compose file

* use lb load balancer
* add constraints to choose the nodes
* a new user defined overlay network is created
  * No need to use link between containers

```
version: '2'
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

## Deployment and scaling of the application

* switch to the swarm master contexte
  * ```eval $(docker-machine env --swarm demo0)```
* run application using networking option
  * ```docker-compose up```
* scaling
  * ```docker-compose scale app=5```
* messageApp API is available through http://192.168.99.101:8000/message
  * IP of the swarm master
  * Port of the load balancer


