<?php

namespace Parsequent\Objects;

use Parsequent\ParseTools;
use Parsequent\ParseHelpers;
use Parsequent\Relations\ParseRelations;

class ParseObjects
{
    public static function Create($credentials, $className = '', $data = [], $options = [])
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

        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s", $protocol, $host, $port, $className);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s", $protocol, $host, $port, $className);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }

    public static function Read($credentials, $className = '', $objectId = '', $options = [])
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
        if (isset($options['include']) && count($options['include']) >= 1) {
            $queryIn['include'] = $options['include'];
        }

        if (count($queryIn) >= 1) {
            if ($database === '') {
                $url = sprintf("%s://%s:%s/classes/%s/%s?%s", $protocol, $host, $port, $className, $objectId, http_build_query($queryIn));
            } else {
                $url = sprintf("%s://%s:%s/" . $database . "/classes/%s/%s?%s", $protocol, $host, $port, $className, $objectId, http_build_query($queryIn));
            }
        } else {
            if ($database === '') {
                $url = sprintf("%s://%s:%s/classes/%s/%s", $protocol, $host, $port, $className, $objectId);
            } else {
                $url = sprintf("%s://%s:%s/" . $database . "/classes/%s/%s", $protocol, $host, $port, $className, $objectId);
            }
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
        if (isset($options['relation']) && $options['relation'] >= 1 && $res->status === true) {
            foreach ($options['relation'] as $rel) {
                $onClassName = '';
                $columnName = '';
                $relInclude = [];
                $relSplit = '';
                if (strpos($rel, '|') !== false) {
                    $relSplit = explode('|', $rel);
                    $columnName = $relSplit[0];
                    $incSplitter = [];
                    if (strpos($relSplit[1], ',')) {
                        $incSplitter = explode('.', $relSplit[1]);
                        foreach ($incSplitter as $inc) {
                            array_push($relInclude, $inc);
                        }
                    } else {
                        array_push($relInclude, $relSplit[1]);
                    }
                } else {
                    $columnName = $rel;
                }

                foreach ($res->output as $key => $value) {
                    if ($columnName === $key) {
                        $onClassName = $res->output->$key->className;
                    }
                }

                $relation = ParseRelations::ReadRelation($credentials, $className, $objectId, [
                    'name' => $columnName,
                    'className' => $onClassName
                ], [
                    'include' => $relInclude,
                    'masterKey' => $options['masterKey'] ?? false
                ]);
                if ($relation->status) {
                    $res->output->$columnName = $relation->output;
                }
            }
        }
        return $res;
    }

    public static function Update($credentials, $className = '', $objectId = '', $data = [], $options = [])
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

    public static function Delete($credentials, $className = '', $objectId = '', $options = [])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = array(
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey'])
        );

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if (config('parsequent.sessionValidation') === true) {
            array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
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
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }

    public static function Batch($credentials, $data, $options = [])
    {
        $data = [
            'requests' => ParseTools::array2Json($data)
        ];

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

        if ($database === '') {
            $url = sprintf("%s://%s:%s/batch", $protocol, $host, $port);
        } else {
            $url = sprintf("%s://%s:%s/%s/batch", $protocol, $host, $port, $database);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        $res = [
            "output" => $output,
            "code" => $httpCode['http_code'],
            "status" => true
        ];
        $res = ParseTools::array2Json($res);
        return $res;
    }
}
