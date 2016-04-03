<?php
require('header.php');

if(isset($_SESSION['access_token'])) {
    if($VKObject->isAuthorized()){
        $VKUsers = new \FreedomCore\VK\API\VKUsers($VKObject);
        $searchUsers = $VKUsers->get(['{ID_ONE}', '{ID_TWO'}, '{ID_N}'])
        echo "<pre>";
        print_r($searchUsers);
        echo "</pre>";
    } else {
       header('Location: /login.php'); 
    }
} else {
    header('Location: /login.php');
}