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

Example of a run without providing parameters

```
$ ./swarm.sh
-> swarm will start with 1 manager and 2 workers
-> creating Docker host for manager (please wait)
-> creating Docker host for worker 1 (please wait)
-> creating Docker host for worker 2 (please wait)
-> init swarm
Swarm initialized: current node (1mjj1lexqflvjibzble3i1jg5) is now a manager.
-> join worker to the swarm
This node joined a Swarm as a worker.
-> join worker to the swarm
This node joined a Swarm as a worker.
-> deploy service with 5 replicas with exposed port 8080
-> waiting for service 4675wvkghv6tdgxzqxbey2l4x to be available
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
... retrying in 2 seconds
-> service available on http://192.168.99.100:8080
ID            NAME  REPLICAS  IMAGE                COMMAND
4675wvkghv6t  city  5/5       lucj/randomcity:1.1
ID                         NAME    SERVICE  IMAGE                LAST STATE          DESIRED STATE  NODE
483v64jv89sc5ctu9f94327tq  city.1  city     lucj/randomcity:1.1  Running 20 seconds  Running        manager
do86o8k9oncucdxf3f27kod2y  city.2  city     lucj/randomcity:1.1  Running 20 seconds  Running        worker2
6wnygan733i7y6s22k43hao00  city.3  city     lucj/randomcity:1.1  Running 20 seconds  Running        worker1
eik0vqnh8spxaqakx33nq4pps  city.4  city     lucj/randomcity:1.1  Running 20 seconds  Running        worker1
2a26tfou2sp25jia6jdjyol87  city.5  city     lucj/randomcity:1.1  Running 20 seconds  Running        manager
```
