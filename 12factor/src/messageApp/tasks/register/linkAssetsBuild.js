/**
 * `tasks/register/linkAssetsBuild.js`
 *
 * ---------------------------------------------------------------
 *
 * For more information see:
 *   https://sailsjs.com/anatomy/tasks/register/link-assets-build.js
 *
 */
module.exports = function(grunt) {
  grunt.registerTask('linkAssetsBuild', [
    'sails-linker:devJsBuild',
    'sails-linker:devStylesBuild',
    'sails-linker:clientSideTemplatesBuild'
  ]);
};
