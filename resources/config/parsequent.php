<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    /**
     * You can create many credentials in here
     */
    'drivers' => [
        'local' => [
            "appId" => "",
            "clientKey" => "",
            "restKey" => "",
            "masterKey" => "",
            'protocol' => "https",
            'host' => "parseapi.local.com",
            'database' => "",
            'port' => 443,
            'headerAppID' => 'X-Parse-Application-Id',
            'headerRestKey' => 'X-Parse-REST-API-Key',
            'headerMasterKey' => 'X-Parse-Master-Key',
            'headerClientKey' => 'X-Parse-Client-Key',
            'headerSessionToken' => 'X-Parse-Session-Token',
            'headerAccountKey' => 'X-Parse-Account-Key',
            'headerRevocableSession' => 'X-Parse-Revocable-Session',
            'headerClientVersion' => 'X-Parse-Client-Version',
            'headerJobStatusId' => 'X-Parse-Job-Status-Id',
            'headerPushStatusId' => 'X-Parse-Push-Status-Id',
            'storageKey' => 'session_parse'
        ]
    ],

    /**
    * Configure the driver as default
    */
    'driver' => 'local',

    /**
    * If you enable this configuration, the session token validation check will be performed on every http request
    *
    * And
    * 
    * You can use this parameter for session token validation
    * we recommend using this in the middleware
    */
    'sessionValidation' => false,

    /**
     * The Permission Delimiter is used for the permission string as shown below 
     * "Tags-Create" 
     * "className[delimiter]permission"
     * 
     * Available permission:
     * ['get', 'find', 'count', 'create', 'update', 'delete', 'addField']
     * 
     */
    'permissionDelimiter' => '-'
];
