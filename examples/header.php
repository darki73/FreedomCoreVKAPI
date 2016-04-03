<?php
require getcwd() . '/vendor/autoload.php';
session_start();

/**
 * Define Directory Separator Short Code if not defined
 */
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * Define Project Folder Short Code if not defined
 */
if(!defined('PROJECT_FOLDER')) define('PROJECT_FOLDER', getcwd());

/**
 * Define Content Folder Short Code if not defined
 */
if(!defined('CONTENT_FOLDER')) define('CONTENT_FOLDER', PROJECT_FOLDER . DS . 'Content' . DS);

$dataArray = [
    'key'       =>  '',
    'secret'    =>  ''
];


if(isset($_SESSION['access_token'])){
    $dataArray['token'] = $_SESSION['access_token'];
    $VKObject = new FreedomCore\VK\VKBase($dataArray['key'], $dataArray['secret'], $dataArray['token']);
    $VKObject->setAPIVersion('5.50');
} else {
    $VKObject = new \FreedomCore\VK\VKBase($dataArray['key'], $dataArray['secret']);
    $VKObject->setAPIVersion('5.50');
}
