<?php

namespace FreedomCore\VK\API;

use FreedomCore\VK\VKBase;
use FreedomCore\VK\VKException;

class VKDocuments extends VKAPI {

    /**
     * API Method for this class
     * @var string
     */
    protected $apiMethod = 'docs.';

    /**
     * VKDocuments constructor.
     * @param VKBase $vkObject
     */
    public function __construct(VKBase $vkObject){
        parent::__construct($vkObject);
    }

    /**
     * Returns detailed information about user or community documents
     * @param int $ownerID
     * @param int $setCount
     * @param int $setOffset
     * @return mixed
     * @throws VKException
     */
    public function get($ownerID, $setCount = 10, $setOffset = 0){
        parent::isAllowed();
        $requestParameters = [
            'owner_id'  =>  $ownerID,
            'offset'    =>  $setOffset,
            'count'     =>  $setCount
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * @param string | array $documentIDorArray
     * @return mixed
     * @throws VKException
     */
    public function getById($documentIDorArray){
        parent::isAllowed();
        $requestParameters = [
            'docs'  =>  (is_array($documentIDorArray)) ? implode(',', $documentIDorArray) : $documentIDorArray
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns the server address for document upload.
     * @param $communityID
     * @return mixed
     */
    public function getUploadServer($communityID){
        parent::isAllowed();
        $requestParameters = [
            'group_id'  =>  $communityID
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }
    

}