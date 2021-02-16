<?php
/***** SPICE-HEADER-SPACEHOLDER *****/
use SpiceCRM\includes\RESTManager;
use SpiceCRM\includes\SpiceAttachments\KREST\controllers\SpiceAttachmentsKRESTController;


/**
 * get a Rest Manager Instance
 */
$RESTManager = RESTManager::getInstance();

/**
 * register the Extension
 */
$RESTManager->registerExtension('spiceattachments', '1.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/attachment',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceSaveAttachmentHash',
        'description' => 'saves an attachment as a hash file',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/attachment',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceGetAttachmentHash',
        'description' => 'get an attachment as a bean hash file',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/attachment/count',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceCountAttachments',
        'description' => 'counts the number off attachments',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/{beanName}/{beanId}/attachment/{attachmentId}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceDeleteAttachment',
        'description' => 'deletes an attachment',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/attachment/ui',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceSaveAttachment',
        'description' => 'saves and uploads an attachment',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/attachment/ui',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceGetBeanAttachment',
        'description' => 'get the attachments from a bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/attachment/{attachmentId}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceGetAttachment',
        'description' => 'get an attachment by id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/attachment/{attachmentId}/download',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceDownloadAttachment',
        'description' => 'downloads an attachment by id',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceAttachments',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'saveAttachment',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceAttachments/module/{beanName}/{beanId}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'getAttachments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceAttachments/module/{beanName}/{beanId}/count',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'getAttachmentsCount',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceAttachments/module/{beanName}/{beanId}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'saveAttachments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceAttachments/module/{beanName}/{beanId}/{attachmentId}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'getAttachment',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceAttachments/module/{beanName}/{beanId}/byfield/{fieldprefix}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'getAttachmentForField',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'delete',
        'route'       => '/spiceAttachments/module/{beanName}/{beanId}/{attachmentId}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'deleteAttachment',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceAttachments/module/{beanName}/{beanId}/clone/{fromBeanName}/{fromBeanId}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'cloneAttachments',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceAttachments/categories/{module}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceGetAttachmentCategory',
        'description' => 'get the attachment categories',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/spiceAttachments/admin',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'getAnalysis',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceAttachments/admin/cleanup',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'cleanErroneous',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'post',
        'route'       => '/spiceAttachments/{id}',
        'class'       => SpiceAttachmentsKRESTController::class,
        'function'    => 'SpiceUpdateAttachmentData',
        'description' => '',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
];

$RESTManager->registerRoutes($routes);


