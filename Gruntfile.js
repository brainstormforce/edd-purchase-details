module.exports = function (grunt) {
    'use strict';
    // Project configuration
    var autoprefixer = require('autoprefixer');
    var flexibility = require('postcss-flexibility');

    var rtlcss_dist_files = [];
    var sass_dist_files = [];
    var postcss_style_src = [];
    var cssmin_css_files = [];
    var uglify_js_files = [{
        expand: true,
        cwd: "assets/js/unminified",
        src: ["*.js"],
        dest: "assets/js/minified",
        ext: '.min.js'
    }];

    var pkgInfo = grunt.file.readJSON('package.json');

    cssmin_css_files.push({ //.css to min.css
        expand: true,
        cwd: "assets/css/unminified",
        src: ["**.css"],
        dest: "assets/css/minified",
        ext: ".min.css",
    });

    grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),

            rtlcss: {
                options: {
                    // rtlcss options
                    config: {
                        preserveComments: true,
                        greedy: true
                    },
                    // generate source maps
                    map: false
                },
                dist: {
                    files: rtlcss_dist_files
                }
            },

            sass: {
                options: {
                    sourcemap: 'none',
                    outputStyle: 'expanded'
                },
                dist: {
                    files: sass_dist_files
                }
            },

            postcss: {
                options: {
                    map: false,
                    processors: [
                        flexibility,
                        autoprefixer({
                            browsers: [
                                'Android >= 2.1',
                                'Chrome >= 21',
                                'Edge >= 12',
                                'Explorer >= 7',
                                'Firefox >= 17',
                                'Opera >= 12.1',
                                'Safari >= 6.0'
                            ],
                            cascade: false
                        })
                    ]
                },
                style: {
                    expand: true,
                    src: postcss_style_src
                }
            },

            uglify: {
                js: {
                    options: {
                        compress: {
                            drop_console: true // <-
                        }
                    },
                    files: uglify_js_files
                }
            },

            cssmin: {
                options: {
                    keepSpecialComments: 0
                },
                css: {
                    files: cssmin_css_files
                }
            },

            copy: {
                main: {
                    options: {
                        mode: true
                    },
                    src: [
                        '**',
                        '*.zip',
                        '!node_modules/**',
                        '!build/**',
                        '!css/sourcemap/**',
                        '!.git/**',
                        '!bin/**',
                        '!.gitlab-ci.yml',
                        '!bin/**',
                        '!tests/**',
                        '!phpunit.xml.dist',
                        '!*.sh',
                        '!*.map',
                        '!Gruntfile.js',
                        '!package.json',
                        '!.gitignore',
                        '!phpunit.xml',
                        '!README.md',
                        '!sass/**',
                        '!codesniffer.ruleset.xml',
                        '!vendor/**',
                        '!admin/bsf-core/vendor/**',
                        '!composer.json',
                        '!composer.lock',
                        '!package-lock.json',
                        '!phpcs.xml.dist',
                    ],
                    dest: 'edd-purchase-details/'
                }
            },

            compress: {
                main: {
                    options: {
                        archive: 'edd-purchase-details-' + pkgInfo.version + '.zip',
                        mode: 'zip'
                    },
                    files: [
                        {
                            src: [
                                './edd-purchase-details/**'
                            ]

                        }
                    ]
                }
            },

            clean: {
                main: ["edd-purchase-details"],
                zip: ["*.zip"]

            },

            makepot: {
                target: {
                    options: {
                        domainPath: '/',
                        potFilename: 'languages/edd-purchase-details.pot',
                        exclude: [
                            'admin/bsf-core',
                        ],
                        potHeaders: {
                            poedit: true,
                            'x-poedit-keywordslist': true
                        },
                        type: 'wp-plugin',
                        updateTimestamp: true
                    }
                }
            },

            addtextdomain: {
                options: {
                    textdomain: 'edd-purchase-details',
                },
                target: {
                    files: {
                        src: [
                            '*.php',
                            '**/*.php',
                            '!node_modules/**',
                            '!php-tests/**',
                            '!bin/**',
                            '!admin/bsf-core/**',
                            '!classes/library/**'
                        ]
                    }
                }
            },

            bumpup: {
                options: {
                    updateProps: {
                        pkg: 'package.json'
                    }
                },
                file: 'package.json'
            },

            replace: {
                plugin_main: {
                    src: ['edd-purchase-details.php'],
                    overwrite: true,
                    replacements: [
                        {
                            from: /Version: \bv?(?:0|[1-9]\d*)\.(?:0|[1-9]\d*)\.(?:0|[1-9]\d*)(?:-[\da-z-A-Z-]+(?:\.[\da-z-A-Z-]+)*)?(?:\+[\da-z-A-Z-]+(?:\.[\da-z-A-Z-]+)*)?\b/g,
                            to: 'Version: <%= pkg.version %>'
                        }
                    ]
                },

                plugin_const: {
                    src: ['edd-purchase-details.php'],
                    overwrite: true,
                    replacements: [
                        {
                            from: /EDD_PD_VER', '.*?'/g,
                            to: 'EDD_PD_VER\', \'<%= pkg.version %>\''
                        }
                    ]
                }
            },
            wp_readme_to_markdown: {
                your_target: {
                    files: {
                        'README.md': 'readme.txt'
                    }
                },
            },

        }
    );

    // Load grunt tasks
    grunt.loadNpmTasks('grunt-rtlcss');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-bumpup');
    grunt.loadNpmTasks('grunt-text-replace');
    grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );

    // rtlcss, you will still need to install ruby and sass on your system manually to run this
    grunt.registerTask('rtl', ['rtlcss']);

    // SASS compile
    grunt.registerTask('scss', ['sass']);

    // Style
    grunt.registerTask('style', ['scss', 'postcss:style', 'rtl']);

    // min all
    grunt.registerTask('minify', ['style', 'uglify:js', 'cssmin:css']);

    // Grunt release - Create installable package of the local files
    grunt.registerTask('release', ['clean:zip', 'copy', 'compress', 'clean:main']);

    // i18n
    grunt.registerTask('i18n', ['addtextdomain', 'makepot']);

    //readme
    grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );
    

    grunt.registerTask('runall', [ 'clean:zip', 'copy', 'compress', 'clean:main','style', 'uglify:js', 'cssmin:css','rtlcss','sass','scss', 'postcss:style', 'rtl','addtextdomain', 'makepot']);

    // Bump Version - `grunt bump-version --ver=<version-number>`
    grunt.registerTask('version-bump', function (ver) {

        var newVersion = grunt.option('ver');

        if (newVersion) {
            newVersion = newVersion ? newVersion : 'patch';

            grunt.task.run('bumpup:' + newVersion);
            grunt.task.run('replace');
        }
    });

    grunt.util.linefeed = '\n';
};