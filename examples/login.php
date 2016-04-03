<?php
require('header.php');

$AuthURL = $VKObject->getAuthorizationURL('offline,docs,groups,notify,friends,status,notifications', 'https://vkapi.local/callback.php');
echo "<a href='".$AuthURL."'>Login</a>";