# 1 - Codebase

**one application <=> one codebase**

If there are several codebase, it's not an application, it's a distributed system containing multiple applications.

One codebase used for several deployments of the application
* development
* staging
* production

## What does that mean for our application ?

We will use Git versioning system (could have chosen subversion, ...) to handle our source code.

* Create a repo on [Github](https://github.com)
* Put the code under git

```
$ echo "# messageApp" >> README.md
$ git init
$ git add .
$ git commit -m 'First commit'
$ git remote add origin git@github.com:GITUSER/messageApp.git
$ git push origin master
```

The update we've done is not linked with Docker, but we'll see in a next chapter that this will greatly help the integration of several components of the Docker ecosystem.

[Previous](00_application.md) - [Next](02_dependencies.md)
