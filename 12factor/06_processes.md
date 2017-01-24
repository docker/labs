# 6 - Processes

An application is made up of several processes.

Each process must be stateless and must not have local storage (sessions, ...).

This is required
* for scalability
* fault tolerance (crashes, ...)

The data that need to be persisted, must be saved in a stateful resources (Database, shared filesystem, ...)

Eg: sessions can easily be saved in a Redis kv store

Note: Sticky session violate 12 factor.

## What does that mean for our application ?

In _config/sessions.js_, we need to modify the adapter to store session in a distributed Redis kv store (MongoDB is another possible option).

```
module.exports.session = {
  ...
  adapter: 'redis',
  host: process.env.REDIS_HOST || 'localhost',
  ...
};
```

Once done, the app needs to be rebuilt `docker build -t message-app:v0.2 .`

**REDIS_HOST** needs to be added to the docker-compose file as the new release will run against this kv store.

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
      - "8000:80"
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

[Previous](05_build_release_run.md) - [Next](07_port_binding.md)
