# 11 - Logs

Logs need to be handle as a timeseries of textual events

The application should not handle or save logs locally but must write them in stdout / stderr.

A lot of services offer a centralized log management ([Elastic Stack / ELK](https://www.elastic.co/products) , [Splunk](http://splunk.com), [Logentries](https://logentries.com), ...), and most of them are very easily integrated with Docker.

Example of Logentries dashboard:

![Logentries](https://dl.dropboxusercontent.com/u/2330187/docker/labs/12factor/logentries.png)

## What does that mean for our application ?

In order to centralize the logs, we can add a **log** service in our docker-compose file. The API token (provided by logentries) needs to be added to the service.

As we can see in the volume section, the Docker socket needs to be mounted so logentries container can retrieve each logs emitted by the running containers and send them to logentries external service.

```
log:
  command: '-t XXXXXX-XXXXX-XXXXX-XXXXX'
  image: 'logentries/docker-logentries’
  restart: always
  volumes:
    - '/var/run/docker.sock:/var/run/docker.sock'
```


[Previous](10_dev_prod_parity.md) - [Next](12_admin_processes.md)
