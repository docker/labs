# Purpose

This tutorial starts with a simple Node.js application (HTTP Rest API built with [Sails.js](http://sailsjs.org/)) and details the steps needed to *Dockerize* it and ensure its scalability.

The application stores data in a MongoDB database. This tutorial does not address the scaling of the MongoDB part.

Note: Do not hesitate to provide any comments / feedback you may have, that will help make this tutorial better.

# Pre-requisites

Some of the Docker basics will be reviewed but it is recommended to follow [Docker for beginners](https://github.com/docker/labs/tree/master/beginner) prior to follow this tutorial in order to get a clear understanding of what is inside Docker and how to use it.

# Let's start

[Setup our sample node application](1_node_application.md)

[Create the application's image](2_application_image.md)

[Publish image on Docker Store](3_publish_image.md)

[Single Docker host networking](4_single_host_networking.md)

[Multiple Docker hosts networking](5_multiple_hosts_networking.md)

[Deploy on a Docker Swarm](6_deploy_on_swarm.md)

# Summary

We've covered several important aspects of Docker and hopefully this helped to have a better understanding of the platform.

[What we've done so far](summary.md)

Once again, feedback / comments are more than welcome :)
