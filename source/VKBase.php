<?php

namespace FreedomCore\VK;

use FreedomCore\VK\API\VKAccount;
use FreedomCore\VK\API\VKUsers;

class VKBase {

    /**
     * Application ID
     * @var string
     */
    private $applicationID;

    /**
     * Application Secret Key
     * @var string
     */
    private $APISecret;

    /**
     * API Version. If null, uses latest available version
     * @var string
     */
    private $APIVersion;

    /**
     * Acess Token String
     * @var string
     */
    private $accessToken;

    /**
     * Authorization Status
     * @var bool
     */
    private $authorizationStatus = false;

    /**
     * CURL Object
     * @var Resource
     */
    private $curlObject;

    /**
     * Users Access Permissions
     * @var int
     */
    private $permissionsMask = 0;

    /**
     * Determine if permissions mask is set
     * @var bool
     */
    private $isPermissionsMaskSet = false;

    /**
     * URL For Authorization
     */
    const AUTHORIZATION_URL = 'https://oauth.vk.com/authorize';

    /**
     * URL For Access Token
     */
    const ACCESS_TOKEN_URL = 'https://oauth.vk.com/access_token';

    /**
     * URL For VK API
     */
    const METHOD_URL = 'https://api.vk.com/method/';

    /**
     * URL For OAuth Token
     */
    const TOKEN_URL = 'https://oauth.vk.com/token';

    const DEFAULT_CALLBACK = 'https://api.vk.com/blank.html';

    const PACKAGE_VERSION = '1.0.0';

    /**
     * VK constructor.
     * @param string $appID
     * @param string $apiSecret
     * @param string $accessToken
     * @throws VKException
     */
    public function __construct($appID, $apiSecret, $accessToken = null){
        $this->applicationID = $appID;
        $this->APISecret = $apiSecret;
        $this->accessToken = $accessToken;
        $this->curlObject = curl_init();
        if(!is_null($accessToken)){
            if(!$this->isPermissionsMaskSet){
                $VKUser = new VKUsers($this);
                $VKAccount = new VKAccount($this);
                $CurrentUser = $VKUser->get([''])['response'][0]['uid'];
                $this->setPermissionsMask($VKAccount->getAppPermissions($CurrentUser)['response']);
                $this->isPermissionsMaskSet = true;
                unset($VKUser);
                unset($VKAccount);
            }
        }
    }

    /**
     * VK destructor.
     */
    public function __destruct() {
        curl_close($this->curlObject);
    }

    /**
     * Set API Version Provided By User
     * @param int $apiVersion
     */
    public function setAPIVersion($apiVersion){
        $this->APIVersion = $apiVersion;
    }

    /**
     * Returns Base API URL
     * @param string $apiMethod
     * @param string $responseFormat
     * @return string
     */
    public function getAPIUrl($apiMethod, $responseFormat = 'json'){
        return self::METHOD_URL . $apiMethod . '.' . $responseFormat;
    }

    /**
     * Returns Authorization Link With Passed Parameters
     * @param string $apiSettings
     * @param string $callbackURL
     * @param string $responseType
     * @param bool $testMode
     * @return string
     */
    public function getAuthorizationURL($apiSettings = '', $callbackURL = self::DEFAULT_CALLBACK, $responseType = 'code', $testMode = false){
        $allowedTypes = ['token', 'code'];
        $requestParameters = [
            'client_id'     =>  $this->applicationID,
            'scope'         =>  $apiSettings,
            'redirect_uri'  =>  $callbackURL,
            'response_type' =>  (in_array($responseType, $allowedTypes)) ? $responseType : 'code'
        ];

        if($testMode) $requestParameters['test_mode'] = 1;

        return $this->createURL(self::AUTHORIZATION_URL, $requestParameters);
    }

    /**
     * Returns Access Token From Authorization Link
     * @param $resultCode
     * @param string $callbackURL
     * @return mixed
     * @throws VKException
     */
    public function getAccessToken($resultCode, $callbackURL = self::DEFAULT_CALLBACK){
        if(!is_null($this->accessToken) && $this->authorizationStatus) {
            throw new VKException('Already Authorized!', 1);
        }

        $requestParameters = [
            'client_id'     => $this->applicationID,
            'client_secret' =>  $this->APISecret,
            'redirect_uri'  =>  $callbackURL,
            'code'          =>  $resultCode
        ];

        $apiResponse = json_decode($this->performRequest($this->createURL(self::ACCESS_TOKEN_URL, $requestParameters)), true);

        try {
            if(isset($apiResponse['error'])) {
                throw new VKException($apiResponse['error'] . (!isset($apiResponse['error_description']) ?: ': ' . $apiResponse['error_description']), '0');
            } else {
                $this->authorizationStatus = true;
                $this->accessToken = $apiResponse['access_token'];
                return $apiResponse;
            }
        } catch (VKException $ex){
            echo $ex->getMessage();
            return [];
        }
    }

    /**
     * Returns User Authorization Status
     * @return bool
     */
    public function isAuthorized(){
        return !is_null($this->accessToken);
    }


    /**
     * Execute API Method With Parameters and return Result
     * @param string $apiMethod
     * @param array $requestParameters
     * @param string $resultType
     * @param string $requestMethod
     * @return mixed
     */
    public function apiQuery($apiMethod, $requestParameters = [], $resultType = 'array', $requestMethod = 'get'){
        $requestParameters['timestamp'] = time();
        $requestParameters['api_id']    = $this->applicationID;
        $requestParameters['random']    = rand(0, 10000);

        if(!array_key_exists('access_token', $requestParameters) && !is_null($this->accessToken)) {
            $requestParameters['access_token'] = $this->accessToken;
        }

        if(!array_key_exists('v', $requestParameters) && !is_null($this->APIVersion)) {
            $requestParameters['v'] = $this->APIVersion;
        }

        ksort($requestParameters);

        $parametersSignature = '';
        foreach($requestParameters as $pKey=>$pValue){
            if(is_array($pValue))
                $pValue = implode(', ', $pValue);
            $parametersSignature .= $pKey . '=' . $pValue;
        }
        $parametersSignature .= $this->APISecret;

        $requestParameters['sig'] = md5($parametersSignature);

        if($apiMethod == 'execute' || $requestMethod == 'post'){
            $apiResponse = $this->performRequest($this->getAPIUrl($apiMethod, $resultType == 'array' ? 'json' : $resultType), "POST", $requestParameters);
        } else {
            $apiResponse = $this->performRequest($this->createURL($this->getAPIUrl($apiMethod, $resultType == 'array' ? 'json' : $resultType), $requestParameters));
        }

        try {
            $decodedJSON = json_decode($apiResponse, true);
            if(isset($decodedJSON['error']))
                throw new VKException($decodedJSON['error']['error_msg'], $decodedJSON['error']['error_code'], $decodedJSON['error']['request_params']);

            return $resultType == 'array' ? $decodedJSON : $apiResponse;
        } catch(VKException $ex){
            echo $ex->getMessage();
            return [];
        }
    }

    /**
     * Set Permissions Mask
     * @param int $permMask
     */
    public function setPermissionsMask($permMask){
        $this->permissionsMask = $permMask;
    }

    /**
     * Get Permissions Mask
     * @return int
     */
    public function getPermissionsMask(){
        return $this->permissionsMask;
    }

    /**
     * Concatenate Keys And Values Of Parameters Array And Return URL String
     * @param string $urlString
     * @param array $parametersArray
     * @return string
     */
    private function createURL($urlString, $parametersArray){
        $urlString .= '?' . http_build_query($parametersArray);
        return $urlString;
    }

    /**
     * Execute Request
     * @param string $requestURL
     * @param string $requestMethod
     * @param array $postFields
     * @return string
     */
    private function performRequest($requestURL, $requestMethod = 'GET', $postFields = []){
        curl_setopt_array($this->curlObject, [
            CURLOPT_USERAGENT       =>  'FreedomCore/' . self::PACKAGE_VERSION . ' VK OAuth Client',
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_POST            => ($requestMethod == 'POST'),
            CURLOPT_POSTFIELDS      => $postFields,
            CURLOPT_URL             => $requestURL
        ]);

        return curl_exec($this->curlObject);
    }

}

/**
 * Class VKException
 * @package FreedomCore\VK
 */
class VKException extends \Exception {

    /**
     * VKException constructor.
     * @param string $message
     * @param int $code
     * @param null $parameters
     */
    public function __construct($message, $code, $parameters = null) {
        $Message = '<h1>API Request Error!</h1>';
        $Message .= '<table width="100%">';

        $Message .= '<tr><td width="10%"><strong>Error Code:</strong></td> <td>'.$code.'</td></tr>';
        $APIError = $this->codeToErrorText($code);
        $Message .= '<tr><td width="10%"><strong>API Message:</strong></td> <td>'.$APIError['title'].' <span style="color: gray;">('.$APIError['description'].')</span></td></tr>';
        $Message .= '<tr><td width="10%"><strong>Error Message:</strong></td> <td>'.$message.'</td></tr>';
        if($parameters != null && is_array($parameters)){
            $Message .= '<tr><td width="10%"><strong>Request Parameters:</strong></td> <td>';

            $Message .= '<table width="15%">';
            foreach($parameters as $parameter){
                $Message .= "<tr><td><strong>".$parameter['key']."</strong></td><td width='15%'>=></td><td>".$parameter['value']."</td></tr>";
            }
            $Message .= '</table>';
            $Message .= '</td></tr>';
        }

        $Message .= '</table>';
        parent::__construct($Message, $code);
    }

    /**
     * Convert INT Code To Full Description
     * @param $Code
     * @return mixed
     */
    private function codeToErrorText($Code){
        $errorsData = [
            1       =>  ['title' => 'Unknown error occurred',  'description' => 'Try again later.'],
            2       =>  ['title' => 'Application is disabled. Enable your application or use test mode ',  'description' => 'You need to switch on the app in Settings (https://vk.com/editapp?id={Your API_ID} or use the test mode (test_mode=1).'],
            3       =>  ['title' => 'Unknown method passed ',  'description' => 'Check the method name: <a href="http://vk.com/dev/methods" target="_blank">http://vk.com/dev/methods</a> '],
            4       =>  ['title' => 'Incorrect signature ',  'description' => 'Check if the signature has been formed correctly: <a href="https://vk.com/dev/api_nohttps" target="_blank"></a>'],
            5       =>  ['title' => 'User authorization failed ',  'description' => 'Make sure that you use a correct authorization type. To work with the methods without a secureprefix you need to authorize a user with one of these ways:  http://vk.com/dev/auth_sites, http://vk.com/dev/auth_mobile.'],
            6       =>  ['title' => 'Too many requests per second ',  'description' => 'Decrease the request frequency or use the execute method. More details on frequency limits here: <a href="http://vk.com/dev/api_requests" target="_blank">http://vk.com/dev/api_requests</a>'],
            7       =>  ['title' => 'Permission to perform this action is denied ',  'description' => 'Make sure that your have received required permissions during the authorization. You can do it with the account.getAppPermissions method.'],
            8       =>  ['title' => 'Invalid request ',  'description' => 'Check the request syntax and used parameters list (it can be found on a method description page) '],
            9       =>  ['title' => 'Flood control ',  'description' => 'You need to decrease the count of identical requests. For more efficient work you may use execute or JSONP.'],
            10      =>  ['title' => 'Internal server error',  'description' => 'Try again later.'],
            11      =>  ['title' => 'In test mode application should be disabled or user should be authorized',  'description' => 'Switch the app off in Settings: https://vk.com/editapp?id={Your API_ID}.'],
            14      =>  ['title' => 'Captcha needed ',  'description' => 'Work with this error is explained in detail on the <a href="https://vk.com/dev/need_confirmation" target="_blank">separate page</a>'],
            15      =>  ['title' => 'Access denied ',  'description' => 'Make sure that you use correct identifiers and the content is available for the user in the full version of the site.'],
            16      =>  ['title' => 'HTTP authorization failed',  'description' => 'To avoid this error check if a user has the \'Use secure connection\' option enabled with the account.getInfo method.'],
            17      =>  ['title' => 'Validation required ',  'description' => 'Make sure that you don\'t use a token received with http://vk.com/dev/auth_mobile for a request from the server. It\'s restricted. The validation process is described on the <a href="https://vk.com/dev/need_confirmation" target="_blank">separate page</a>.'],
            20      =>  ['title' => 'Permission to perform this action is denied for non-standalone applications ',  'description' => 'If you see this error despite your app has the Standalone type, make sure that you use redirect_uri=https://oauth.vk.com/blank.html. Details here: http://vk.com/dev/auth_mobile.'],
            21      =>  ['title' => 'Permission to perform this action is allowed only for Standalone and OpenAPI applications',  'description' => ''],
            23      =>  ['title' => 'This method was disabled ',  'description' => 'All the methods available now are listed here: <a href="http://vk.com/dev/methods" target="_blank">http://vk.com/dev/methods</a>'],
            24      =>  ['title' => 'Confirmation required ',  'description' => 'Confirmation process is described on the <a href="https://vk.com/dev/need_confirmation" target="_blank">separate page</a>'],
            100     =>  ['title' => 'One of the parameters specified was missing or invalid ',  'description' => 'Check the required parameters list and their format on a method description page.'],
            101     =>  ['title' => 'Invalid application API ID ',  'description' => 'Find the app in the administrated list in settings: <a href="http://vk.com/apps?act=settings" target="_blank">http://vk.com/apps?act=settings</a> And set the correct API_ID in the request.'],
            103     =>  ['title' => 'Out of limits', 'description' => 'Out of limits'],
            104     =>  ['title' => 'Not found', 'description' => 'Not found'],
            113     =>  ['title' => 'Invalid user id ',  'description' => 'Make sure that you use a correct id. You can get an id using a screen name with the utils.resolveScreenName method'],
            150     =>  ['title' => 'Invalid timestamp ',  'description' => 'You may get a correct value with the utils.getServerTime method.'],
            200     =>  ['title' => 'Access to album denied ',  'description' => 'Make sure you use correct ids (owner_id is always positive for users, negative for communities) and the current user has access to the requested content in the full version of the site.'],
            201     =>  ['title' => 'Access to audio denied ',  'description' => 'Make sure you use correct ids (owner_id is always positive for users, negative for communities) and the current user has access to the requested content in the full version of the site.'],
            203     =>  ['title' => 'Access to group denied ',  'description' => 'Make sure that the current user is a member or admin of the community (for closed and private groups and events).'],
            300     =>  ['title' => 'This album is full ',  'description' => 'You need to delete the odd objects from the album or use another album.'],
            500     =>  ['title' => 'Permission denied. You must enable votes processing in application settings',  'description' => 'Check the app settings: http://vk.com/editapp?id={Your API_ID}&section=payments'],
            600     =>  ['title' => 'Permission denied. You have no access to operations specified with given object(s)',  'description' => ''],
            603     =>  ['title' => 'Some ads error occurred',  'description' => ''],
            1260    =>  ['title' => 'Invalid screen name',  'description' => 'This screen name is already in use or invalid'],
        ];

        return (!array_key_exists($Code, $errorsData)) ? $errorsData[1] : $errorsData[$Code];
    }

}