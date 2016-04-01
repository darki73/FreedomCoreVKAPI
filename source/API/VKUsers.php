<?php

namespace FreedomCore\VK\API;

use FreedomCore\VK\VKBase;
use FreedomCore\VK\VKException;

/**
 * Class VKUsers
 * @package FreedomCore\VK
 */
class VKUsers extends VKAPI {

    /**
     * API Method for this class
     * @var string
     */
    protected $apiMethod = 'users.';

    /**
     * Default Fields For Selection
     */
    const standardFields = [ 'sex', 'online', 'country', 'city', 'bdate' ];

    /**
     * VKUsers constructor.
     * @param VKBase $vkObject
     */
    public function __construct(VKBase $vkObject) {
        parent::__construct($vkObject);
        parent::isAllowed();
    }

    /**
     * Returns detailed information on users
     * @param string[] $usersIDs
     * @param array $requestFields
     * @param string $nameCase
     * @return mixed
     * @throws VKException
     */
    public function get($usersIDs, $requestFields = self::standardFields, $nameCase = 'nom') {
        if (!is_array($usersIDs)) {
            throw new VKException('First Parameters Must Be Represented By Array Of Users IDs', 1);
        }
        if (!is_array($requestFields)) {
            throw new VKException('Second Parameters Must Be Represented By Array Of Fields To Be Requested', 1);
        }

        $requestParameters = [
            'user_ids'      =>  implode(',', $usersIDs),
            'fields'        =>  $this->returnAllowedFields($requestFields),
            'name_case'     =>  $this->returnAllowedNC($nameCase)
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns a list of users matching the search criteria
     * @param string $searchQuery
     * @param int $isOnline
     * @param array $requestFields
     * @param int $sortBy
     * @param int $displayCount
     * @return array
     * @throws VKException
     */
    public function search($searchQuery, $isOnline = 1, $requestFields = self::standardFields, $sortBy = 0, $displayCount = 5) {
        if (!is_array($requestFields)) {
            throw new VKException('Forth Parameters Must Be Represented By Array Of Fields To Be Requested', 1);
        }

        $requestFields = $this->returnAllowedFields($requestFields);
        $sortBy = ($sortBy > 1 || $sortBy < 0) ? 0 : $sortBy;
        $isOnline = ($isOnline > 1 || $isOnline < 0) ? 1 : $isOnline;

        $requestParameters = [
            'q'         =>  $searchQuery,
            'sort'      =>  $sortBy,
            'count'     =>  $displayCount,
            'fields'    =>  $requestFields,
            'online'    =>  $isOnline
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns information whether a user installed the application
     * @param int $userID
     * @return mixed
     * @throws VKException
     */
    public function isAppUser($userID) {
        $requestParameters = [
            'user_id'   =>  $userID
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns a list of IDs of users and communities followed by the user
     * @param int $userID
     * @param int $combineResults
     * @param array $requestFields
     * @param int $resultCount
     * @return mixed
     * @throws VKException
     */
    public function getSubscriptions($userID, $combineResults = 0, $requestFields = self::standardFields, $resultCount = 20) {
        $requestParameters = [
            'user_id'   =>  $userID,
            'extended'  =>  ($combineResults > 1 || $combineResults < 0) ? 0 : $combineResults,
            'count'     =>  $resultCount,
            'fields'    =>  $this->returnAllowedFields($requestFields)
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns a list of IDs of followers of the user in question, sorted by date added, most recent first
     * @param int $userID
     * @param int $setOffset
     * @param int $displayCount
     * @param array $requestFields
     * @param string $nameCase
     * @return mixed
     * @throws VKException
     */
    public function getFollowers($userID, $setOffset = 0, $displayCount = 100, $requestFields = self::standardFields, $nameCase = 'nom') {
        $requestParameters = [
            'user_id'   =>  $userID,
            'offset'    =>  $setOffset,
            'count'     =>  $displayCount,
            'fields'    =>  $this->returnAllowedFields($requestFields),
            'name_case' =>  $this->returnAllowedNC($nameCase)
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Get Nearby Users Based On Current Latitude and Longitude
     * @param float $currentLatitude
     * @param float $currentLongitude
     * @param int $setTimeOut
     * @param int $setRadius
     * @param array $requestFields
     * @param string $nameCase
     * @return mixed
     * @throws VKException
     */
    public function getNearby($currentLatitude, $currentLongitude, $setTimeOut = 7200, $setRadius = 1, $requestFields = self::standardFields, $nameCase = 'nom') {
        $requestParameters = [
            'latitude'  =>  $currentLatitude,
            'longitude' =>  $currentLongitude,
            'timeout'   =>  ($setTimeOut < 0) ? 7200 : $setTimeOut,
            'radius'   =>  ($setRadius < 0 || $setRadius > 4) ? 1 : $setRadius,
            'fields'    =>  $this->returnAllowedFields($requestFields),
            'name_case' =>  $this->returnAllowedNC($nameCase)
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Return fields which are allowed to be used
     * @param $fieldsArray
     * @return string
     */
    private function returnAllowedFields($fieldsArray) {
        $allowedFields = [ 'photo_id', 'verified', 'sex', 'bdate', 'city', 'country', 'home_town', 'has_photo', 'photo_50', 'photo_100', 'photo_200_orig', 'photo_200', 'photo_400_orig', 'photo_max', 'photo_max_orig', 'online', 'lists', 'domain', 'has_mobile', 'contacts', 'site', 'education', 'universities', 'schools', 'status', 'last_seen', 'followers_count', 'common_count', 'occupation', 'nickname', 'relatives', 'relation', 'personal', 'connections', 'exports', 'wall_comments', 'activities', 'interests', 'music', 'movies', 'tv', 'books', 'games', 'about', 'quotes', 'can_post', 'can_see_all_posts', 'can_see_audio', 'can_write_private_message', 'can_send_friend_request', 'is_favorite', 'is_hidden_from_feed', 'timezone', 'screen_name', 'maiden_name', 'crop_photo', 'is_friend', 'friend_status', 'career', 'military', 'blacklisted', 'blacklisted_by_me' ];
        foreach ($fieldsArray as $fKey => $fValue) { 
            if (!in_array($fValue, $allowedFields)) { 
                unset($fieldsArray[ $fKey ]); 
            }
        }

        return implode(',', $fieldsArray);
    }

    /**
     * Return Allowed Name Case
     * @param string $ncValue
     * @return string
     */
    private function returnAllowedNC($ncValue) {
        $allowedNameCases = [ 'nom', 'gen', 'dat', 'acc', 'ins', 'abl' ];
        if (!in_array($ncValue, $allowedNameCases)) {
            $ncValue = 'nom';
        }
        return $ncValue;
    }
}