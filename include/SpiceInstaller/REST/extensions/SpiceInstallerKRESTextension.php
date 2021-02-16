<?php
use SpiceCRM\includes\SpiceInstaller\REST\controllers\SpiceInstallerKRESTController;
use Slim\Routing\RouteCollectorProxy;

$app->get('/isysinfo', [new SpiceInstallerKRESTController(), 'getSysInfo']);
$app->group('/spiceinstaller', function (RouteCollectorProxy $group)  use ($app) {
    $group->get('/check', [new SpiceInstallerKRESTController(), 'checkSystem']);
    $group->get('/checkreference', [new SpiceInstallerKRESTController(), 'checkReference']);
    $group->get('/getlanguages', [new SpiceInstallerKRESTController(), 'getLanguages']);
    $group->post('/checkdb', [new SpiceInstallerKRESTController(), 'checkDB']);
    $group->post('/checkfts', [new SpiceInstallerKRESTController(), 'checkFTS']);
    $group->post('/install', [new SpiceInstallerKRESTController(), 'install']);
});
