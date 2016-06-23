# Test on swarm 1.12

A quick script that deploys a sample http server on a swarm created with Engine 1.12 on virtualbox

The http server returns a json object with ip of the container that handled the request and a random city in the world.

Example:
```
$ curl 192.168.99.100:8080
{"message":"10.255.0.9 suggests to visit Necamudu"}
curl 192.168.99.100:8080
{"message":"10.255.0.11 suggests to visit Foejroh"}
```

Several parameters can be provided:
* total number of nodes (1 manager + N workers)
* number of replicas for the deployed service (lucj/randomcity:1.1)
* port exposed by the cluster
