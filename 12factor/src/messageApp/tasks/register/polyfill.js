/**
 * `tasks/register/polyfill.js`
 *
 * ---------------------------------------------------------------
 *
 * For more information see:
 *   https://sailsjs.com/anatomy/tasks/register/polyfill.js
 *
 */
module.exports = function(grunt) {
  grunt.registerTask('polyfill:prod', 'Add the polyfill file to the top of the list of files to concatenate', ()=>{
    grunt.config.set('concat.js.src', [require('sails-hook-grunt/accessible/babel-polyfill')].concat(grunt.config.get('concat.js.src')));
  });
  grunt.registerTask('polyfill:dev', 'Add the polyfill file to the top of the list of files to copy and link', ()=>{
    grunt.config.set('copy.dev.files', grunt.config.get('copy.dev.files').concat({
      expand: true,
      cwd: require('path').dirname(require('sails-hook-grunt/accessible/babel-polyfill')),
      src: require('path').basename(require('sails-hook-grunt/accessible/babel-polyfill')),
      dest: '.tmp/public/polyfill'
    }));
    var devLinkFiles = grunt.config.get('sails-linker.devJs.files');
    grunt.config.set('sails-linker.devJs.files', Object.keys(devLinkFiles).reduce((linkerConfigSoFar, glob)=>{
      linkerConfigSoFar[glob] = ['.tmp/public/polyfill/polyfill.min.js'].concat(devLinkFiles[glob]);
      return linkerConfigSoFar;
    }, {}));
  });
};

