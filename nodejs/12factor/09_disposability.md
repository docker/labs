# 9 - Disposability

The processus of the application must be disposable.

Each one must ensure
* a quick startup
  * ease the horizontal scalability
* a clean shutdown
  * stop listening on the http port
  * finish the handling of the current request
  * usage of a queueing system for long lasting (worker type) processus

## What does that mean for our application ?

Our application only exposes HTTP requests easy and quick to perform. If we were to have some long lasting worker processus, the usage of a queueing system, like Apache Kafka, would be a great choice. Kafka stores indexes of events processed by each worker. When a worker is restared, it can provide an index indicating at which point in time it needs to restart the event handling.

[Docker Hub](https://hub.docker.com) offers several image of Kafka ([Spotify](https://hub.docker.com/r/spotify/kafka/), [Wurstmeister](https://hub.docker.com/r/wurstmeister/kafka/), ...) that can easily be integrated in the docker-compose file of the application.

Below is an example of how Kafka (and zookeeper) could be added to our docker-compose file. Of course, this means the applicatio has been slightly changed to be able to write and read from Kafka.


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
