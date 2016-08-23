#!/bin/bash

# Stop machines
docker-machine stop worker1 worker2 worker3 manager1 manager2 manager3

# remove machines
docker-machine rm worker1 worker2 worker3 manager1 manager2 manager3
