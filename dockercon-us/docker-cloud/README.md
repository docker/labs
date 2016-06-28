# Lab 3: Docker Cloud

> **Difficulty**: Beginner

> **Time**: Approximately 45 minutes

> In this lab you will deploy a web application using Docker Cloud. You will complete the following tasks as part of the lab:

> - [Task 0: Configure the prerequisites](#prerequisits)
- [Task 1: Install the Docker Cloud CLI on a management host](#cli-install)
- [Task 2: Deploy the Docker Cloud agent on a Docker host](#install_node)
- [Task 3: Deploy a service](#deploy_service)
  - [Task 3.1: Check the service](#check_service)
- [Task 4: Deploy an application using a CI/CD pipeline](#deploy_app)
  - [Task 4.1: Configure Docker Cloud autobuilds](#autobuild)
  - [Task 4.2: Test autobuilds](#test_autobuild)
  - [Task 4.3: Configure and test autodeploy](#autodeploy)

## What is Docker Cloud?

Docker Cloud is Docker's cloud platform to build, ship and run your containerized applications. Docker Cloud enables teams to come together to collaborate on their projects and to automate complex continuous delivery flows. So you can focus on working and improving your app, and leave the rest up to Docker Cloud.
Docker Cloud offers a set of services that can be used individually or together for an end-to end solution. These services are:

####Build

- A **continuous integration** (CI) service, to automate the build and test of your code repositories. It integrates with both Github and Bitbucket.
 
####Ship

- A **registry service** to manage your public and private Docker image repositories. 

- **Docker Security Scanning**, a service that automatically scans your Docker repositories for known vulnerabilities. With DSS you can easily see if your containers are affected by any known security vulnerabilities, as well as find out when and how the vulnerability was introduced into your applications. As new vulnerabilities are found, your repositories are automatically scanned and you are notified. 

####Run

- **Infrastructure provisioning and management service** Right from within Docker Cloud you can provision interconnected node clusters on the most popular cloud providers: such as Amazon, Azure and Digital Ocean. You can easily scale your infrastructure up and down, and bulk update your entire infrastructure when a new Docker version is available, for example.

- **Application deployment and management service** Docker Cloud lets you run any dockerized application publicly accessible on any registry in the world. Docker Cloud supports popular features such us: load balancing, DNS round robin service endpoints, auto re-deploys, rolling updates, rollbacks, log aggregation, and many others. You can simply paste your compose file, and get started. You can even exec into individual containers if you'd like to do some hands-on debugging. 

Docker Cloud can also send you and your team **Slack notifications**, so you get notified when a build succeeds, or a test failed, or one of your apps in staging has been automatically updated.

Finally, with Organizations, you can now assign **role-based access control** to repositories, applications, and infrastructure, empowering your teams to come together and focus on different aspects of the Continuous Delivery process

##Document conventions
When you encounter a phrase in between `<` and `>`  you are meant to substitute in a different value. 

For instance if you see `ssh <username>@<hostname>` you would actually type something like `ssh labuser@v111node0-adaflds023asdf-23423kjl.appnet.com`

You will be asked to SSH into various nodes. These nodes are referred to as **v111node0** and **v111node1** (optional) etc. 

## <a name="prerequisites"></a>Task 0: Prerequisites

In order to complete this lab, you will need the following:

- A Docker ID
- A management host (you can use your laptop or a virtual machine with Docker Engine 1.11)
- A managed node which needs to be a virtual machine running Docker Engine 1.11
- A GitHub account
- Git installed locally on your machine (if you are using your machine for the *management host*)

### Obtain a Docker ID

If you do not already have a Docker ID, you will need to create one now. Creating a Docker ID is free, and allows you to use both [Docker Cloud](https://cloud.docker.com) and [Docker Hub](https://hub.docker.com).

If you already have a Docker ID, skip to the next prerequisite.

To create a Docker ID:

1. Use your web browser to visit [`https://cloud.docker.com`](https://cloud.docker.com)

2. Near the bottom middle of the screen click `Create Account`

3. Choose a Docker ID, supply your email address, and choose a password

4. Click `Sign up`

5. Check your email (**including your spam folder**) for an email with the subject `Please confirm email for your Docker ID`

6. Click the `Confirm Your Email` link in the body of the message

7. You should be redirected back to `https://cloud.docker.com`

You now have a Docker ID. Remember to keep the password safe and secure.

### Choose a management host

As part of this lab you will need a designated machine that has the Docker Cloud CLI installed. The rest of this document will refer to this as the *management host*. 

You have two options with regards to choosing a *management host*:

- **Option 1 (recommended)**: Use your own laptop

	In order to use your own laptop, you will need to have Docker installed. You can find instructions on how to install docker on our <a href="https://www.docker.com/products/docker">products page</a>.

	We recommend you install either the Docker for Mac or Docker for Windows beta. 

	If you choose this option, you will install the Docker Cloud CLI and execute commands in a terminal or command window on your laptop.

- **Option 2**: Use a virtual machine
	
	If you do not wish to install any software locally you can use one a VM as your *management host*

	If you choose this option, you will install the Docker Cloud CLI an execute all commands on the virtual machine which we'll refer to as **v111node1**. The VM will need to have Docker 1.11 installed. 
		
  
### GitHub account

In order to complete the CI/CD portions of this lab, you will need an account on GitHub. If you do not already have one you can create one for free at [GitHub](https://github.com).

Continue with the lab as soon as you have completed the prerequisites.

### Git installed 

Visit <a href="https://git-scm.com/book/en/v2/Getting-Started-Installing-Git">the git website</a> for information how how to install `git`

# <a name="cli-install"></a>Task 1: Install the Docker Cloud CLI

In this step you will install the Docker Cloud Command Line Interface (CLI) on your *management host*.

The Docker Cloud CLI allows you to interact directly with Docker Cloud, and you will be using it, along with the Docker Cloud web UI, as part of this lab.

Installing the Docker Cloud CLI differs based on the operating system of your *management host*.

1. Make sure you are logged on to your *management host*: Either a local terminal/command window if using Docker for Mac or Docker for Windows, or an SSH session to **v111node1** if you are using a VM.

2. Install the `docker-cloud` CLI.

  **Linux and Windows systems:** Execute the following command (if you do not have pip installed, you will be prompted to install it using the command `sudo apt-get install python-pip`)

		$ sudo pip install docker-cloud

  **Mac OS X:** Execute the following command (you will need to have `Brew` installed)

		$ brew install docker-cloud
		
  > **Note**: If you do not have brew installed, you can install it copy this command into your local command window to install it:
  `/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"`

4. Verify the install by typing `docker-cloud -v`. This will show the version of the Docker Cloud CLI running on your system.

		$ docker-cloud -v
		docker-cloud 1.0.5

> **Note**: The actual version number may differ than what is shown above. 

You now have the Docker Cloud CLI installed on your *management host* and are ready to start using Docker Cloud.

> **Note**: You can uninstall the Docker Cloud CLI by running `pip uninstall docker-cloud` on Linux and Windows, or `brew uninstall docker-cloud`on OS X.

# <a name="install_node"></a>Task 2: Deploy the Docker Cloud Agent on a Docker host

*Docker hosts* that are managed by Docker Cloud are called *nodes*. In this step you will install the Docker Cloud agent on a *Docker host* and register it as a *node* with Docker Cloud. Later in the lab you will use Docker Cloud to deploy containers to this node.

Docker Cloud allows you to easily spin up new instances on various cloud platforms and deploy the Docker Cloud agent to them so that they can be Docker Cloud nodes. It also let's you deploy the agent to **existing** Docker hosts so that they can also be Docker Cloud nodes.

In this step you'll deploy the Docker Cloud agent to an existing Docker host (**v111node0**) in your lab.

> **Note** that this is **v111node0** which is different than **v111node1** that you *may* have used for your *management host* in the previous step.

1. Open a terminal window and SSH into **v111node0** 

		ssh <username>@<v111node0 hostname>

1. Navigate to [`https://cloud.docker.com`](https://cloud.docker.com) and login with your Docker ID.

2. Click the **Create a Node** icon on the welcome screen

    > **Note**: For this lab you are using the free tier of Docker cloud, this only allows you to add one managed node. Adding a 2nd managed node will fail.

3. Click **Bring your own node**

	![byon_button](./images/byon_button.png)

4. The dialog that appears lists the supported Operating Systems and provides the command that you will use to deploy the Docker Cloud agent. The command includes a token that allows the agent to communicate and register with Docker Cloud.

    ![](images/node-byoh-wizard-v2.png)

5. Copy the command to your clipboard.

6. Navigate back to your terminal session for **v111node0**

7. Paste the command onto the command prompt on **v111node0**

		$ curl -Ls https://get.cloud.docker.com/ | sudo -H sh -s c7a941OHAIac9419e837f940fab9aa4f1

 If prompted ender the password for **v111node0**
	
 > **Note**: Remember to cut and paste the command and token from the Docker Cloud UI and not the one form the example above.

    The command downloads a script which installs and configures the Docker Cloud agent and registers the host as a *node* with Docker Cloud.

    Upon completion you should see something similar to:

    ```
    -> Configuring dockercloud-agent...
    -> Starting dockercloud-agent service...
	dockercloud-agent start/running, process 1893
	-> Done!

	*******************************************************************************
	Docker Cloud Agent installed successfully
	*******************************************************************************

	You can now deploy containers to this node using Docker Cloud
	```


6. Switch back to your web browser and confirm that the new Linux host is detected as shown below.

	> **Note**: In some instances the agent will successfully install, but the web interface does not automatically update. If after a minute or two your web interface continues to indicate that it's waiting for the agent to connect, refresh the Docker cloud page. 

	![byon_success](./images/byon_success.png)
	

7. Click **Close Window**

You have successfully added **v111node0** as a Docker Cloud *node*. This means Docker Cloud can manage **v111node0** and deploy containers to it.

# <a name="deploy_service"></a>Task 3: Deploy a Service

In this step you will use the Docker Cloud web UI to deploy a simple application comprising a single *service*.

A *service* is a group of containers based off the same tagged image (`image:tag`). 

When you create a service in the Docker Cloud web interface, a wizard walks you through configuring the service in three steps.

+ **Step 1 - Choose a Container Image:** Docker Cloud supports images form public and private repos on Docker Hub and thid party registries. It also provides a set of *Jumpstart* repos that are designed to make deploying simple applications easy.
+  **Step 2 - Configure the Service:** Services have various properties and values that need setting. These include: a service a name, initial number of containers, which ports to expose/publish, the entrypoint command, memory and CPU limits.
+  **Step 3 - Set Environment variables:** Each service has a set of environment variables that are used to configure the service, such as linking your service to other services in Docker Cloud.

> **Note**: In this lab  we won't be working with environment variables or connecting data volumes, but these are also available as optional steps in the wizard.

Let's get started by selecting a service to deploy.

1. Click the **Services** link in the menu on the left hand side of the Docker Cloud web UI.

	![services_icon](images/services_icon.png)

2. Click **Create**.

	![](images/create-first-service.png)

3. Click the rocket icon near the top of the page and click on the **dockercloud/hello-world** image from the **Miscellaneous** section.

  This will take you to the **Services\Wizard** page.

    ![](images/first-service-wizard.png)

  The **dockercloud/hello-world** image creates a container (service) that runs an NGINX web server that displays a simple *hello world* web page.

  For the purposes of this lab, the only modification you need to make on this page is to expose a port and map it to a node (host) port. Let's do that.

4. Scroll down to the **Ports** section and place a check in the **Published** check box.


5. Replace **dynamic** with "8080".

	![](images/port_8080.jpg)

	> **Note**: Two containers on the same node cannot publish to the same port. If you have completed other labs that already have a container on the node using port 8080, this operation will fail.

6. Click **Create and deploy**.


  Docker Cloud will now create and deploy service. This may take a minute or two while the image is downloaded and the container deployed.

![](images/first-service-create-and-deploy-button.png)

Once the service is deployed you will be shown the detailed view of the Service. This view contains six informational sections:

  - **Containers**: lists the containers that are part of this service and their status. This is also where you'd go to scale the number of containers in the service up or down.
  - **Endpoints**: shows a list of available service and container endpoints.
  - **Triggers**: allows you to set triggers that perform automatic actions such as scaling a node or redeploying an image when the source updates.
  - **Links**: lists the links between services. For this tutorial this section will be empty.
  - **Volumes**: lists the volumes attached to the service to store data. For this tutorial this section will be empty.
  - **Environment Variables**: lists the environment variables for the service.

Two additional tabs of information are available for each service:

  - **Logs**: shows the recent logs from all the containers in this service.
  - **Timeline**: a timeline of API calls, and accompanying logs, that were performed against the service.

The service is now deployed and can be reached over the internet on port 8080.

## <a name="check_service"></a>Task 3.1: Check the service

Let's make sure the service is up and listening for requests.

Make sure you are logged in to the Docker Cloud web UI and on the details page of the service deployed in the previous step.

1. Click the **Timeline** tab and select **Service Start** to see a log output similar to the one below.

	> **Note**: It can take a couple of minutes for the container to deploy.

	![](images/first-service-timeline.png)

2. Click back onto the **General** tab

	Notice that the hello-world status line shows as **Running** once the service is deployed successfully.

	The **Containers** list further down the **General** tab shows all of the containers in this service. There should just be one for now.

	![](images/first-service-container-list.png)

3. Click the container's name to go to the container's detail view.

	From this page you can see additional information about the container, such as endpoints, logs, environment variables, volumes, a terminal, and the containers own timeline.

	![](images/first-service-container.png)

	The **Endpoints** section lists the endpoints (ports) that this container is listening on. In the screenshot above, there is a single endpoint: **hello-world-66622790-1.9ab56d66.container.docker.io:8080**. The endpoint is composed of both the container's hostname and a port number.

4. Click the small link icon in the **Endpoints** section to open a new browser tab to the applications home page. You will see the **hello-world** message and the ID of the container that responded to the request (at this point the service only has one container).

	![](images/first-service-webpage.png)

    You can also click the **Service Endpoint** from the Service's detailed view. The main difference between *service endpoints* and *container endpoints* is that service endpoints load balance across all containers that are part of the service.

**Congratulations!** You've successfully deployed your first service using Docker Cloud.


# <a name="deploy_app"></a>Task 4: Deploy and application using a CI/CD pipeline

One of the most powerful features of Docker Cloud is the ability to define end-to-end CI/CD pipelines. In this part of the lab you're going to link your GitHub account to Docker Cloud to facilitate seamless application delivery.

In order to complete this step you'll need to:
- be logged in to GitHub
- have Docker Cloud linked to your GitHub account
- have `git` installed on your *management host*

To link Docker Cloud with GitHub, click the **Cloud Settings** link in the menu on the left hand side of the Docker Cloud web UI. Scroll down to the **Source providers** section. Click the **power socket** icon and follow the procedure to link your GitHub account.

![](./images/power_socket.jpg)

Now that you've got Docker Cloud linked to your GitHub account We'll start by forking a demo repo.

1. In your web browser navigate to <a href="https://github.com/Cloud-Demo-Team/voting-demo.git"> https://github.com/Cloud-Demo-Team/voting-demo.git</a>.

2. Click the **Fork** button in the upper right hand corner to create your own copy of the repository.

Now we'll clone the repository into our local Docker environment. The following commands will be executed in the terminal or command window for your *management host*.

> **Note**: Be sure to be logged on and running the next commands from your *management host*

3 Change to your home directory

  `$ cd` (for Linux machines)

  `$ cd %userprofile%` (for Windows machines)

4. Clone the repository (you will need to have `git` installed and the `git` binary present in your PATH)

		$ git clone https://github.com/<your github user name>/voting-demo.git

		Cloning into 'voting-demo'...
		remote: Counting objects: 481, done.
		remote: Total 481 (delta 0), reused 0 (delta 0), pack-reused 481
		Receiving objects: 100% (481/481), 105.01 KiB | 0 bytes/s, done.
		Resolving deltas: 100% (246/246), done.
		Checking connectivity... done.

  This will create a copy of the forked repo in a directory called `voting-demo` within your home directory.

5. Change directory into the repo directory

		$ cd voting-demo

6. List the directory contents

  Linux: `$ ls`

  Windows: `$ dir`

	The various YAML files define how the application will be deployed in various environments such as production and staging.

    If you open `docker-compose.yml` you will see that it defines an app with 4 services:

	+ **votinglb**: A load balancer based on HAProxy
	+ **voting**: A web front end to allows users to cast votes
	+ **results**: A web front end that allows you to see the results of the vote
	+ **redis**: A persistent data store for storing voting data

7. Test the application locally

		$ docker-compose up -d

	This will start the application on your *management host*. You will see Docker Compose build several images and ultimately finish with something like this:

		Creating votingdemo_redis_1
		Creating votingdemo_voting_1
		Creating votingdemo_results_1
		Creating votingdemo_votinglb_1

8. Check to see if the voting front end is working by navigating to either `http://localhost` if you are using Docker for Mac or Docker for windows OR the hostname of **v111node1** in your web browser.

  If this does not work, run a `docker ps` command and open your web browser to the IP address shown next to the `votingdemo_votinglb_1` container

	> **Note**: The voting app is running on port 80

	![](images/voting.png)

9. Check to see if the results front end is working by opening a new tab in your browser to either`http://localhost:8000` or `http://<hostname for v111node1>:8000` depending on which option you chose for your *management host*

	> **Note**: You will not see any results until you cast a vote using the voting front end. As you change your vote you can move back to results screen to see the results change.

	![](images/results.png)

Congratulations! You have successfully deployed a simple web app using Docker Cloud.

# <a name="autobuild"></a>Task 4.1: Configure autobuilds

Docker Cloud can automatically build new images when updates are pushed to a repository on GitHub.

In this step you're going to build two GitHub repositories - one for the **voting** part of the app and one for the **results** part. You'll configure them both so that each time a change is pushed to them an updated Docker image will be built.

1. In your web browser return to Docker Cloud and click the **Repositories** link on the left hand side.

	![](images/repositories.png)

2. Click **Create** near the top right of the page

3. Enter the following information

	+ **Name**: results
	+ **Description**: Results service for the Docker voting app

4. Click **Create**

	You'll be taken to the details page for ythe new repository. From here you're going to link your GitHub repository and instruct Docker Cloud to rebuild the image whenever a change is pushed to GitHub.

6. Select the **Builds** tab and click the **Link to GitHub** button

7. Make sure the appropriate organization is populated, and enter **voting-demo** for repository

8. Enter **/results** for the Dockerfile path.

9. Make sure **Autobuild** is selected. This is the switch that tells Docker Cloud to build a new image every time a change is *pushed* to GitHub

10. Click **Save and Build** at the bottom of the page.

  You will be taken back to your repository page, notice the status is flashing `building`, It may take a minute or so for the build to complete.

### Create a second repository
Repeat steps 1-11 with the following modifications:

  Create Repo (Step 3)
  + **Name**: voting
  + **Description**: Voting service for the Docker voting app

Specifying the Dockerfile path (Step 8)
  + Enter **/voting** for the Dockerfile path

Well done! You've created two new repos and configured them to autobuild whenever new changes are pushed to the associated GitHub repos.

# <a name="test_autobuild"></a>Task 4.2: Test autobuilds

Switch back the command line of your *management host*. 

> **Note**: If you are not in the `voting-demo` directory that was created when you cloned the repo earlier, change into it now.

1. Change to the voting directory

		$ cd voting

2. Use vi or your favorite text editor to open `app.py`
  + To use `vi` on Linux: `$ vi app.py`
  + To use `notepad.exe` on Windows: `$ notepad app.py`

3. Scroll down to find the lines containing `optionA` and `optionB`, and change **Dev** and **Ops** to **Futbol** and **Soccer**

		optionA = "Futbol"
		optionB = "Soccer"

4. Save your changes

5. Commit changes to the repository and push to GitHub using `git add`, `git commit`, and `git push`
       
       ```
		$ git add *

		$ git commit -m "changing the voting options"
		[master 2ab640a] changing the voting options
 		1 file changed, 3 insertions(+), 2 deletions(-)

 		$ git push origin master
 		Counting objects: 4, done.
		Delta compression using up to 8 threads.
		Compressing objects: 100% (4/4), done.
		Writing objects: 100% (4/4), 380 bytes | 0 bytes/s, done.
		Total 4 (delta 3), reused 0 (delta 0)
		To https://github.com/<your github repo>/voting-demo.git
   		c1788a1..2ab640a  master -> master
       ```
> **Note:** If you have two factor authentication (2FA) configured on your GitHub account you will need to enter your personal access token (PAT) instead of your password when prompted.

6. In the Docker Cloud web UI, navigate back to the **voting** repo and notice that the status is **BUILDING**.

	> **Note**: It can take several minutes for a build job to complete

	![](images/building.png)

1. Click the **Timeline** tab near the top of the screen

	![](images/timeline.png)

1. Click `Build in master:/voting`

	Here you can see the status of the build process

	![](images/build_status.png)

Congratulations. You have configured your Docker Cloud to build a new Docker image each time you push a change to your application's repository on GitHub.

# <a name="autodeploy"></a>Task 4.3: Configure automated deployments

Now that you have Docker Cloud configured to update your images whenever new code is pushed to GitHub, you will configure the voting application to redeploy each service anytime the underlying image is changed.

The overall flow is as follows: Push changes to GitHub -> Autobuild of the affected Docker Cloud image -> Automatically redeploy the service that uses that image -> Application up to date!

Applications deployed on Docker Cloud are referred to as **Stacks** and are defined by a YAML file much like they are with Docker Compose. In this step you will be using the `docker-cloud.yml` file inside the `voting-demo` directory that you cloned earlier. The contents of the file are listed below.

		redis:
  			image: 'redis:latest'
		results:
  			autoredeploy: true
  			image: 'cloudorg/results:latest'
  			links:
   	 			- redis
  			ports:
    			- '8000:80'
  			restart: always
		voting:
			autoredeploy: true
		  	image: 'cloudorg/voting:latest'
		  	links:
		  	  	- redis
		  	restart: always
		  	target_num_containers: 4
		votinglb:
			image: 'dockercloud/haproxy:latest'
		  	links:
		  		- voting
		  	ports:
		  		- '80:80'
		  	roles:
		    	- global

This file, like the Docker Compose file we ran earlier, will stand up four services. But there are a couple of things to note:

+ The inclusion of the **autoredploy** flag will cause both the **voting** and **results** services to be automatically redeployed if the underlying image is changed.
+ The **target_num_containers** flag in the **voting** service will ensure that the service initially starts with four containers.

Let's go ahead and deploy the application.

1. On your *management host* Change into the `voting-demo` directory

		$ cd ~/voting-demo

2. Use `vi` or your favorite text editor to modify the `docker-cloud.yml` file. Currently the images for **voting** and **results** are pointing at the **cloudorg** organization. You need to replace **cloudorg** with your Docker ID.

		results:
  			autoredeploy: true
  			image: '<your Docker ID>'/results:latest'

	and

		voting:
			autoredeploy: true
		  	image: '<your Docker ID>/voting:latest'

	> **Note**: You do NOT need to change the organization for the **haproxy** image.

3. Authenticate to Docker

		$ docker login
		Login with your Docker ID to push and pull images from Docker Hub. If you don't have a Docker ID, head over to https://hub.docker.com to create one.
		Username: <your Docker ID>
		Password: <your Docker ID password>
		Login Succeeded

4. Start the stack using the Docker Cloud CLI

		$ docker-cloud stack up
		5087205f-80c5-498a-9005-0ff9a29e48f0

	> **Note**: You can also stand up stacks form the **Stacks** page of the Docker Cloud web interface.

5. Back in the Docker Cloud web UI, click the **Stacks** icon in the left hand menu.

	![](images/stacks_icon.png)

	You should see your Stack running.

	![](images/stack_running.png)

6. Click on the stack name - `voting-demo`

7. Scroll down to the **Endpoints** section and click on the small link icons at the end of the two lines under **Service Endpoints**

  Notice the voting app now says "Futbol VS Soccer" instead of "Dev vs Ops". This is the result of the change we made earlier.

	> **Note**: As before you won't see anything on the results page until you vote.

Now that you have your application up and running, let's push a change to GitHub and watch Docker Cloud redeploy the application.

8. Switch back to your terminal or command window on your *management host*.

9. Change to the voting directory

		$ cd ~/voting-demo/voting

10. Use vi or your favorite text editor to open `app.py`

		$ vi app.py

11. Scroll down to find the lines containing `optionA` and `optionB`, and change **Futbol** and **Soccer** to **Seattle** and **San Francisco**

		optionA = "Seattle"
		optionB = "San Francisco"

12. Save your changes

13. Commit changes to the repository and push to GitHub using `git add`, `git commit`, and `git push`

		$ git add *

		$ git commit -m "changing the voting options"
		[master 2ab640a] changing the voting options
 		1 file changed, 3 insertions(+), 2 deletions(-)

 		$ git push origin master
 		Counting objects: 4, done.
		Delta compression using up to 8 threads.
		Compressing objects: 100% (4/4), done.
		Writing objects: 100% (4/4), 380 bytes | 0 bytes/s, done.
		Total 4 (delta 3), reused 0 (delta 0)
		To https://github.com/<your github repo>/voting-demo.git
   		c1788a1..2ab640a  master -> master

14. Switch back to Docker Cloud in your web browser

15. Click **Repositories** in the left-hand menu

16. Navigate to the **voting** repository and click the repository name

17. Notice the status is flashing **BUILDING**

	> **Note**: It can take several minutes for a build job to complete

	![](images/building.png)

18. Click the **Timeline** tab near the top of the screen

	![](images/timeline.png)

19. Click the running task `Build in master:/voting`

	Here you can see the status of the build process

	![](images/build_status.png)

20. Once the build finishes you can click on the **Services** link in the left hand menu and see the **voting** service and the **results** service *redeploying*.

  It only takes a few seconds to redeploy each service, so you may miss this.

21. Once both services have redeployed with the updated images, if you refresh the **voting** and **results** web pages (you should still have a tab open for each of them) you will see that the values now show as **Seattle** and **San Francisco**.

Congratulations! You have successfully deployed an application and configured it to automatically redeploy any time changes are pushed to its GitHub repo.

This completes the Docker Cloud lab. **Have a Docker employee verify your lab results and collect your contact information to receive a coupon code for 4 free private repositories and an additional node on Docker Cloud.**

In this lab you learned how to configure a node with Docker Cloud, create a service from the Docker Cloud jumpstart images, and then deploy this service to your own node using the Docker Cloud UI.

Next, you defined an end-to-end CI/CD pipeline by configuring Docker Cloud autobuilds and then configured the application to automatically redeploy any time changes are pushed to its GitHub repo.

Feel free to continue to explore additional features of Docker Cloud!
