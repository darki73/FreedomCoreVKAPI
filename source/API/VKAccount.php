<?php

namespace FreedomCore\VK\API;

use FreedomCore\VK\VKBase;

class VKAccount extends VKAPI {

    /**
     * API Method for this class
     * @var string
     */
    protected $apiMethod = 'account.';

    /**
     * Default Filters To Be Used
     */
    const defaultFilters = [ 'friends', 'messages', 'groups' ];

    /**
     * VKAccount constructor.
     * @param VKBase $vkObject
     */
    public function __construct(VKBase $vkObject) {
        parent::__construct($vkObject);
        parent::isAllowed();
    }

    /**
     * Returns non-null values of user counters
     * @param array $selectedFilters
     * @return array
     */
    public function getCounters($selectedFilters = self::defaultFilters) {
        $requestParameters = [
            'filter'    =>  $this->getAllowedFilters($selectedFilters)
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Sets an application screen name (up to 17 characters), that is shown to the user in the left menu.
     * @param $userID
     * @param $applicationName
     * @return mixed
     */
    public function setNameInMenu($userID, $applicationName) {
        $requestParameters = [
            'user_id'   =>  $userID,
            'name'      =>  $applicationName
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Marks the current user as online for 15 minutes.
     * (This method is available only to standalone-applications.)
     * @return mixed
     */
    public function setOnline() {
        $requestParameters = [
            'voip'  =>  1
        ];
        return $this->executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Marks a current user as Offline.
     * (This method is available only to standalone-applications.)
     * @return mixed
     */
    public function setOffline() {
        return $this->executeQuery(__FUNCTION__, [ ]);
    }

    /**
     * Returns a list of active ads (offers) which executed by the user will bring him/her respective number of votes to his balance in the application.
     * @param int $setOffset
     * @param int $setCount
     * @return array
     */
    public function getActiveOffers($setOffset = 0, $setCount = 100) {
        $requestParameters = [
            'offset'        =>  $setOffset,
            'count'         =>  ($setCount > 100 || $setCount < 0) ? 100 : $setCount
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Gets settings of the current user in this application.
     * @param int $userID
     * @return mixed
     */
    public function getAppPermissions($userID) {
        $requestParameters = [
            'user_id'   =>  $userID
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Get String of Filters Which Are Allowed To Be Used
     * @param $filtersArray
     * @return string
     */
    private function getAllowedFilters($filtersArray) {
        $existingFilters = [
            'friends',
            'messages',
            'photos',
            'videos',
            'notes',
            'gifts',
            'events',
            'groups',
            'sdk'
        ];
        
        foreach ($filtersArray as $fKey => $fValue) { 
            if(!in_array($fValue, $existingFilters)) {
                unset($filtersArray[ $fKey ]); 
            }
        }
        return implode(',', $filtersArray);
    }
}