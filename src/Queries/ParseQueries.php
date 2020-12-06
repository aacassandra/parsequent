<?php

namespace Parsequent\Queries;

use Parsequent\ParseHelpers;
use Parsequent\ParseTools;

class ParseQueries
{
    public static function Basic($credentials = '', $className = '', $options = [])
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

        $queryIn = [];

        if (isset($options['limit']) && is_int($options['limit'])) {
            $queryIn['limit'] = $options['limit'];
        } else {
            $queryIn['limit'] = 10000;
        }

        if (isset($options['skip']) && is_int($options['skip'])) {
            $queryIn['skip'] = $options['skip'];
        }

        if (isset($options['order']) && is_string($options['order'])) {
            $queryIn['order'] = $options['order'];
        }

        if (isset($options['keys']) && is_string($options['keys'])) {
            $queryIn['keys'] = $options['keys'];
        }

        if (isset($options['excludeKeys']) && is_string($options['excludeKeys'])) {
            $queryIn['excludeKeys'] = $options['excludeKeys'];
        }

        if (isset($options['include']) && count($options['include']) >= 1) {
            $queryIn['include'] = $options['include'];
        }

        if (isset($options['where']) && count($options['where']) >= 1) {
            $queryIn['where'] = ParseHelpers::restConditional($options['where'], false);
        }

        if (isset($options['orWhere']) && count($options['orWhere']) >= 1) {
            $queryArr = [];
            foreach ($options['orWhere'] as $whereOr) {
                array_push($queryArr, ParseHelpers::restConditional([$whereOr], false));
            }

            $queryFixArr = [
                '$or' => []
            ];
            foreach ($queryArr as $key => $value) {
                array_push($queryFixArr['$or'], $value);
            }
            $queryIn['where']['$or'] = $queryArr;
        }
        $queryIn['where'] = json_encode($queryIn['where']);
        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s?%s", $protocol, $host, $port, $className, http_build_query($queryIn));
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s?%s", $protocol, $host, $port, $className, http_build_query($queryIn));
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        $response = ParseHelpers::responseHandler($httpCode, $output);
        if ($response->status === true) {
            if (count($response->output->results) === 0) {
                $response->status = false;
            } else {
                $response->output = $response->output->results;
            }
        }

        if (isset($options['relation']) && $options['relation'] >= 1 && $response->status === true) {
            $newResponse = [];
            foreach ($response->output as $ii => $obj) {
                $newObj = ParseTools::json2Array($obj);
                // Example: $options['relation'] = ['users:_User','roles:_Role']
                foreach ($options['relation'] as $relation) {
                    $relationSplit = explode(":", $relation);
                    if (count($relationSplit) == 2) {
                        $relColumn = $relationSplit[0];
                        $relClass = $relationSplit[1];
                        $getRelation = ParseHelpers::getRestRelation($credentials, [
                            'class' => $className,
                            'objectId' => $obj->objectId,
                            'relColumn' => $relColumn,
                            'relClass' => $relClass
                        ]);
                        if ($getRelation->status) {
                            foreach ($newObj as $key => $child) {
                                if ($key == $relColumn) {
                                    $newObj[$key] = $getRelation->output->results;
                                }
                            }
                        }
                    }
                }

                $newObj = ParseTools::array2Json($newObj);
                array_push($newResponse, $newObj);
            }
            $response->output = $newResponse;
        }
        return $response;
    }

    public static function CountingObjects($credentials, $className = '', $options = [])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = [
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey']),
        ];

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        $queryIn = [];

        $queryIn['count'] = 1;
        $queryIn['limit'] = 0;
        $queryIn['where'] = ParseHelpers::restConditional($options['where'], false);

        $queryIn['where'] = json_encode($queryIn['where']);
        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s?%s", $protocol, $host, $port, $className, http_build_query($queryIn));
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s?%s", $protocol, $host, $port, $className, http_build_query($queryIn));
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
        if ($res->status) {
            $res->output = $res->output->count;
        }

        return $res;
    }
}
