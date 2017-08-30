# Docker Content Trust Basics

# Lab Meta

> **Difficulty**: Beginner

> **Time**: Approximately 10 minutes

In this lab you'll learn how to enable Docker Content Trust as well as perform some basic signing and verification operations.

You will complete the following steps as part of this lab.

- [Step 1 - Enable Docker Content Trust](#enable_dct)
- [Step 2 - Push and sign an image](#push)
- [Step 4 - Clean-up](#clean)

# Prerequisites

You will need all of the following to complete this lab:

- At least one Linux-based Docker hosts running Docker 1.13 or higher
- The Docker host can be running in Swarm Mode
- This lab was built and tested using Ubuntu 16.04 and Docker 17.04.0-ce

# <a name="enable_dct"></a>Step 1: Enable Docker Content Trust

In this step you will enable Docker Content Trust on a single node. You will test it by pulling an unsigned and a signed image.

Execute all of the commands in this section form **node1** in your lab.

1. Enable Docker Content Trust

   ```
   $ export DOCKER_CONTENT_TRUST=1
   ```

   Docker Content Trust is now enabled on this host and you will no longer be able to pull unsigned images.

2. Try pulling an unsigned image (any unsigned image will do, you do not have to use the one in this demo)

   ```
   $ docker image pull nigelpoulton/tu-demo
   Using default tag: latest
   Error: remote trust data does not exist for docker.io/nigelpoulton/tu-demo: notary.docker.io does not have trust data for docker.io/nigelpoulton/tu-demo
   ```

   The operation fails because the image is not signed (no trust data for the image).

3. Try pulling the official `alpine:latest` image

   ```
   $ docker image pull alpine:latest
   Pull (1 of 1): alpine:latest@sha256:58e1a1bb75...3f105138f97eb53149673c4
   sha256:58e1a1bb75...3f105138f97eb53149673c4: Pulling from library/alpine
   627beaf3eaaf: Pull complete
   Digest: sha256:58e1a1bb75...3f105138f97eb53149673c4
   Status: Downloaded newer image for alpine@sha256:58e1a1bb75...3f105138f97eb53149673c4
   Tagging alpine@sha256:58e1a1bb75...3f105138f97eb53149673c4 as alpine:latest
   ```

   This time the operation succeeds. This is because the image is signed - all **official** images are signed.

In this step you have seen how easy it is to enable Docker Content Trust (exporting the `DOCKER_CONTENT_TRUST` environment variable with a value of `1`). You have also proved that it is working by attempting to pull an unsigned image.


# <a name="push"></a>Step 2: Push and sign an image

In this step you will tag an image and push it to a new repository within your own namespace on Docker Cloud. You will perform this step from the host that you enabled Docker Content Trust on in the previous step. This will ensure that the image gets signed when you push it.

To complete this step you will need a Docker ID.

Execute all of the following commands from **node1** (or whichever node you used for the previous step).

1. Tag the `alpine:latest` image so that it can be pushed to a new repository in your namespace on Docker Cloud.

   This command will add the following additional tag to the `alpine:latest` image: `nigelpoulton/sec-test:latest`. The format of the tag is **docker-id/repo-name/image-tag**. Be sure to replace the **docker-id** in the following command with your own Docker ID.

   ```
   $ docker image tag alpine:latest nigelpoulton/sec-test:latest
   ```
2. Verify the tagging operation worked

   ```
   $ docker image ls
   REPOSITORY              TAG      IMAGE ID       CREATED        SIZE
   alpine                  latest   4a415e366388   4 weeks ago    3.99MB
   nigelpoulton/sec-test   latest   4a415e366388   4 weeks ago    3.99MB
   ```
   Look closely and see that the image with **IMAGE ID** `4a415e366388` has two **REPOSITORY** tags.

3. Login to Docker Cloud with your own Docker ID

   ```
   $ docker login
   Login with your Docker ID to push and pull images from Docker Store...
   Username: <your-docker-id>
   Password:
   Login Succeeded
   ```

4. Push the image to a new repository in your Docker Cloud namespace. Remember to use the image tag you created earlier that includes your own Docker ID.

   > NOTE: As part of this `push` operation you will be asked to enter two new keys:
   - A new root key (this only happens the first time you push an image after enabling DCT)
   - A repository signing key

   ```
   $ docker image push nigelpoulton/sec-test:latest
   The push refers to a repository [docker.io/nigelpoulton/sec-test]
   23b9c7b43573: Pushed
   latest: digest: sha256:d0a670140...35edb294e4a7a152a size: 528
   Signing and pushing trust metadata
   You are about to create a new root signing key passphrase...
   <Snip>
   Enter passphrase for new root key with ID 66997be: <root key passphrase>
   Repeat passphrase for new root key with ID 66997be: <root key passphrase>
   Enter passphrase for new repository key with ID 7ccd1b4 (docker.io/nigelpoulton/sec-test): <repo key passphrase>
   Repeat passphrase for new repository key with ID 7ccd1b4 (docker.io/nigelpoulton/sec-test): <repo key passphrase>
   Finished initializing "docker.io/nigelpoulton/sec-test"
   Successfully signed "docker.io/nigelpoulton/sec-test":latest
   ```

   The output above shows the image being signed as part of the normal `docker image push` command - no extra commands or steps are required to sign images with Docker Content Trust enabled.

Congratulations. You have pushed and signed an image.

By default the root and repository keys are stored below `~/.docker/trust`.

In the real world you will need to generate strong passphrases for each key and store them in a secure password manager/vault.

# <a name="clean"></a>Step 3: Clean-up

The following commands will clean-up the artifacts from this lab.

1. Delete the tagged image you created in Step 2

   ```
   $ docker image rm nigelpoulton/sec-test:latest
   Untagged: nigelpoulton/sec-test:latest
   Untagged: nigelpoulton/sec-test@sha256:d0a6701...4e4a7a152a
   ```

2. Delete the alpine:latest image

   ```
   $ docker image rm alpine:latest
   Untagged: alpine:latest
   Untagged: alpine@sha256:58e1a...38f97eb53149673c4
   Deleted: sha256:4a415e366...718a4698e91ef2a526
   Deleted: sha256:23b9c7b43...5f22803bcbe9ab24c2
   ```
3. Disable Docker Content Trust.

   ```
   $ export DOCKER_CONTENT_TRUST=
   ```

4. Login to Docker Cloud > Locate the repository you created with the `docker image push` command > Click Settings > Delete the repository.
