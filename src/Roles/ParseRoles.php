<?php

namespace Parsequent\Roles;

use Parsequent\ParseTools;
use Parsequent\ParseHelpers;
use Parsequent\Customs\ParseCustoms;
use Parsequent\Objects\ParseObjects;
use Parsequent\Schema\ParseSchema;
use Parsequent\Relations\ParseRelations;
use Parsequent\Users\ParseUsers;

class ParseRoles
{
    public static function Create($credentials, $roleName = '', $options = [
        'acl' => [],
        'users' => [],
        'roles' => [],
        'permissions' => [],
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
                $roles = ParseRoles::Read($credentials);
                if ($roles->status) {
                    $addUsers = ParseRelations::AddRelation($credentials, '_Role', $objectId, [
                        'name' => 'users',
                        'className' => '_User'
                    ], $options['users'], $options);
                    if (!$addUsers->status) {
                        return $addUsers;
                    }

                    $roles = $roles->output;
                    foreach ($roles as $role) {
                        if ($role->objectId === $objectId) {
                            $DataWillBatch = [];
                            $db = '';
                            if ($credentials['database'] !== '') {
                                $db = "/" . $credentials['database'];
                            }
                            foreach ($options['users'] as $user) {
                                array_push($DataWillBatch, [
                                    "method" => "PUT",
                                    "path" => "$db/classes/_User/$user",
                                    "body" => [
                                        'role' => [
                                            '__type' => 'Pointer',
                                            'className' => '_Role',
                                            'objectId' => $role->objectId
                                        ]
                                    ]
                                ]);
                            }

                            $updateUsers = ParseObjects::Batch($credentials, $DataWillBatch, $options);
                            if (!$updateUsers->status) {
                                return $updateUsers;
                            }
                        }

                        if ($role->name !== $roleName) {
                            $remove = ParseRelations::RemoveRelation($credentials, '_Role', $role->objectId, [
                                'name' => 'users',
                                'className' => '_User'
                            ], $options['users'], $options);
                            if (!$remove->status) {
                                return $remove;
                            }
                        }
                    }
                }
            }

            if (isset($options['permissions'])) {
                $sync = ParseRoles::RoleHasPermission($credentials, $objectId, $options['permissions'], $options);
                if (!$sync->status) {
                    return $sync;
                }
            }
        }

        return $res;
    }

    public static function Read($credentials, $options = [])
    {
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
        'permissions' => [],
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

        if (isset($options['permissions'])) {
            $data['permissions'] = $options['permissions'];
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
            $roles = ParseRoles::Read($credentials);
            if ($roles->status) {
                $roles = $roles->output;
                if (isset($options['users']) && count($options['users']) >= 1) {
                    $DataWillBatch = [];
                    $db = '';
                    if ($credentials['database'] !== '') {
                        $db = "/" . $credentials['database'];
                    }

                    if (isset($options['users']['RemoveRelation']) && count($options['users']['RemoveRelation']) >= 1) {
                        $removing = ParseRelations::RemoveRelation($credentials, '_Role', $objectId, [
                            'name' => 'users',
                            'className' => '_User'
                        ], $options['users']['RemoveRelation'], $options);
                        if (!$removing->status) {
                            return $removing;
                        }

                        foreach ($roles as $role) {
                            if ($role->objectId === $objectId) {
                                foreach ($options['users']['RemoveRelation'] as $user) {
                                    array_push($DataWillBatch, [
                                        "method" => "PUT",
                                        "path" => "$db/classes/_User/$user",
                                        "body" => [
                                            'role' => null
                                        ]
                                    ]);
                                }
                            }
                        }
                    }

                    if (isset($options['users']['AddRelation']) && count($options['users']['AddRelation']) >= 1) {
                        $adding = ParseRelations::AddRelation($credentials, '_Role', $objectId, [
                            'name' => 'users',
                            'className' => '_User'
                        ], $options['users']['AddRelation'], $options);
                        if (!$adding->status) {
                            return $adding;
                        }

                        foreach ($roles as $role) {
                            if ($role->objectId === $objectId) {
                                foreach ($options['users']['AddRelation'] as $user) {
                                    array_push($DataWillBatch, [
                                        "method" => "PUT",
                                        "path" => "$db/classes/_User/$user",
                                        "body" => [
                                            'role' => [
                                                '__type' => 'Pointer',
                                                'className' => '_Role',
                                                'objectId' => $role->objectId
                                            ]
                                        ]
                                    ]);
                                }
                            }

                            if ($role->objectId !== $objectId) {
                                ParseRelations::RemoveRelation($credentials, '_Role', $role->objectId, [
                                    'name' => 'users',
                                    'className' => '_User'
                                ], $options['users']['AddRelation'], $options);
                            }
                        }
                    }
                    if (count($DataWillBatch) >= 1) {
                        $updateUsers = ParseObjects::Batch($credentials, $DataWillBatch, $options);
                        if (!$updateUsers->status) {
                            return $updateUsers;
                        }
                    }
                }

                if (isset($options['roles']) && count($options['roles']) >= 1) {
                    if (isset($options['roles']['RemoveRelation']) && count($options['roles']['RemoveRelation']) >= 1) {
                        $removing = ParseRelations::RemoveRelation($credentials, '_Role', $objectId, [
                            'name' => 'roles',
                            'className' => '_Role'
                        ], $options['roles']['RemoveRelation'], $options);
                        if (!$removing->status) {
                            return $removing;
                        }
                    }
                    if (isset($options['roles']['AddRelation']) && count($options['roles']['AddRelation']) >= 1) {
                        $adding = ParseRelations::AddRelation($credentials, '_Role', $objectId, [
                            'name' => 'roles',
                            'className' => '_Role'
                        ], $options['roles']['AddRelation'], $options);
                        if (!$adding->status) {
                            return $adding;
                        }
                    }
                }

                if (isset($options['permissions'])) {
                    $options['roleNeedUpdate'] = false;
                    $sync = ParseRoles::RoleHasPermission($credentials, $objectId, $options['permissions'], $options);
                    if (!$sync->status) {
                        return $sync;
                    }
                }
            }
        }
        return $res;
    }

    public static function Delete($credentials, $objectId = '', $options = [])
    {
        $sync = ParseRoles::RoleHasPermission($credentials, $objectId, [], $options);
        if (!$sync->status) {
            return $sync;
        }

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

    public static function RoleHasPermission($credentials, $objectId = '', $permissions = [], $options = [
        'masterKey' => false,
        'roleNeedUpdate' => true
    ])
    {
        $options = [
            'roleNeedUpdate' => isset($options['roleNeedUpdate']) ? $options['roleNeedUpdate'] : true,
            'masterKey' => $options['masterKey'] ?? false
        ];

        if (isset($permissions) && count($permissions) >= 1) :
            $options['permissions'] = $permissions;
        else :
            $options['permissions'] = [];
        endif;

        $run = function () use ($credentials, $objectId, $permissions, $options) {
            $options['objectId'] = $objectId;
            unset($options['roleNeedUpdate']);
            $role = ParseCustoms::Read($credentials, '_Role', $options);
            if ($role->status) {
                $roleName = $role->output->name;
                $vDataPermission = [];
                $className = '';
                if (count($permissions) > 0) :
                    foreach ($permissions as $key => $pm) {
                        $splitter = explode(config('parsequent.permissionDelimiter'), $pm);
                        $className = $splitter[0];
                        $permission = $splitter[1];
                        if (!isset($vDataPermission[$className])) {
                            $vDataPermission[$className] = [];
                            array_push($vDataPermission[$className], $permission);
                        } else {
                            array_push($vDataPermission[$className], $permission);
                        }
                    }
                else :
                    $className = '_Role';
                endif;

                $schemas = ParseSchema::Read($credentials, $options);
                if (!$schemas->status) {
                    return $schemas;
                }
                $schemas = $schemas->output;
                if (count($vDataPermission) >= 1) {
                    $newVDataPermission = [];
                    foreach ($vDataPermission as $className => $actionPermissions) {
                        if (strtolower($className) === 'role') :
                            $newVDataPermission['_Role'] = $actionPermissions;
                        elseif (strtolower($className) === 'user') :
                            $newVDataPermission['_User'] = $actionPermissions;
                        else :
                            $newVDataPermission[$className] = $actionPermissions;
                        endif;
                    }
                    $vDataPermission = $newVDataPermission;

                    foreach ($vDataPermission as $className => $actionPermissions) {
                        foreach ($schemas as $schema) {
                            if ($schema->className === $className) {
                                unset($options['objectId']);
                                $clp = ParseRoles::RoleClassPermission($credentials, $roleName, $className, $actionPermissions, $schema->classLevelPermissions, $options);
                                if (!$clp->status) {
                                    return $clp;
                                }
                            }
                        }
                    }
                }

                foreach ($schemas as $schema) {
                    $sync = ParseRoles::RoleSyncPermission($credentials, $roleName, $vDataPermission, ParseTools::json2Array($schema), $options);
                    if (!$sync->status) {
                        return $sync;
                    }
                }
            }
        };

        if ($options['roleNeedUpdate']) :
            $update = ParseRoles::Update($credentials, $objectId, $options);
            if ($update->status && isset($permissions) && count($permissions) >= 0) :
                $run();
                return $update;
            else :
                return $update;
            endif;
        else :
            $run();
            return ParseTools::array2Json([
                'status' => true
            ]);
        endif;
    }

    public static function RoleClassPermission($credentials, $roleName, $className, $permissions, $defaultClassLevelPermissions, $options)
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

        $data = [
            'classLevelPermissions' => ParseTools::json2Array($defaultClassLevelPermissions)
        ];

        $availablePermissions = [
            'read' => false,
            'find' => false,
            'get' => false,
            'count' => false,

            'write' => false,
            'create' => false,
            'update' => false,
            'delete' => false,

            'add' => false,
            'addField' => false
        ];
        $classLevelPermissions = $data['classLevelPermissions'];
        $vRoleName = "role:$roleName";
        foreach ($permissions as $permission) {
            if (lcfirst($permission) === 'read') {
                $availablePermissions['find'] = true;
                $availablePermissions['get'] = true;
                $availablePermissions['count'] = true;
                $classLevelPermissions['find'][$vRoleName] = true;
                $classLevelPermissions['get'][$vRoleName] = true;
                $classLevelPermissions['count'][$vRoleName] = true;
            } elseif (lcfirst($permission) === 'write') {
                $availablePermissions['create'] = true;
                $availablePermissions['update'] = true;
                $availablePermissions['delete'] = true;
                $classLevelPermissions['create'][$vRoleName] = true;
                $classLevelPermissions['update'][$vRoleName] = true;
                $classLevelPermissions['delete'][$vRoleName] = true;
            } elseif (lcfirst($permission) === 'add') {
                $availablePermissions['addField'] = true;
                $classLevelPermissions['addField'][$vRoleName] = true;
            } elseif (
                lcfirst($permission) === 'get' ||
                lcfirst($permission) === 'find' ||
                lcfirst($permission) === 'count' ||
                lcfirst($permission) === 'create' ||
                lcfirst($permission) === 'edit' ||
                lcfirst($permission) === 'update' ||
                lcfirst($permission) === 'delete' ||
                lcfirst($permission) === 'addField'
            ) {
                $fixPm = '';
                if (lcfirst($permission) === 'edit'):
                    $fixPm = 'update';
                else:
                    $fixPm = lcfirst($permission);
                endif;

                $availablePermissions[$fixPm] = true;
                $classLevelPermissions[$fixPm][$vRoleName] = true;
            }
        }

        foreach ($availablePermissions as $key => $del) {
            if ($del === false) {
                unset($classLevelPermissions[$key][$vRoleName]);
            }
            if ($key !== 'read' && $key !== 'write' && $key !== 'add') {
                unset($classLevelPermissions[$key]['*']);
            }
        }

        $data['classLevelPermissions'] = $classLevelPermissions;

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

    public static function RoleSyncPermission($credentials, $roleName, $roleHasPermissions, $schema, $options)
    {
        $res = ParseTools::array2Json([
            'status' => true
        ]);
        $listPermission = ['get', 'find', 'count', 'create', 'update', 'delete', 'addField'];
        $CLP = $schema['classLevelPermissions'];
        $available = 0;
        $availableAnotherRoles = 0;
        foreach ($CLP as $key => $clp) {
            foreach ($clp as $clpKey => $value) {
                if ($clpKey === "role:$roleName") {
                    $available = $available + 1;
                } elseif (strpos($clpKey, 'role:') !== false) {
                    $availableAnotherRoles = $availableAnotherRoles + 1;
                }
            }
            if (isset($clp["role:$roleName"])) {
            }
        }

        if ($available === 0) {
            return $res;
        }

        $roleNeedRemoved = 0;
        if (count($roleHasPermissions) >= 1) {
            foreach ($roleHasPermissions as $className => $permission) {
                if ($className !== $schema['className']) {
                    $roleNeedRemoved = $roleNeedRemoved + 1;
                };
            }
        } else {
            $roleNeedRemoved = $roleNeedRemoved + 1;
        }

        if ($roleNeedRemoved === 0) {
            return $res;
        }
        foreach ($listPermission as $permission) {
            unset($CLP[$permission]["role:$roleName"]);
            if (count($CLP[$permission]) == 0 && $availableAnotherRoles === 0) {
                $CLP[$permission]['*'] = true;
            }
        }

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

        $data = [
            'classLevelPermissions' => $CLP
        ];

        if ($database === '') {
            $url = sprintf("%s://%s:%s/schemas/%s", $protocol, $host, $port, $schema['className']);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/schemas/%s", $protocol, $host, $port, $schema['className']);
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
