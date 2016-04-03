<?php
require('header.php');


$accessToken = $VKObject->getAccessToken($_REQUEST['code'], 'https://vkapi.local/callback.php');

$_SESSION['access_token'] = $accessToken['access_token'];
header('Location: /');