<?php

namespace FreedomCore\VK\API;

use FreedomCore\VK\VKBase;
use FreedomCore\VK\VKException;

class VKAPI {

    /**
     * VK Object
     * @var VKBase
     */
    protected $VKObject;

    /**
     * API Method for this class
     * @var string
     */
    protected $apiMethod = '';

    const PERMISSION_NOTIFY         = 1;
    const PERMISSION_FRIENDS        = 2;
    const PERMISSION_PHOTOS         = 4;
    const PERMISSION_AUDIO          = 8;
    const PERMISSION_VIDEO          = 16;
    const PERMISSION_DOCS           = 131072;
    const PERMISSION_NOTES          = 2048;
    const PERMISSION_PAGES          = 128;
    const PERMISSION_LMLINK         = 256;
    const PERMISSION_STATUS         = 1024;
    const PERMISSION_OFFERS         = 32;
    const PERMISSION_QUESTIONS      = 64;
    const PERMISSION_WALL           = 8192;
    const PERMISSION_GROUPS         = 262144;
    const PERMISSION_MESSAGES       = 4096;
    const PERMISSION_EMAIL          = 4194304;
    const PERMISSION_NOTIFICATIONS  = 524288;
    const PERMISSION_STATS          = 1048576;
    const PERMISSION_ADDS           = 32768;


    /**
     * VKUsers constructor.
     * @param VKBase $vkObject
     */
    public function __construct(VKBase $vkObject) {
        $this->VKObject = $vkObject;
    }

    /**
     * Is User Authorized To Make an API Request
     * @param int $requiredPermission
     * @return boolean|null
     * @throws VKException
     */
    protected function isAllowed($requiredPermission = null) {
        if ($requiredPermission != null){
            try {
                $isValidPermission = $this->VKObject->getPermissionsMask() & $requiredPermission;
                if (!$isValidPermission) {
                    throw new VKException('Insufficient Permissions Received!', 1);
                }
            } catch (VKException $ex) {
                echo $ex->getMessage();
                die();
            }
        }

        try {
            if (!$this->VKObject->isAuthorized()) {
                throw new VKException('User not Authorized to make this API Request!', 1);
            }
        } catch (VKException $ex) {
            echo $ex->getMessage();
            die();
        }
    }

    /**
     * Execute API Call
     * @param string $apiMethod
     * @param $requestParameters
     * @return mixed
     */
    protected function executeQuery($apiMethod, $requestParameters) {
        return $this->VKObject->apiQuery($this->apiMethod.$apiMethod, $requestParameters);
    }
}