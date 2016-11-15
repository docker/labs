## In-container Java Development: NetBeans IDE

### Pre-requisites

* [Docker for OSX or Docker for Windows](https://www.docker.com/products/docker)
* [NetBeans IDE](https://netbeans.org/downloads/)
* [Java Development Kit](http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html)

### Getting Started

Using your git client clone the repository.

```
git clone https://github.docker.com/labs
cd labs/developer-tools/java-debugging
```

Open NetBeans IDE, Click on `Open Project...`

![](images/netbeans_open_project_menu.png)

Select `app` and click on `Open Project`.

![](images/netbeans_open_project_app.png)

### Building the application

The application is a basic Spring MVC application that receives user input from a form, writes the data to a database, and queries the database.

The application is built using Maven. To build the application click on `Run` > `Build Project`.

![](images/netbeans_build_project_menu.png)

The results of the build will be displayed in the console.

![](images/netbeans_build_project_console.png)

### Running the application

Open a terminal and go to the application directory. Start the application with docker-compose

<pre>&gt; docker-compose up </pre>

Docker will build the images for Apache Tomcat and MySQL and start the containers. It will also mount the application directory (`./app/target/UserSignup`) as a data volume on the host system to the Tomcat webapps directory in the web server container.

Open a browser window and go to:
'localhost:8080'; you should see the Tomcat home page

![](images/tomcat_home3.png)

When the Tomcat image was built, the user roles were also configured. Click on the `Manager App` button to see the deployed applications. When prompted for username and password, enter `system` and `manager` respectively to log into the Tomcat Web Application Manager page.

![](images/tomcat_web_application_manager3.png)

You can use the Manager page to `Start`, `Stop`, `Reload` or `Undeploy` web applications.

To go to the application, Click on `/UserSignup` link.

![](images/app_index_page3.png)

### Debugging the Application

In the application, click on `Signup` to create a new user. Fill out the registration form and click `Submit`

![](images/app_debug_signup2.png)

Click `Yes` to confirm.

![](images/app_debug_signup_confirm.png)

Test out the login.

![](images/app_debug_login2.png)

Oh no!

![](images/app_debug_login_fail2.png)

#### Configure Remote Debugging

In the menu click on `Debug` > `Attach Debugger...`

![](images/netbeans_debug_attach_debugger_menu.png)

Make sure that the port is set to 8000, click on `OK`.

![](images/netbeans_debug_attach_debugger_configure.png)

#### Finding the Error

Since the problem is with the password, lets see how the password is set in the User class. In the User class, the setter for password is scrambled using [rot13](https://en.wikipedia.org/wiki/ROT13) before it is saved to the database.

Since we enabled remote debugging earlier, you should see the Daemon Threads for Tomcat in the `Debugging` window. Set a breakpoint for in the User class where the password is set.

![](images/netbeans_debug_User_breakpoint.png)

Register a new user with the username of 'Moby' and with 'm0by' as the password, click `Submit`, click `yes`

![](images/app_register_moby2.png)

NetBeans will display the code at the breakpoint and the value of password in the variables window. Note that the value is `m0by`

![](images/netbeans_debug_User_moby.png)

Click on `Continue` icon or press `F5` to let the code run.

![](images/netbeans_debug_resume.png)

Next, set a breakpoint on the getPassword in the User class to see the value returned for password. You can also toggle off the breakpoint for setPassword. Try to log into the application. Look at the value for password in the NetBeans variables window, note that it is `z0ol` which is `m0by` using ROT13.

![](images/netbeans_debug_User_show_user.png)

In this MVC application the UserController uses the findByLogin method in the UserServiceImpl class which uses the findByUsername method to retrieve the information from the database. It then checks to see if the password from the form matches the user password. Since the password from the login form is not scrambled using ROT13, it does not match the user password and you cannot log into the application.

To fix this, apply ROT13 to the password by adding

```
import com.docker.UserSignup.utit.Rot13

String passwd = Rot13.rot13(password);
```
![](images/netbeans_debug_UserServiceImpl_code.png)

Set a breakpoint in UserServiceImpl on the findByLogin method. Press `F11` or click on `Run` > `Build Project` to update the deployed code. Log in again and look at the values for the breakpoint. The 'passwd' variable is `z0ol` which matches the password for the user moby.

![](images/netbeans_debug_UserServiceImpl_values.png)

Continue (`F5`) and you should successfully log in.

![](images/app_debug_success.png)
