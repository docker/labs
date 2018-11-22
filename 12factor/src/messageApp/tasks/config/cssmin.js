/**
 * `tasks/config/cssmin`
 *
 * ---------------------------------------------------------------
 *
 * Together with the `concat` task, this is the final step that minifies
 * all CSS files from `assets/styles/` (and potentially your LESS importer
 * file from `assets/styles/importer.less`)
 *
 * For more information, see:
 *   https://sailsjs.com/anatomy/tasks/config/cssmin.js
 *
 */
module.exports = function(grunt) {

  grunt.config.set('cssmin', {
    dist: {
      src: ['.tmp/public/concat/production.css'],
      dest: '.tmp/public/min/production.min.css'
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
  //    $ npm install grunt-contrib-cssmin --save-dev --save-exact
  //    ```
  //
  //
  // 2. Then uncomment the following code:
  //
  // ```
  // // Load Grunt plugin from the node_modules/ folder.
  // grunt.loadNpmTasks('grunt-contrib-cssmin');
  // ```
  // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

};
