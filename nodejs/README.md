# Purpose

This tutorial starts from a simple Node.js application (HTTP Rest API built with [Sails.js](http://sailsjs.org/)) and details what is needed to *Dockerize* it and ensure the scalability.

The application used as a base is a Node.js application exposing a REST API and storing data in a MongoDB dabatase. This tutorial does not address the scaling of the MongoDB part. This will be done in a next tutorial dedicated to the database.

Node: do not hesitate to provide any comments / feedback you may have, that will help make this tutorial better.

# Pre-requisites

Some of the Docker basis will be reviewed in this tutorial but it is recommended to follow [Docker for beginners](https://github.com/lucj/labs/tree/master/beginner) prior to follow this tutorial so you can have a clear understanding of what Docker is made of and how to use it.

# Let's start

Note: the following items needs to be followed in the order as they appear below

[Setup a sample node application](https://github.com/lucj/labs/blob/master/nodejs/1_node_application.md)

[Define our application's images](https://github.com/lucj/labs/blob/master/nodejs/2_application_image.md)

[Publish image on Docker Hub](https://github.com/lucj/labs/blob/master/nodejs/3_pulish_image.md)

[Single Docker host networking](https://github.com/lucj/labs/blob/master/nodejs/4_single_host_networking.md)

[Multiple Docker hosts networking](https://github.com/lucj/labs/blob/master/nodejs/5_multiple_hosts_networking.md)

[Deploy on a Docker Swarm](https://github.com/lucj/labs/blob/master/nodejs/6_deploy_on_swarm.md)

# Summary

We've covered several important aspects of Docker and hopefully this can help to have a better understanding of the platform.

[What we've done so far](https://github.com/lucj/labs/blob/master/nodejs/summary.md)

Once again, feedback / comments are more than welcome :)
