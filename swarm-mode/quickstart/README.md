# docker swarm

Get in touch with the new `docker swarm` feature in Docker 1.12.

## Build a local swarm with Docker Machine

To create a local swarm with Docker Machine use the following script

```bash
./buildswarm-vbox.sh
```

It will create two VirtualBox machines `sw01` and `sw02`. The machine `sw01` is
the swarm manager, the machine `sw02` joins the swarm.

To run further commands, just login to the machine `sw01` with

```bash
docker-machine ssh sw01
```

or run each command through ssh, eg.

```bash
docker-machine ssh sw01 docker node ls
ID               NAME  MEMBERSHIP  STATUS  AVAILABILITY  MANAGER STATUS  LEADER
0j9p42jcyur9a *  sw01  Accepted    Ready   Active        Reachable       Yes
16cznbhxupre5    sw02  Accepted    Ready   Active                        
```

### Remote swarm with Docker Machine at DigitalOcean

To build a multi machine swarm at DigitalOcean, run this script with your token.

```bash
DO_TOKEN=xxxx ./buildswarm-do.sh
```

Then control your swarm with commands on your swarm manager node

```bash
docker-machine ssh do-sw01 docker node ls
```
