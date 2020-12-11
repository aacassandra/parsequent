<?php

namespace Parsequent\Queries;

use Parsequent\ParseHelpers;
use Parsequent\Relations\ParseRelations;

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

        if (config('parsequent.sessionValidation') === true) {
            array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
        }

        $queryIn = [];

        if (isset($options['limit']) && is_int($options['limit']) && $options['limit'] !== 0) {
            $queryIn['limit'] = $options['limit'];
        } else {
            $queryIn['limit'] = 10000;
        }

        if (isset($options['skip']) && is_int($options['skip']) && $options['skip'] !== 0) {
            $queryIn['skip'] = $options['skip'];
        }

        if (isset($options['order']) && is_string($options['order']) && $options['order'] !== '') {
            $queryIn['order'] = $options['order'];
        }

        if (isset($options['keys']) && is_string($options['keys']) && $options['keys'] !== '') {
            $queryIn['keys'] = $options['keys'];
        }

        if (isset($options['excludeKeys']) && is_string($options['excludeKeys']) && $options['excludeKeys'] !== '') {
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
        if ((isset($options['where']) && count($options['where']) >= 1) || (isset($options['orWhere']) && count($options['orWhere']) >= 1)) {
            $queryIn['where'] = json_encode($queryIn['where']);
        }

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
        if ($res->status === true) {
            if (count($res->output->results) === 0) {
                $res->status = false;
            } else {
                $res->output = $res->output->results;
            }
        }

        if (isset($options['relation']) && $options['relation'] >= 1 && $res->status === true) {
            foreach ($res->output as $ii => $obj) {
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

                    foreach ($obj as $key => $value) {
                        if ($columnName === $key) {
                            $onClassName = $obj->$key->className;
                        }
                    }

                    $relation = ParseRelations::ReadRelation($credentials, $className, $obj->objectId, [
                        'name' => $columnName,
                        'className' => $onClassName
                    ], [
                        'include' => $relInclude,
                        'masterKey' => $options['masterKey'] ?? false
                    ]);
                    if ($relation->status) {
                        $obj->$columnName = $relation->output;
                    } else {
                        $obj->$columnName = [];
                    }
                }
            }
        }
        return $res;
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

        if (config('parsequent.sessionValidation') === true) {
            array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
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
