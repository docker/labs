## <a name="challenges"></a>Challenges of Networking Containers and Microservices

Microservices practices have increased the scale of applications which has put even more importance on the methods of connectivity and isolation that we provide to applications. The Docker networking philosophy is application driven. It aims to provide options and flexibility to the network operators as well as the right level of abstraction to the application developers. 

Like any design, network design is a balancing act. __Docker Datacenter__ and the Docker ecosystem provides multiple tools to network engineers to achieve the best balance for their applications and environments. Each option provides different benefits and tradeoffs. The remainder of this guide details each of these choices so network engineers can understand what might be best for their environments.

Docker has developed a new way of delivering applications, and with that, containers have also changed some aspects of how we approach networking. The following topics are common design themes for containerized applications:

- __Portability__
	- _How do I guarantee maximum portability across diverse network environments while taking advantage of unique network characteristics?_

- __Service Discovery__ 
	- _How do I know where services are living as they are scaled up and down?_

- __Load Balancing__
	- _How do I share load across services as services themselves are brought up and scaled?_

- __Security__ 
	- _How do I segment to prevent the right containers from accessing each other?_
	- _How do I guarantee that a container with application and cluster control traffic is secure?_

- __Performance__  
 	- _How do I provide advanced network services while minimizing latency and maximizing bandwidth?_

- __Scalability__  
 	- _How do I ensure that none of these characteristics are sacrificed when scaling applications across many hosts?_

### <a name="concepts"></a>Concepts
This section contains 14 different short networking concept chapters. Feel free to skip right to the [tutorials](../tutorials.md) if you feel you are ready and come back here if you need a refresher. The concept chapters are:

1. [The Container Networking Model](01-cnm.md)

1. [Drivers](02-drivers.md)

1. [Linux Networking Fundamentals](03-linux-networking.md)

1. [Docker Network Control Plane](04-docker-network-cp.md)

1. [Bridge Networks](05-bridge-networks.md)

1. [Overlay Networks](06-overlay-networks.md)

1. [MACVLAN](07-macvlan.md)

1. [Host (Native) Network Driver](08-host-networking.md)

1. [Physical Network Design Requirements](09-physical-networking.md)

1. [Load Balancing Design Considerations](10-load-balancing.md)

1. [Security](11-security.md)

1. [IP Address Management](12-ipaddress-management.md)

1. [Troubleshooting](13-troubleshooting.md)

1. [Network Deployment Models](14-network-models.md)
