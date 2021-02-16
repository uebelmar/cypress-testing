module.exports = function (grunt) {

    grunt.initConfig({

    });


    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-replace');
    grunt.loadNpmTasks('grunt-zip');
    grunt.loadNpmTasks('grunt-regex-replace');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-angular-templates');


    // for kreporter
    var krestConfig = {
        clean:{
            KRESTpackaging: [
                "packaging/KREST/**/*",
                "packaging/KREST/**/.*",
                "packaging/KREST"
            ]
        },
        copy: {
            KRESTpackaging: {
                files: [
                    {
                        src: ['KREST/packaging/manifest.php'],
                        dest: 'packaging/KREST/manifest.php'
                    },{
                        src: ['KREST/packaging/license.txt'],
                        dest: 'packaging/KREST/license.txt'
                    },
                    {
                        src: ['KREST/*.*'],
                        dest: 'packaging/KREST/',
						dot: true
                    },{
                        src: ['KREST/.htaccess'],
                        dest: 'packaging/KREST/',
						dot: true
                    },
                    // { //moved to vendor slim since KREST 3.0.0 & spicecrm 20180400 and introduction of slim 3.9.2
                    //     src: ['KREST/Slim/**/*.*'],
                    //     dest: 'packaging/KREST/'
                    // },
                        {
                         src: ['vendor/slim/**/*.*', 'vendor/composer/**/*.*', 'vendor/container-interop/**/*.*', 'vendor/nikic/**/*.*', 'vendor/pimple/**/*.*', 'vendor/psr/**/*.*', 'vendor/autoload.php'],
                         dest: 'packaging/KREST/vendor/'
                    },{
                        src: ['KREST/extensions/core.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/extensions/login.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/extensions/metadata.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/extensions/module.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/extensions/SpiceCRMMobile.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/extensions/spicefts.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/extensions/user.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/extensions/utils.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/handlers/exceptionClasses.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/handlers/ModuleHandler.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/handlers/spicecrmmobile.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/handlers/user.php'],
                        dest: 'packaging/KREST/'
                    },{
                        src: ['KREST/handlers/utils.php'],
                        dest: 'packaging/KREST/'
                    }
                ]
            }
        },
        'regex-replace': {
            KRESTpackaging: {
                src: ['modules/KReports/**/*.php'],
                actions: [
                    {
                        search: /\?\>/,
                        replace: '',
                        flags: 'g'
                    }
                ]
            }
        },
        zip: {
            KRESTpackaging: {
                cwd: 'packaging/KREST/',
                dest: 'packaging/krest_core_3_0_0.zip',
                src: ['packaging/KREST/**/*', 'packaging/KREST/**/.*']
            }
        }
    };

    grunt.config.merge(krestConfig);
    grunt.registerTask('KRESTpackaging', ['clean:KRESTpackaging', 'copy:KRESTpackaging', 'zip:KRESTpackaging','clean:KRESTpackaging']);


    // for kreporter
    var kreporterConfig = {
        concat: {
            KReportdashlets: {
                src: [
                    'jssource/spice_src_files/modules/KReports/Common/initalization.js',
                    'jssource/spice_src_files/modules/KReports/Dashlets/KReportPresentationDashlet/KReportPresentation_debug.js',
                    'jssource/spice_src_files/modules/KReports/Dashlets/KReportVisualizationDashlet/KReportVisualization_debug.js',
                    'jssource/spice_src_files/modules/KReports/Common/helpers.js',
                    'jssource/spice_src_files/modules/KReports/Dashlets/generic/dashletgeneric_debug.js'
                ],
                dest: 'jssource/spice_src_files/modules/KReports/Dashlets/KReportDashlets_debug.js'
            },
            KReportdashlets7: {
                src: [
                    //'jssource/spice_src_files/modules/KReports/Common/initalization.js',
                    'jssource/spice_src_files/modules/KReports/Dashlets/KReportPresentationDashlet/KReportPresentation7_debug.js',
                    'jssource/spice_src_files/modules/KReports/Dashlets/KReportVisualizationDashlet/KReportVisualization7_debug.js',
                    //'jssource/spice_src_files/modules/KReports/Common/helpers.js',
                ],
                dest: 'jssource/spice_src_files/modules/KReports/KReportDashlets7_debug.js'
            },
            KReportcommon: {
                src: [
                    'jssource/spice_src_files/modules/KReports/Common/base64.js',
                    'jssource/spice_src_files/modules/KReports/Common/initalization.js',
                    'jssource/spice_src_files/modules/KReports/Common/stores.js',
                    'jssource/spice_src_files/modules/KReports/Common/helpers.js',
                ],
                dest: 'jssource/spice_src_files/modules/KReports/KReporterCommon.js'
            },
            KReportgooglemaps: {
                src: [
                    'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/libs/markerclusterer.js',
                    'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/libs/StyledMarker.js',
                    'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/libs/googlemapslegend.js',
                    'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/libs/oms.min.js',
                    'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/libs/googlemapsrouteplanner.js',
                    'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/libs/googlemapscircledesigner.js'
                ],
                dest: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapslibs.js'
            },
            KReportDesginer: {
                src: [
                    //'jssource/spice_src_files/modules/KReports/Common/initalization.js',
                    'jssource/spice_src_files/modules/KReports/Designer/model/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/model/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Designer/store/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/stores.js',
                    'jssource/spice_src_files/modules/KReports/Designer/controllers/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/helpers.js',
                    'jssource/spice_src_files/modules/KReports/Designer/fields/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Designer/windows/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Designer/view/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Designer/Application.js'],
                dest: 'jssource/spice_src_files/modules/KReports/KReporterDesigner.js'
            },
            KReportViewer: {
                src: [
                    //'jssource/spice_src_files/modules/KReports/Common/initalization.js',
                    'jssource/spice_src_files/modules/KReports/Viewer/model/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/model/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Viewer/store/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/stores.js',
                    'jssource/spice_src_files/modules/KReports/Viewer/controllers/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/helpers.js',
                    'jssource/spice_src_files/modules/KReports/Viewer/fields/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Viewer/windows/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Viewer/view/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Viewer/Application.js'],
                dest: 'jssource/spice_src_files/modules/KReports/KReporterViewer.js'
            },
            KReportBucketmanager: {
                src: [
                    //'jssource/spice_src_files/modules/KReports/Common/initalization.js',
                    'jssource/spice_src_files/modules/KReports/Bucketmanager/model/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Bucketmanager/store/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Bucketmanager/controllers/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/helpers.js',
                    'jssource/spice_src_files/modules/KReports/Bucketmanager/fields/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Bucketmanager/windows/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Bucketmanager/view/**/*.js',
                    'jssource/spice_src_files/modules/KReports/Bucketmanager/Application.js'],
                dest: 'jssource/spice_src_files/modules/KReports/KReporterBucketmanager.js'
            },
            KReportDListManager: {
                src: [
                    //'jssource/spice_src_files/modules/KReports/Common/initalization.js',
                    'jssource/spice_src_files/modules/KReports/DListManager/model/**/*.js',
                    'jssource/spice_src_files/modules/KReports/DListManager/store/**/*.js',
                    'jssource/spice_src_files/modules/KReports/DListManager/controllers/**/*.js',
                    //'jssource/spice_src_files/modules/KReports/Common/helpers.js',
                    'jssource/spice_src_files/modules/KReports/DListManager/fields/**/*.js',
                    'jssource/spice_src_files/modules/KReports/DListManager/windows/**/*.js',
                    'jssource/spice_src_files/modules/KReports/DListManager/view/**/*.js',
                    'jssource/spice_src_files/modules/KReports/DListManager/Application.js'],
                dest: 'jssource/spice_src_files/modules/KReports/KReporterDListManager.js'
            },
            KReportCategoriesManager: {
                src: [
                    'jssource/spice_src_files/modules/KReports/CategoriesManager/model/**/*.js',
                    'jssource/spice_src_files/modules/KReports/CategoriesManager/store/**/*.js',
                    'jssource/spice_src_files/modules/KReports/CategoriesManager/controllers/**/*.js',
                    'jssource/spice_src_files/modules/KReports/CategoriesManager/fields/**/*.js',
                    'jssource/spice_src_files/modules/KReports/CategoriesManager/windows/**/*.js',
                    'jssource/spice_src_files/modules/KReports/CategoriesManager/view/**/*.js',
                    'jssource/spice_src_files/modules/KReports/CategoriesManager/Application.js'],
                dest: 'jssource/spice_src_files/modules/KReports/KReporterCategoriesManager.js'
            },
            KReportkpublishing: {
                src: [
                    'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/KReportGenericSubpanel_debug.js',
                    'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/KReportPresentationSubpanel_debug.js',
                    'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/KReportVisualizationSubpanel_debug.js'
                ],
                dest: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/kpublishingview_debug.js'
            },
            KReportamcharts: {
                src: [
                    'vendor/amcharts/serial.js',
                    'vendor/amcharts/pie.js',
                    'vendor/amcharts/funnel.js',
                    'vendor/amcharts/xy.js'
                ],
                dest: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartslibs.js'
            },
            KReportammap: {
                src: [
                    'vendor/ammap/ammap.js',
                    'vendor/ammap/maps/js/worldLow.js'
                ],
                dest: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammaplibs.js'
            },
        },
        jshint: {
            KReportplugins: [
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardview/standardviewpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardview/standardview_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwpreview/standardwpreviewviewpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwpreview/standardviewwpreview_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwsummary/standardwsummaryviewpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwsummary/standardviewwsummary_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/groupedview/groupedviewpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/groupedview/groupedview_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/pivot/pivotpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/pivot/pivot_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/treeview/treeviewpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Presentation/treeview/treeview_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kcsvexport/kcsvexport_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdfexport/kpdfexportpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdfexport/kpdfexport_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kexcelexport/kexcelexport_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldown_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldownview_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/kpublishing_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/kpublishingview_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kscheduling/kscheduling_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksnapshots/ksnapshot_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksnapshots/ksnapshotpanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexport_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_basic/ktargetlistexportmenu_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexportmenu_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/kqueryanalizer/kqueryanalizer_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksavedfilters/ksavedfilters_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksavedfilters/ksavedfiltersview_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts/googlechartspanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts/googlechartsviz_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartspanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartsviz_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartspanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartsviz_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartstools_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapspanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapsviz_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlegeo/googlegeopanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlegeo/googlegeoviz_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartspanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartsviz_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammappanel_debug.js',
                'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammapviz_debug.js'
            ],
            KReportdashlets: [
                'jssource/spice_src_files/modules/KReports/Dashlets/KReportDashlets_debug.js',
                'jssource/spice_src_files/modules/KReports/KReportDashlets7_debug.js',
            ],
            KReportCommon: [
                'jssource/spice_src_files/modules/KReports/KReporterCommon.js'
            ],
            KReportDesginer: [
                'jssource/spice_src_files/modules/KReports/KReporterDesigner.js'
            ],
            KReportViewer: [
                'jssource/spice_src_files/modules/KReports/KReporterViewer.js'
            ],
            KReportBucketmanager: [
                'jssource/spice_src_files/modules/KReports/KReporterBucketmanager.js'
            ],
            KReportDListmanager: [
                'jssource/spice_src_files/modules/KReports/KReporterDListManager.js'
            ],
            KReportCategoriesManager: [
                'jssource/spice_src_files/modules/KReports/KReporterCategoriesManager.js'
            ],
            options: {
                evil: true,
                loopfunc: true
            }
        },
        uglify: {
            options: {
                banner: '/* * *******************************************************************************\r\n' +
                '* This file is part of KReporter. KReporter is an enhancement developed\r\n' +
                '* by aac services k.s.. All rights are (c) 2016 by aac services k.s.\r\n' +
                '*\r\n' +
                '* This Version of the KReporter is licensed software and may only be used in\r\n' +
                '* alignment with the License Agreement received with this Software.\r\n' +
                '* This Software is copyrighted and may not be further distributed without\r\n' +
                '* witten consent of aac services k.s.\r\n' +
                '*\r\n' +
                '* You can contact us at info@kreporter.org\r\n' +
                '******************************************************************************* */\r\n'
            },
            KReportCommon: {
                files: {
                    'modules/KReports/js/KReporterCommon.js': 'jssource/spice_src_files/modules/KReports/KReporterCommon.js'
                }
            },
            KReportDesginer: {
                files: {
                    'modules/KReports/js/KReporterDesigner.js': 'jssource/spice_src_files/modules/KReports/KReporterDesigner.js'
                }
            },
            KReportViewer: {
                files: {
                    'modules/KReports/js/KReporterViewer.js': 'jssource/spice_src_files/modules/KReports/KReporterViewer.js'
                }
            },
            KReportBucketmanager: {
                files: {
                    'modules/KReports/js/KReporterBucketmanager.js': 'jssource/spice_src_files/modules/KReports/KReporterBucketmanager.js'
                }
            },
            KReportDListManager: {
                files: {
                    'modules/KReports/js/KReporterDListManager.js': 'jssource/spice_src_files/modules/KReports/KReporterDListManager.js'
                }
            },
            KReportCategoriesManager: {
                files: {
                    'modules/KReports/js/KReporterCategoriesManager.js': 'jssource/spice_src_files/modules/KReports/KReporterCategoriesManager.js'
                }
            },
            KReportDashlets: {
                files: {
                    'modules/KReports/Dashlets/KReportDashlets.js': 'jssource/spice_src_files/modules/KReports/Dashlets/KReportDashlets_debug.js'
                }
            },
            KReportDashlets7: {
                files: {
                    'modules/KReports/js/KReportDashlets7.js': 'jssource/spice_src_files/modules/KReports/KReportDashlets7_debug.js'
                }
            },
            KReportPlugins: {
                files: {
                    'modules/KReports/Plugins/Visualization/highcharts/highchartspanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartspanel_debug.js',
                    'modules/KReports/Plugins/Visualization/highcharts/highchartsviz.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartsviz_debug.js',
                    'modules/KReports/Plugins/Visualization/highcharts/highchartstools.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartstools_debug.js',
                    'modules/KReports/Plugins/Visualization/googlecharts/googlechartspanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts/googlechartspanel_debug.js',
                    'modules/KReports/Plugins/Visualization/googlecharts/googlechartsviz.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts/googlechartsviz_debug.js',
                    'modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartspanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartspanel_debug.js',
                    'modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartsviz.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartsviz_debug.js',
                    'modules/KReports/Plugins/Visualization/googlemaps/googlemapsviz.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapsviz_debug.js',
                    'modules/KReports/Plugins/Visualization/googlemaps/googlemapspanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapspanel_debug.js',
                    'modules/KReports/Plugins/Visualization/googlemaps/googlemapslibs.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapslibs.js',
                    'modules/KReports/Plugins/Visualization/googlegeo/googlegeoviz.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlegeo/googlegeoviz_debug.js',
                    'modules/KReports/Plugins/Visualization/googlegeo/googlegeopanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlegeo/googlegeopanel_debug.js',
                    'modules/KReports/Plugins/Visualization/amcharts/amchartspanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartspanel_debug.js',
                    'modules/KReports/Plugins/Visualization/amcharts/amchartsviz.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartsviz_debug.js',
                    'modules/KReports/Plugins/Visualization/amcharts/amchartslibs.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartslibs.js',
                    'modules/KReports/Plugins/Visualization/ammap/ammappanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammappanel_debug.js',
                    'modules/KReports/Plugins/Visualization/ammap/ammapviz.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammapviz_debug.js',
                    'modules/KReports/Plugins/Visualization/ammap/ammaplibs.js': 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammaplibs.js',
                    'modules/KReports/Plugins/Presentation/standardview/standardviewpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardview/standardviewpanel_debug.js',
                    'modules/KReports/Plugins/Presentation/standardview/standardview.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardview/standardview_debug.js',
                    'modules/KReports/Plugins/Presentation/standardwpreview/standardwpreviewviewpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwpreview/standardwpreviewviewpanel_debug.js',
                    'modules/KReports/Plugins/Presentation/standardwpreview/standardviewwpreview.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwpreview/standardviewwpreview_debug.js',
                    'modules/KReports/Plugins/Presentation/standardwsummary/standardwsummaryviewpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwsummary/standardwsummaryviewpanel_debug.js',
                    'modules/KReports/Plugins/Presentation/standardwsummary/standardviewwsummary.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwsummary/standardviewwsummary_debug.js',
                    'modules/KReports/Plugins/Presentation/groupedview/groupedviewpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/groupedview/groupedviewpanel_debug.js',
                    'modules/KReports/Plugins/Presentation/groupedview/groupedview.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/groupedview/groupedview_debug.js',
                    'modules/KReports/Plugins/Presentation/pivot/pivotpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/pivot/pivotpanel_debug.js',
                    'modules/KReports/Plugins/Presentation/pivot/pivot.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/pivot/pivot_debug.js',
                    'modules/KReports/Plugins/Presentation/treeview/treeviewpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/treeview/treeviewpanel_debug.js',
                    'modules/KReports/Plugins/Presentation/treeview/treeview.js': 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/treeview/treeview_debug.js',
                    'modules/KReports/Plugins/Integration/kcsvexport/kcsvexport.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kcsvexport/kcsvexport_debug.js',
                    'modules/KReports/Plugins/Integration/kpdfexport/kpdfexportpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdfexport/kpdfexportpanel_debug.js',
                    'modules/KReports/Plugins/Integration/kpdfexport/kpdfexport.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdfexport/kpdfexport_debug.js',
                    'modules/KReports/Plugins/Integration/kexcelexport/kexcelexport.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kexcelexport/kexcelexport_debug.js',
                    'modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldown.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldown_debug.js',
                    'modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldownview.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldownview_debug.js',
                    'modules/KReports/Plugins/Integration/kpublishing/kpublishing.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/kpublishing_debug.js',
                    'modules/KReports/Plugins/Integration/kpublishing/kpublishingview.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/kpublishingview_debug.js',
                    'modules/KReports/Plugins/Integration/kscheduling/kscheduling.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kscheduling/kscheduling_debug.js',
                    'modules/KReports/Plugins/Integration/ksnapshots/ksnapshot.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksnapshots/ksnapshot_debug.js',
                    'modules/KReports/Plugins/Integration/ksnapshots/ksnapshotpanel.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksnapshots/ksnapshotpanel_debug.js',
                    'modules/KReports/Plugins/Integration/ktargetlistexport_basic/ktargetlistexportmenu.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_basic/ktargetlistexportmenu_debug.js',
                    'modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexport.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexport_debug.js',
                    'modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexportmenu.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexportmenu_debug.js',
                    'modules/KReports/Plugins/Integration/kqueryanalizer/kqueryanalizer.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kqueryanalizer/kqueryanalizer_debug.js',
                    'modules/KReports/Plugins/Integration/ksavedfilters/ksavedfilters.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksavedfilters/ksavedfilters_debug.js',
                    'modules/KReports/Plugins/Integration/ksavedfilters/ksavedfiltersview.js': 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksavedfilters/ksavedfiltersview_debug.js'
                }
            }
        },
        clean:{
            KReportpackaging: [
                "modules/KReports/packaging/BasePackage/**/*",
                "modules/KReports/packaging/CorePackage/**/*",
                "modules/KReports/packaging/ExtensionPackage/**/*",
                "modules/KReports/packaging/Sugar7Package/**/*"
            ]
        },
        copy: {
            KReportplugins: {
                files: [
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardview/standardviewpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/standardview/standardviewpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardview/standardview_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/standardview/standardview.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwpreview/standardwpreviewviewpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/standardwpreview/standardwpreviewviewpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwpreview/standardviewwpreview_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/standardwpreview/standardviewwpreview.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwsummary/standardwsummaryviewpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/standardwsummary/standardwsummaryviewpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/standardwsummary/standardviewwsummary_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/standardwsummary/standardviewwsummary.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/groupedview/groupedviewpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/groupedview/groupedviewpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/groupedview/groupedview_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/groupedview/groupedview.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/pivot/pivotpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/pivot/pivotpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/pivot/pivot_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/pivot/pivot.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/treeview/treeviewpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/treeview/treeviewpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Presentation/treeview/treeview_debug.js',
                        dest: 'modules/KReports/Plugins/Presentation/treeview/treeview.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kcsvexport/kcsvexport_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kcsvexport/kcsvexport.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdfexport/kpdfexportpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kpdfexport/kpdfexportpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdfexport/kpdfexport_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kpdfexport/kpdfexport.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kexcelexport/kexcelexport_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kexcelexport/kexcelexport.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldown_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldown.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldownview_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kpdrilldown/kpdrilldownview.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/kpublishing_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kpublishing/kpublishing.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kpublishing/kpublishingview_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kpublishing/kpublishingview.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kscheduling/kscheduling_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kscheduling/kscheduling.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksnapshots/ksnapshot_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/ksnapshots/ksnapshot.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksnapshots/ksnapshotpanel_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/ksnapshots/ksnapshotpanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexport_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexport.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_basic/ktargetlistexportmenu_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/ktargetlistexport_basic/ktargetlistexportmenu.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexportmenu_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/ktargetlistexport_extended/ktargetlistexportmenu.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/kqueryanalizer/kqueryanalizer_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/kqueryanalizer/kqueryanalizer.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksavedfilters/ksavedfilters_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/ksavedfilters/ksavedfilters.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Integration/ksavedfilters/ksavedfiltersview_debug.js',
                        dest: 'modules/KReports/Plugins/Integration/ksavedfilters/ksavedfiltersview.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapsviz_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlemaps/googlemapsviz.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapspanel_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlemaps/googlemapspanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/googlemapslibs.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlemaps/googlemapslibs.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlemaps/images/**/*.*',
                        dest: 'modules/KReports/Plugins/Visualization/googlemaps/images/',
                        expand: true,
                        flatten: true,
                        filter: 'isFile'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlegeo/googlegeoviz_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlegeo/googlegeoviz.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlegeo/googlegeopanel_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlegeo/googlegeopanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts/googlechartsviz_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlecharts/googlechartsviz.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts/googlechartspanel_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlecharts/googlecharts.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartsviz_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartsviz.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/googlecharts_ext/googlechartspanel_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/googlecharts_ext/googlecharts.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartsviz_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/highcharts/highchartsviz.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartspanel_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/highcharts/highchartspanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/highcharts/highchartstools_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/highcharts/highchartstools.js'
                    },
                    {

                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartsviz_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/amcharts/amchartsviz.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartslibs.js',
                        dest: 'modules/KReports/Plugins/Visualization/amcharts/amchartslibs.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/amcharts/amchartspanel_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/amcharts/amchartspanel.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammapviz_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/ammap/ammapviz.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammaplibs.js',
                        dest: 'modules/KReports/Plugins/Visualization/ammap/ammaplibs.js'
                    },
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Plugins/Visualization/ammap/ammappanel_debug.js',
                        dest: 'modules/KReports/Plugins/Visualization/ammap/ammappanel.js'
                    }
                ]
            },
            KReportdashlets: {
                files: [
                    {
                        src: 'jssource/spice_src_files/modules/KReports/Dashlets/KReportDashlets_debug.js',
                        dest: 'modules/KReports/Dashlets/KReportDashlets.js'
                    }
                ]
            },
            KReportdashlets7: {
                files: [
                    {
                        src: 'jssource/spice_src_files/modules/KReports/KReportDashlets7_debug.js',
                        dest: 'modules/KReports/js/KReportDashlets7.js'
                    }
                ]
            },
            KReportmain: {
                files: [
                    {
                        src: 'jssource/spice_src_files/modules/KReports/KReporterDesigner.js', dest: 'modules/KReports/js/KReporterDesigner.js'},
                    {src: 'jssource/spice_src_files/modules/KReports/KReporterViewer.js', dest: 'modules/KReports/js/KReporterViewer.js'},
                    {src: 'jssource/spice_src_files/modules/KReports/KReporterCommon.js', dest: 'modules/KReports/js/KReporterCommon.js'}
                ]
            },
            KReportbasePackage: {
                files: [
                    {
                        src: ['modules/KReports/packaging/manifests/manifest_base.php'],
                        dest: 'modules/KReports/packaging/BasePackage/manifest.php'
                    },
                    {
                        src: ['modules/KReports/packaging/licenses/gpl_license.txt'],
                        dest: 'modules/KReports/packaging/BasePackage/license.txt'
                    },
                    {
                        src: ['vendor/extjs6/ext-all.js'],
                        dest: 'modules/KReports/packaging/BasePackage/'
                    },
                    {
                        src: ['themes/SpiceTheme/extjs6/ext6_override.css'],
                        dest: 'modules/KReports/packaging/BasePackage/'
                    },
                    {
                        src: ['themes/SpiceTheme/extjs6/spicecrm-theme/**/*'],
                        dest: 'modules/KReports/packaging/BasePackage/'
                    },
                    {
                        src: ['KREST/extensions/KReporter.php'],
                        dest: 'modules/KReports/packaging/BasePackage/KREST/KReporter.php'
                    }
                ]
            },
            KReportcorePackage: {
                files: [
                    {
                        src: ['modules/KReports/packaging/manifests/manifest_core.php'],
                        dest: 'modules/KReports/packaging/CorePackage/manifest.php'
                    },
                    {
                        src: ['modules/KReports/packaging/licenses/gpl_license.txt'],
                        dest: 'modules/KReports/packaging/CorePackage/License.txt'
                    },
                    {
                        src: ['modules/KReports/packaging/misc/images/KReports32.png'],
                        dest: 'modules/KReports/packaging/CorePackage/images/KReports32.png',
                        flatten: true
                    },
                    {
                        src: ['modules/KReports/packaging/misc/images/KReports.gif'],
                        dest: 'modules/KReports/packaging/CorePackage/images/KReports.gif',
                    },
                    {
                        src: ['modules/KReports/packaging/misc/Extensions/application/Ext/Include/KReports.AjaxBan.php'],
                        dest: 'modules/KReports/packaging/CorePackage/include/KReports.AjaxBan.php'
                    },
                    {
                        src: ['modules/KReports/packaging/misc/Extensions/application/Ext/Language/en_us.KReporterCore.php'],
                        dest: 'modules/KReports/packaging/CorePackage/language/en_us.KReports.php'
                    },
                    {
                        src: ['modules/KReports/*.php'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/plugins.dictionary'],
                        dest: 'modules/KReports/packaging/CorePackage/modules/KReports/plugins.dictionary'
                    },
                    {
                        src: ['modules/KReports/Menu.basic.php'],
                        dest: 'modules/KReports/packaging/CorePackage/modules/KReports/Menu.php'
                    },
                    {
                        src: ['modules/KReports/config/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/images/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/',
                        flatten: true
                    },
                    {
                        src: ['modules/KReports/js/KReporterDes*.js','modules/KReports/js/KReporterVi*.js', 'modules/KReports/js/KReporterCom*.js'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/language/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/metadata/**/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/tpls/DetailView.tpl','modules/KReports/tpls/EditView.tpl'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/Plugins/prototypes/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/Plugins/Integration/kcsvexport/**/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/Plugins/Integration/ktargetlistexport_basic/**/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/Plugins/Presentation/standardview/**/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    },
                    {
                        src: ['modules/KReports/Plugins/Visualization/googlecharts/**/*.*'],
                        dest: 'modules/KReports/packaging/CorePackage/'
                    }
                ]
            },
            KReportextensionPackage: {
                files: [
                    {
                        src: ['modules/KReports/packaging/manifests/manifest_extension.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/manifest.php'
                    },
                    {
                        src: ['modules/KReports/packaging/licenses/aac_license.txt'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/license.txt'
                    },
                    {
                        src: ['vendor/highcharts/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['vendor/phpexcel/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['vendor/tcpdf6/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['vendor/amcharts/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    //{
                    //    src: ['vendor/ammap/**/*.*'],
                    //    dest: 'modules/KReports/packaging/ExtensionPackage/'
                    //},
                    {
                        src: ['modules/KReports/js/KReporterDL*.js','modules/KReports/js/KReporterBu*.js', 'modules/KReports/js/KReporterCockpit.js', 'modules/KReports/js/KReporterCategoriesManager.js'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['modules/KReports/Dashlets/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['modules/KReports/tpls/BucketmanagerView.tpl','modules/KReports/tpls/CategoriesView.tpl', 'modules/KReports/tpls/DListManagerView.tpl', 'modules/KReports/tpls/CockpitView.tpl'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['modules/KReports/css/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['modules/KReports/views/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['modules/KReports/plugins.dictionary.extended'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/modules/KReports/plugins.dictionary.extended'
                    },
                    {
                        src: ['modules/KReports/Menu.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/modules/KReports/Menu.php'
                    },
                    {
                        src: ['modules/KReports/plugins.dictionary.empty'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/custom/plugins.dictionary'
                    },
                    {
                        src: ['modules/KReports/Plugins/Integration/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['modules/KReports/Plugins/Presentation/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['modules/KReports/Plugins/Visualization/**/*.*'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/'
                    },
                    {
                        src: ['custom/Extension/modules/Schedulers/Ext/ScheduledTasks/kreports.schedulertask.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/Schedulers/kreports.schedulertask.php'
                    },
                    {
                        src: ['custom/Extension/modules/Schedulers/Ext/Language/en_us.kreports.schedulertask.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/language/en_us.kreports.schedulertask.php'
                    },
                    {
                        src: ['custom/metadata/kreport.snapshot.metadata.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/metadata/kreport.snapshot.metadata.php'
                    },
                    {
                        src: ['custom/metadata/kreport.scheduler.metadata.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/metadata/kreport.scheduler.metadata.php'
                    },
                    {
                        src: ['custom/metadata/kreport.bucketmanager.metadata.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/metadata/kreport.bucketmanager.metadata.php'
                    },
                    {
                        src: ['custom/metadata/kreport.dlistmanager.metadata.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/metadata/kreport.dlistmanager.metadata.php'
                    },
                    {
                        src: ['custom/metadata/kreport.savedfilters.metadata.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/metadata/kreport.savedfilters.metadata.php'
                    },
                    {
                        src: ['custom/metadata/kreport.categories.metadata.php'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/metadata/kreport.categories.metadata.php'
                    },
                    {
                        src: ['vendor/angular/angular.min.js'],
                        dest: 'modules/KReports/packaging/ExtensionPackage/vendor/angular/angular.min.js'

                    }
                ]
            },
            KReportsugar7Package: {
                files: [
                    {
                        src: ['modules/KReports/packaging/manifests/manifest_sugar7.php'],
                        dest: 'modules/KReports/packaging/Sugar7Package/manifest.php'
                    },
                    {
                        src: ['modules/KReports/packaging/licenses/aac_license.txt'],
                        dest: 'modules/KReports/packaging/Sugar7Package/license.txt'
                    },
                    {
                        src: ['modules/KReports/clients/**/*.*'],
                        dest: 'modules/KReports/packaging/Sugar7Package/'
                    },
                    {
                        src: ['modules/KReports/js/KReportDash*.js', 'modules/KReports/js/KReporterCom*.js'],
                        dest: 'modules/KReports/packaging/Sugar7Package/'
                    },
                    {
                        src: ['modules/KReports/packaging/misc/JSGroupings/kreporter.php'],
                        dest: 'modules/KReports/packaging/Sugar7Package/custom/kreporter.php'
                    }
                ]
            }
        },
        'regex-replace': {
            KReportphpbanner: {
                src: ['modules/KReports/**/*.php', 'modules/KReports/plugins.dictionary'],
                actions: [
                    {
                        search: /\/\* \*(.|[\r\n])*?\* \*\//,
                        replace: '/* * *******************************************************************************\r\n' +
                        '* This file is part of KReporter. KReporter is an enhancement developed\r\n' +
                        '* by aac services k.s.. All rights are (c) 2016 by aac services k.s.\r\n' +
                        '*\r\n' +
                        '* This Version of the KReporter is licensed software and may only be used in\r\n' +
                        '* alignment with the License Agreement received with this Software.\r\n' +
                        '* This Software is copyrighted and may not be further distributed without\r\n' +
                        '* witten consent of aac services k.s.\r\n' +
                        '*\r\n' +
                        '* You can contact us at info@kreporter.org\r\n' +
                        '******************************************************************************* */',
                        flags: 'g'
                    }
                ]
            },
            KReportjsbanner: {
                src: ['modules/KReports/clients/**/*.js'],
                actions: [
                    {
                        search: /\/\* \*(.|[\r\n])*?\* \*\//,
                        replace: '/* * *******************************************************************************\r\n' +
                        '* This file is part of KReporter. KReporter is an enhancement developed\r\n' +
                        '* by aac services k.s.. All rights are (c) 2016 by aac services k.s.\r\n' +
                        '*\r\n' +
                        '* This Version of the KReporter is licensed software and may only be used in\r\n' +
                        '* alignment with the License Agreement received with this Software.\r\n' +
                        '* This Software is copyrighted and may not be further distributed without\r\n' +
                        '* witten consent of aac services k.s.\r\n' +
                        '*\r\n' +
                        '* You can contact us at info@kreporter.org\r\n' +
                        '******************************************************************************* */',
                        flags: 'g'
                    }
                ]
            },
            KReporttplbanner: {
                src: ['modules/KReports/**/*.tpl'],
                actions: [
                    {
                        search: /\{\* \*(.|[\r\n])*?\* \*\}/,
                        replace: '{* * *******************************************************************************\r\n' +
                        '* This file is part of KReporter. KReporter is an enhancement developed\r\n' +
                        '* by aac services k.s.. All rights are (c) 2016 by aac services k.s.\r\n' +
                        '*\r\n' +
                        '* This Version of the KReporter is licensed software and may only be used in\r\n' +
                        '* alignment with the License Agreement received with this Software.\r\n' +
                        '* This Software is copyrighted and may not be further distributed without\r\n' +
                        '* witten consent of aac services k.s.\r\n' +
                        '*\r\n' +
                        '* You can contact us at info@kreporter.org\r\n' +
                        '******************************************************************************* *}',
                        flags: 'g'
                    }
                ]
            },
            KReportendings: {
                src: ['modules/KReports/**/*.php'],
                actions: [
                    {
                        search: /\?\>/,
                        replace: '',
                        flags: 'g'
                    }
                ]
            }
        },
        zip: {
            KReportbasePackage: {
                cwd: 'modules/KReports/packaging/BasePackage/',
                dest: 'modules/KReports/packaging/KReporter_Base_4_4_0.zip',
                src: ['modules/KReports/packaging/BasePackage/**/*']
            },
            KReportcorePackage: {
                cwd: 'modules/KReports/packaging/CorePackage/',
                dest: 'modules/KReports/packaging/KReporter_Core_4_4_0.zip',
                src: ['modules/KReports/packaging/CorePackage/**/*']
            },
            KReportextensionPackage: {
                cwd: 'modules/KReports/packaging/ExtensionPackage/',
                dest: 'modules/KReports/packaging/KReporter_Extension_4_4_0.zip',
                src: ['modules/KReports/packaging/ExtensionPackage/**/*']
            },
            KReportsugar7Package: {
                cwd: 'modules/KReports/packaging/Sugar7Package/',
                dest: 'modules/KReports/packaging/KReporter_Sugar7_4_4_0.zip',
                src: ['modules/KReports/packaging/Sugar7Package/**/*']
            }
        }
    };

    grunt.config.merge(kreporterConfig);

    grunt.registerTask('KReportdefault', ['concat', 'jshint', 'copy:KReportplugins', 'copy:KReportmain', 'copy:KReportdashlets', 'copy:KReportdashlets7']);
    grunt.registerTask('KReportrelease', ['concat', 'jshint', 'uglify']);
    grunt.registerTask('KReportgooglemaps', ['concat:KReportgooglemaps']);
    grunt.registerTask('KReportamcharts', ['concat:KReportamcharts']);
    grunt.registerTask('KReportammap', ['concat:KReportammap']);
    grunt.registerTask('KReportkpublishing', ['concat:KReportkpublishing']);
    grunt.registerTask('KReportlicenseHeaders', ['regex-replace:KReportphpbanner','regex-replace:KReportjsbanner', 'regex-replace:KReporttplbanner']);
    grunt.registerTask('KReportcleanEndings', ['regex-replace:KReportendings']);
    grunt.registerTask('KReportpackaging', ['clean:KReportpackaging', 'copy:KReportcorePackage','copy:KReportbasePackage','copy:KReportextensionPackage', 'copy:KReportsugar7Package', 'zip', 'clean:KReportpackaging']);

    var ftsConfig = {
        concat: {
            FTSManager: {
                src: [
                    'jssource/spice_src_files/modules/Administration/FullTextSearch/model/**/*.js',
                    'jssource/spice_src_files/modules/Administration/FullTextSearch/store/**/*.js',
                    'jssource/spice_src_files/modules/Administration/FullTextSearch/controllers/**/*.js',
                    'jssource/spice_src_files/modules/Administration/FullTextSearch/fields/**/*.js',
                    'jssource/spice_src_files/modules/Administration/FullTextSearch/windows/**/*.js',
                    'jssource/spice_src_files/modules/Administration/FullTextSearch/view/**/*.js',
                    'jssource/spice_src_files/modules/Administration/FullTextSearch/Application.js'],
                dest: 'jssource/spice_src_files/modules/Administration/FullTextSearch/FTSManager_debug.js'
            }
        },
        jshint: {
            FTSManager: [
                'jssource/spice_src_files/modules/Administration/FullTextSearch/FTSManager_debug.js',
                'jssource/spice_src_files/include/javascript/spicefts.js',
                'jssource/spice_src_files/include/SpiceFTSManager/spiceglobalfts.js'
            ]
        },
        copy: {
            FTSManager: {
                files: [
                    {
                        src: 'jssource/spice_src_files/include/SpiceFTSManager/spiceglobalfts.js',
                        dest: 'include/SpiceFTSManager/js/spiceglobalfts.js'
                    }
                ]
            }
        },
        uglify: {
            /*
            options: {
                banner: '/* * *******************************************************************************\r\n' +
                '* This file is part of SpiceCRM FulltextSearch. SpiceCRM FulltextSearch is an enhancement developed\r\n' +
                '* by aac services k.s.. All rights are (c) 2016 by aac services k.s.\r\n' +
                '*\r\n' +
                '* This Version of the SpiceCRM FulltextSearch is licensed software and may only be used in\r\n' +
                '* alignment with the License Agreement received with this Software.\r\n' +
                '* This Software is copyrighted and may not be further distributed without\r\n' +
                '* witten consent of aac services k.s.\r\n' +
                '*\r\n' +
                '* You can contact us at info@spicecrm.io\r\n' +
                '******************************************************************************* */ // \r\n'
           //  },
            FTSManager: {
                files: {
                    'modules/Administration/javascript/FTSManager.js': 'jssource/spice_src_files/modules/Administration/FullTextSearch/FTSManager_debug.js',
                    'include/javascript/spicefts.js': 'jssource/spice_src_files/include/javascript/spicefts.js',
                    'include/javascript/spiceglobalfts.js': 'jssource/spice_src_files/include/SpiceFTSManager/spiceglobalfts.js'
                }
            }
        }
    };

    grunt.config.merge(ftsConfig);

    grunt.registerTask('FTSDefault', ['concat:FTSManager', 'jshint:FTSManager', 'copy:FTSManager', 'uglify:FTSManager']);

    var tamconfig = {
        concat: {
            TAMOrgManagerCore: {
                src: [
                    'modules/KorgObjects/jsource/CoreConfigurator/model/**/*.js',
                    'modules/KorgObjects/jsource/CoreConfigurator/store/**/*.js',
                    'modules/KorgObjects/jsource/CoreConfigurator/controllers/**/*.js',
                    'modules/KorgObjects/jsource/CoreConfigurator/fields/**/*.js',
                    'modules/KorgObjects/jsource/CoreConfigurator/windows/**/*.js',
                    'modules/KorgObjects/jsource/CoreConfigurator/view/**/*.js',
                    'modules/KorgObjects/jsource/CoreConfigurator/Application.js'],
                dest: 'modules/KorgObjects/jsource/CoreConfigurator/TAMOrgCore.js'
            },
            TAMOrgObjectManager: {
                src: [
                    'modules/KorgObjects/jsource/OrgObjectManager/model/**/*.js',
                    'modules/KorgObjects/jsource/OrgObjectManager/store/**/*.js',
                    'modules/KorgObjects/jsource/OrgObjectManager/controllers/**/*.js',
                    'modules/KorgObjects/jsource/OrgObjectManager/fields/**/*.js',
                    'modules/KorgObjects/jsource/OrgObjectManager/windows/**/*.js',
                    'modules/KorgObjects/jsource/OrgObjectManager/view/**/*.js',
                    'modules/KorgObjects/jsource/OrgObjectManager/Application.js'],
                dest: 'modules/KorgObjects/jsource/OrgObjectManager/TAMOrgObjectManager.js'
            },
            TAMAuthTypeManager: {
                src: [
                    'modules/KAUthProfiles/jsource/AuthTypeManager/model/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthTypeManager/store/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthTypeManager/controllers/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthTypeManager/fields/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthTypeManager/windows/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthTypeManager/view/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthTypeManager/Application.js'],
                dest: 'modules/KAUthProfiles/jsource/AuthTypeManager/TAMAuthTypeManager.js'
            },
            TAMAuthProfileManager: {
                src: [
                    'modules/KAUthProfiles/jsource/AuthProfileManager/model/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthProfileManager/store/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthProfileManager/controllers/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthProfileManager/fields/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthProfileManager/windows/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthProfileManager/view/**/*.js',
                    'modules/KAUthProfiles/jsource/AuthProfileManager/Application.js'],
                dest: 'modules/KAUthProfiles/jsource/AuthProfileManager/TAMAuthProfileManager.js'
            }
        },
        jshint: {
            TAMOrgManagerCore: [
                'modules/KorgObjects/jsource/CoreConfigurator/TAMOrgCore.js',
                'modules/KorgObjects/jsource/OrgObjectManager/TAMOrgObjectManager.js',
                'modules/KAUthProfiles/jsource/AuthTypeManager/TAMAuthTypeManager.js',
                'modules/KAUthProfiles/jsource/AuthProfileManager/TAMAuthProfileManager.js'
            ]
        },
        uglify: {
            options: {
                banner: '/* * *******************************************************************************\r\n' +
                '* This file is part of SpiceCRM FulltextSearch. SpiceCRM FulltextSearch is an enhancement developed\r\n' +
                '* by aac services k.s.. All rights are (c) 2016 by aac services k.s.\r\n' +
                '*\r\n' +
                '* This Version of the SpiceCRM FulltextSearch is licensed software and may only be used in\r\n' +
                '* alignment with the License Agreement received with this Software.\r\n' +
                '* This Software is copyrighted and may not be further distributed without\r\n' +
                '* witten consent of aac services k.s.\r\n' +
                '*\r\n' +
                '* You can contact us at info@spicecrm.io\r\n' +
                '******************************************************************************* */\r\n'
            },
            TAMOrgManagerCore: {
                files: {
                    'modules/KorgObjects/js/TAMOrgCore.js': 'modules/KorgObjects/jsource/CoreConfigurator/TAMOrgCore.js',
                    'modules/KorgObjects/js/TAMOrgObjectManager.js': 'modules/KorgObjects/jsource/OrgObjectManager/TAMOrgObjectManager.js',
                    'modules/KAUthProfiles/js/TAMAuthTypeManager.js': 'modules/KAUthProfiles/jsource/AuthTypeManager/TAMAuthTypeManager.js',
                    'modules/KAUthProfiles/js/TAMAuthProfileManager.js': 'modules/KAUthProfiles/jsource/AuthProfileManager/TAMAuthProfileManager.js'
                }
            }
        },
        copy: {
            TAMOrgManagerCore: {
                files: [
                    {
                        src: 'modules/KorgObjects/jsource/CoreConfigurator/TAMOrgCore.js',
                        dest: 'modules/KorgObjects/js/TAMOrgCore.js'
                    },{
                        src: 'modules/KorgObjects/jsource/OrgObjectManager/TAMOrgObjectManager.js',
                        dest: 'modules/KorgObjects/js/TAMOrgObjectManager.js'
                    },{
                        src: 'modules/KAUthProfiles/jsource/AuthTypeManager/TAMAuthTypeManager.js',
                        dest: 'modules/KAUthProfiles/js/TAMAuthTypeManager.js'
                    },{
                        src: 'modules/KAUthProfiles/jsource/AuthProfileManager/TAMAuthProfileManager.js',
                        dest: 'modules/KAUthProfiles/js/TAMAuthProfileManager.js'
                    }
                ]
            }
        }
    };

    grunt.config.merge(tamconfig);

    grunt.registerTask('TAMDefault', ['concat:TAMOrgManagerCore','concat:TAMOrgObjectManager', 'concat:TAMAuthTypeManager', 'concat:TAMAuthProfileManager', 'jshint:TAMOrgManagerCore', 'copy:TAMOrgManagerCore']);


    var quotaManagerConfig = {
        concat: {
            QuotaManager: {
                src: [
                    'jssource/spice_src_files/modules/Users/QuotaManager/model/**/*.js',
                    'jssource/spice_src_files/modules/Users/QuotaManager/store/**/*.js',
                    'jssource/spice_src_files/modules/Users/QuotaManager/controllers/**/*.js',
                    'jssource/spice_src_files/modules/Users/QuotaManager/fields/**/*.js',
                    'jssource/spice_src_files/modules/Users/QuotaManager/windows/**/*.js',
                    'jssource/spice_src_files/modules/Users/QuotaManager/view/**/*.js',
                    'jssource/spice_src_files/modules/Users/QuotaManager/Application.js'],
                dest: 'jssource/spice_src_files/modules/Users/QuotaManager/QuotaManager_debug.js'
            },
        },
        jshint: {
            QuotaManager: [
                'jssource/spice_src_files/modules/Users/QuotaManager/QuotaManager_debug.js'
            ]
        },
        uglify: {
            QuotaManager: {
                files: {
                    'modules/Users/javascript/QuotaManager.js': 'jssource/spice_src_files/modules/Users/QuotaManager/QuotaManager_debug.js'
                }
            }
        }
    };

    grunt.config.merge(quotaManagerConfig);

    grunt.registerTask('QuotaManager', ['concat:QuotaManager', 'jshint:QuotaManager', 'uglify:QuotaManager']);


    var themeConf = {
        clean: {
            theme:[
                "jssource/spice_src_files/themes/SpiceTheme/js/Widgets.con.js",
                "jssource/spice_src_files/themes/SpiceTheme/js/templates.con.js",
                "jssource/spice_src_files/themes/SpiceTheme/js/complete.js",
                "themes/SpiceTheme/css/app.css",
                "themes/SpiceTheme/css/app.scss",
                "themes/SpiceTheme/css/app.css.map"
            ]
        },
        ngtemplates: {
            themewidgets: {
                src: 'themes/SpiceTheme/js/Widgets/**/*.html',
                dest: 'jssource/spice_src_files/themes/SpiceTheme/js/templates.con.js',
                options: {
                    module: 'SpiceCRM',
                    htmlmin: {collapseWhitespace: true, collapseBooleanAttributes: true}
                }
            }
        },
        concat: {
            themewidgets: {
                src: ['jssource/spice_src_files/themes/SpiceTheme/js/Widgets/**/*.js'],
                dest: 'jssource/spice_src_files/themes/SpiceTheme/js/Widgets.con.js'
            },
            themecomplete: {
                src: ['jssource/spice_src_files/themes/SpiceTheme/js/SpiceCRM.js', 'jssource/spice_src_files/themes/SpiceTheme/js/Widgets.con.js','jssource/spice_src_files/themes/SpiceTheme/js/utils/*.js', 'jssource/spice_src_files/themes/SpiceTheme/js/templates.con.js', 'jssource/spice_src_files/themes/SpiceTheme/js/SpiceTheme_debug.js'],
                dest: 'themes/SpiceTheme/js/complete.js'
            },
            themecss: {
                src: ['themes/SpiceTheme/css/*.css', 'themes/SpiceTheme/css/*.scss','!themes/SpiceTheme/css/app.scss','!themes/SpiceTheme/css/app.css', '!themes/SpiceTheme/css/print.css', '!themes/SpiceTheme/css/yui.css', '!themes/SpiceTheme/css/wizard.css', '!themes/SpiceTheme/css/deprecated.css', '!themes/SpiceTheme/css/chart.css'],
                dest: 'themes/SpiceTheme/css/app.scss'
            }
        },
        uglify: {
            theme: {
                files: {
                    'themes/SpiceTheme/js/complete.js': 'themes/SpiceTheme/js/complete.js'
                }
            }
        },
        sass: {
            theme: {
                files: {
                    'themes/SpiceTheme/css/app.css': 'themes/SpiceTheme/css/app.scss'
                }
            }
        },
        jshint: {
            theme: ['themes/SpiceTheme/js/complete.js']
        }
    };

    grunt.config.merge(themeConf);

    grunt.registerTask('SpiceTheme', ['clean:theme','ngtemplates:themewidgets','concat:themewidgets','concat:themecomplete','concat:themecss','jshint:theme','uglify:theme','sass:theme']);
    grunt.registerTask('SpiceTheme-debug', ['clean:theme','ngtemplates:themewidgets','concat:themewidgets','concat:themecomplete','concat:themecss','jshint:theme','sass:theme']);


//@deprecated since 2018.10.001
//     var deployConfig = {
//         concat: {
//             KLandscapeManager:{
//                 src: [
//                     'jssource/spice_src_files/modules/KDeploymentSystems/LandscapeManager/model/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/LandscapeManager/store/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/LandscapeManager/controllers/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/LandscapeManager/view/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/LandscapeManager/Application.js'],
//                 dest: 'modules/KDeploymentSystems/js/LandscapeManager.js'
//             },
//             KDeploymentManager: {
//                 src: [
//                     'jssource/spice_src_files/modules/KDeploymentSystems/DeploymentManager/model/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/DeploymentManager/store/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/DeploymentManager/controllers/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/DeploymentManager/view/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentSystems/DeploymentManager/Application.js'],
//                 dest: 'modules/KDeploymentSystems/js/DeploymentManager.js'
//             },
//             KChangeRequests: {
//                 src: [
//                     'jssource/spice_src_files/modules/KDeploymentCRs/ChangeRequestManager/model/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentCRs/ChangeRequestManager/store/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentCRs/ChangeRequestManager/controllers/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentCRs/ChangeRequestManager/view/**/*.js',
//                     'jssource/spice_src_files/modules/KDeploymentCRs/ChangeRequestManager/Application.js'],
//                 dest: 'modules/KDeploymentCRs/js/KDeploymentCRs.js'
//             },
//             KReleasePackageManager: {
//                 src: [
//                     'jssource/spice_src_files/modules/KReleasePackages/ReleasePackageManager/model/**/*.js',
//                     'jssource/spice_src_files/modules/KReleasePackages/ReleasePackageManager/store/**/*.js',
//                     'jssource/spice_src_files/modules/KReleasePackages/ReleasePackageManager/controllers/**/*.js',
//                     'jssource/spice_src_files/modules/KReleasePackages/ReleasePackageManager/view/**/*.js',
//                     'jssource/spice_src_files/modules/KReleasePackages/ReleasePackageManager/Application.js'],
//                 dest: 'modules/KReleasePackages/js/KDeploymentRPs.js'
//             }
//         },
//         jshint: {
//             options: {
//                 evil: true,
//                 loopfunc: true
//             },
//             KLandscapeManager: [
//                 'modules/KDeploymentSystems/LandscapeManager/LandscapeManager.js'
//             ],
//             KDeploymentManager: [
//                 'modules/KDeploymentSystems/js/DeploymentManager.js'
//             ],
//             KChangeRequests: [
//                 'modules/KDeploymentCRs/js/KDeploymentCRs.js'
//             ],
//             KReleasePackageManager: [
//                 'modules/KReleasePackages/js/KDeploymentRPs.js'
//             ]
//         },
//         uglify: {
//             KChangeRequests: {
//                 files: {
//                     'modules/KDeploymentCRs/js/KDeploymentCRs.js': 'modules/KDeploymentCRs/js/KDeploymentCRs.js'
//                 }
//             },
//             KLandscapeManager: {
//                 files: {
//                     'modules/KDeploymentSystems/js/LandscapeManager.js': 'modules/KDeploymentSystems/js/LandscapeManager.js'
//                 }
//             },
//             KDeploymentManager: {
//                 files: {
//                     'modules/KDeploymentSystems/js/DeploymentManager.js': 'modules/KDeploymentSystems/js/DeploymentManager.js'
//                 }
//             },
//             KReleasePackageManager: {
//                 files: {
//                     'modules/KReleasePackages/js/KDeploymentRPs.js': 'modules/KReleasePackages/js/KDeploymentRPs.js'
//                 }
//             }
//         }
//     };

    // grunt.config.merge(deployConfig);
    //
    // grunt.registerTask('KLandscapeManager-debug', ['concat:KLandscapeManager','jshint:KLandscapeManager']);
    // grunt.registerTask('KDeploymentManager-debug', ['concat:KDeploymentManager', 'jshint:KDeploymentManager']);
    // grunt.registerTask('KChangeRequests-debug', ['concat:KChangeRequests', 'jshint:KChangeRequests']);
    // grunt.registerTask('KReleasePackageManager-debug', ['concat:KReleasePackageManager', 'jshint:KReleasePackageManager']);
    // grunt.registerTask('KLandscapeManager', ['concat:KLandscapeManager','jshint:KLandscapeManager','uglify:KLandscapeManager']);
    // grunt.registerTask('KDeploymentManager', ['concat:KDeploymentManager', 'jshint:KDeploymentManager','uglify:KDeploymentManager']);
    // grunt.registerTask('KChangeRequests', ['concat:KChangeRequests', 'jshint:KChangeRequests','uglify:KChangeRequests']);
    // grunt.registerTask('KReleasePackageManager', ['concat:KReleasePackageManager', 'jshint:KReleasePackageManager','uglify:KReleasePackageManager']);
};