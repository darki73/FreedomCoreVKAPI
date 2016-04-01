<?php

namespace FreedomCore\VK\API;

use FreedomCore\VK\VKBase;

class VKGroups extends VKAPI {

    /**
     * API Method for this class
     * @var string
     */
    protected $apiMethod = 'groups.';

    /**
     * Permission Required To Work With This Extension
     * @var int
     */
    protected $requiredPermission = parent::PERMISSION_GROUPS;

    /**
     * Default Fields For Selection
     */
    const defaultFields = [ 'description', 'members_count', 'status', 'contacts' ];

    /**
     * VKGroups constructor.
     * @param VKBase $vkObject
     */
    public function __construct(VKBase $vkObject) {
        parent::__construct($vkObject);
        parent::isAllowed($this->requiredPermission);
    }

    /**
     * Returns information specifying whether a user is a member of a community
     * @param int | string $groupID
     * @param int $userID
     * @param int $isExtended
     * @return mixed
     */
    public function isMember($groupID, $userID, $isExtended = 0) {
        $requestParameters = [
            'group_id'      =>  $groupID,
            'user_id'       =>  $userID,
            'extended'      =>  ($isExtended > 1 || $isExtended < 0) ? 0 : $isExtended
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns information about communities by their ID (IDs)
     * @param int | string $groupID
     * @param array $requestFields
     * @return mixed
     */
    public function getById($groupID, $requestFields = self::defaultFields) {
        $requestParameters = [
            'fields'    => $this->getAllowedFields($requestFields)
        ];

        if (is_array($groupID)) {
            $requestParameters['group_ids'] = implode(',', $groupID);
        } else {
            $requestParameters['group_id'] = $groupID;
        }

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns a list of the communities to which a user belongs
     * @param int $userID
     * @param int $isExtended
     * @param string $setFilter
     * @param array $requestFields
     * @return mixed
     */
    public function get($userID, $isExtended = 0, $setFilter = null, $requestFields = self::defaultFields) {
        $allowedFilterTypes = ['admin', 'editor', 'moder', 'groups', 'publics', 'events'];
        $requestParameters = [
            'user_id'   =>  $userID,
            'extended'  =>  ($isExtended > 1 || $isExtended < 0) ? 0 : $isExtended,
            'fields'    =>  $this->getAllowedFields($requestFields)
        ];
        if ($setFilter != null && in_array($setFilter, $allowedFilterTypes)) { 
            $requestParameters['filter'] = $setFilter; 
        }

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns a list of community members
     * @param int | string $groupID
     * @param array $requestFields
     * @return mixed
     */
    public function getMembers($groupID, $requestFields = VKUsers::standardFields) {
        $requestParameters = [
            'group_id'  =>  $groupID,
            'fields'    =>  $requestFields
        ];

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * With this method you can join the group or public page, and also confirm your participation in an event.
     * @param int $groupID
     * @param null $ifMeeting
     * @return mixed
     */
    public function join($groupID, $ifMeeting = null) {
        $requestParameters['group_id'] = $groupID;
        if ($ifMeeting != null){ 
            if ($ifMeeting == 1 || $ifMeeting == 0) { 
                $requestParameters['not_sure']  =   $ifMeeting; 
            } 
        }

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * With this method you can leave a group, public page, or event.
     * @param int $groupID
     * @return mixed
     */
    public function leave($groupID) {
        $requestParameters['group_id'] = $groupID;

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Searches for communities by substring.
     * @param string $searchQuery
     * @param string $groupType
     * @return mixed
     */
    public function search($searchQuery, $groupType = null) {
        $requestParameters['q'] = $searchQuery;
        if ($groupType != null && in_array($groupType, ['group', 'page', 'event'])) { 
            $requestParameters['type'] = $groupType; 
        }

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns a list of invitations to join communities and events.
     * @param int $isExtended
     * @return mixed
     */
    public function getInvites($isExtended = 0) {
        $requestParameters['extended'] = ($isExtended > 1 || $isExtended < 0) ? 0 : $isExtended;
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns invited users list of a community
     * @param int $groupID
     * @param array $requestFields
     * @return mixed
     */
    public function getInvitedUsers($groupID, $requestFields = VKUsers::standardFields) {
        $requestParameters = [
            'group_id'  => $groupID,
            'fields ' => $requestFields
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Adds a user to a community blacklist
     * @param int $groupID
     * @param int $userID
     * @param int $banReason -  1 — spam 2 — verbal abuse 3 — strong language 4 — irrelevant messages 0 — other (default)
     * @param string $banComment - Comment of ban action
     * @param int $endDateTimeStamp - Unix Time
     * @param int $commentVisible - Show Comment To User (1 - Yes | 0 - No)
     */
    public function banUser($groupID, $userID, $banReason = 0, $banComment = '', $endDateTimeStamp = null, $commentVisible = 1) {
        $requestParameters = [
            'group_id'          =>  $groupID,
            'user_id'           =>  $userID,
            'reason'            =>  ($banReason < 0 || $banReason > 4) ? 0 : $banReason,
            'comment'           =>  $banComment,
            'end_date'          =>  ($endDateTimeStamp == null) ? strtotime(date("Y-m-d", strtotime("+1 week"))) : $endDateTimeStamp,
            'comment_visible'   =>  $commentVisible
        ];

        parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Deletes a user from a community blacklist
     * @param int $groupID
     * @param int $userID
     * @return mixed
     */
    public function unbanUser($groupID, $userID) {
        $requestParameters = [
            'group_id'  => $groupID,
            'user_id'   => $userID
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Returns a list of users on a community blacklist (Requires Moderator Status)
     * @param int $groupID
     * @return mixed
     */
    public function getBanned($groupID) {
        return parent::executeQuery(__FUNCTION__, ['group_id' => $groupID]);
    }

    /**
     * Creates a new community
     * @param string $groupTitle
     * @param string $groupDescription
     * @param string $groupType
     * @param int $subType
     * @return mixed
     */
    public function create($groupTitle, $groupDescription, $groupType = 'group', $subType = null) {
        $allowedGroupTypes = ['group', 'event', 'public'];
        $requestParameters = [
            'title'     =>  $groupTitle,
            'type'      =>  (in_array($groupType, $allowedGroupTypes)) ? $groupType : 'group'
        ];
        if ($groupType != 'public') { 
            $requestParameters['description'] = $groupDescription; 
        }
        if ($subType != null) { 
            $requestParameters['subtype'] = ($subType > 4 || $subType < 1) ? 2 : $subType;
        }

        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Edits a community
     * @param int $groupID
     * @param string $groupTitle
     * @param string $groupDescription
     * @param string $groupScreenName
     * @param int $groupAccess
     * @param string $groupWebSite
     * @param int $groupSubject
     * @return array
     */
    public function edit($groupID, $groupTitle, $groupDescription, $groupScreenName, $groupAccess, $groupWebSite, $groupSubject) {
        $requestParameters = [
            'group_id'      =>  $groupID,
            'title'         =>  $groupTitle,
            'description'   =>  $groupDescription,
            'screen_name'   =>  $groupScreenName,
            'access'        =>  ($groupAccess > 2 || $groupAccess < 0) ? 1 : $groupAccess,
            'website'       =>  $groupWebSite,
            'subject'       =>  ($groupSubject < 1 || $groupSubject > 42) ? 26 : $groupSubject,
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Get Group Settings
     * @param int $groupID
     * @return mixed
     */
    public function getSettings($groupID) {
        return parent::executeQuery(__FUNCTION__, ['group_id' => $groupID]);
    }

    /**
     * Get Group Access Request
     * @param int $groupID
     * @param array $requestFields
     * @param int $setCount
     * @return array
     */
    public function getRequests($groupID, $requestFields = null, $setCount = 20) {
        if ($requestFields == null) {
            $requestFields = VKUsers::standardFields;
        }

        $requestParameters = [
            'group_id'  =>  $groupID,
            'fields'    =>  $requestFields,
            'count'     =>  $setCount
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Invite User To Group
     * @param int $groupID
     * @param int $userID
     * @return mixed
     */
    public function invite($groupID, $userID) {
        $requestParameters = [
            'group_id'  =>  $groupID,
            'user_id'   =>  $userID
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Remove User From Group
     * @param int $groupID
     * @param int $userID
     * @return mixed
     */
    public function removeUser($groupID, $userID) {
        $requestParameters = [
            'group_id'  =>  $groupID,
            'user_id'   =>  $userID
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }

    /**
     * Approve User Request To Join Group
     * @param int $groupID
     * @param int $userID
     * @return mixed
     */
    public function approveRequest($groupID, $userID) {
        $requestParameters = [
            'group_id'  =>  $groupID,
            'user_id'   =>  $userID
        ];
        return parent::executeQuery(__FUNCTION__, $requestParameters);
    }


    /**
     * Get Fields That Allowed To Be Used With Groups API
     * @param $fieldsArray
     * @return string
     */
    private function getAllowedFields($fieldsArray) {
        $groupsFields = [
            'group_id',
            'name',
            'screen_name',
            'is_closed',
            'is_admin',
            'admin_level',
            'is_member',
            'type',
            'photo',
            'photo_medium',
            'photo_big',
            'city',
            'country',
            'place',
            'description',
            'wiki_page',
            'members_count',
            'counters',
            'start_date ',
            'end_date',
            'can_post',
            'can_see_all_posts',
            'activity',
            'status',
            'contacts'
        ];

        foreach($fieldsArray as $fKey => $fValue) {
            if (!in_array($fValue, $groupsFields)) {
                unset($fieldsArray[$fKey]);
            }
        }
        return implode(',', $fieldsArray);
    }
}