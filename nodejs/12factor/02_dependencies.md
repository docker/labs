# 2 - Dependencies

Application's dependencies must be declared and isolated

## What does that mean for our application ?

Declaration are done in package.json file.

Let's add sails-mongo (mongodb driver) as we'll need it very quicky

`npm install sails-mongo --save`

The package.json file should look like the following:

```
{
  "name": "messageApp",
  "private": true,
  "version": "0.0.0",
  "description": "a Sails application",
  "keywords": [],
  "dependencies": {
    "ejs": "2.3.4",
    "grunt": "0.4.5",
    "grunt-contrib-clean": "0.6.0",
    "grunt-contrib-coffee": "0.13.0",
    "grunt-contrib-concat": "0.5.1",
    "grunt-contrib-copy": "0.5.0",
    "grunt-contrib-cssmin": "0.9.0",
    "grunt-contrib-jst": "0.6.0",
    "grunt-contrib-less": "1.1.0",
    "grunt-contrib-uglify": "0.7.0",
    "grunt-contrib-watch": "0.5.3",
    "grunt-sails-linker": "~0.10.1",
    "grunt-sync": "0.2.4",
    "include-all": "~0.1.6",
    "rc": "1.0.1",
    "sails": "~0.12.3",
    "sails-disk": "~0.10.9",
    "sails-mongo": "^0.12.0"  // Newly added dependency
  },
  "scripts": {
    "debug": "node debug app.js",
    "start": "node app.js"
  },
  "main": "app.js",
  "repository": {
    "type": "git",
    "url": "git://github.com/GITUSER/messageApp.git"
  },
  "author": "AUTHOR",
  "license": ""
}
```

Dependencies are isolated within _node-modules_ folder where all the [npm](https://npmjs.org) libraries are compiled and installed.

```
$ ls node_modules/
ejs                  grunt-contrib-coffee grunt-contrib-cssmin grunt-contrib-uglify grunt-sync           sails
grunt                grunt-contrib-concat grunt-contrib-jst    grunt-contrib-watch  include-all          sails-disk
grunt-contrib-clean  grunt-contrib-copy   grunt-contrib-less   grunt-sails-linker   rc                   sails-mongo
```

[Previous](01_codebase.md) - [Next](03_configuration.md)
