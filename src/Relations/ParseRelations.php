<?php

namespace Parsequent\Relations;

use Parsequent\ParseHelpers;

class ParseRelations
{
    public static function ReadRelation($credentials = '', $className = '', $objectId = '', $column = [
        'name' => '',
        'className' => ''
    ], $options = [
        'include' => [],
        'masterKey' => false
    ])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = [
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey'])
        ];

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if (config('parsequent.sessionValidation') === true) {
            array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
        }

        $queryIn = [];

        $queryIn['where'] = [
            '$relatedTo' => [
                'object' => [
                    '__type' => 'Pointer',
                    'className' => $className,
                    'objectId' => $objectId
                ],
                'key' => $column['name']
            ]
        ];

        if (isset($options['include']) && count($options['include']) >= 1) {
            $queryIn['include'] = $options['include'];
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s?%s", $protocol, $host, $port, $column['className'], http_build_query($queryIn));
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s?%s", $protocol, $host, $port, $column['className'], http_build_query($queryIn));
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
        if ($res->status && isset($res->output->results) && count($res->output->results) >= 1) {
            $res->output = $res->output->results;
        }
        return $res;
    }

    public static function AddRelation($credentials, $className = '', $objectId = '', $column = [
        'name' => '',
        'className' => ''
    ], $objects = [], $options = [
        'masterKey' => false
    ])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = array(
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey']),
            "Content-Type: application/json"
        );

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if (config('parsequent.sessionValidation') === true) {
            array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
        }

        if (isset($column['name']) && $column['name'] !== '') {
            $data[$column['name']] = [
                '__op' => 'AddRelation',
                'objects' => []
            ];
            if (isset($objects) && count($objects) >= 1) {
                foreach ($objects as $value) {
                    array_push($data[$column['name']]['objects'], [
                        "__type" => "Pointer",
                        "className" => $column['className'] ?? '',
                        "objectId" => $value
                    ]);
                }
            }
        } else {
            $data[''] = [
                '__op' => 'AddRelation',
                'objects' => []
            ];
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s/%s", $protocol, $host, $port, $className, $objectId);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s/%s", $protocol, $host, $port, $className, $objectId);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }

    public static function RemoveRelation($credentials, $className = '', $objectId = '', $column = [
        'name' => '',
        'className' => ''
    ], $objects = [], $options = [
        'masterKey' => false
    ])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = array(
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey']),
            "Content-Type: application/json"
        );

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if (config('parsequent.sessionValidation') === true) {
            array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
        }

        if (isset($column['name']) && $column['name'] !== '') {
            $data[$column['name']] = [
                '__op' => 'RemoveRelation',
                'objects' => []
            ];
            if (isset($objects) && count($objects) >= 1) {
                foreach ($objects as $value) {
                    array_push($data[$column['name']]['objects'], [
                        "__type" => "Pointer",
                        "className" => $column['className'] ?? '',
                        "objectId" => $value
                    ]);
                }
            }
        } else {
            $data[''] = [
                '__op' => 'RemoveRelation',
                'objects' => []
            ];
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s/%s", $protocol, $host, $port, $className, $objectId);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s/%s", $protocol, $host, $port, $className, $objectId);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }
}
