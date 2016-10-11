#Developer Tools Tutorials

This directory contains tutorials on how to set-up and use common developer tools with Docker. We encourage you to [contribute](../contribute.md) your own tutorials here.

## IDEs

With the introduction of [Docker for Mac](https://www.docker.com/products/docker#/mac) and [Docker for Windows](https://www.docker.com/products/docker#/windows), developers on those platforms got to use a feature that developers on [Docker for Linux](https://www.docker.com/products/docker#linux) had all along: in-container development. With improvements in volume management, Docker is able to detect when code in a volume changes, and update the code in the container. That means you get features like live debugging in a running container, without having to rebuild the container.

In order to take advantage of this feature in an IDE, there is some set-up required as there is for any project. The following sections describe how to configure different languages and IDEs to do in-container development.

### [Java Developer Tools](https://github.com/docker/labs/tree/master/developer-tools/java-debugging) including:
+ Eclipse
+ IntelliJ
+ Netbeans

### [Node.js Developer Tools](https://github.com/docker/labs/blob/master/developer-tools/nodejs-debugging/README.md) including:
+ Visual Studio Code
