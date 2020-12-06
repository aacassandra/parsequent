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
            'headerPushStatusId' => 'X-Parse-Push-Status-Id'
        ]
    ],

    /**
    * Configure the driver as default
    */
    'driver' => 'local',

    /**
    * You can use this parameter for session token validation
    * we recommend using this in the middleware for the backend / front end
    */
    'sessionValidation' => false
];
