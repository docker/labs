# Designing Scalable, Portable Docker Container Networks

## What You Will Learn

Docker containers wrap a piece of software in a complete filesystem that contains everything needed to run: code, runtime, system tools, system libraries – anything that can be installed on a server. This guarantees that the software will always run the same, regardless of its environment. By default, containers isolate applications from one another and the underlying infrastructure, while providing an added layer of protection for the application. 

What if the applications need to communicate with each other, the host, or an external network? How do you design a network to allow for proper connectivity while maintaining application portability, service discovery, load balancing, security, performance, and scalability? This document addresses these network design challenges as well as the tools available and common deployment patterns. It does not specify or recommend physical network design but provides options for how to design Docker networks while considering the constraints of the application and the physical network.

### Prerequisites

Before continuing, being familiar with Docker concepts and Docker Swarm is recommended:
 
- [Docker concepts](https://docs.docker.com/engine/understanding-docker/)
- [Docker Swarm](https://docs.docker.com/engine/swarm/) and the newly introduced [Swarm mode concepts](https://docs.docker.com/engine/swarm/key-concepts/#/services-and-tasks)

### Networking concepts
This tutorial allows you to dive right in and try code in the [Quick Tutorials](tutorials.md) section, or deep dive into this series of tutorials:

1. [Networking Basics](A1-network-basics.md)
1. [Bridge Networking](A2-bridge-networking.md)
1. [Overlay Networking](A3-overlay-networking.md)
1. [HTTP Routing Mesh](A4-HTTP%20Routing%20Mesh.md)

Or you can first dive deep into the [Network Concepts](concepts/) before trying in out in code yourself.
