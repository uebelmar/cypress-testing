/**
 * copy files for core release / more release
 * SPICECRM RELEASE CORE: clean:core release:core
 * SPICECRM RELEASE MORE: clean:more release:more
 *
 * other tasks are subtasks needed for maintasks
 * @type {*|Gulp}
 */

require('./gulpfile_patterns.js');
var gulp = require('gulp');
var concat = require('gulp-concat');
var replace = require('gulp-replace');
var rename = require("gulp-rename");
var releasecore = require('gulp-copy');
var releasemore = require('gulp-copy');
var del = require("del");
var moment = require('moment');
var buildDate = moment(new Date()).format('YYYY-MM-DD');
var zip = require('gulp-zip');

////////////// RELEASE FOLDERS //////////////

var folder_core = 'spicecrm_be_release_core';
var folder_more = 'spicecrm_be_release_more';

////////////// HEADERS //////////////
// spiceHeaderPattern, spiceHeader
// spiceSugarHeaderPattern, spiceSugarHeader
// are now defined as global variables and stored in gulpfile_patterns.js
// so that re-use of thos viariables is possible in other gulpfiles


////////////// FILES //////////////

var excludeFilesCommon = [
    //'!.gitignore',
    // '!.htaccess',
    '!*.bak',
    '!*.log',
    '!*.jar',
    '!*.zip',
    '!*.zjn.*',
    '!composer.json',
    '!composer.lock',
    '!config.php',
    '!config_override.php',
    '!cron.bat',
    '!Gruntfile.js',
    '!gulpfile_patterns.js',
    '!gulpfile_release.js',
    '!gulpfile_upgradecustomer.js',
    '!manifest.php',
    '!package.json',
    '!package-lock.json',
    '!files.md5'
];

var includeFilesCommon = ['*.*','LICENSE', '.gitignore', '.htaccess','.gitignore',
    'data/**/*.*',
    'include/**/*.*',
    'KREST/**/*.*',
    '!KREST/**/*.idea',
    '!KREST/**/*.zip',
    '!KREST/**/dev.php',
    '!KREST/**/logview.php',
    '!KREST/packaging/**',

    'language/**/*.*',
    'metadata/**/*.*',
    'modules/**/*.*',
    'service/**/*.*',
    'soap/**/*.*',
    'vendor/**/*.*'

];


var excludeFilesCore = ['' +
'!include/Alcatel/**',
    '!include/CleverReach/**',
    '!include/DialogMail/**',
    '!include/MailChimp/**',
    '!include/SpiceBCardReader/**',
    '!include/SpiceCRMExchange/**',
    '!include/SpiceCRMGsuite/**',
    '!include/SpiceDuns/**.php',
    '!include/SpiceSocket/**',
    '!include/StarFaceVOIP/**',
    '!include/SugarObjects/implements/kauthmanaged/**/*.*',
    '!include/SugarObjects/implements/spiceaclterritories/**/*.*',
    '!include/VoiceOverIP/**',
    '!metadata/kreport*.php',
    '!metadata/*potentialsMetaData.php',
    '!metadata/knowledgedocuments_knowledgedocumentsMetaData.php',
    '!metadata/priceConditionsMetaData.php',
    '!metadata/sales*.php',
    '!metadata/sapidoc*.php',
    '!metadata/spiceaclterr*.php',
    '!metadata/scrumuserstories_systemdeploymentcrsMetaData.php',
    '!metadata/serviceorders_accountsMetaData.php',
    '!metadata/serviceorders_serviceequipmentsMetaData.php',
    '!metadata/serviceequipmentsMetaData.php',
    '!metadata/sysevalanchelog.metadata.php',
    '!metadata/sysexchange.fieldmapping.metadata.php',
    '!metadata/sysexchange.inboundrecords.metadata.php',
    '!metadata/sysexchange.outboundrecords.metadata.php',
    '!metadata/sysexchange.userconfig.metadata.php',
    '!metadata/sysgroupwarebeansyncqueue.metadata.php',
    '!metadata/systemdeploymentreleases_usersMetaData.php',
    '!metadata/system_shorturls.php',
    '!metadata/starface.metadata.php',
    '!metadata/sysexchange*.php',
    '!metadata/systemdeployment.metadata.php',
    '!metadata/system_exchange_user_config.php',
    '!metadata/voip.metadata.php',
    '!modules/BonusCards/**',
    '!modules/BonusPrograms/**',
    '!modules/ContactCCDetails/**',
    '!modules/ContactsOnlineProfiles/**',
    '!modules/GoogleCalendar/KREST/**',
    '!modules/GoogleCalendar/GoogleCalendar.php',
    '!modules/GoogleCalendar/GoogleCalendarEvent.php',
    '!modules/GoogleCalendar/GoogleCalendarRestHandler.php',
    '!modules/GoogleCalendar/GSuiteUserConfig.php',
    '!modules/GoogleLanguage/**',
    '!modules/GoogleTasks/**',
    '!modules/GoogleOAuth/**',
    '!modules/Inquiries/**',
    '!modules/KAuthProfiles/**',
    '!modules/KAUthProfiles/**',
    '!modules/KorgObjects/**',
    '!modules/KOrgObjects/**',
    '!modules/KnowledgeBooks/**/*',
    '!modules/KnowledgeDocuments/**/*',
    '!modules/KnowledgeDocumentAccessLogs/**/*',
    '!modules/KPortfolios/**',
    '!modules/KReports/**',
    '!modules/LandingPages/**',
    '!modules/Mailboxes/Handlers/A1Handler.php',
    '!modules/Mailboxes/Handlers/Ews*.php',
    '!modules/Mailboxes/Handlers/Gsuite*.php',
    '!modules/Mailboxes/Handlers/GSuite*.php',
    '!modules/Mailboxes/Handlers/MailgunHandler.php',
    '!modules/Mailboxes/Handlers/SendgridHandler.php',
    '!modules/Mailboxes/Handlers/TwillioHandler.php',
    '!modules/Mailboxes/KREST/controllers/EwsController.php',
    '!modules/Mailboxes/KREST/controllers/SendgridController.php',
    '!modules/Mailboxes/KREST/controllers/TwilioController.php',
    '!modules/Mailboxes/KREST/extensions/ews.php',
    '!modules/Mailboxes/KREST/extensions/mailgun.php',
    '!modules/Mailboxes/KREST/extensions/mailgun_webhooks.php',
    '!modules/Mailboxes/KREST/extensions/sendgrid.php',
    '!modules/Mailboxes/KREST/extensions/sendgrid_webhooks.php',
    '!modules/Mailboxes/KREST/extensions/twilio.php',
    '!modules/Mailboxes/KREST/extensions/twilio_webhooks.php',
    '!modules/OrgUnits/**',
    '!modules/Potentials/**',
    '!modules/Price*/**',
    '!modules/Product*/**',
    '!modules/Question*/**',
    '!modules/Travel*/**',
    '!modules/Schedulers/ScheduledTasks/**',
    '!modules/ServiceCal*/**',
    '!modules/ServiceEquipments/**',
    '!modules/ServiceF*/**',
    '!modules/ServiceLocations/**',
    '!modules/ServiceOrder*/**',
    '!modules/ServiceQueues/**',
    '!modules/ServiceCalls/**',
    '!modules/ServiceTickets/KREST/**',
    '!modules/ServiceTickets/mailboxprocessors/**',
    '!modules/ServiceTicketNotes/**',
    '!modules/ServiceTicketProlongations/**',
    '!modules/ServiceTicketSLA*/**',
    '!modules/ServiceTicketStages/**',
    '!modules/Sales*/**',
    '!modules/SAPIdocs/**',
    '!modules/ScrumEpics/**',
    '!modules/ScrumThemes/**',
    '!modules/ScrumUserStories/**',
    '!modules/Skype/**',
    '!modules/SpiceACLTerritories/**',
    '!modules/SpiceUIGenerator/**',
    '!modules/SystemDeploymentC*/**',
    '!modules/SystemDeploymentPackages/KREST/**',
    '!modules/SystemDeploymentPackages/SystemDeploymentPackage.php',
    '!modules/SystemDeploymentPackages/vardefs.php',
    '!modules/SystemDeploymentR*/**',
    '!modules/SystemDeploymentS*/**',
    '!modules/SystemHolidayCalendar*/**',
    '!modules/TestUnits/**',
    '!modules/TextMessage*/**',
    '!modules/TRDocumentation/**',
    '!modules/TRExchangeConnector/**',
    '!modules/UOM*/**',
    '!modules/Workflow*/**',
    '!vendor/amchart/**',
    '!vendor/ammap/**'
];


// var excludeFilesMore = ['!include/SugarObjects/implements/spiceaclterritories/**/*.*',
//
//     '!modules/SpiceUIGenerator/**',
// ];

var kreporterCore =[
    'metadata/kreport.categories.metadata.php',
    'modules/KReports/*.*',
    'modules/KReports/config/**/*.*',
    'modules/KReports/KREST/extensions/KReporter.php',
    'modules/KReports/KREST/controllers/KReportsKRESTController.php',
    'modules/KReports/Plugins/Integration/kcsvexport/**/*.*',
    'modules/KReports/Plugins/Integration/ktargetlistexport_basic/**/*.*',
    'modules/KReports/Plugins/prototypes/**/*.*',
    'modules/KReports/Plugins/Presentation/standardview/**/*.*',
    'modules/KReports/Plugins/Visualization/googlecharts/**/*.*',
    '!modules/KReports/plugins.dictionary.empty',
    '!modules/KReports/plugins.dictionary.extended',
];
var kreporterMore =[
    'modules/KReports/*.*',
    'vendor/amchart/**/*.*',
    'vendor/ammap/**/*.*',
];


////////////// CLEANERS //////////////

gulp.task('clean:core', function () {
    var source = ['../' + folder_core + '/**/*',
        '../' + folder_core + '/.gitignore',
        '../' + folder_core + '/.htaccess',
        '../' + folder_core + '/*.log',
        '../' + folder_core + '/.idea'];
    return del(source, {force: true});
});
gulp.task('clean:more', function () {
    var source = ['../' + folder_more + '/**/*',
        '../' + folder_core + '/.gitignore',
        '../' + folder_more + '/.htaccess',
        '../' + folder_more + '/*.log',
        '../' + folder_more + '/.idea'];
    return del(source, {force: true});
});

////////////// CORE //////////////
gulp.task('bundle:core', function () {
    //del('../' + folder_core + '/**/*');

    var source = [];
    source = source.concat(includeFilesCommon);
    source = source.concat(excludeFilesCommon);
    source = source.concat(excludeFilesCore);

    return gulp.src(source, {base: './'})
        .pipe(replace(spiceSugarHeaderPattern, spiceSugarHeader))
        .pipe(replace(spiceHeaderPattern, spiceHeader))
        .pipe(gulp.dest('../' + folder_core));
});

gulp.task('bundle:kreportercore', function () {
    var source = [];
    source = source.concat(kreporterCore);
    return gulp.src(source, {base: './'})
        .pipe(replace(spiceSugarHeaderPattern, spiceSugarHeader))
        .pipe(replace(spiceHeaderPattern, spiceHeader))
        .pipe(gulp.dest('../' + folder_core));
});

gulp.task('release:core', gulp.series('clean:core', 'bundle:core', 'bundle:kreportercore'), function(done){
    done();
});



////////////// MORE //////////////

gulp.task('bundle:more', function () {
    //del('../' + folder_core + '/**/*');

    var source = [];
    source = source.concat(includeFilesCommon);
    source = source.concat(excludeFilesCommon);
//    source = source.concat(excludeFilesMore);

    return gulp.src(source, {base: './'})
        .pipe(replace(spiceSugarHeaderPattern, spiceSugarHeader))
        .pipe(replace(spiceHeaderPattern, spiceHeader))
        .pipe(gulp.dest('../' + folder_more));
});
gulp.task('bundle:kreportermore', function () {
    var source = [];
    source = source.concat(kreporterMore);
    return gulp.src(source, {base: './'})
        .pipe(replace(spiceSugarHeaderPattern, spiceSugarHeader))
        .pipe(replace(spiceHeaderPattern, spiceHeader))
        .pipe(gulp.dest('../' + folder_more));
});

gulp.task('release:more', gulp.series('clean:more', 'bundle:more','bundle:kreportermore'), function(done){
    done();
});



