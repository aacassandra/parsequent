<?php

namespace Parsequent;

use Illuminate\Support\Arr;
use Parsequent\Customs\ParseCustoms;
use Parsequent\Objects\ParseObjects;
use Parsequent\Queries\ParseQueries;
use Parsequent\Roles\ParseRoles;
use Parsequent\Users\ParseUsers;
use Parsequent\ParseHelpers;

class Parse
{
    // Parse Object
    /**
     * Create
     *
     * @param  string $className
     * @param  array $data
     * @param  array $options
     * @return object
     */
    public static function Create(string $className, array $data, array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseObjects::Create(ParseHelpers::Credentials(), $className, $data, $options);
    }

    /**
     * Read
     *
     * @param  string $className
     * @param  array $options
     * @return object
     */
    public static function Read(string $className, array $options = [
        'objectId' => '',
        'where' => [],
        'orWhere' => [],
        'limit' => 0,
        'skip' => 0,
        'order' => '',
        'keys' => '',
        'include' => [],
        'relation' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['objectId', 'where', 'orWhere', 'limit', 'skip', 'order', 'keys', 'include', 'relation', 'masterKey']);
        return ParseCustoms::Read(ParseHelpers::Credentials(), $className, $options);
    }

    /**
     * Update
     *
     * @param  string $className
     * @param  array $data
     * @param  array $options
     * @return object
     */
    public static function Update(string $className, array $data, array $options = [
        'objectId' => '',
        'where' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['objectId', 'where', 'masterKey']);
        return ParseCustoms::Update(ParseHelpers::Credentials(), $className, $data, $options);
    }

    /**
     * Delete
     *
     * @param  string $className
     * @param  array $options
     * @return object
     */
    public static function Delete(string $className, array $options = [
        'objectId' => '',
        'where' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['objectId', 'where', 'masterKey']);
        return ParseCustoms::Delete(ParseHelpers::Credentials(), $className, $options);
    }

    /**
     * Batch
     *
     * @param  array $data
     * @param  array $options
     * @return object
     */
    public static function Batch(array $data, array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseObjects::Batch(ParseHelpers::Credentials(), $data, $options);
    }

    // Parse Queries
    /**
     * Counting Objects
     *
     * @param  string $className
     * @param  array $options
     * @return object
     */
    public static function CountingObjects(string $className, array $options = [
        'where' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['where', 'masterKey']);
        return ParseQueries::CountingObjects(ParseHelpers::Credentials(), $className, $options);
    }

    // Parse Users
    /**
     * Sign In
     *
     * @param  string $username
     * @param  string $password
     * @param  array $options
     * @return object
     */
    public static function SignIn(string $username, string $password, array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseUsers::SignIn(ParseHelpers::Credentials(), $username, $password, $options);
    }

    /**
     * Sign Out
     *
     * @return object
     */
    public static function SignOut()
    {
        return ParseUsers::SignOut(ParseHelpers::Credentials());
    }

    /**
     * Verifying Emails
     *
     * @param  string $email
     * @param  array $options
     * @return object
     */
    public static function VerifyingEmails(string $email, array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseUsers::VerifyingEmails(ParseHelpers::Credentials(), $email, $options);
    }

    /**
     * Requesting A Password Reset
     *
     * @param  string $email
     * @param  array $options
     * @return object
     */
    public static function PasswordReset(string $email, array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseUsers::PasswordReset(ParseHelpers::Credentials(), $email, $options);
    }

    /**
     * Validating Session Tokens
     *
     * @param  string $sessionToken
     * @param  array $options
     * @return object
     */
    public static function ValidatingSessionTokens(string $sessionToken, array $options = [
        'include' => [],
        'relation' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['include', 'relation', 'masterKey']);
        return ParseUsers::ValidatingSessionTokens(ParseHelpers::Credentials(), $sessionToken, $options);
    }

    /**
     * Create User
     *
     * @param  string $username
     * @param  string $password
     * @param  array $options
     * @return object
     */
    public static function CreateUser(string $username, string $password, array $options = [
        'role' => '',
        'data' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['role', 'data', 'masterKey']);
        return ParseUsers::CreateUser(ParseHelpers::Credentials(), $username, $password, $options);
    }

    /**
     * Read User
     *
     * @param  array $options
     * @return object
     */
    public static function ReadUser(array $options = [
        'objectId' => '',
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['objectId', 'masterKey']);
        return ParseUsers::ReadUser(ParseHelpers::Credentials(), $options);
    }

    /**
     * Updating Users
     *
     * @param  string $objectId
     * @param  array $data
     * @param  array $options
     * @return object
     */
    public static function UpdateUser(string $objectId, array $options = [
        'role' => '',
        'data' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['role', 'data', 'masterKey']);
        return ParseUsers::UpdateUser(ParseHelpers::Credentials(), $objectId, $options);
    }

    /**
     * Delete User
     *
     * @param  string $objectId
     * @param  array $options
     * @return object
     */
    public static function DeleteUser(string $objectId, array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseUsers::DeleteUser(ParseHelpers::Credentials(), $objectId, $options);
    }

    // Parse Roles
    /**
     * Create Role
     *
     * @param  string $roleName
     * @param  array $options
     * @return object
     */
    public static function CreateRole(string $roleName, array $options = [
        'acl' => [],
        'users' => [],
        'roles' => [],
        'data' => [],
        'permissions' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['acl', 'users', 'roles', 'data', 'permissions', 'masterKey']);
        return ParseRoles::Create(ParseHelpers::Credentials(), $roleName, $options);
    }

    /**
     * Read Role
     *
     * @param  string $options['roleName]    All data will be displayed if the roleName is empty
     * @return object
     */
    public static function ReadRole(array $options = [
        'objectId' => '',
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['objectId', 'masterKey']);
        return ParseRoles::Read(ParseHelpers::Credentials(), $options);
    }

    /**
     * Update Role
     *
     * @param  string $objectId
     * @param  array $options
     * @return object
     */
    public static function UpdateRole(string $objectId, array $options = [
        'acl' => [],
        'users' => [
            'RemoveRelation' => [],
            'AddRelation' => []
        ],
        'roles' => [
            'RemoveRelation' => [],
            'AddRelation' => []
        ],
        'permissions' => [],
        'data' => [],
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['acl', 'users', 'roles', 'permissions', 'data', 'masterKey']);
        return ParseRoles::Update(ParseHelpers::Credentials(), $objectId, $options);
    }

    /**
     * Delete Role
     *
     * @param  string $objectId
     * @param  array $options
     * @return object
     */
    public static function DeleteRole(string $objectId, array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseRoles::Delete(ParseHelpers::Credentials(), $objectId, $options);
    }

    /**
     * Role Has Permission
     *
     * @param  string $objectId
     * @param  array $options
     * @return object
     */
    public static function RoleHasPermission(string $objectId, array $permissions = [], array $options = [
        'masterKey' => false
    ])
    {
        $options = Arr::only($options, ['masterKey']);
        return ParseRoles::RoleHasPermission(ParseHelpers::Credentials(), $objectId, $permissions, $options);
    }
}
