<?php
/***** SPICE-HEADER-SPACEHOLDER *****/

use SpiceCRM\includes\RESTManager;
use SpiceCRM\KREST\controllers\ModuleController;

$RESTManager = RESTManager::getInstance();

/**
 * register the extension
 */
$RESTManager->registerExtension('module', '2.0');

$routes = [
    [
        'method'      => 'post',
        'route'       => '/bean/file/upload',
        'class'       => ModuleController::class,
        'function'    => 'uploadFile',
        'description' => 'Attachment upload',
        'options'     => ['noAuth' => false, 'adminOnly' => false],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}',
        'class'       => ModuleController::class,
        'function'    => 'getBeanList',
        'description' => 'Get bean list',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}',
        'class'       => ModuleController::class,
        'function'    => 'postBean',
        'description' => 'Post bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/export',
        'class'       => ModuleController::class,
        'function'    => 'exportBeanList',
        'description' => 'Export bean list',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/duplicates',
        'class'       => ModuleController::class,
        'function'    => 'checkBeanDuplicates',
        'description' => 'Check bean duplicates',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/{beanName}',
        'class'       => ModuleController::class,
        'function'    => 'deleteBeans',
        'description' => 'Delete beans',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}',
        'class'       => ModuleController::class,
        'function'    => 'getBean',
        'description' => 'Get bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}',
        'class'       => ModuleController::class,
        'function'    => 'addBean',
        'description' => 'Add bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/{beanName}/{beanId}',
        'class'       => ModuleController::class,
        'function'    => 'deleteBean',
        'description' => 'Delete bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/duplicates',
        'class'       => ModuleController::class,
        'function'    => 'getBeanDuplicates',
        'description' => 'Get bean duplicates',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/auditlog',
        'class'       => ModuleController::class,
        'function'    => 'getBeanAuditlog',
        'description' => 'Get bean auditlog',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/noteattachment',
        'class'       => ModuleController::class,
        'function'    => 'getBeanAttachments',
        'description' => 'Get bean attachments',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/noteattachment/download',
        'class'       => ModuleController::class,
        'function'    => 'downloadBeanAttachment',
        'description' => 'Download bean attachment',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/noteattachment',
        'class'       => ModuleController::class,
        'function'    => 'setBeanAttachment',
        'description' => 'Set bean attachment',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/checklist/{fieldname}/{item}',
        'class'       => ModuleController::class,
        'function'    => 'postChecklist',
        'description' => 'Post checklist',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/{beanName}/{beanId}/checklist/{fieldname}/{item}',
        'class'       => ModuleController::class,
        'function'    => 'deleteChecklist',
        'description' => 'Delete checklist',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'get',
        'route'       => '/module/{beanName}/{beanId}/related/{linkname}',
        'class'       => ModuleController::class,
        'function'    => 'getRelatedBean',
        'description' => 'Get related bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/related/{linkname}',
        'class'       => ModuleController::class,
        'function'    => 'addRelatedBean',
        'description' => 'Add related bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'put',
        'route'       => '/module/{beanName}/{beanId}/related/{linkname}',
        'class'       => ModuleController::class,
        'function'    => 'setRelatedBean',
        'description' => 'Set related bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'delete',
        'route'       => '/module/{beanName}/{beanId}/related/{linkname}',
        'class'       => ModuleController::class,
        'function'    => 'deleteRelatedBean',
        'description' => 'Delete related bean',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
    [
        'method'      => 'post',
        'route'       => '/module/{beanName}/{beanId}/merge_bean',
        'class'       => ModuleController::class,
        'function'    => 'mergeBeans',
        'description' => 'Merge beans',
        'options'     => ['noAuth' => false, 'adminOnly' => false, 'moduleRoute' => true],
    ],
];

$RESTManager->registerRoutes($routes);

