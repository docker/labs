# Lab: Distribution and Trust

> **Difficulty**: Intermediate

> **Time**: Approximately 30 minutes

This lab focuses on understanding and securing image distribution. We'll
start with a simple `docker pull` and build up to using Docker Content Trust (DCT).

You will complete the following steps as part of this lab.

- [Step 1 - Pulling images by tag](#tag)
- [Step 2 - Pulling images by digest](#digest)
- [Step 3 - Docker Content Trust](#trust)
- [Step 4 - Official Images](#official)
- [Step 5 - Extra for experts](#extra)

# Prerequisites

You will need all of the following to complete this lab:

- A Docker Host running Docker 1.10 or higher (preferably Docker 1.12 or higher).
- This lab uses environment variables which can be clunky when working with `sudo`. Therefore none of the Docker commands in this particular lab are preceded with `sudo`.

# <a name="tag"></a>Step 1: Pulling images by tag

The most common and basic way to pull Docker images is by `tag`. The is where you specify an image name followed by an alphanumeric tag. The image name and tag are separated by a colon `:`. For example:

   ```
   $ docker pull alpine:edge
   ```

This command will pull the Alpine image tagged as `edge`. The corresponding image can be found [here on Docker Store](https://store.docker.com/images/alpine).

If no tag is specified, Docker will pull the image with the `latest` tag.

1. Pull the Alpine image with the `edge` tag.

   ```
   $ docker pull alpine:edge

   edge: Pulling from library/alpine
   e587fa4f6e1f: Pull complete
   Digest: sha256:e5ab6f0941eb01c41595d35856f16215021a941e9893501d632ed4c0ee4e53a6
   Status: Downloaded newer image for alpine:edge
   ```

2. Confirm that the pull was successful.

   ```
   $ docker images

   REPOSITORY          TAG                 IMAGE ID            CREATED             SIZE
   alpine              edge                3fc33d6d5e74        4 weeks ago         4.799 MB
   ```

3. Run a new container from the image and ping www.docker.com.

   ```
   $ docker run --rm -it alpine:edge sh

   / # ping www.docker.com
   PING www.docker.com (104.239.220.248): 56 data bytes
   64 bytes from 104.239.220.248: seq=0 ttl=40 time=94.424 ms
   64 bytes from 104.239.220.248: seq=1 ttl=40 time=94.549 ms
   64 bytes from 104.239.220.248: seq=2 ttl=40 time=94.455 ms
   ^C
   ```

4. Exit the container.

In this step you pulled an image by tag and verified the pull by running a container from it.

# <a name="digest"></a>Step 2: Pulling images by digest

Pulling by tag is easy and convenient. However, tags are mutable, and the same tag can refer to different images over time. For example, you can add updates to an image and push the updated image using the same tag as a previous version of the image. This scenario where a single tag points to multiple versions of an image can lead to bugs and vulnerabilities in your production environments.

This is why pulling by digest is such a powerful operation. Thanks to the content-addressable storage model used by Docker images, we can target pulls to specific image contents by pulling by digest. In this step you'll see how to pull by digest.

1.  Pull the Alpine image with the `sha256:b7233dafbed64e3738630b69382a8b231726aa1014ccaabc1947c5308a8910a7` digest.

   ```
   $ docker pull alpine@sha256:b7233dafbed64e3738630b69382a8b231726aa1014ccaabc1947c5308a8910a7

   sha256:b7233dafbed64e3738630b69382a8b231726aa1014ccaabc1947c5308a8910a7: Pulling from library/alpine
   6589332ef57c: Pull complete
   Digest: sha256:b7233dafbed64e3738630b69382a8b231726aa1014ccaabc1947c5308a8910a7
   Status: Downloaded newer image for alpine@sha256:b7233dafbed64e3738630b69382a8b231726aa1014ccaabc1947c5308a8910a7
   ```

2. Check that the pull succeeded.

   ```
   $ docker images --digests alpine

   REPOSITORY          TAG                 DIGEST                                                                    IMAGE ID            CREATED             SIZE
   alpine              edge                <none>                                                                    3fc33d6d5e74        4 weeks ago         4.799 MB
   alpine              <none>              sha256:b7233dafbed64e3738630b69382a8b231726aa1014ccaabc1947c5308a8910a7   79c898d40088        7 weeks ago         4.799 MB
   ```

   Notice that there are now two Alpine images in the Docker Hosts local repository. One lists the `edge` tag. The other lists `<none>` as the tag along with the `b7233daf...` digest.

The content addressable storage model used by Docker images means that any changes made to an image will result in a new digest for the updated image. This means it is not possible for two images with different contents to have the same digest.

In this step you learned how to pull images by digest and list image digests using the `docker images` command. For more information about the `docker pull` command, see the [documentation](https://docs.docker.com/engine/reference/commandline/pull/).

# <a name="trust"></a>Step 3: Docker Content Trust

It's not easy to find the digest of a particular image tag. This is because it is computed from the hash of the image contents and stored in the image manifest. The image manifest is then stored in the Registry. This is why we needed a `docker pull` by tag to find digests previously. It would also be desirable to have additional security guarantees such as image freshness.

Enter Docker Content Trust: a system currently in the Docker Engine that verifies the publisher of images without sacrificing usability. Docker Content Trust implements [The Update Framework](https://theupdateframework.github.io/) (TUF), an NSF-funded research project succeeding Thandy of the Tor project. TUF uses a key hierarchy to ensure recoverable key compromise and robust freshness guarantees.

Under the hood, Docker Content Trust handles name resolution from IMAGE tags to IMAGE digests by signing its own metadata -- when Content Trust is enabled, docker will verify the signatures and expiration dates in the metadata before rewriting a pull by tag command to a pull by digest.

In this step you will enable Docker Content Trust, sign images as you push them, and pull signed and unsigned images.

1.  Enable Docker Content Trust by setting the DOCKER_CONTENT_TRUST environment variable.

   ```
   $ export DOCKER_CONTENT_TRUST=1
   ```

   > **Note:** If you are using `sudo` with your Docker commands, you will need to preceded the above command so that it looks like this`sudo export DOCKER_CONTENT_TRUST=1`

   It is worth nothing that although Docker Content Trust is now enabled, all Docker commands remain the same. Docker Content Trust will work silently in the background.

2. Pull the `riyaz/dockercon:trust` signed image.

   ```
   $ docker pull riyaz/dockercon:trust

   Pull (1 of 1): riyaz/dockercon:trust@sha256:88a7163227a54bf0343aae9e7a4404fdcdcfef8cc777daf9686714f4376ede46
   sha256:88a7163227a54bf0343aae9e7a4404fdcdcfef8cc777daf9686714f4376ede46: Pulling from riyaz/dockercon
   fae91920dcd4: Pull complete
   Digest: sha256:88a7163227a54bf0343aae9e7a4404fdcdcfef8cc777daf9686714f4376ede46
   Status: Downloaded newer image for riyaz/dockercon@sha256:88a7163227a54bf0343aae9e7a4404fdcdcfef8cc777daf9686714f4376ede46
   Tagging riyaz/dockercon@sha256:88a7163227a54bf0343aae9e7a4404fdcdcfef8cc777daf9686714f4376ede46 as riyaz/dockercon:trust
   ```

   Look closely at the output of the `docker pull` command and take particular notice of the name translation - how the command is translated to the digest as shown below:

   ```
   Pull (1 of 1): riyaz/dockercon:trust@sha256:88a7163227a54bf0343aae9e7a4404fdcdcfef8cc777daf9686714f4376ede46
   ```

3.  Pull and unsigned image.

   ```
   $ docker pull riyaz/dockercon:untrusted

   No trust data for untrusted
   ```

   You cannot pull unsigned images with Docker Content Trust enabled. Once Docker Content Trust is enabled you can only pull, run, or build with trusted images.

4.  Tag and push your own signed image with Docker Content Trust.

   ```
   $ docker tag alpine:edge <your-docker-id>/alpine:trusted
   $ docker push <your-docker-id>/alpine:trusted

   The push refers to a repository [docker.io/nigelpoulton/alpine]
   4fe15f8d0ae6: Pushed
   trusted: digest: sha256:dc89ce8401da81f24f7ba3f0ab2914ed9013608bdba0b7e7e5d964817067dc06 size: 528
   Signing and pushing trust metadata
   You are about to create a new root signing key passphrase. This passphrase
   will be used to protect the most sensitive key in your signing system. Please
   choose a long, complex passphrase and be careful to keep the password and the
   key file itself secure and backed up. It is highly recommended that you use a
   password manager to generate the passphrase and keep it safe. There will be no way to recover this key. You can find the key in your config directory.
   Enter passphrase for new root key with ID fef644e:
   Repeat passphrase for new root key with ID fef644e:
   Enter passphrase for new repository key with ID b4fd76d (docker.io/nigelpoulton/alpine):
   Repeat passphrase for new repository key with ID b4fd76d (docker.io/nigelpoulton/alpine):
   Finished initializing "docker.io/nigelpoulton/alpine"
   Successfully signed "docker.io/nigelpoulton/alpine":trusted
   ```

   This command will prompt you for passphrases. This is because
   Docker Content Trust is generating a hierarchy of keys with different signing roles. Each key is encrypted with a passphrase, and it is best practice is to provide different passphrases for each key.

   The **root key** is the most important key in TUF as it can rotate any other key in the system. The root key should be kept offline, ideally in hardware crypto device. It is stored in `~/.docker/trust/private/root_keys` by default.

   The **tagging key** is the only local key required to push new tags to an existing repo, and is stored in `~/.docker/trust/private/tuf_keys` by default.

   Feel free to explore the `~/.docker/trust` directory to view the internal metadata and key information that Docker Content Trust generates.

5. Disable Docker Content Trust.

   ```
   $ export DOCKER_CONTENT_TRUST=0
   ```

6. Pull the image you just pushed in the previous step.

   ```
   $ docker pull <your-docker-id>/alpine:trusted

   trusted: Pulling from nigelpoulton/alpine
   Digest: sha256:dc89ce8401da81f24f7ba3f0ab2914ed9013608bdba0b7e7e5d964817067dc06
   Status: Image is up to date for nigelpoulton/alpine:trusted
   ```

7. Enable Docker Content Trust.

   ```
   $ export DOCKER_CONTENT_TRUST=1
   ```

8. Pull the same image again.

   ```
   $ docker pull <your-docker-id>/alpine:trusted

   Pull (1 of 1): nigelpoulton/alpine:trusted@sha256:dc89ce8401da81f24f7ba3f0ab2914ed9013608bdba0b7e7e5d964817067dc06
   sha256:dc89ce8401da81f24f7ba3f0ab2914ed9013608bdba0b7e7e5d964817067dc06: Pulling from nigelpoulton/alpine
   Digest: sha256:dc89ce8401da81f24f7ba3f0ab2914ed9013608bdba0b7e7e5d964817067dc06
   Status: Downloaded newer image for nigelpoulton/alpine@sha256:dc89ce8401da81f24f7ba3f0ab2914ed9013608bdba0b7e7e5d964817067dc06
   Tagging nigelpoulton/alpine@sha256:dc89ce8401da81f24f7ba3f0ab2914ed9013608bdba0b7e7e5d964817067dc06 as nigelpoulton/alpine:trusted
   ```

   Take note of the difference between the pull of a signed image with Docker Content Trust enabled and disabled. With Docker Content Trust enabled the image pull is converted from a tagged image pull to a digest image pull.

In this step you have seen how to enable and disable Docker Content Trust. You have also seen how to sign images that you push. For more information about Docker Content Trust, see [the documentation](https://docs.docker.com/engine/security/trust/).

# <a name="official"></a>Step 4: Official images

All images in Docker Hub under the `library` organization (currently viewable at: https://hub.docker.com/explore/)
are deemed "Official Images."  These images undergo a rigorous, [open-source](https://github.com/docker-library/official-images/)
review process to ensure they follow best practices. These best practices include signing, being lean, and having clearly written Dockerfiles. For these reasons, it is strongly recommended that you use official images whenever possible.

Official images can be pulled with just their name and tag. You do not have to precede the image name with `library/` or any other repository name.


# <a name="extra"></a>Step 5: Extra for Experts

Docker Content Trust is powered by [Notary](https://github.com/docker/notary), an open-source TUF-client and server that can operate over arbitrary trusted collections of data. Notary has its own CLI with robust features
such as the ability to rotate keys and remove trust data. In this step you will play with the Notary CLI and a local instance of the Notary server instead of the one deployed alongside Docker Hub.

1.  Get a notary client.

   This can be done by downloading a binary directly from the [releases page](https://github.com/docker/notary/releases), or by cloning the notary repository into a valid Go repository structure (instructions at the end of the README) and building a client by running `make binaries`. If you build the notary binary yourself, it will be placed in the `bin` subdirectory within the notary git repo directory.

2.  Use the notary client to inspect the existing Alpine repository on Docker Hub.

```
   $ notary -s https://notary.docker.io -d ~/.docker/trust list docker.io/library/alpine

   NAME                                 DIGEST                                SIZE (BYTES)    ROLE
------------------------------------------------------------------------------------------------------
2.6      e9cec9aec697d8b9d450edd32860ecd363f2f3174c8338beb5f809422d182c63   1374           targets
2.7      9f08005dff552038f0ad2f46b8e65ff3d25641747d3912e3ea8da6785046561a   1374           targets
3.1      0796cca706c64170c29cfefbdd67f32e25dab2247fc31956c86773dae825800f   506            targets
3.2      9c6c40abb6a9180603068a413deca450ef13c381974b392a25af948ca87c3c14   506            targets
3.3      4fa633f4feff6a8f02acfc7424efd5cb3e76686ed3218abf4ca0fa4a2a358423   506            targets
3.4      3dcdb92d7432d56604d4545cbd324b14e647b313626d99b889d0626de158f73a   506            targets
edge     e5ab6f0941eb01c41595d35856f16215021a941e9893501d632ed4c0ee4e53a6   506            targets
latest   3dcdb92d7432d56604d4545cbd324b14e647b313626d99b889d0626de158f73a   506            targets
```

   Note that `docker.io/` must be prepended to the image name for hub images.  You should also try your own image you pushed to hub earlier!

3.  If you haven't already, clone the [Notary](https://github.com/docker/notary) repository.

   ```
   $ git clone https://github.com/docker/notary.git

   Cloning into 'notary'...
   remote: Counting objects: 17156, done.
   remote: Compressing objects: 100% (26/26), done.
   remote: Total 17156 (delta 5), reused 0 (delta 0), pack-reused 17129
   Receiving objects: 100% (17156/17156), 27.07 MiB | 11.20 MiB/s, done.
   Resolving deltas: 100% (9734/9734), done.
   Checking connectivity... done.
   ```

4. Bring up a local notary server and signer.

   You will need to run this command from inside the root folder of the notary repository. The operation may take a minute or two to complete.

   ```
   $ docker-compose up -d
   Pulling mysql (mariadb:10.1.10)...
   10.1.10: Pulling from library/mariadb
   03e1855d4f31: Pull complete
   a3ed95caeb02: Pull complete
   ea9cb3d7d346: Pull complete
   e47839e262bb: Pull complete
   f568a56c1fd0: Pull complete
   cc98c1dfbf81: Pull complete
   98a99d2efdc4: Pull complete
   0b304232c8e6: Pull complete
   d65a44f4573e: Pull complete
   Digest: sha256:10d0179f08a4fb0c785142ca73367921f46a93c2ee7c84831ae3543522156a6c
   Status: Downloaded newer image for mariadb:10.1.10
   <SNIP>
   Creating notary_mysql_1
   Creating notary_signer_1
   Creating notary_server_1
   ```

5.  Add `127.0.0.1 notary-server` to `/etc/hosts` (or if using `docker-machine`, add `$(docker-machine ip) notary-server)`.

6. Copy the config and certificate required to talk to your local Notary server.

   ```
   $ mkdir -p ~/.notary && cp cmd/notary/config.json cmd/notary/root-ca.crt ~/.notary
   ```

7.  Initialize a new trusted collection on your local server

   ```
   $ notary init example.com/scripts

   No root keys found. Generating a new root key...
You are about to create a new root signing key passphrase. This passphrase
will be used to protect the most sensitive key in your signing system. Please
choose a long, complex passphrase and be careful to keep the password and the
key file itself secure and backed up. It is highly recommended that you use a
password manager to generate the passphrase and keep it safe. There will be no
way to recover this key. You can find the key in your config directory.
Enter passphrase for new root key with ID 6f3b2d0:
Repeat passphrase for new root key with ID 6f3b2d0:
Enter passphrase for new targets key with ID 4bc290d (example.com/scripts):
Repeat passphrase for new targets key with ID 4bc290d (example.com/scripts):
   ```

   You will be prompted for passphrases for the root and repository keys. This is for the same reasons as it was when you first pushed a repo with Docker Content Trust enabled.

8.  Add content to your trusted collection by running a sequence of `notary add example.com/scripts <NAME> <FILE>`, `notary publish example.com/scripts`, and `notary list example.com/scripts`. This is a the sequence, in order:
    - Stages adding a target with `notary add`
    - Attempts to publish this target to the server with `notary publish`
    - Fetches and displays all trust data for example.com (verifying output as it is downloaded)

To remove targets, you can make use of the `notary remove example.com/scripts <NAME>` command, followed by a `notary publish example.com/scripts`.

Please note that this `docker-compose` setup will persist notary's database data in a volume; if you'd like to wipe out all state when running this lab running multiple times, you will have to remove this volume in addition to restarting the notary server, signer, and database containers.

### Key rotation with the notary client

Let's look at key rotation with Notary.

In the event of key-compromise, it's a simple procedure to rotate keys with notary. Simply determine which key you wish to rotate, and run `notary key rotate`.

1. List all of the keys in your notary repository

   ```
   $ notary key list
   ROLE             GUN                                        KEY ID                                          LOCATION
   ------------------------------------------------------------------------------------------------------------------------------------
   root                             6f3b2d0269343cafafca59f05d738389a27c9408cbe1345188325913620a6b80   file (/root/.notary/private)
   snapshot   example.com/scripts   7c9487f2d2a42d1f91020285483d15b5d85d8e22799c64141c5cafc87997795c   file (/root/.notary/private)
   targets    example.com/scripts   4bc290d883fa33020a528abbce61accf78745f4f28be243f3cc9f44f11297258   file (/root/.notary/private)
   ```

2. Rotate a key.

   In this example we'll rotate the `targets` key.  

   ```
   $ notary key rotate example.com/scripts targets

   Enter passphrase for new targets key with ID a4413c0 (example.com/scripts):
   Repeat passphrase for new targets key with ID a4413c0  (example.com/scripts):
   Enter passphrase for root key with ID 6f3b2d0:
   Enter passphrase for snapshot key with ID 7c9487f (example.com/scripts):
   ```

3. Confirm that the targets key ID has changed.

   ```
   $ notary key list

   ROLE             GUN                                        KEY ID                                          LOCATION
   ------------------------------------------------------------------------------------------------------------------------------------
   root                             6f3b2d0269343cafafca59f05d738389a27c9408cbe1345188325913620a6b80   file (/root/.notary/private)
   snapshot   example.com/scripts   7c9487f2d2a42d1f91020285483d15b5d85d8e22799c64141c5cafc87997795c   file (/root/.notary/private)
   targets    example.com/scripts   a4413c080788746ac34db2077a0bcc41be84dfce34d8a7d2d55942d125e4c69f   file (/root/.notary/private)
   ```

   For more information about keys, please see the [this section of the Notary Service Architecture documentation](https://docs.docker.com/notary/service_architecture/#brief-overview-of-tuf-keys-and-roles)

### Role delegation with notary

Now we'll explore delegation roles in notary. Delegation roles are a subset of the targets role, and are ideal for assigning signing privileges to collaborators and CI systems because no private key sharing is required.  Here's a [demo of setting up delegation roles](https://asciinema.org/a/4nclzcuus3ubdcu88xmepz8u4) to illustrate the steps below:

1. Rotate the snapshot key to the server.

   This is done by default when creating new Content Trust repositories in Docker 1.10+.

   ```
   $ notary key rotate example.com/scripts snapshot -r
   Enter passphrase for root key with ID 6f3b2d0:
   ```

   This is so that delegation roles will only require their own delegation's private key to publish to trusted collections.

2. Have your delegate generate an x509 certificate + private key pair with openssl - [instructions here](https://docs.docker.com/engine/security/trust/trust_delegation/#generating-delegation-keys).  

3. Retrieve their certificate, `delegation.crt`.

4. Add their delegation role.

   ```
   $ notary delegation add example.com/scripts targets/releases delegation.crt --all-paths
   ```

   This command will allow the collaborator to push any target (from `--all-paths`) to the `targets/releases` role if they can sign with their private key `delegation.key` in order to produce a valid signature that can be verified by `delegation.crt`'s public key material.

   Be aware that this commmand only stages the delegation role addition.

5. Publish the addition of the delegation role

   ```
   $ notary publish example.com/scripts
   ```

6. Check that the delegation role was added.

   ```
   $ notary delegation list example.com/scripts
   ```

Your collaborator should now be able to publish content (`docker push`) with Docker Content Trust enabled, or with a `notary add example.com/scripts <NAME> <FILE> -r targets/releases`.  You can verify their pushes by running a `notary list example.com/scripts -r targets/releases`

You can add additional keys to the same role with additional `delegation add` commands, like so: `notary delegation add example.com/scripts targets/releases delegation2.crt delegation3.crt`, followed by a publish

For more commands over delegation roles, please consult the notary [advanced usage documentation](https://docs.docker.com/notary/advanced_usage/#work-with-delegation-roles).

# Summary

Congratulations. You now know the advantages of image digests over image tags. You have also seen how to enable Docker Content Trust and push signed images. Finally you have seen how to perform some advanced tasks using the notary server and client.
