## Desarrollo Java en Contenedor: NetBeans IDE

### Pre-requisitos

* [Docker for OSX or Docker for Windows](https://www.docker.com/products/docker)
* [NetBeans IDE](https://netbeans.org/downloads/)
* [Java Development Kit](http://www.oracle.com/technetwork/java/javase/downloads/jdk8-downloads-2133151.html)

### Empezando

Usar cliente git para clonar el repositorio.

```
git clone https://github.com/spara/registration-docker.git
```

Abrir NetBeans IDE, clic en `Open Project...`

![](images/netbeans_open_project_menu.png)

Seleccionar `app` y clic en `Open Project`.

![](images/netbeans_open_project_app.png)

### Construyendo la aplicación

La aplicación es una aplicación Spring MVC básica que recibe datos del usuario de un formulario, escribe los datos en la base de datos, y consulta la base de datos.

La aplicación se construye usando Maven. Para construir la aplicación clic en `Run` > `Build Project`.

![](images/netbeans_build_project_menu.png)

Los resultados del build serán mostrados en la consola.

![](images/netbeans_build_project_console.png)

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

### Depurando la Aplicación

En la aplicación, clic en `Signup` para crear un nuevo usuario. Completar el formulario de registro y clic en `Submit`

![](images/app_debug_signup2.png)

Clic `Yes` para confirmar.

![](images/app_debug_signup_confirm.png)

Probar el inicio de sesión.

![](images/app_debug_login2.png)

Oh no!

![](images/app_debug_login_fail2.png)

#### Configurar Depuración Remota

En el menu clic en `Debug` > `Attach Debugger...`

![](images/netbeans_debug_attach_debugger_menu.png)

Asegurar que el puerto establecido es 8000, clic en `OK`.

![](images/netbeans_debug_attach_debugger_configure.png)

#### Buscando el Error

Dado que el problema es la contraseña, veamos como la contraseña se establece en la clase User. En la clase User, el setter para la contraseña es mezclado usando [rot13](https://en.wikipedia.org/wiki/ROT13) antes de ser salvado en la base de datos.

Dado que habilitamos el depurador remoto previamente, debes ver los Daemon Threads para Tomcat en la ventana `Debugging`. Establece un punto de interrupción para la clase User donde el password es establecido.

![](images/netbeans_debug_User_breakpoint.png)

Registrar un nuevo usuario con el usuario de 'Moby' y con 'm0by' como contraseña, clic `Submit`, clic `yes`

![](images/app_register_moby2.png)

NetBeans mostrará el código en el punto de interrupción y el valor de la contraseña en la ventana variables. Observar que el valor es `m0by`

![](images/netbeans_debug_User_moby.png)

Clic en el icono `Continue` o presionar `F5` para permitir ejecutar el código.

![](images/netbeans_debug_resume.png)

A continuación, establecer el punto de interrupción en getPassword en la clase User para ver los valores retornados para la contraseña. También puede cambiar el punto de interrupción para setPassword. Tratar de acceder a la aplicación. Ver el valor de la contraseña en la ventana variables de Eclipse, observar que es `z0ol` el cual es `m0by` usando ROT13.

![](images/netbeans_debug_User_show_user.png)

En esta aplicación MVC el UserController usa el método findByLogin en la clase UserServiceImpl la cual usa el método findByUsername para recuperar la información de la base de datos. A continuación, verifica que la contrasenña del formulario conincide con la contraseña del usuario. Dado que la contraseña del formulario de inicio de sesión no es mezclado usando ROT13, este no coincide con la contraseña del usuario y no puedes acceder a la aplicación.

Para solucionar esto, aplicar ROT13 a la contraseña agregando

```
import com.docker.UserSignup.utit.Rot13

String passwd = Rot13.rot13(password);
```
![](images/netbeans_debug_UserServiceImpl_code.png)

Establecer un punto de interrupción en UserServiceImpl en el método findByLogin. Presiona `F11` o haz clic en `Run` > `Build Project` para actuaizar el código desplegado. Iniciar sesión otra vez y mirar los valores para el punto de interrupción. La variable 'passwd' es 'z0ol' la cual coincide con la contraseña para el usuario moby.

![](images/netbeans_debug_UserServiceImpl_values.png)

Continuar (`F5`) y debes acceder exitosamente.

![](images/app_debug_success.png)
