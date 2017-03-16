## In-container Java Development: Eclipse

### Pre-requisites

* [Docker for OSX or Docker for Windows](https://www.docker.com/products/docker)
* [Eclipse](http://www.eclipse.org/downloads/) (install Eclipse IDE for Java EE Developers)
* [Java Development Kit](http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html)
* [Maven for Eclipse](http://www.eclipse.org/m2e/) (see instructions for adding the Maven plug-in to Eclipse)

### Getting Started

On the command line clone the [registration-docker](https://github.com/docker/labs) repository

```
git clone https://github.com/docker/labs
cd labs/developer-tools/java-debugging
```

In Eclipse, import the app directory of that project as an existing maven project

`File`> `Import` Select `Maven`> `Existing Maven Projects`> `Next`

![](images/eclipse_import_existing_maven_project_1.png)
 
 
Select the app subdirectory of the directory where you cloned the project.
 
![](images/eclipse_import_existing_maven_project_2.png)
 
 
Select the pom.xml from the app directory, click `Finish`. 
 
![](images/eclipse_import_existing_maven_project_3.png)


### Building the application

The application is a basic Spring MVC application that receives user input from a form, writes the data to a database, and queries the database.

The application is built using Maven. To build the application click on `Run` > `Run configurations`

![](images/eclipse_maven_run_config3.png)

Select `Maven build` > `New`

![](images/eclipse_maven_build_new.png)

Enter a `Name` for the configuration.

Set the base direct of the application `<path>/registration-docker/app`.

Set the `Goals` to `clean install`.

Click `Apply`

Click `Run`

![](images/eclipse_maven_run_config_apply.png)

The results of the build will be displayed in the console.

![](images/eclipse_maven_console_build_result.png)

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

Tomcat supports remote debugging the Java Platform Debugger Architecture (JPDA). Remote debugging was enabled when the tomcat image (registration-webserver) was built.

To configure remote debugging in Eclipse, click on `Run` > `Debug Configurations ...`

![](images/eclipse_debug_configure2.png)

Select `Remote Java Application` and click on `Launch New Configuration` icon

![](images/eclipse_debug_configure_new.png)

Enter a `Name` for the configuration. Select the project using the `browse` button. Click on `Apply` to save the configuration and click on `Debug` to start the debugging connection between Tomcat and Eclipse.

![](images/eclipse_debug_configure_docker.png)

#### Finding the Error

Since the problem is with the password, lets see how the password is set in the User class. In the User class, the setter for password is scrambled using [rot13](https://en.wikipedia.org/wiki/ROT13) before it is saved to the database.

![](images/eclipse_debug_User_password.png)

Try registering a new user using the debugger. In Eclipse, change the view or Perspective to the debugger by clicking on `Window` > `Perspective` > `Open Perspective` > `Debug`

![](images/eclipse_debug_perspective.png)

Eclipse will switch to the debug perspective. Since we enable remote debugging earlier, you should see the Daemon Threads for Tomcat in the debug window. Set a breakpoint for in the User class where the password is set.

![](images/eclipse_debug_User_breakpoint.png)

Register a new user with the username of 'Moby' and with 'm0by' as the password, click `Submit`, click `yes`

![](images/app_register_moby2.png)

Eclipse will display the code at the breakpoint and the value of password in the variables window. Note that the value is `m0by`

![](images/eclipse_debug_User_moby.png)

Click on `resume` or press `F8` to let the code run.

![](images/eclipse_debug_resume.png)

Next, set a breakpoint on the getPassword in the User class to see the value returned for password. You can also toggle off the breakpoint for setPassword.

![](images/eclipse_debug_User_getPassword.png)

Try to log into the application. Look at the value for password in the Eclipse variables window, note that it is `z0ol` which is `m0by` using ROT13.

![](images/eclipse_debug_User_show_user.png)

In this MVC application the UserController uses the findByLogin method in the UserServiceImpl class which uses the findByUsername method to retrieve the information from the database. It then checks to see if the password from the form matches the user password. Since the password from the login form is not scrambled using ROT13, it does not match the user password and you cannot log into the application.

To fix this, apply ROT13 to the password by adding an import near the top of the file

```
import com.docker.UserSignup.util.Rot13
```

and replace the contents of `findByLogin` with

```
public boolean findByLogin(String userName, String password) {  
    User usr = userRepository.findByUserName(userName);

    String passwd = Rot13.rot13(password);

    if(usr != null && usr.getPassword().equals(passwd)) {
        return true;
    }

    return false;
}
```

![](images/eclipse_debug_UserServiceImpl_code.png)

Set a breakpoint in UserServiceImpl on the findByLogin method. Log in again and look at the values for the breakpoint. The 'passwd' variable is `z0ol` which matches the password for the user moby.

![](images/eclipse_debug_UserServiceImpl_values.png)

Continue (`F8`) and you should successfully log in.

![](images/app_debug_success.png)
