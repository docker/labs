/**
 * `tasks/config/watch`
 *
 * ---------------------------------------------------------------
 *
 * Run predefined tasks whenever certain files are added, changed or deleted.
 *
 * For more information, see:
 *   https://sailsjs.com/anatomy/tasks/config/watch.js
 *
 */
module.exports = function(grunt) {

  grunt.config.set('watch', {
    assets: {

      // Assets to watch:
      files: [
        'assets/**/*',
        'tasks/pipeline.js',
        '!**/node_modules/**'
      ],

      // When assets are changed:
      tasks: [
        'syncAssets',
        'linkAssets'
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
  //    $ npm install grunt-contrib-watch --save-dev --save-exact
  //    ```
  //
  //
  // 2. Then uncomment the following code:
  //
  // ```
  // // Load Grunt plugin from the node_modules/ folder.
  // grunt.loadNpmTasks('grunt-contrib-watch');
  // ```
  // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

};
