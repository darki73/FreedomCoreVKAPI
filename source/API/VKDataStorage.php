<?php

namespace FreedomCore\VK\API;


use FreedomCore\VK\VKBase;
use FreedomCore\VK\VKException;

/**
 * Class VKDataStorage
 * @package FreedomCore\VK
 */
class VKDataStorage extends VKAPI {

    /**
     * API Method for this class
     * @var string
     */
    protected $apiMethod = 'storage.';

    /**
     * VKDataStorage constructor.
     * @param VKBase $vkObject
     */
    public function __construct(VKBase $vkObject) {
        parent::__construct($vkObject);
    }

    /**
     * Returns a value of variable with the name set by key parameter
     * @param string $storageKey
     * @param string $storageKeys
     * @param int $userID
     * @param int $isGlobal
     * @throws VKException
     * @return array
     */
    protected function get($storageKey, $storageKeys, $userID, $isGlobal) {
        parent::isAllowed();
        $requestParameters = [
            'key'       =>  substr($storageKey, 0, 100),
            'keys'      =>  $storageKeys,
            'user_id'   =>  $userID,
            'global'    =>  ($isGlobal > 1 || $isGlobal < 0) ? 0 : $isGlobal

        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Saves a value of variable with the name set by key parameter
     * @param string $storageKey
     * @param string $keyValue
     * @param int $userID
     * @param int $isGlobal
     * @throws VKException
     * @return array
     */
    protected function set($storageKey, $keyValue, $userID, $isGlobal) {
        parent::isAllowed();
        $requestParameters = [
            'key'       =>  substr($storageKey, 0, 100),
            'value'     =>  $keyValue,
            'user_id'   =>  $userID,
            'global'    =>  ($isGlobal > 1 || $isGlobal < 0) ? 0 : $isGlobal

        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns the names of all variables
     * @param int $userID
     * @param int $isGlobal
     * @param int $setOffset
     * @param int $returnResult
     * @throws VKException
     * @return array
     */
    protected function getKeys($userID, $isGlobal, $setOffset = 0, $returnResult = 100) {
        parent::isAllowed();
        $requestParameters = [
            'user_id'   =>  $userID,
            'global'    =>  ($isGlobal > 1 || $isGlobal < 0) ? 0 : $isGlobal,
            'offset'    =>  $setOffset,
            'count'     =>  ($returnResult > 1000 || $returnResult < 0) ? 100 : $returnResult,

        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

}