# Linux Registry Lab

A registry is a service for storing and accessing Docker images. [Docker Cloud](https://cloud.docker.com) and [Docker Store](https://store.docker.com) are the best-known hosted registries, which you can use to store public and private images. You can also run your own registry using the open-source [Docker Registry](https://docs.docker.com/registry), which is a Go application in a Alpine Linux container.

## What You Will Learn

You'll learn how to:

- run a local registry in a container and configure your Docker engine to use the registry;

- generate SSL certificates (using Docker!) and run a secure local registry with a friendly domain name;

- generate encrypted passwords (using Docker!) and run an authenticated, secure local registry over HTTPS with basic auth.

> Note. The open-source registry does not have a Web UI, so there's no friendly interface like [Docker Cloud](https://cloud.docker.com) or [Docker Store](https://store.docker.com). Instead there is a [REST API](https://docs.docker.com/registry/spec/api/) you can use to query the registry. For a local registry which has a Web UI and role-based access control, Docker, Inc. has the [Trusted Registry](https://www.docker.com/sites/default/files/Docker%20Trusted%20Registry.pdf) product.

### Prerequisites

You'll need Docker running on Linux and be familiar with the key Docker concepts, and with Docker volumes:

- [Docker concepts](https://docs.docker.com/engine/understanding-docker/)
- [Docker volumes](https://docs.docker.com/engine/tutorials/dockervolumes/)

## The Lab

- [Part 1 - Running a Registry Container](part-1.md)
- [Part 2 - Running a Secured Registry Container](part-2.md)
- [Part 3 - Using Basic Authentication with a Secured Registry](part-3.md)
