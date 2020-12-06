<?php

namespace Parsequent;

use Parsequent\ParseTools;
use DateTime;

class ParseHelpers
{
    public static function restConditional($options, $encoding = true)
    {
        $query = [];
        foreach ($options as $where) {
            $where = ParseTools::array2Json($where);
            if (isset($where[1]) && $where[1] === 'equalTo' && !isset($query[$where[0]])) {
                $query[$where[0]] = $where[2];
            } elseif (isset($where[1]) && $where[1] === 'notEqualTo' && !isset($query[$where[0]])) {
                $query[$where[0]] = [
                    '$ne' => $where[2]
                ];
            } elseif (isset($where[1]) && $where[1] === 'equalToPointer' && !isset($query[$where[0]])) {
                $pointer = ParseTools::needFormat('pointer', [$where[2][0], $where[2][1]]);
                $query[$where[0]] = $pointer;
            } elseif (isset($where[1]) && $where[1] === 'notEqualToPointer' && !isset($query[$where[0]])) {
                $pointer = ParseTools::needFormat('pointer', [$where[2][0], $where[2][1]]);
                $query[$where[0]] = [
                    '$ne' => $pointer
                ];
            } elseif (isset($where[1]) && $where[1] === 'containedIn' && !isset($query[$where[0]])) {
                $query[$where[0]] = [
                    '$in' => $where[2]
                ];
            } elseif (isset($where[1]) && $where[1] === 'greaterThan' && !isset($query[$where[0]])) {
                $query[$where[0]] = [
                    '$gt' => $where[2] * 1
                ];
            } elseif (isset($where[1]) && $where[1] === 'lessThan' && !isset($query[$where[0]])) {
                $query[$where[0]] = [
                    '$lt' => $where[2] * 1
                ];
            } elseif (isset($where[1]) && $where[1] === 'greaterThanOrEqualTo' && !isset($query[$where[0]])) {
                $query[$where[0]] = [
                    '$gte' => $where[2] * 1
                ];
            } elseif (isset($where[1]) && $where[1] === 'lessThanOrEqualTo' && !isset($query[$where[0]])) {
                $query[$where[0]] = [
                    '$lte' => $where[2] * 1
                ];
            } elseif (isset($where[1]) && $where[1] === 'exists' && !isset($query[$where[0]])) {
                $query[$where[0]] = [
                    '$exists' => $where[2]
                ];
            }
        }

        if ($encoding) {
            $query = json_encode($query);
        }

        return $query;
    }

    public static function batchConditional($dat = array())
    {
        if ($dat[0] == 'string') {
            return '"' . $dat[1] . '":"' . $dat[2] . '"';
        } elseif ($dat[0] == 'number') {
            return '"' . $dat[1] . '":' . ($dat[2] * 1);
        } elseif ($dat[0] == 'boolean') {
            if ($dat[2] == 'true' || $dat[2] == true || $dat[2] == 'True' || $dat[2] == 1) {
                return '"' . $dat[1] . '":true';
            } else {
                return '"' . $dat[1] . '":false';
            }
        } elseif ($dat[0] == 'pointer') {
            return '"' . $dat[1] . '":{
              "__type": "Pointer", "className": "' . $dat[3] . '", "objectId": "' . $dat[2] . '"
            }';
        } elseif ($dat[0] == 'array') {
            return '"' . $dat[1] . '":' . json_encode(array_values($dat[2]));
        } elseif ($dat[0] == 'object') {
            return '"' . $dat[1] . '":' . json_encode($dat[2]);
        } elseif ($dat[0] == 'geopoint') {
            return '"' . $dat[1] . '":{
              "__type": "GeoPoint", "latitude": ' . ($dat[2] * 1) . ', "longitude": ' . ($dat[3] * 1) . '
            }';
        }
    }

    public static function errorMessageHandler($output)
    {
        $fixOutput = '{
            "code":0,
            "message":""
        }';
        $fixOutput = json_decode($fixOutput);
        $fixOutput->code = isset($output->code) ? $output->code : 504;
        if ($fixOutput->code == 504) {
            $fixOutput->message = 'Gateway Time-out';
        } elseif (isset($output->error)) {
            $fixOutput->message = $output->error;
        } elseif (isset($output->message)) {
            $fixOutput->message = $output->message;
        } else {
            $fixOutput->message = "";
        }

        return $fixOutput;
    }

    public static function responseHandler($httpCode, $output)
    {
        if ($httpCode['http_code'] == 200 || $httpCode['http_code'] == 201) {
            if (isset($output->message)) {
                $output = ParseHelpers::errorMessageHandler($output);
                $response = [
                    "output" => $output->message,
                    "code" => $output->code,
                    "status" => false
                ];
            } else {
                if (json_encode($output) === '{}') {
                    $response = [
                        "output" => json_decode('{}'),
                        "code" => $httpCode['http_code'],
                        "status" => true
                    ];
                } else {
                    $response = [
                        "output" => $output,
                        "code" => $httpCode['http_code'],
                        "status" => true
                    ];
                }
            }
        } else {
            $output = ParseHelpers::errorMessageHandler($output);
            $response = [
                "output" => $output->message,
                "code" => $output->code,
                "status" => false
            ];
        }
        $response = ParseTools::array2Json($response);
        return $response;
    }

    public static function getRestRelation($credentials = [], $parent = ["class" => '', "objectId" => '', "relColumn" => '', "relClass" => ''])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = array(
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey']),
            sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey'])
        );

        $queryIn = [];
        $queryIn['limit'] = 10000;

        $queryIn['where'] = '{"$relatedTo":{"object":{"__type":"Pointer","className":"_Role","objectId":"' . $parent['objectId'] . '"},"key":"' . $parent['relColumn'] . '"}}';


        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s?%s", $protocol, $host, $port, '_User', http_build_query($queryIn));
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s?%s", $protocol, $host, $port, '_User', http_build_query($queryIn));
        }


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        $response = null;
        if ($httpCode['http_code'] == 200) {
            if (isset($output->message)) {
                $output = ParseHelpers::errorMessageHandler($output);
                $response = [
                    "output" => [
                        "code" => $output->code,
                        "message" => $output->message
                    ],
                    "status" => false
                ];
            } else {
                if (isset($output->results)) {
                    $response = [
                        "output" => $output,
                        "status" => true
                    ];
                } else {
                    $response = [
                        "output" => $output,
                        "statusCode" => $httpCode,
                        "status" => false
                    ];
                }
            }
        } else {
            $output = ParseHelpers::errorMessageHandler($output);
            $response = [
                "output" => [
                    "code" => $output->code,
                    "message" => $output->message
                ],
                "statusCode" => $httpCode,
                "status" => false
            ];
        }
        $response = ParseTools::array2Json($response);
        return $response;
    }

    public static function objectSet($data)
    {
        $fixData = [];
        foreach ($data as $dat) {
            if (isset($dat[3])) {
                if ($dat[0] == 'pointer') {
                    $fixData[$dat[1]] = [
                        '__type' => 'Pointer',
                        'className' => $dat[3],
                        'objectId' => $dat[2]
                    ];
                } elseif ($dat[0] == 'pointerOfArray') {
                    $Arr = [];
                    foreach ($dat[2] as $value) {
                        array_push($Arr, [
                            '__type' => 'Pointer',
                            'className' => $dat[3],
                            'objectId' => $value
                        ]);
                    }
                    $fixData[$dat[1]] = $Arr;
                }
            } else {
                if ($dat[0] == 'string') {
                    $fixData[$dat[1]] = $dat[2];
                } elseif ($dat[0] == 'date') {
                    // $dat[2]: 2020-05-25 22:12 or 09/17/2020 00:00
                    $dt = new DateTime($dat[2]);
                    $fixData[$dat[1]] = [
                        '__type' => 'Date',
                        'iso' => $dt->format('Y-m-d\TH:i:s.') . substr($dt->format('u'), 0, 3) . 'Z'
                    ];
                } elseif ($dat[0] == 'number') {
                    $fixData[$dat[1]] = $dat[2] * 1;
                } elseif ($dat[0] == 'boolean') {
                    if ($dat[2] === 'false') {
                        $fixData[$dat[1]] = false;
                    } elseif ($dat[2] === 'true') {
                        $fixData[$dat[1]] = true;
                    } elseif ($dat[2] === false) {
                        $fixData[$dat[1]] = false;
                    } elseif ($dat[2] === true) {
                        $fixData[$dat[1]] = true;
                    }
                } elseif ($dat[0] == 'array') {
                    $fixData[$dat[1]] = $dat[2];
                } elseif ($dat[0] == 'object') {
                    $fixData[$dat[1]] = ParseTools::array2Json($dat[2]);
                } elseif ($dat[0] == 'image') {
                    // Comming Soon
                } elseif ($dat[0] == 'geopoint') {
                    $fixData[$dat[1]] = [
                        '__type' => 'GeoPoint',
                        'latitude' => $dat[2] * 1,
                        'longitude' => $dat[3] * 1
                    ];
                } elseif ($dat[0] == 'delete') {
                    $fixData[$dat[1]] = [
                        '__op' => 'Delete'
                    ];
                }
            }
        }
        return $fixData;
    }
}
