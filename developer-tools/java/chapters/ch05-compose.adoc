:imagesdir: images

[[Docker_Compose]]
= Multi-container application using Docker Compose

[quote, github.com/docker/compose]
Docker Compose is a tool for defining and running complex applications with Docker. With Compose, you define a multi-container application in a single file, then spin your application up in a single command which does everything that needs to be done to get it running.

An application using Docker containers will typically consist of multiple containers. With Docker Compose, there is no need to write shell scripts to start your containers. All the containers are defined in a configuration file using _services_, and then `docker-compose` script is used to start, stop, and restart the application and all the services in that application, and all the containers within that service. The complete list of commands is:

[options="header"]
|====
| Command | Purpose
| `build` | Build or rebuild services
| `help` | Get help on a command
| `kill` | Kill containers
| `logs` | View output from containers
| `port` | Print the public port for a port binding
| `ps` | List containers
| `pull` | Pulls service images
| `restart` | Restart services
| `rm` | Remove stopped containers
| `run` | Run a one-off command
| `scale` | Set number of containers for a service
| `start` | Start services
| `stop` | Stop services
| `up` | Create and start containers
| `migrate-to-labels  Recreate containers to add labels
|====

The application used in this section will show how to query a Couchbase sample data using simple Java EE application deployed on WildFly. The Java EE application will use JAX-RS to publish REST endpoint which will then be invoked using `curl`.

WildFly and Couchbase will be running in two separate containers, and thus making this a multi-container application.

== Configuration File

. Entry point to Docker Compose is Compose file, usually called `docker-compose.yml`. Create a new directory `javaee`. In that directory, create a new file `docker-compose.yml` in it. Use the following contents:

```
version: '3'
services:
  web:
    image: arungupta/couchbase-javaee:travel
    environment:
      - COUCHBASE_URI=db
    ports:
      - 8080:8080
      - 9990:9990
    depends_on:
      - db
  db:
    image: arungupta/couchbase:travel
    ports:
      - 8091:8091
      - 8092:8092 
      - 8093:8093 
      - 11210:11210
```

In this Compose file:

. Two services in this Compose are defined by the name `db` and `web` attributes
. Image name for each service defined using `image` attribute
. The `arungupta/couchbase:travel` image starts Couchbase server, configures it using http://developer.couchbase.com/documentation/server/current/rest-api/rest-endpoints-all.html[Couchbase REST API], and loads a sample bucket
. The `arungupta/couchbase-javaee:travel` image starts WildFly and deploys application WAR file built from https://github.com/arun-gupta/couchbase-javaee. Clone that project if you want to build your own image.
. `environment` attribute defines environment variables accessible by the application deployed in WildFly. `COUCHBASE_URI` refers to the database service. This is used in the application code as shown at https://github.com/arun-gupta/couchbase-javaee/blob/master/src/main/java/org/couchbase/sample/javaee/Database.java.
. Port forwarding is achieved using `ports` attribute
. `depends_on` attribute allows to express dependency between services. In this case, Couchbase will be started before WildFly. Application-level health are still user's responsibility.

=== Start Application

All services in the applicaiton can be started, in detached mode, by giving the command:

```
docker-compose up -d
```

An alternate Compose file name can be specified using `-f` option.

An alternate directory where the compose file exists can be specified using `-p` option.

This shows the output as:

```
Creating network "javaee_default" with the default driver
Creating javaee_db_1
Creating javaee_web_1
```

The output may differ slightly if the images are downloaded as well.

Started services can be verified using the command `docker-compose ps`:

```
           Name                       Command                       State                        Ports            
-----------------------------------------------------------------------------------------------------------------
javaee_db_1                  /entrypoint.sh /opt/couchb   Up                           11207/tcp,                 
                             ...                                                       0.0.0.0:11210->11210/tcp,  
                                                                                       11211/tcp, 18091/tcp,      
                                                                                       18092/tcp, 18093/tcp,      
                                                                                       0.0.0.0:8091->8091/tcp,    
                                                                                       0.0.0.0:8092->8092/tcp,    
                                                                                       0.0.0.0:8093->8093/tcp,    
                                                                                       8094/tcp                   
javaee_web_1                 /opt/jboss/wildfly/bin/sta   Up                           0.0.0.0:8080->8080/tcp,    
                             ...                                                       0.0.0.0:9990->9990/tcp     
```


This provides a consolidated view of all the services, and containers within each of them.

Alternatively, the containers in this application, and any additional containers running on this Docker host can be verified by using the usual `docker container ls` command:

```
339e35369e1f        arungupta/couchbase-javaee:travel   "/opt/jboss/wildfl..."   3 minutes ago       Up 3 minutes        0.0.0.0:8080->8080/tcp, 0.0.0.0:9990->9990/tcp                                                                javaee_web_1
599cd4ea5de4        arungupta/couchbase:travel          "/entrypoint.sh /o..."   3 minutes ago       Up 3 minutes        8094/tcp, 0.0.0.0:8091-8093->8091-8093/tcp, 11207/tcp, 11211/tcp, 0.0.0.0:11210->11210/tcp, 18091-18093/tcp   javaee_db_1
```

Service logs can be seen using `docker-compose logs` command.

`depends_on` attribute in Compose definition file ensures the container start up order. But application-level start up needs to be ensured by the applications running inside container. In our case, WildFly starts up rather quickly but takes a few seconds for the database to start up. This means the Java EE application deployed in WildFly is not able to communicate with the database. This outlines a best practice when building micro services applications: you must code defensively and ensure in your application initialization that the micro services you depend on have started, without assuming startup order. This is shown in the database initialization code at https://github.com/arun-gupta/couchbase-javaee/blob/master/src/main/java/org/couchbase/sample/javaee/Database.java. It performs the following checks:

. Bucket exists
. Query service of Couchbase is up and running
. Sample bucket is fully loaded

The logs for the application can be shown using `docker-compose logs -f`:

[source, text]
----
web_1  | 02:15:13,813 INFO  [com.couchbase.client.core.node.Node] (cb-io-1-4) Connected to Node db
web_1  | 02:15:16,270 INFO  [stdout] (ServerService Thread Pool -- 65) Trying to connect to the database
web_1  | 02:15:16,301 INFO  [com.couchbase.client.core.node.Node] (cb-io-1-1) Connected to Node db
web_1  | 02:15:16,624 INFO  [com.couchbase.client.core.config.ConfigurationProvider] (cb-computations-3) Opened bucket travel-sample
web_1  | 02:15:16,660 INFO  [stdout] (ServerService Thread Pool -- 65) Sleeping for 3 secs (waiting for travel-sample bucket) ...
web_1  | 02:15:19,662 INFO  [stdout] (ServerService Thread Pool -- 65) Bucket found!
web_1  | 02:15:19,867 INFO  [stdout] (ServerService Thread Pool -- 65) Sleeping for 3 secs (waiting for Query service or bucket to be loaded) ...
web_1  | 02:15:22,887 INFO  [stdout] (ServerService Thread Pool -- 65) Sleeping for 3 secs (waiting for Query service or bucket to be loaded) ...

. . .

web_1  | 02:16:37,416 INFO  [org.wildfly.extension.undertow] (ServerService Thread Pool -- 65) WFLYUT0021: Registered web context: /airlines
web_1  | 02:16:37,546 INFO  [org.jboss.as.server] (ServerService Thread Pool -- 34) WFLYSRV0010: Deployed "airlines.war" (runtime-name : "airlines.war")
web_1  | 02:16:37,781 INFO  [org.jboss.as] (Controller Boot Thread) WFLYSRV0060: Http management interface listening on http://127.0.0.1:9990/management
web_1  | 02:16:37,781 INFO  [org.jboss.as] (Controller Boot Thread) WFLYSRV0051: Admin console listening on http://127.0.0.1:9990
web_1  | 02:16:37,782 INFO  [org.jboss.as] (Controller Boot Thread) WFLYSRV0025: WildFly Full 10.1.0.Final (WildFly Core 2.2.0.Final) started in 97931ms - Started 443 of 691 services (404 services are lazy, passive or on-demand)
----

=== Verify Application

Now that the WildFly and Couchbase servers have been configured, let's access the application. You need to specify IP address of the host where WildFly is running (`localhost` in our case).

The endpoint can be accessed in this case as:

    curl -v http://localhost:8080/airlines/resources/airline

The output is shown as:

```
*   Trying ::1...
* Connected to localhost (::1) port 8080 (#0)
> GET /airlines/resources/airline HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.43.0
> Accept: */*
> 
< HTTP/1.1 200 OK
< Connection: keep-alive
< X-Powered-By: Undertow/1
< Server: WildFly/10
< Content-Type: application/octet-stream
< Content-Length: 1402
< Date: Fri, 03 Feb 2017 02:22:43 GMT
< 
* Connection #0 to host localhost left intact
[{"travel-sample":{"country":"United States","iata":"Q5","callsign":"MILE-AIR","name":"40-Mile Air","icao":"MLA","id":10,"type":"airline"}}, {"travel-sample":{"country":"United States","iata":"TQ","callsign":"TXW","name":"Texas Wings","icao":"TXW","id":10123,"type":"airline"}}, {"travel-sample":{"country":"United States","iata":"A1","callsign":"atifly","name":"Atifly","icao":"A1F","id":10226,"type":"airline"}}, {"travel-sample":{"country":"United Kingdom","iata":null,"callsign":null,"name":"Jc royal.britannica","icao":"JRB","id":10642,"type":"airline"}}, {"travel-sample":{"country":"United States","iata":"ZQ","callsign":"LOCAIR","name":"Locair","icao":"LOC","id":10748,"type":"airline"}}, {"travel-sample":{"country":"United States","iata":"K5","callsign":"SASQUATCH","name":"SeaPort Airlines","icao":"SQH","id":10765,"type":"airline"}}, {"travel-sample":{"country":"United States","iata":"KO","callsign":"ACE AIR","name":"Alaska Central Express","icao":"AER","id":109,"type":"airline"}}, {"travel-sample":{"country":"United Kingdom","iata":"5W","callsign":"FLYSTAR","name":"Astraeus","icao":"AEU","id":112,"type":"airline"}}, {"travel-sample":{"country":"France","iata":"UU","callsign":"REUNION","name":"Air Austral","icao":"REU","id":1191,"type":"airline"}}, {"travel-sample":{"country":"France","iata":"A5","callsign":"AIRLINAIR","name":"Airlinair","icao":"RLA","id":1203,"type":"airline"}}]
```

This shows 10 airlines from the `travel-sample` bucket.

A single resource can be obtained:

    curl -v http://localhost:8080/airlines/resources/airline/137

It shows the output:

```
*   Trying ::1...
* Connected to localhost (::1) port 8080 (#0)
> GET /airlines/resources/airline/137 HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.43.0
> Accept: */*
> 
< HTTP/1.1 200 OK
< Connection: keep-alive
< X-Powered-By: Undertow/1
< Server: WildFly/10
< Content-Type: application/octet-stream
< Content-Length: 131
< Date: Fri, 03 Feb 2017 02:24:26 GMT
< 
* Connection #0 to host localhost left intact
{"travel-sample":{"country":"France","iata":"AF","callsign":"AIRFRANS","name":"Air France","icao":"AFR","id":137,"type":"airline"}}
```

A new resource can be created:

    curl -v -H "Content-Type: application/json" -X POST -d '{"country":"France","iata":"A5","callsign":"AIRLINAIR","name":"Airlinair","icao":"RLA","type":"airline"}' http://localhost:8080/airlines/resources/airline

```
*   Trying ::1...
* Connected to localhost (::1) port 8080 (#0)
> POST /airlines/resources/airline HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.43.0
> Accept: */*
> Content-Type: application/json
> Content-Length: 104
> 
* upload completely sent off: 104 out of 104 bytes
< HTTP/1.1 200 OK
< Connection: keep-alive
< X-Powered-By: Undertow/1
< Server: WildFly/10
< Content-Type: application/octet-stream
< Content-Length: 117
< Date: Fri, 03 Feb 2017 02:24:51 GMT
< 
* Connection #0 to host localhost left intact
{"country":"France","iata":"A5","callsign":"AIRLINAIR","name":"Airlinair","icao":"RLA","id":"19810","type":"airline"}
```

The output shows the id of the newly created resource.

Let's update this resource using the id:

    curl -v -H "Content-Type: application/json" -X PUT -d '{"country":"France","iata":"A5","callsign":"AIRLINAIR","name":"Airlin Air","icao":"RLA","type":"airline","id": "19810"}' http://localhost:8080/airlines/resources/airline/19810

The only change is name from `"AirlineAir"` to `"Airlin Air"`.

```
*   Trying ::1...
* Connected to localhost (::1) port 8080 (#0)
> PUT /airlines/resources/airline/19810 HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.43.0
> Accept: */*
> Content-Type: application/json
> Content-Length: 119
> 
* upload completely sent off: 119 out of 119 bytes
< HTTP/1.1 200 OK
< Connection: keep-alive
< X-Powered-By: Undertow/1
< Server: WildFly/10
< Content-Type: application/octet-stream
< Content-Length: 118
< Date: Fri, 03 Feb 2017 02:25:18 GMT
< 
* Connection #0 to host localhost left intact
{"country":"France","iata":"A5","callsign":"AIRLINAIR","name":"Airlin Air","icao":"RLA","id":"19810","type":"airline"}
```

Let's delete the created resource:

    curl -v -X DELETE http://localhost:8080/airlines/resources/airline/19810

```
*   Trying ::1...
* Connected to localhost (::1) port 8080 (#0)
> DELETE /airlines/resources/airline/19810 HTTP/1.1
> Host: localhost:8080
> User-Agent: curl/7.43.0
> Accept: */*
> 
< HTTP/1.1 200 OK
< Connection: keep-alive
< X-Powered-By: Undertow/1
< Server: WildFly/10
< Content-Type: application/octet-stream
< Content-Length: 136
< Date: Fri, 03 Feb 2017 02:25:36 GMT
< 
* Connection #0 to host localhost left intact
{"travel-sample":{"country":"France","iata":"A5","callsign":"AIRLINAIR","name":"Airlin Air","icao":"RLA","id":"19810","type":"airline"}}
```

== Shutdown Application

Shutdown the application using `docker-compose down`:

```
Stopping javaee_web_1 ... done
Stopping javaee_db_1 ... done
Removing javaee_web_1 ... done
Removing javaee_db_1 ... done
Removing network javaee_default
```

This stops the container in each service and removes the services. It also deletes any networks that were created as part of this application.

