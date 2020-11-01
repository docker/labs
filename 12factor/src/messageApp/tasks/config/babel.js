/**
 * `tasks/config/babel`
 *
 * ---------------------------------------------------------------
 *
 * Transpile >=ES6 code for broader browser compatibility.
 *
 * For more information, see:
 *   https://sailsjs.com/anatomy/tasks/config/babel.js
 *
 */
module.exports = function(grunt) {

  grunt.config.set('babel', {
    dist: {
      options: {
        presets: [require('sails-hook-grunt/accessible/babel-preset-env')]
      },
      files: [
        {
          expand: true,
          cwd: '.tmp/public',
          src: ['js/**/*.js'],
          dest: '.tmp/public'
        }
      ]
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
  //    $ npm install grunt-babel --save-dev --save-exact
  //    ```
  //
  //
  // 2. Then uncomment the following code:
  //
  // ```
  // // Load Grunt plugin from the node_modules/ folder.
  // grunt.loadNpmTasks('grunt-babel');
  // ```
  // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

};
