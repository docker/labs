# Modernize Traditional Apps

There are millions of traditional .NET apps running key functions in enterprises. But they're expensive to maintain, complex to upgrade and may be running on old or unsupported versions of Windows. 

Those apps are great candidates for moving to Docker, which you can do *without changing code or rewriting the app*. Running .NET apps in a modern application platform adds [agility, portability and security](https://www.docker.com/sites/default/files/DC_SB_Microsoft.pdf) to existing apps.

These labs walk through modernization programs for typical .NET application architectures. In each case we start with a sample app in a Visual Studio solution, then we follow the same process:

- Package up a Docker image to compile the application, so we can build it without Visual Studio.

- Package up the application into a Docker image, so the app can run on any Windows machine running Docker.

- Run the app in Docker, together with any dependencies.

- Modernize the app, focusing on key features and using the key benefits of the Docker platform.

## Labs

- [Modernize ASP.NET Web Applications](modernize-aspnet/README.md)

### Planned

- Modernize WCF+WPF Smart Client Applications

- Modernize Messaging-Based Integration Apps

