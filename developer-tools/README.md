# Developer Tools Tutorials

This directory contains tutorials on how to set-up and use common developer tools and programming languages with Docker. We encourage you to [contribute](../contribute.md) your own tutorials here.

## IDEs

With the introduction of [Docker for Mac](https://www.docker.com/products/docker#/mac) and [Docker for Windows](https://www.docker.com/products/docker#/windows), developers on those platforms got to use a feature that developers on [Docker for Linux](https://www.docker.com/products/docker#linux) had all along: in-container development. With improvements in volume management, Docker is able to detect when code in a volume changes, and update the code in the container. That means you get features like live debugging in a running container, without having to rebuild the container.

You can also have all your dependencies in a container. All you need is Docker and something to edit your source files on your machine, and you're good to go. That means you can, for instance, debug Node.js in your container without having Node.js on your local machine.

In order to take advantage of this feature in an IDE, there is some set-up required as there is for any project. The following sections describe how to configure different languages and IDEs to do in-container development.

### [Java Developer Tools](https://github.com/docker/labs/tree/master/developer-tools/java-debugging) including:
+ Eclipse
+ IntelliJ
+ Netbeans

### [Node.js Developer Tools](https://github.com/docker/labs/blob/master/developer-tools/nodejs-debugging/README.md) including:
+ Visual Studio Code

## Programming languages
This is a more comprehensive section detailing how to set-up and optimize your experience using Docker with particular programming languages.

+ [Java](java/)
+ [Node.js](nodejs/porting/)
