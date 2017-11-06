# Rails on Docker

Ruby on Rails is a popular web application framework. Implementng RoR in containers presents challenges because of conflicting workflows and practices between Ruby on Rails and container based development. Developing RoR applications in containers is feasible keeping in mind that these differences can be overcome by changing workflows and practices.

## Challenges and Remediations

### Different versions of Ruby

There are many different versions of Ruby in use. Ruby applications can be based on 1.8, 1.9, 2.0 and all intermediary versions up to the latest release. This includes "upstream" Ruby but also REE (an "Enterprise Edition" set of patches, for older versions of Ruby), and a number of "Ruby on X" ports: JRuby (on top of the JVM), IronRuby (on top of .NET), Maglev, Rubinius, RubyMotion, and more. 

In contrast, there are also many many versions of Python in use, but the majority of applications use the lastest 2.X or 3.X versions. Similarly, Java applications use the last two major releases of either Java released by Oracle or OpenJDK. 

Earlier versions of Ruby are used in production because upgrading presents challenges such as, critical gems are not backward-compatible. Debugging tools like pry, rubocop, byebug... do work on many versions, but not consistently on all Ruby verisons.

As a result, Ruby developers (and people deploying Ruby apps) rely on tools like [rvm](https://rvm.io/) or [rbenv](https://github.com/rbenv/rbenv) to install a specific version of Ruby. These tools enable developers to switch between different versions of Ruby, and between different sets of gems (when different applications have conflicting requirements).

Ruby programmers expect to be able to request a specific version of Ruby on demand. This is not currently available on Docker Store and developers will have to create Dockerfiles that are specific to development environment requirements. Docker Store currently has Ruby 2.1, 2.2, 2.3; which means the images on Docker Store can support newer applications.  However, migrating older applications to a container may require custom builds to meet Ruby version requirements.


#### Use RVM to manage Ruby Versions

Using RVM poses other challenges. Specifically, RVM works with a combination of custom PATH and shell functions. After installing RVM in a Dockerfile (e.g. ```curl get.rvm.io | bash```); to use RVM, it must be run in a login shell. However, the shell started by Docker (when executing a "RUN" build step) is not a login shell.

One workaround is to wrap all commands with "bash -l -c", e.g.:

```
RUN bundle install
```

Becomes

```
RUN bash -l -c "bundle install"
```

Another workaround is to symlink /bin/sh to /usr/local/rvm/bin/rvm-shell. If you do that, you also need to change /bin/which so that it uses #!/bin/dash instead of #!/bin/bash (on many Linux systems, /bin/which is implemented as a shell script; and rvm-shell depends on /bin/which; so you get an infinite loop). Albeit clunky, that workaround is the one that was the most satisfying (less side effects, less potential for mistake if you forget to wrap RUN commands properly, less risk to use system Ruby my mistake...)

### Bundler
[Bundler](http://bundler.io/) is a very popular tool in the Ruby world. It is "an exit from dependency hell, and ensures that the gems needed are present in development, staging, and production."

#### Adding Gems during development
Bundler makes sure that a Ruby app can access the gems listed in the Gemfile, and only those gems. This is different from Python's pip: with pip, you can install a bunch of dependencies from a requirements.txt file, then manually install a few extras. With bundler, everything has to be in the Gemfile. This is advantage when you want to avoid "leaking" dependencies; i.e. being unaware that your project does, in fact, use some gems that are not declared in your Gemfile. However, it means that in a Docker environment, you have to use a separate Gemfile.tip file where you add extra gems during development.

```
# Example from: https://github.com/cpuguy83/docker-rails-dev-demo/blob/master/Dockerfile

FROM ruby:2.2
RUN apt-get update && apt-get install -y sqlite3 libsqlite3-dev openssl libssl-dev libyaml-dev libreadline-dev libxml2-dev libxslt1-dev
WORKDIR /opt/myapp
ENV RAILS_ENV production

# Add Gemfile stuff first as a build optimization
# This way the `bundle install` is only run when either Gemfile or Gemfile.lock is changed
# This is because `bundle install` can take a long time
# Without this optimization `bundle install` would run if _any_ file is changed within the project, no bueno
ADD Gemfile /opt/myapp/
ADD Gemfile.lock /opt/myapp/
RUN bundle install

# This will now install anything in Gemfile.tip
# This way you can add new gems without rebuilding _everything_ to add 1 gem
# Anything that was already installed from the main Gemfile will be re-used
ADD Gemfile.tip /opt/myapp/
RUN bundle install


# Add rake and its dependencies
ADD config /opt/myapp/config
ADD Rakefile /opt/myapp/

# Add everything else
# Any change to any file after this point (if not in .dockerignore) will cause the build cache to be busted here
# This includes changes to the Dockerfile itself
# Goal here is to do as little as possible after this entry
ADD . /opt/myapp

ENV PATH /opt/myapp/bin:$PATH
ENTRYPOINT ["/opt/myapp/bin/start.rb"]
CMD ["server"]
```
#### Caching Gem Files to Avoid Long Build Times
Bundler becomes particularly problematic when switching back and forth between different branches. Each new branch with a different Gemfile (even if 90% of the versions therein are the same) will trigger a full build (which can easily amount to half an hour for an average Rails project, with a decent computer and internet access; and can be multiple hours for a big project and/or a more modest hardware setup or internet access). The [work around](http://ilikestuffblog.com/2014/01/06/how-to-skip-bundle-install-when-deploying-a-rails-app-to-docker/) to Bundler running on each build is to copy the Gemfile and Gemfile.lock to a temporary directory and run Bundler from there. If neither file is changed during subsequent builds, the ADD instructions will be cached. Below is a working example:

```
FROM ubuntu:12.10
MAINTAINER brian@morearty.org
 
# Install dependencies.
RUN apt-get update
RUN apt-get install -y curl git build-essential ruby1.9.3 libsqlite3-dev
RUN gem install rubygems-update --no-ri --no-rdoc
RUN update_rubygems
RUN gem install bundler sinatra --no-ri --no-rdoc
 
# Copy the Gemfile and Gemfile.lock into the image. 
# Temporarily set the working directory to where they are. 
WORKDIR /tmp 
ADD railsapp/Gemfile Gemfile
ADD railsapp/Gemfile.lock Gemfile.lock
RUN bundle install 
 
# Everything up to here was cached. This includes
# the bundle install, unless the Gemfiles changed.
# Now copy the app into the image.
ADD railsapp /opt/railsapp
 
# Set the final working dir to the Rails app's location.
WORKDIR /opt/railsapp
 
# Set up a default runtime command
CMD rails server thin
```

The previous issue becomes totally unbearable when doing a git bisect, or trying to find which specific revision introduced a specific bug or broke a specific test. Each modification (even minor) of the Gemfile triggers a 30-minute full rebuild (instead of taking a couple of minutes tops).

#### Using Bundler Under Limited Bandwidth 

In the case of with slow internet connections, it is possible to run a local gem server. Rubygems.org provides a [guide](http://guides.rubygems.org/run-your-own-gem-server/) for running a local gem server. The caveat to a local gem server is that the Dockerfile becomes then less portable, since it relies on the gem server to be up. Note that even with a mirror, a lot of gems take a long time to install, because they build native extensions.

In practice, the divide and conquer strategy appears to be successful; i.e. spin up cloud instances, split the commits in ranges, and assign a range to each instance, and do a simple "docker build" each time. Howerver, in a test of ~100 commits (with more than half of the time spent installing dependencies vs. actually running tests) this took over 6 hours

Mounting a volume across builds would help, since bundler could use this as a gem cache. (This also has been asked by people building Python apps, in particular using SciPy and NumPy, which are notoriously slow to build.) People generally understand the argument that build-time volumes break reproducibility of builds, but it still leaves them short of a good solution to achieve fast builds.

### RAILS_ENV

One of the tenets of Docker is to use the same container images in dev, testing, preprod, and prod. In Rails, there is the notion of environments. A typical Rails project will have dev, production, test, staging. (Not always all of them, sometimes more; but this is typical.)

Setting the RAILS_ENV environment variable switches between environments. Each environment has:

* a different set of gems (you don't have the debugging tools in production),
* a different way to handle static assets (you don't precompile JS, CSS, etc. in dev),
* a different database (at least a different server, but sometimes a totally different engine),

This architecture is not the standard Docker model, but works well for Ruby development practices.

### Assets Pipeline

Assets include javascript, CSS, images... These resources often need to be transformed before being served to the user:

* javascript can be "minified";
* CSS can be compiled (using e.g. SASS or LESS);
* images can be resized or converted.

In dev mode, this is done on-the-fly by the application server (a file can be edited and immediately reload in your browser, without having to recompile assets or restart the application server).

In production, however, assets are pre-compiled, and served by a static server. Sometimes, pre-compiled assets are pushed to a CDN instead of being served directly. 

Assets are meant to be heavily cached by the clients. To enable that, asset URLs are seeded, e.g. "/assets/main.css" becomes "/assets/main-eb665b0c5b3ae55b0765b570d225700e.css", and when the file is modified, the name changes too (to force a cache miss from the clients). This means:

* that at any given time, a client might still have (in cache) an older version of a CSS, JS, or other asset, itself referencing another asset that hasn't been loaded yet (if the asset is loaded on demand, e.g. a hover image); therefore, old versions of assets should be kept forever (or at least long enough to avoid breaking too many users when rolling out a new version);
* that a manifest is generated after the pre-compilation, containing the mapping giving the seeded path for each asset. The manifest is required by the application server to produce correct URLs pointing to the assets.

The assets pipeline has an extra challenge: when pre-compiling assets, the whole Rails framework is brought up, including the database connection (even if it's not strictly necessary 99% of the time). This means that the database server must be available to pre-compile the assets. This makes it extra difficult to run the assets pipeline from a Dockerfile.

#### RAILS_ENV and Assets Pipeline

This requires a departure from the typical Ruby workflow. Having a single environment that always compute assets on the fly is the typical Docker pattern, instead of handling dev and production separately. To avoid overloading the app server with assets compilation, a caching web server can be used such asSquid or Varnish. In this specific scenario, since expiration is not a concern, NGINX can adequately address the issue of computing assets on the fly.

### App Server vs Web Server

Rails, like a few other complex frameworks, makes a clear distinction between the application server and the web server. The web server handles static files (and often deals with redirections, logging, conditionals...) and hands off everything else to the app server, which is the actual Ruby part. The web server is (almost always) necessary, because it appears to be unfeasible to achieve high performance static file serving with a pure Ruby solution. The most frequently used web servers are Apache or NGINX, while the popular app servers include Thin, Unicorn, and Mongrel.

Sometimes the app server is a module in the web server. While the latter seems simpler from an architectural point of view, it implies a more complex configuration for the web server, since the subtleties of "which Ruby interpreter to use" and "which gem set to activate" now propagate to the web server. Furthermore, app server and web server have to share some information about the static assets (see previous paragraph).

#### Containerization: App Server vs Web Server

There are at minimum four possibilities for app server and web server containerization, each with their pros and cons:

* app server and web server in separate containers, assets pre-compiled at build time: this achieves clean separation, and efficient handling of assets; but the assets have to be built in the app server, then extracted to be copied to the web server before building the web server image; moreover, the database has to be available at build time;

* app server and web server in separate containers, assets pre-compiled at run time, placed in a volume: this achieves clean separation; there is a transient period (just after deployment) where assets have yet to be built, and the service is not fully operational; so zero-downtime deployment requires extra steps; since the assets are built at run-time, database availability is not an issue;

* app server and web server in separate processes but in the same container: this considerably simplifies asset handling (though database availability remains an issue), but it requires using a process manager in the container, since we're running two independent processes in the same container;

* app server and web server in the same process (and in the same container): this obviates the question of whether to put them in separate containers or not, but it is much less flexible, since it reduces the choice of app servers down to Passenger and mod_ruby. Also, configuration becomes a bit trickier (but admittedly, this should be a one-time cost).


### Database Migrations

Rails has a sophisticated database migration mechanism, helping operators to ensure that the database schema is exactly in sync with ActiveRecord models as they are declared in the application code. This is not a problem and Rails even comes with a few functions to run migrations in a rather safe way, i.e. "bring up the database schema to what it should be, no matter what."

However, this mechanism hasn't been designed with automation in mind. The typical workflow when deploying a Rails app is to execute Capistrano from a central machine. Capistrano will distribute the new version of the code, pre-compile assets, execute database migrations, and so on. But in a container-based environment, there is no central machine. If migrations are executed by the app container, in a scaled environment, you end up with multiple migrations running at the same time. This requires some extra rules.

In addition there is an overall reliance on the database for any rake task. It is common practice that virtually all rake tasks (like assets pre-compilations) would bring up the database driver, even when it's not necessary at all. This adds an extra constraint on helper scripts and various tools that people write to maintain and operate their code.

#### Containers and Database Migrations

This can be alleviated by the use of the (undocumented) rake task "db:abort_if_pending_migrations", which assesses whether the database is ready.

The general idea is to use an entrypoint that will:

* wait for the database server to be up and accepting connections;
* (if using a separate database for the app) check if the application database exists, and create it otherwise;
* check if migrations are pending (this will also check if the schema has been created), and run migrations otherwise;
* if the previous step failed, try again (this will protect against concurrent migration execution by multiple frontends).

This is probably not optimal (especially from a concurrent execution point of view) but it does the job acceptably.

#### Containers and Database Access

As indicated above, many things (including asset pre-compilation) require database access, even though it is frequently unnecessary. Heroku veterans told me that their solution had been to mock the database presence when running specific things (like assets pre-compilation) to let those steps run without the database.

## Resources

Here are additional sources to checkout. 

* [Ruby on Docker Store](https://store.docker.com/images/ruby)
* [Compose and Rails Quickstart](https://docs.docker.com/compose/rails/)
* [Rails & Assets with Docker](http://red-badger.com/blog/2016/06/22/docker-and-assets-and-rails-oh-my/)
* [Docker on Rails Demo](https://github.com/cpuguy83/docker-rails-dev-demo)
