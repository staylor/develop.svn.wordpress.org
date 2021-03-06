/*global module:false*/
module.exports = function(grunt) {
	var path = require('path');
	var SOURCE_DIR = 'src/';
	var BUILD_DIR = 'build/';

	// Load tasks. 
	require('matchdep').filterDev('grunt-*').forEach( grunt.loadNpmTasks );

	// Project configuration.
	grunt.initConfig({
		clean: {
			all: [BUILD_DIR],
			dynamic: {
				dot: true,
				expand: true,
				cwd: BUILD_DIR,
				src: []
			},
			tinymce: {
				src: [
					'<%= concat.tinymce.dest %>',
					BUILD_DIR + 'wp-includes/js/tinymce/wp-tinymce-schema.min.js',
					BUILD_DIR + 'wp-includes/js/tinymce/mark_loaded.js'
				]
			}
		},
		copy: {
			files: {
				files: [
					{
						dot: true,
						expand: true,
						cwd: SOURCE_DIR,
						src: [
							'**',
							'!**/.{svn,git}/**', // Ignore version control directories.
							'!wp-includes/version.php' // Exclude version.php
						],
						dest: BUILD_DIR
					},
					{
						src: 'wp-config-sample.php',
						dest: BUILD_DIR
					}
				]
			},
			version: {
				options: {
					processContent: function( src, filepath ) {
						return src.replace( /^(\$wp_version.+?)-src';/m, "$1';" );
					}
				},
				files: [
					{
						src: SOURCE_DIR + 'wp-includes/version.php',
						dest: BUILD_DIR + 'wp-includes/version.php'
					}
				]
			},
			dynamic: {
				dot: true,
				expand: true,
				cwd: SOURCE_DIR,
				dest: BUILD_DIR,
				src: []
			}
		},
		cssmin: {
			core: {
				expand: true,
				cwd: SOURCE_DIR,
				dest: BUILD_DIR,
				ext: '.min.css',
				src: [
					'wp-admin/css/*.css',
					'wp-includes/css/*.css',
					// Exceptions
					'!wp-admin/css/farbtastic.css'
				]
			}
		},
		qunit: {
			files: ['tests/qunit/**/*.html']
		},
		uglify: {
			core: {
				expand: true,
				cwd: SOURCE_DIR,
				dest: BUILD_DIR,
				ext: '.min.js',
				src: [
					'wp-admin/js/*.js',
					'wp-includes/js/*.js',
					'wp-includes/js/plupload/handlers.js',
					'wp-includes/js/plupload/wp-plupload.js',
					'wp-includes/js/tinymce/plugins/wp*/js/*.js',
					'wp-includes/js/tinymce/wp-tinymce-schema.js',
					// Exceptions
					'!wp-admin/js/custom-header.js', // Why? We should minify this.
					'!wp-admin/js/farbtastic.js',
					'!wp-admin/js/iris.min.js',
					'!wp-includes/js/backbone.min.js',
					'!wp-includes/js/swfobject.js',
					'!wp-includes/js/underscore.min.js',
					'!wp-includes/js/zxcvbn.min.js',
					// Hard-coded in editimage.html
					'!wp-includes/js/tinymce/plugins/wpeditimage/js/editimage.js'
				]
			},
			tinymce: {
				expand: true,
				cwd: SOURCE_DIR,
				dest: BUILD_DIR,
				src: [
					'wp-includes/js/tinymce/plugins/wordpress/editor_plugin_src.js',
					'wp-includes/js/tinymce/plugins/wp*/editor_plugin_src.js',
					'wp-includes/js/tinymce/mark_loaded_src.js'
				],
				// TinyMCE plugins use a nonstandard naming scheme: plugin files are named
				// `editor_plugin_src.js`, and are compressed into `editor_plugin.js`.
				rename: function(destBase, destPath) {
					destPath = destPath.replace(/_src.js$/, '.js');
					return path.join(destBase || '', destPath);
				}
			}
		},
		concat: {
			tinymce: {
				options: {
					separator: '\n',
					process: function( src, filepath ) {
						return '// Source: ' + filepath.replace( BUILD_DIR, '' ) + '\n' + src;
					}
				},
				src: [
					BUILD_DIR + 'wp-includes/js/tinymce/tiny_mce.js',
					BUILD_DIR + 'wp-includes/js/tinymce/wp-tinymce-schema.min.js',
					BUILD_DIR + 'wp-includes/js/tinymce/themes/advanced/editor_template.js',
					BUILD_DIR + 'wp-includes/js/tinymce/plugins/*/editor_plugin.js',
					BUILD_DIR + 'wp-includes/js/tinymce/mark_loaded.js'
				],
				dest: BUILD_DIR + 'wp-includes/js/tinymce/wp-tinymce.js'
			}
		},
		compress: {
			tinymce: {
				options: {
					mode: 'gzip',
					level: 9
				},
				src: '<%= concat.tinymce.dest %>',
				dest: BUILD_DIR + 'wp-includes/js/tinymce/wp-tinymce.js.gz'
			}
		},
		watch: {
			all: {
				files: [
					SOURCE_DIR + '**',
					// Ignore version control directories.
					'!' + SOURCE_DIR + '**/.{svn,git}/**'
				],
				tasks: ['clean:dynamic', 'copy:dynamic'],
				options: {
					dot: true,
					spawn: false,
					interval: 2000
				}
			},
			test: {
				files: ['tests/qunit/**'],
				tasks: ['qunit']
			}
		}
	});

	// Register tasks.

	// Copy task.
	grunt.registerTask('copy:all', ['copy:files', 'copy:version']);
	
	// Build task.
	grunt.registerTask('build', ['clean:all', 'copy:all', 'cssmin:core', 'uglify:core',
		'uglify:tinymce', 'concat:tinymce', 'compress:tinymce', 'clean:tinymce']);

	// Testing task.
	grunt.registerTask('test', ['qunit']);

	// Default task.
	grunt.registerTask('default', ['build']);

	// Add a listener to the watch task.
	//
	// On `watch:all`, automatically updates the `copy:dynamic` and `clean:dynamic`
	// configurations so that only the changed files are updated.
	grunt.event.on('watch', function( action, filepath, target ) {
		if ( target != 'all' )
			return;

		var relativePath = path.relative( SOURCE_DIR, filepath );
		var cleanSrc = ( action == 'deleted' ) ? [relativePath] : [];
		var copySrc = ( action == 'deleted' ) ? [] : [relativePath];

		grunt.config(['clean', 'dynamic', 'src'], cleanSrc);
		grunt.config(['copy', 'dynamic', 'src'], copySrc);
	});
};
