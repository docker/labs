# 7 - Port binding

This factor is related to the exposition of the application to the outside.

To be compliant with 12 factor, an app must use specialized dependencies (such as http server, ...) and exposes its service through a port.

The host has the responsibility to route the request to the correct application through port mapping.

## What does that mean for our application ?

Docker already handles that for us, as we can see in the docker-compose file. The **app** container exposes port 80 internally and the host maps it against its port 8000.

```
version: '3'
services:
  mongo:
    image: mongo:3.2
    volumes:
      - mongo-data:/data/db
    expose:
      - "27017"
  kv:
    image: redis:alpine
    volumes:
      - redis-data:/data
    expose:
      - "6379"
  app:
    image: message-app:v0.2 # New version taking into account REDIS_URL
    ports:
      - "8000:80"     // app service is exposed on the port 8000 of the host
    links:
      - mongo
    depends_on:
      - mongo
    environment:
      - MONGO_URL=mongodb://mongo/messageApp
      - REDIS_URL=redis
volumes:
  mongo-data:
  redis-data:
```

If several instances of the app services needs to be deployed, the configuration above cannot be used as a given port on the host cannot map several ports in the containers.

In this case, we can use a load balancer to which the host will map a port. The load balancer will then be in charge to balance the traffic on the different instances of the services. 

[Previous](06_processes.md) - [Next](08_concurrency.md)
