/**
 * `tasks/config/uglify`
 *
 * ---------------------------------------------------------------
 *
 * Minify client-side JavaScript files using UglifyES.
 *
 * For more information, see:
 *   https://sailsjs.com/anatomy/tasks/config/uglify.js
 *
 */
module.exports = function(grunt) {

  grunt.config.set('uglify', {
    dist: {
      src: ['.tmp/public/concat/production.js'],
      dest: '.tmp/public/min/production.min.js'
    },
    options: {
      mangle: {
        reserved: [
          'AsyncFunction',
          'SailsSocket',
          'Promise',
          'File',
          'FileList',
          'FormData',
          'Location',
          'RttcRefPlaceholder',
        ],
        keep_fnames: true//eslint-disable-line
      },
      compress: {
        keep_fnames: true//eslint-disable-line
      }
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
  //    $ npm install grunt-contrib-uglify --save-dev --save-exact
  //    ```
  //
  //
  // 2. Then uncomment the following code:
  //
  // ```
  // // Load Grunt plugin from the node_modules/ folder.
  // grunt.loadNpmTasks('grunt-contrib-uglify');
  // ```
  // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

};

