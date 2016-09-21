## Desarrollo Java en Contenedor: IntelliJ Community Edition

### Pre-requisitos

* [Docker for OSX or Docker for Windows](https://www.docker.com/products/docker)
* [IntelliJ Community Edition](https://www.jetbrains.com/idea/download/)
* [Java Development Kit](http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html)


### Empezando

En IntelliJ, clonar el repositorio. Clic en `Check out from Version Control` > `Github`

![](images/intelliJ_git_open_project.png)

Si esta es tu primera vez usando Intellij con Github, ingresa tu cuenta de Github.
![](images/intelliJ_git_login.png)

Clonar el repositorio [registration-docker](https://github.com/spara/registration-docker.git).

![](images/intelliJ_git_clone_repository.png)
Clic en `Import project from external model`, seleccionar `Maven`. Clic `Next`

![](images/intellij_github_import_maven.png)

Seleccionar `Search for projects recursively`. Clic `Next`

![](images/intellij_github_import_maven_configure.png)

Seleccionar el proyecto y clic en `Next`

![](images/intellij_github_import_maven_select.png)

Seleccionar el JDK y clic en `Next`

![](images/intellij_github_import_select_sdk.png)

Clic en `Finish`

![](images/intellij_github_import_project_finish.png)

Clic en `Project View` para abrir el proyecto.

![](images/intelliJ_git_open_project_gui.png)

### Construyendo la aplicación

La aplicación es una aplicación Spring MVC básica que recibe datos del usuario de un formulario, escribe los datos en la base de datos, y consulta la base de datos.

La aplicación se construye usando Maven. Para construir la aplicación clic en el icono de la parte inferior izquierda de IntelliJ y seleccionar `Maven Projects`.

![](images/intellij_maven_setup.png)

La ventana `Maven Projects` se abrirá al lado derecho. Las tareas de maven `clean` y `install` necesitan ser establecidas para construir la aplicación.

Para establecer la tarea `clean`, clic en `Lifecycle` para visualizar el árbol de tareas. Clic derecho en `clean` y seleccionar `Create 'UserSignup [clean]'...`

![](images/intellij_maven_goal_clean.png)

Clic `OK` en la ventana `Create Run/Debug Configuration`.

![](images/intellij_maven_goal_clean_menu.png)

De manera similar configurar la tarea `install`. Clic en `install` en el árbol de Lifecycle. Seleccionar `Create 'UserSignup[install]'...`

![](images/intellij_maven_goal_install.png)

Clic `OK` en la ventana `Create Run/Debug Configuration`.

![](images/intelligj_maven_goal_install_menu.png)

Para construir la aplicación ejecutar `clean`

![](images/intellij_maven_goal_clean_run.png)

Luego ejecutar `install`

![](images/intellij_maven_goal_install_run.png)

Cuando la aplicación se construya se visualizará un mensaje de éxito en la ventana de Log.

![](images/intellij_maven_goal_install_log.png)

### Ejecutando la aplicación

Abrir un terminal e ir al directorio de la aplicación. Iniciar la aplicación con docker-compose

<pre>&gt; docker-compose up </pre>

Docker construirá las imágenes para Apache Tomcat y MySQL e iniciará los contenedores. También, montará el directorio de la aplicación (`./app/target/UserSignup`) como volumen de datos en el host del sistema al directorio webapps Tomcat en el contenedor del servidor web.

Abrir una ventana en el explorador e ir a:
'localhost:8080'; debes ver la página de inicio de Tomcat

![](images/tomcat_home3.png)

Cuando la imagen de Tomcat fue construida, los roles de los usuarios fueron configurados. Clic en el botón `Manager App` para visualizar las aplicaciones desplegadas. Cuando se solicite por usuario y contraseña, ingresa `system` y `manager` respectivamente para entrar a la página de Tomcat Web Application Manager.

![](images/tomcat_web_application_manager3.png)

Puedes usar la página Manager para `Start`, `Stop`, `Reload` o `Undeploy` aplicaciones web.

Para ir a la aplicación, clic en el link `/UserSignup`.

![](images/app_index_page3.png)

### Depurando la aplicación

En la aplicación, clic en `Signup` para crear un nuevo usuario. Completar el formulario de registro y clic en `Submit`

![](images/app_debug_signup2.png)

Clic `Yes` para confirmar.

![](images/app_debug_signup_confirm.png)

Probar el inicio de sesión.

![](images/app_debug_login2.png)

Oh no!

![](images/app_debug_login_fail2.png)

#### Configurar Depuración Remota

Tomcat soporta depuración remota usando Java Platform Debugger Architecture (JPDA). Debug Remoto fue habilitado cuando la imagen tomcat (registration-webserver) fue construida.

Para configurar la depuración remota en  IntelliJ, clic en `Run` > `Edit Configuration ...`

![](images/intelij_debug_run_edit_configurations.png)

Agregar una nueva configuración remota.

![](images/intellij_debug_add_remote_configuration.png)

En la ventana `Run\Debug Configurations`, establecer el `Name` de la configuración y en `Settings` establecer el puerto '8000' el puerto de depuración de Tomcat JPDA por defecto. Clic en `OK` para guardar la configuración.

![](images/intellij_debug_tomcat_remote_settings.png)

#### Buscando el Error

Dado que el problema es la contraseña, veamos como la contraseña se establece en la clase User. En la clase User, el setter para la contraseña es mezclado usando [rot13](https://en.wikipedia.org/wiki/ROT13) antes de ser salvado en la base de datos.

![](images/intellij_debug_User_password.png)

Tratar registrando un nuevo usuario usando el depurador. En el menu clic en `Run` > `Debug...`

![](images/intellij_run_debug.png)

Elegir la configuración de depuración remota de Tomcat. La consola de depuración se motrará en la parte inferior de IntelliJ.

![](images/intellij_debug_choose_remote_tomcat.png)

Establecer un punto de interrupción para la clase User donde el password es establecido.

![](images/intellij_debug_set_breakpoint_password.png)

Registrar un nuevo usuario con el usuario de 'Moby' y con 'm0by' como contraseña, clic `Submit`, clic `yes`

![](images/app_register_moby2.png)

IntelliJ mostrará el código en el punto de interrupción y el valor de la contraseña en la ventana variables. Observar que el valor es `m0by`

![](images/intellij_debug_User_moby.png)

Clic en `Resume Program` para permitir ejecutar el código o presionar `F8` para saltar el punto de interrupción.

![](images/intellij_debug_resume.png)

A continuación, establecer el punto de interrupción en getPassword en la clase User para ver los valores retornados para la contraseña. También puede cambiar el punto de interrupción para setPassword.

![](images/intellij_debug_User_getPassword.png)

Tratar de acceder a la aplicación. Ver el valor de la contraseña en la ventana variables de Eclipse, observar que es `z0ol` el cual es `m0by` usando ROT13.

![](images/intellij_debug_User_show_user.png)

En esta aplicación MVC el UserController usa el método findByLogin en la clase UserServiceImpl la cual usa el método findByUsername para recuperar la información de la base de datos. A continuación, verifica que la contrasenña del formulario conincide con la contraseña del usuario. Dado que la contraseña del formulario de inicio de sesión no es mezclado usando ROT13, este no coincide con la contraseña del usuario y no puedes acceder a la aplicación.

Para solucionar esto, aplicar ROT13 a la contraseña agregando

```
import com.docker.UserSignup.utit.Rot13

String passwd = Rot13.rot13(password);
```
![](images/intellij_debug_UserServiceImpl_code.png)

Establecer un punto de interrupción en UserServiceImpl en el método findByLogin. Iniciar sesión otra vez y mirar los valores para el punto de interrupción. La variable 'passwd' es 'z0ol' la cual coincide con la contraseña para el usuario moby.

![](images/intellij_debug_UserServiceImpl_values.png)

Continuar (`F8`) y debes acceder exitosamente.

![](images/app_debug_success.png)
