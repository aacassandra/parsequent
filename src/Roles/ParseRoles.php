<?php

namespace Parsequent\Roles;

use Parsequent\ParseHelpers;
use Parsequent\Customs\ParseCustoms;
use Parsequent\Schema\ParseSchema;
use Parsequent\Relations\ParseRelations;

class ParseRoles
{
    public static function Create($credentials, $roleName = '', $options = [
        'acl' => [],
        'users' => [],
        'roles' => [],
        'data' => [],
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

        if ($database === '') {
            $url = sprintf("%s://%s:%s/roles", $protocol, $host, $port);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/roles", $protocol, $host, $port);
        }

        $data['name'] = $roleName;
        $data['ACL'] = isset($options['acl']) && count($options['acl']) >= 1 ? $options['acl'] : ['*' => ['read' => true]];

        if (isset($options['data']) && count($options['data']) >= 1) {
            foreach ($options['data'] as $key => $value) {
                $data[$key] = $value;
            }
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

        $res = ParseHelpers::responseHandler($httpCode, $output);
        if ($res->status) {
            $objectId = $res->output->objectId;
            if (isset($options['roles']) && count($options['roles']) >= 1) {
                $addRoles = ParseRelations::AddRelation($credentials, '_Role', $objectId, [
                    'name' => 'roles',
                    'className' => '_Role'
                ], $options['roles'], $options);
                if (!$addRoles->status) {
                    return $addRoles;
                }
            }

            if (isset($options['users']) && count($options['users']) >= 1) {
                $addUsers = ParseRelations::AddRelation($credentials, '_Role', $objectId, [
                    'name' => 'users',
                    'className' => '_User'
                ], $options['users'], $options);
                if (!$addUsers->status) {
                    return $addUsers;
                }
            }
        }

        return $res;
    }

    public static function Read($credentials, $options = [])
    {
        if (isset($options['roleName']) && $options['roleName'] !== '') {
            $options['where'] = [
                ['name', 'equalTo', $options['roleName']]
            ];
        }

        return ParseCustoms::Read($credentials, '_Role', $options);
    }

    public static function Update($credentials, $objectId = '', $options = [
        'acl' => [],
        'users' => [
            'RemoveRelation' => [],
            'AddRelation' => []
        ],
        'roles' => [
            'RemoveRelation' => [],
            'AddRelation' => []
        ],
        'data' => [],
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

        if ($database === '') {
            $url = sprintf("%s://%s:%s/classes/%s/%s", $protocol, $host, $port, '_Role', $objectId);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/classes/%s/%s", $protocol, $host, $port, '_Role', $objectId);
        }

        $data = [];
        if (isset($options['acl']) && count($options['acl']) >= 1) {
            $data['ACL'] = $options['acl'];
        }

        if (isset($options['data']) && count($options['data']) >= 1) {
            foreach ($options['data'] as $key => $value) {
                $data[$key] = $value;
            }
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

        $res = ParseHelpers::responseHandler($httpCode, $output);
        if ($res->status) {
            if (isset($options['users']) && count($options['users']) >= 1) {
                if (isset($options['users']['removeRelation'])) {
                    $removing = ParseRelations::RemoveRelation($credentials, '_Role', $objectId, [
                        'name' => 'users',
                        'className' => '_User'
                    ], $options['users']['removeRelation'], $options);
                    if (!$removing->status) {
                        return $removing;
                    }
                }
                if (isset($options['users']['addRelation'])) {
                    $adding = ParseRelations::AddRelation($credentials, '_Role', $objectId, [
                        'name' => 'users',
                        'className' => '_User'
                    ], $options['users']['addRelation'], $options);
                    if (!$adding->status) {
                        return $adding;
                    }
                }
            }
            if (isset($options['roles']) && count($options['roles']) >= 1) {
                if (isset($options['roles']['removeRelation'])) {
                    $removing = ParseRelations::RemoveRelation($credentials, '_Role', $objectId, [
                        'name' => 'roles',
                        'className' => '_Role'
                    ], $options['roles']['removeRelation'], $options);
                    if (!$removing->status) {
                        return $removing;
                    }
                }
                if (isset($options['roles']['addRelation'])) {
                    $adding = ParseRelations::AddRelation($credentials, '_Role', $objectId, [
                        'name' => 'roles',
                        'className' => '_Role'
                    ], $options['roles']['addRelation'], $options);
                    if (!$adding->status) {
                        return $adding;
                    }
                }
            }
        }
        return $res;
    }

    public static function Delete($credentials, $objectId = '', $options = [])
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
        } else {
            array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/%s/%s", $protocol, $host, $port, 'roles', $objectId);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/%s/%s", $protocol, $host, $port, 'roles', $objectId);
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

    public static function RoleHasPermission($credentials, $objectId = '', $options = [
        'permissions' => [],
        'masterKey' => false
    ])
    {
        $data = isset($options['permissions']) && count($options['permissions']) >= 1 ? ['permissions' => $options['permissions']] : [];
        $update = ParseRoles::Update($credentials, $objectId, [
            'data' => $data,
            'masterKey' => $options['masterKey'] ?? false
        ]);
        if ($update->status && isset($options['permissions']) && count($options['permissions']) >= 1) {
            $options['objectId'] = $objectId;
            $role = ParseCustoms::Read($credentials, '_Role', $options);
            if ($role->status) {
                $roleName = $role->output->name;
                $vDataPermission = [];
                foreach ($options['permissions'] as $key => $pm) {
                    $splitter = explode('-', $pm);
                    $className = $splitter[0];
                    $permission = $splitter[1];
                    if (!isset($vDataPermission[$className])) {
                        $vDataPermission[$className] = [];
                        array_push($vDataPermission[$className], $permission);
                    } else {
                        array_push($vDataPermission[$className], $permission);
                    }
                }

                foreach ($vDataPermission as $className => $permissions) {
                    unset($options['objectId']);
                    unset($options['permissions']);
                    $sync = ParseRoles::RoleSyncPermission($credentials, $roleName, $className, $permissions, $options);
                    if (!$sync->status) {
                        return $sync;
                    }
                }
            }
        }
        return $update;
    }

    public static function RoleSyncPermission($credentials, $roleName, $className, $permissions, $options)
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
        // {
        //     classLevelPermissions:
        //     {
        //       "find": {
        //         "requiresAuthentication": true,
        //         "role:admin": true
        //       },
        //       "get": {
        //         "requiresAuthentication": true,
        //         "role:admin": true
        //       },
        //       "create": { "role:admin": true },
        //       "update": { "role:admin": true },
        //       "delete": { "role:admin": true }
        //     }
        //   }
        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        $schemas = ParseSchema::Read($credentials, $options);
        if (!$schemas->status) {
            return $schemas;
        }
        $schemas = $schemas->output;
        foreach ($schemas as $schema) {
            if ($schema->className === $className) {
                $data = [
                    'classLevelPermissions' => $schema->classLevelPermissions
                ];
                $availablePermissions = [
                    'read' => false,
                    'find' => false,
                    'get' => false,
                    'count' => false,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                    'addField' => false
                ];
                $classLevelPermissions = json_encode($data['classLevelPermissions']);
                $classLevelPermissions = json_decode($classLevelPermissions, true);
                $vRoleName = "role:$roleName";
                foreach ($permissions as $permission) {
                    if (lcfirst($permission) === 'read') {
                        $availablePermissions['find'] = true;
                        $availablePermissions['get'] = true;
                        $availablePermissions['count'] = true;
                        $classLevelPermissions['find'][$vRoleName] = true;
                        $classLevelPermissions['get'][$vRoleName] = true;
                        $classLevelPermissions['count'][$vRoleName] = true;
                    } else {
                        $availablePermissions[lcfirst($permission)] = true;
                        $classLevelPermissions[lcfirst($permission)][$vRoleName] = true;
                    }
                }


                foreach ($availablePermissions as $key => $del) {
                    if ($del === false) {
                        unset($classLevelPermissions[$key][$vRoleName]);
                    }
                }
            }
        }

        $data = [
            'classLevelPermissions' => []
        ];

        foreach ($permissions as $key => $permission) {
            if (strtolower($permission) === 'read') {
                $data['classLevelPermissions']['find'] = [
                    "role:$roleName" => true
                ];
                $data['classLevelPermissions']['get'] = [
                    "role:$roleName" => true
                ];
                $data['classLevelPermissions']['count'] = [
                    "role:$roleName" => true
                ];
            } else {
                $data['classLevelPermissions'][strtolower($permission)] = [
                    "role:$roleName" => true
                ];
            }
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/schemas/%s", $protocol, $host, $port, $className);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/schemas/%s", $protocol, $host, $port, $className);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }
}
