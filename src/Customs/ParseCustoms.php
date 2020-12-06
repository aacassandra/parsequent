<?php

namespace Parsequent\Customs;

use Parsequent\ParseHelpers;
use Parsequent\ParseTools;
use Parsequent\Objects\ParseObjects;
use Parsequent\Queries\ParseQueries;

class ParseCustoms
{
    public static function Read($credentials, $className = '', $options = [])
    {
        if (isset($options['objectId']) && $options['objectId'] !== null && $options['objectId'] !== '') {
            return ParseObjects::Read($credentials, $className, $options['objectId'], $options);
        } else {
            $queries = ParseQueries::Basic($credentials, $className, $options);
            return $queries;
        }
    }

    public static function Update($credentials, $className = '', $data = [], $options = [])
    {
        if (isset($options['objectId']) && $options['objectId'] !== null && $options['objectId'] !== '') {
            return ParseObjects::Update($credentials, $className, $options['objectId'], $data, $options);
        } else {
            $queries = ParseQueries::Basic($credentials, $className, $options);
            if ($queries->status === true) {
                $finalData = [];
                $database = '';
                if ($credentials['database'] !== '') {
                    $database = "/" . $credentials['database'];
                }
                foreach ($queries->output as $value) {
                    $objectId = $value->objectId;
                    array_push($finalData, [
                        "method" => "PUT",
                        "path" => "$database/classes/$className/$objectId",
                        "body" => $data
                    ]);
                }

                return ParseObjects::Batch($credentials, $finalData, $options);
            } else {
                return $queries;
            }
        }
    }

    public static function Delete($credentials, $className = '', $options = [])
    {
        if (isset($options['objectId']) && $options['objectId'] !== null && $options['objectId'] !== '') {
            return ParseObjects::Delete($credentials, $className, $options['objectId'], $options);
        } else {
            $queries = ParseQueries::Basic($credentials, $className, $options);
            if ($queries->status === true) {
                $finalData = [];
                $database = '';
                if ($credentials['database'] !== '') {
                    $database = "/" . $credentials['database'];
                }
                foreach ($queries->output as $value) {
                    $objectId = $value->objectId;
                    array_push($finalData, [
                        "method" => "DELETE",
                        "path" => "$database/classes/$className/$objectId"
                    ]);
                }

                return ParseObjects::Batch($credentials, $finalData, $options);
            } else {
                return $queries;
            }
        }
    }
}
