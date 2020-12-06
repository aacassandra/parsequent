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
            'protocol' => "",
            'host' => "",
            'database' => "",
            'port' => "",
            'headerAppID' => "",
            'headerRestKey' => "",
            'headerMasterKey' => "",
            'headerClientKey' => "",
            'headerSessionToken' => "",
            'headerAccountKey' => "",
            'headerRevocableSession' => "",
            'headerClientVersion' => "",
            'headerJobStatusId' => "",
            'headerPushStatusId' => "",
        ]
    ],

    /**
    * Configure the driver as default
    */
    'local' => 'back4app',

    /**
    * You can use this parameter for session token validation
    * we recommend using this in the middleware for the backend / front end
    */
    'sessionValidation' => false
];
