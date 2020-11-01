/**
 * `tasks/config/hash`
 *
 * ---------------------------------------------------------------
 *
 * Implement cache-busting for minified CSS and JavaScript files.
 *
 * For more information, see:
 *   https://sailsjs.com/anatomy/tasks/config/hash.js
 *
 */
module.exports = function(grunt) {

  grunt.config.set('hash', {
    options: {
      mapping: '',
      srcBasePath: '',
      destBasePath: '',
      flatten: false,
      hashLength: 8,
      hashFunction: function(source, encoding){
        if (!source || !encoding) {
          throw new Error('Consistency violation: Cannot compute unique hash for production .css/.js cache-busting suffix, because `source` and/or `encoding` are falsey-- but they should be truthy strings!  Here they are, respectively:\nsource: '+require('util').inspect(source, {depth:null})+'\nencoding: '+require('util').inspect(encoding, {depth:null}));
        }
        return require('crypto').createHash('sha1').update(source, encoding).digest('hex');
      }
    },
    js: {
      src: '.tmp/public/min/*.js',
      dest: '.tmp/public/hash/'
    },
    css: {
      src: '.tmp/public/min/*.css',
      dest: '.tmp/public/hash/'
    }
  });

  // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  // This Grunt plugin is part of the default asset pipeline in Sails,
  // so it's already been automatically loaded for you at this point.
  //
  // Of course, you can always remove this Grunt plugin altogether by
  // deleting this file.  But check this out: you can also use your
  // _own_ custom version of this Grunt plugin.
  //
  // Here's how:
  //
  // 1. Install it as a local dependency of your Sails app:
  //    ```
  //    $ npm install grunt-hash --save-dev --save-exact
  //    ```
  //
  //
  // 2. Then uncomment the following code:
  //
  // ```
  // // Load Grunt plugin from the node_modules/ folder.
  // grunt.loadNpmTasks('grunt-hash');
  // ```
  // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

};
