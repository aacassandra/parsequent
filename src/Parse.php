<?php

namespace Parsequent;

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
        'limit' => 10000,
        'skip' => 400,
        'order' => '',
        'keys' => '',
        'include' => [],
        'relation' => [],
        'masterKey' => false
    ])
    {
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
        'masterKey' => false
    ])
    {
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
        return ParseUsers::CreateUser(ParseHelpers::Credentials(), $username, $password, $options);
    }

    /**
     * Read User
     *
     * @param  string $objectId
     * @param  array $options
     * @return object
     */
    public static function ReadUser(string $objectId, array $options = [])
    {
        return ParseUsers::ReadUser(ParseHelpers::Credentials(), $objectId, $options);
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
        return ParseRoles::Create(ParseHelpers::Credentials(), $roleName, $options);
    }

    /**
     * Read Role
     *
     * @param  string $options['roleName]    All data will be displayed if the roleName is empty
     * @return object
     */
    public static function ReadRole(array $options = [
        'roleName' => '',
        'masterKey' => false
    ])
    {
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
        'data' => [],
        'masterKey' => false
    ])
    {
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
        return ParseRoles::RoleHasPermission(ParseHelpers::Credentials(), $objectId, $permissions, $options);
    }
}
