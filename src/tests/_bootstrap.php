<?php
// Here you can initialize variables that will for your tests

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

$basePath = __DIR__ . '/../../../../';
require_once($basePath . '/../vendor/autoload.php');
require_once($basePath . '/../vendor/yiisoft/yii2/Yii.php');
require_once($basePath . '/common/config/bootstrap.php');
