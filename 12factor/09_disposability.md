# 9 - Disposability

Each process of an application must be disposable.

* it must have a quick startup
  * ease the horizontal scalability
* it must ensure a clean shutdown
  * stop listening on the port
  * finish to handle the current request
  * usage of a queueing system for long lasting (worker type) process

## What does that mean for our application ?

Our application exposes HTTP endPoints that are easy and quick to handle. If we were to have some long lasting worker processes, the usage of a queueing system, like Apache Kafka, would be a great choice.

Kafka stores indexes of events processed by each worker. When a worker is restared, it can provide an index indicating at which point in time it needs to restart the event handling. Doing so no events are lost.

[Docker Store](https://store.docker.com) offers several image of Kafka ([Spotify](https://store.docker.com/community/images/spotify/kafka), [Wurstmeister](https://store.docker.com/community/images/wurstmeister/kafka), ...) that can easily be integrated in the docker-compose file of the application.

Below is an example of how Kafka (and zookeeper) could be added to our docker-compose file. Of course, this means the application has been slightly changed to be able to write and read to/from Kafka.


```
# Kafka message broker
zookeeper:
  image: wurstmeister/zookeeper
  ports:
    - "2181:2181"
kafka:
  image: wurstmeister/kafka
  ports:
    - "9092:9092"
  links:
    - zookeeper:zk
  environment:
    KAFKA_ADVERTISED_HOST_NAME: 192.168.99.100
    KAFKA_CREATE_TOPICS: "DATA:1:1"
  volumes:
    - /var/run/docker.sock:/var/run/docker.sock
```

[Previous](08_concurrency.md) - [Next](10_dev_prod_parity.md)
