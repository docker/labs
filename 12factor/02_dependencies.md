# 2 - Dependencies

Application's dependencies must be declared and isolated

## What does that mean for our application ?

Declaration are done in package.json file.

Let's add sails-mongo (mongodb driver), ajv@^6.0.0 (required by sails-mongo) and connect-redis (a Redis session store) as we'll need them very quicky

`npm install --save sails-mongo ajv@^6.0.0 connect-redis`

The package.json file should look like the following:

```{
  "name": "message-app",
  "private": true,
  "version": "0.0.0",
  "description": "a Sails application",
  "keywords": [],
  "dependencies": {
    "@sailshq/connect-redis": "^3.2.1",
    "@sailshq/lodash": "^3.10.3",
    "@sailshq/socket.io-redis": "^5.2.0",
    "ajv": "^6.5.5",            // Newly added dependency
    "connect-redis": "^3.4.0",  // Newly added dependency
    "grunt": "1.0.1",
    "sails": "^1.1.0",
    "sails-hook-grunt": "^3.1.0",
    "sails-hook-orm": "^2.1.1",
    "sails-hook-sockets": "^1.5.5",
    "sails-mongo": "^1.0.1"     // Newly added dependency
  },
  "devDependencies": {
    "@sailshq/eslint": "^4.19.3"
  },
  "scripts": {
    "start": "NODE_ENV=production node app.js",
    "test": "npm run lint && npm run custom-tests && echo 'Done.'",
    "lint": "eslint . --max-warnings=0 --report-unused-disable-directives && echo '✔  Your .js files look good.'",
    "custom-tests": "echo \"(No other custom tests yet.)\" && echo"
  },
  "main": "app.js",
  "repository": {
    "type": "git",
    "url": "git://github.com/GITUSER/message-app.git"
  },
  "author": "AUTHOR",
  "license": "",
  "engines": {
    "node": "^10.13"
  }
}

```

Dependencies are isolated within _node-modules_ folder where all the [npm](https://npmjs.org) libraries are compiled and installed.

```
$ ls node_modules/
@sailshq                                  escape-html                            lodash.isundefined      restore-cursor
abbrev                                    escape-string-regexp                   loud-rejection          revalidator
accepts                                   eslint-scope                           lru-cache               rimraf
acorn                                     eslint-visitor-keys                    machine                 rndm
acorn-jsx                                 espree                                 machine-as-action       router
after                                     esprima                                machinepack-fs          rttc
ajv                                       esquery                                machinepack-json        run-async
ajv-keywords                              esrecurse                              machinepack-process     rx-lite
anchor                                    estraverse                             machinepack-redis       rx-lite-aggregates
ansi-escapes                              esutils                                machinepack-strings     safe-buffer
ansi-regex                                etag                                   machinepack-urls        safer-buffer
ansi-styles                               eventemitter2                          machinepack-util        sails
argparse                                  exit                                   makeerror               sails-disk
array-find-index                          express                                map-obj                 sails-generate
array-flatten                             express-session                        media-typer             sails-hook-grunt
arraybuffer.slice                         external-editor                        meow                    sails-hook-orm
async                                     eyes                                   merge-defaults          sails-hook-sockets
async-limiter                             falafel                                merge-descriptors       sails-mongo
babel-code-frame                          fast-deep-equal                        merge-dictionaries      sails-stringfile
backo2                                    fast-json-stable-stringify             methods                 sails.io.js-dist
balanced-match                            fast-levenshtein                       mime                    semver
base64-arraybuffer                        fd-slicer                              mime-db                 send
base64id                                  figures                                mime-types              serve-favicon
better-assert                             file-entry-cache                       mimic-fn                serve-static
binary-search-tree                        finalhandler                           minimatch               setprototypeof
blob                                      find-up                                minimist                shebang-command
bluebird                                  findup-sync                            mkdirp                  shebang-regex
body-parser                               flat-cache                             mongodb                 signal-exit
brace-expansion                           flaverr                                mongodb-core            skipper
browserify-transform-machinepack          foreach                                ms                      skipper-disk
browserify-transform-tools                forwarded                              multiparty              slice-ansi
bson                                      fresh                                  mute-stream             socket.io
buffer-from                               fs-extra                               natural-compare         socket.io-adapter
buffer-shims                              fs.realpath                            ncp                     socket.io-client
builtin-modules                           functional-red-black-tree              nedb                    socket.io-parser
bytes                                     get-stdin                              negotiator              sort-route-addresses
caller-path                               getobject                              nopt                    spdx-correct
callsite                                  glob                                   normalize-package-data  spdx-exceptions
callsites                                 globals                                notepack.io             spdx-expression-parse
camelcase                                 graceful-fs                            number-is-nan           spdx-license-ids
camelcase-keys                            graceful-readlink                      object-assign           sprintf
captains-log                              grunt                                  object-component        sprintf-js
chalk                                     grunt-known-options                    object-hash             stack-trace
chardet                                   grunt-legacy-log                       object-keys             statuses
circular-json                             grunt-legacy-log-utils                 on-finished             streamifier
cli-cursor                                grunt-legacy-util                      on-headers              string-width
cli-width                                 has-ansi                               once                    string_decoder
co                                        has-binary2                            onetime                 strip-ansi
coffee-script                             has-cors                               open                    strip-bom
color-convert                             has-flag                               optionator              strip-indent
color-name                                hat                                    os-tmpdir               strip-json-comments
colors                                    hooker                                 parasails               supports-color
commander                                 hosted-git-info                        parley                  switchback
common-js-file-extensions                 http-errors                            parse-json              table
component-bind                            i                                      parseqs                 text-table
component-emitter                         i18n-2                                 parseuri                through
component-inherit                         iconv-lite                             parseurl                tmp
compressible                              ignore                                 path-exists             tmpl
compression                               immediate                              path-is-absolute        to-array
concat-map                                imurmurhash                            path-is-inside          trim-newlines
concat-stream                             include-all                            path-to-regexp          tsscmp
connect                                   indent-string                          path-type               type-check
connect-redis                             indexof                                pend                    type-is
content-disposition                       inflight                               pify                    typedarray
content-type                              inherits                               pinkie                  uid-safe
convert-to-ecmascript-compatible-varname  ini                                    pinkie-promise          uid2
cookie                                    inquirer                               pkginfo                 ultron
cookie-parser                             ipaddr.js                              pluralize               underscore
cookie-signature                          is-arrayish                            prelude-ls              underscore.string
core-util-is                              is-builtin-module                      process-nextick-args    unpipe
crc                                       is-finite                              progress                uri-js
cross-spawn                               is-fullwidth-code-point                prompt                  util-deprecate
csrf                                      is-promise                             proxy-addr              utile
csurf                                     is-resolvable                          pseudomap               utils-merge
currently-unhandled                       is-utf8                                punycode                uuid
cycle                                     isarray                                qs                      uws
dateformat                                isexe                                  random-bytes            validate-npm-package-license
debug                                     isstream                               range-parser            validator
decamelize                                js-tokens                              raw-body                vary
deep-equal                                js-yaml                                rc                      walker
deep-extend                               json-schema-traverse                   read                    waterline
deep-is                                   json-stable-stringify-without-jsonify  read-pkg                waterline-schema
depd                                      jsonfile                               read-pkg-up             waterline-utils
destroy                                   klaw                                   readable-stream         whelk
doctrine                                  levn                                   redent                  which
double-ended-queue                        lie                                    redis                   window-size
ee-first                                  load-json-file                         redis-commands          winston
ejs                                       localforage                            redis-parser            wordwrap
encodeurl                                 lodash                                 regexpp                 wrappy
encrypted-attr                            lodash.iserror                         repeating               write
engine.io                                 lodash.isfunction                      reportback              ws
engine.io-client                          lodash.isobject                        require-uncached        xmlhttprequest-ssl
engine.io-parser                          lodash.isregexp                        require_optional        yallist
error-ex                                  lodash.issafeinteger                   resolve                 yargs
es6-promise                               lodash.isstring                        resolve-from            yeast
```

[Previous](01_codebase.md) - [Next](03_configuration.md)
