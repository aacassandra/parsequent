<?php

namespace Parsequent\Schema;

use Parsequent\ParseHelpers;

class ParseSchema
{
    public static function Read($credentials, $options = [
        'masterKey' => false
    ])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = [
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            "Content-Type: application/json"
        ];

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/schemas", $protocol, $host, $port);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/schemas", $protocol, $host, $port);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        $res = ParseHelpers::responseHandler($httpCode, $output);
        if ($res->status === true) {
            if (count($res->output->results) === 0) {
                $res->status = false;
            } else {
                $res->output = $res->output->results;
            }
        }
        return $res;
    }
}
