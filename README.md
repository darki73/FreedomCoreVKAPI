# [PHP] FreedomCore VK API

[![Latest Stable Version](https://img.shields.io/packagist/v/freedomcore/vk-api.svg)](https://packagist.org/packages/freedomcore/vk-api)
[![Total Downloads](https://img.shields.io/packagist/dt/freedomcore/vk-api.svg)](https://packagist.org/packages/freedomcore/vk-api)
[![Downloads Month](https://img.shields.io/packagist/dm/freedomcore/vk-api.svg)](https://packagist.org/packages/freedomcore/vk-api)
[![License](https://img.shields.io/packagist/l/freedomcore/vk-api.svg)](https://github.com/darki73/FreedomCoreVKAPI/blob/master/LICENSE.md)

A PHP VK API Client based on official [VK API Documentation](https://vk.com/dev)

## Table of Contents
- [Introduction](#introduction)
- [Instructions](#instructions)
    - [Require this package with Composer](#require-this-package-with-composer)
    - [Configuration](#configuration)
- [Resources](#resources)
	- [VKBase](#vkbase-resource)
    - [VKAccount](#vkaccount-resource)
    - [VKDataStorage](#vkdatastorage-resource)
    - [VKDocuments](#vkdocuments-resource)
    - [VKGroups](#vkgroups-resource)
    - [VKUsers](#vkusers-resource)
    
    
## Introduction

This is a pure PHP implementation of VK API Client library.
It can be easy extended by adding additional resource classes to API folder.

Pros:
- Can authorize users with specific scope
- Can check for required scope before executing API call
- Works with: Users, Groups, DataStorage, Account and Documents APIs
- Easy to modify
- Easy to use

-----
This code is available on
[Github](https://github.com/darki73/FreedomCoreVKAPI). Pull requests are welcome.

### Require this package with Composer

Install this package through [Composer](https://getcomposer.org/).
Edit your project's `composer.json` file to require
`freedomcore/vk-api`.

Create *composer.json* file
```js
{
    "name": "nameofyourproject/nameofyourproject",
    "type": "project",
    "require": {
        "php": ">=5.6.0",
        "freedomcore/vk-api": "*"
    }
}
```

and run `composer update`

**or**

run this command in your command line:

```
composer require freedomcore/vk-api
```

### Configuration

1. Create New Object  
    a. To work with Library *before* authorization completed  
    ```
    $vkObject = new FreedomCore\VK\VKBase('{APPLICATION_ID}', '{API_SECRET}');
    ```  
    b. To work with Library *after* authorization completed  
    ```  
    $vkObject = new FreedomCore\VK\VKBase('{APPLICATION_ID}', '{API_SECRET}', '{ACCESS_TOKEN}');
    ```
2. How to authorize user  
    a. Generate Authorization Link  
    ```
    $vkObject->getAuthorizationURL('{SCOPE}', '{CALLBACK_URL}');
    ```  
    b. Get Access Token By Using Key From Callback  
    ```
    $vkObject->getAccessToken('{CODE}');
    ```  
    c. Check, if user authorized  
    ```
    $vkObject->isAuthorized(); // returns true | false
    ```  
3. Now you can work with API  

## Resources

### VKBase Resource

This is the main Library resource

There are 4 API Methods presented in this Resource:  
1. ```setAPIVersion({API_VERSION})``` - Set API Version Provided By User  
2. ```getAuthorizationURL({SCOPE}, {CALLBACK_URL}, {RESPONSE_TYPE}, {TEST_MODE})``` - Returns Authorization Link With Passed Parameters  
3. ```getAccessToken({CODE}, {CALLBACK_URL})``` - Returns Access Token From Authorization Link  
4. ```isAuthorized()``` - Returns User Authorization Status  
  
### VKAccount Resource

This Resource allows you to get user related data

***How to use:***

Initialize New *VKAccount* object:  

```php
$VKAccount = new FreedomCore\VK\API\VKAccount($vkObject);
```  
Now you can call methods, e.g.  
```php
$getCounters = $VKAccount->getCounters();
$getApplicationPermissions = $VKAccount->getAppPermissions({USER_ID});
```

There are 6 API Methods presented in this Resource:  
1. ```getCounters({USER_FIELDS optional)``` - Fetches all counters which are greater than zero (messages, videos, friends, etc)  
2. ```setNameInMenu({USER_ID}, {APPLICATION_NAME}``` - Creates Sidebar Link to application, if user allowed it  
3. ```setOnline()``` - Sets status to Online for 15 minutes  
4. ```setOffline()``` - Sets status to Offline  
5. ```getActiveOffers({OFFSET optional}, {COUNT optional})``` - Returns a list of active ads (offers)   
6. ```getAppPermissions()``` - Gets settings of the current user in this application  


### VKDataStorage Resource
*Missing Description*


There are 3 API Methods presented in this Resource:  
1. ```get({KEY}, {KEYS}, {USER_ID}, {IS_GLOBAL})``` - Returns a value of variable with the name set by key parameter  
2. ```set({KEY}, {VALUE}, {USER_ID}, {IS_GLOBAL})``` - Saves a value of variable with the name set by key parameter  
3. ```getKeys({USER_ID}, {IS_GLOBAL}, {OFFSET optional}, {COUNT optional})``` - Returns the names of all variables  

### VKDocuments Resource
***Attention:*** *This Resourse is incomplete | There are 7 more methods to be added*

This Resource allows you to work with documents which are belong to user/group

***How to use:***

Initialize New *VKDocuments* object:  

```php
$VKDocuments = new FreedomCore\VK\API\VKDocuments($vkObject);
```  
Now you can call methods, e.g.  
```php
$isMember = $VKDocuments->get(123456, 100); // returns 100 results
$searchForGroups = $VKDocuments->getById('123456_654321'); // gets document by ID
```

There are 3 API Methods presented in this Resource:  
1. ```get({OWNER_ID}, {COUNT optional}, {OFFSET optional})``` - Returns detailed information about user or community documents  
2. ```getById({DOCUMENT_ID_OR_ARRAY_OF_IDS})``` - Returns information about documents by their IDs  
3. ```getUploadServer({COMMUNITY_ID})``` - Returns the server address for document upload  

### VKGroups Resource

***How to use:***

This Resource allows you to manage groups and work with them

Initialize New *VKGroups* object:  

```php
$VKGroups = new FreedomCore\VK\API\VKGroups($vkObject);
```  
Now you can call methods, e.g.  
```php
$isMember = $VKGroups->isMember(123456, 654321, 1);
$searchForGroups = $VKGroups->search('FreedomCore');
```

There are 19 API Methods presented in this Resource:  
1. ```isMember({GROUP_ID}, {USER_ID}, {IS_EXTENDED optional})``` - Returns information specifying whether a user is a member of a community  
2. ```getById({GROUP_ID}, {GROUP_FIELDS})``` - Returns information about communities by their ID (IDs)  
3. ```get({USER_ID}, {IS_EXTENDED optional}, {FILTER optional}, {GROUP_FIELDS optional})``` - Returns a list of the communities to which a user belongs  
4. ```getMembers({GROUP_ID}, {USERS_FIELDS optional})``` - Returns a list of community members  
5. ```join({GROUP_ID}, {IS_EVENT optional})``` - With this method you can join the group or public page, and also confirm your participation in an event.  
6. ```leave({GROUP_ID})``` - With this method you can leave a group, public page, or event.  
7. ```search({QUERY}, {GROUP_TYPE optional})``` - Searches for communities by substring.  
8. ```getInvites({IS_EXTENDED optional})``` - Returns a list of invitations to join communities and events.  
9. ```getInvitedUsers({GROUP_ID}, {USERS_FIELDS optional})``` - Returns invited users list of a community ***(Requires Moderator Status)***  
10. ```banUser({GROUP_ID}, {USER_ID}, {REASON optional}, {COMMENT optional}, {BAN_ENDS optional}, {SHOW_COMMENT optional})``` - Adds a user to a community blacklist ***(Requires Moderator Status)***  
11. ```unbanUser({GROUP_ID}, {USER_ID})``` - Deletes a user from a community blacklist ***(Requires Moderator Status)***  
12. ```getBanned({GROUP_ID})``` - Returns a list of users on a community blacklist ***(Requires Moderator Status)***  
13. ```create({TITLE}, {DESCRIPTION}, {TYPE optional}, {SUB_TYPE optional})``` - Creates a new community  
14. ```edit({GROUP_ID}, {TITLE}, {DESCRIPTION}, {SCREEN_NAME}, {ACCESS}, {WEBSITE}, {SUBJECT})``` - Edits a community ***(THIS METHOD INCOMPLETE)***  
15. ```getSettings({GROUP_ID})``` - Get Group Settings  
16. ```getRequests({GROUP_ID}, {FIELDS optional}, {COUNT optional})``` - Get Group Access Requests  
17. ```invite({GROUP_ID}, {USER_ID})``` - Invite User To Group ***(Requires Moderator Status)***  
18. ```removeUser({GROUP_ID}, {USER_ID})``` - Remove User From Group ***(Requires Moderator Status)***  
19. ```approveRequest({GROUP_ID}, {USER_ID})``` - Approve User Request To Join Group ***(Requires Moderator Status)***  

### VKUsers Resource

This Resource allows you to work with users and their data

***How to use:***

Initialize New *VKUsers* object:  

```php
$VKUsers= new FreedomCore\VK\API\VKUsers($vkObject);
```  
Now you can call methods, e.g.  
```php
$isMember = $VKUsers->isMember(123456, 654321, 1);
$searchForGroups = $VKUsers->search('FreedomCore');
```

There are 6 API Methods presented in this Resource:  
1. ```get({USER_IDS array}, {FIELDS optional}, {CASE optional})``` - Returns detailed information on users  
2. ```search({QUERY}, {IS_ONLINE optional}, {FIELDS optional}, {SORT optional}, {COUNT optional})``` - Returns a list of users matching the search criteria  
3. ```isAppUser({USER_ID})``` - Returns information whether a user installed the application  
4. ```getSubscriptions({USER_ID}, {COMBINE optional}, {FIELDS optional}, {COUNT optional})``` - Returns a list of IDs of users and communities followed by the user  
5. ```getFollowers({USER_ID}, {OFFSET optional}, {COUNT optional}, {FIELDS optional}, {CASE optional})``` - Returns a list of IDs of followers of the user in question, sorted by date added, most recent first  
6. ```getNearby({LATITUDE}, {LONGITUDE}, {TIMEOUT optional}, {RADIUS optional}, {FIELDS optional}, {CASE optional})``` - Get Nearby Users Based On Current Latitude and Longitude  
