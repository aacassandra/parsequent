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
    public static function Create(string $className, array $data, array $options = [])
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
    public static function Read(string $className, array $options = [])
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
    public static function Update(string $className, array $data, array $options = [])
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
    public static function Delete(string $className, array $options = [])
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
    public static function Batch(array $data, array $options = [])
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
    public static function CountingObjects(string $className, array $options = [])
    {
        return ParseQueries::CountingObjects(ParseHelpers::Credentials(), $className, $options);
    }

    // Parse Users
    /**
     * SignUp
     *
     * @param  string $username
     * @param  string $password
     * @param  array $data
     * @param  array $options
     * @return object
     */
    public static function SignUp(string $username, string $password, array $data, array $options = [])
    {
        return ParseUsers::SignUp(ParseHelpers::Credentials(), $username, $password, $data, $options);
    }

    /**
     * SignIn
     *
     * @param  string $username
     * @param  string $password
     * @param  array $options
     * @return object
     */
    public static function SignIn(string $username, string $password, array $options = [])
    {
        return ParseUsers::SignIn(ParseHelpers::Credentials(), $username, $password, $options);
    }

    /**
     * Verifying Emails
     *
     * @param  string $email
     * @param  array $options
     * @return object
     */
    public static function VerifyingEmails(string $email, array $options = [])
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
    public static function PasswordReset(string $email, array $options = [])
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
    public static function ValidatingSessionTokens(string $sessionToken, array $options = [])
    {
        return ParseUsers::ValidatingSessionTokens(ParseHelpers::Credentials(), $sessionToken, $options);
    }

    /**
     * Updating Users
     *
     * @param  string $objectId
     * @param  array $data
     * @param  array $options
     * @return object
     */
    public static function UpdateUser(string $objectId, array $data, array $options = [
        'masterKey' => false
    ])
    {
        return ParseUsers::UpdateUser(ParseHelpers::Credentials(), $objectId, $data, $options);
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
            'RevmoeRelation' => [],
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
    public static function DeleteRole(string $objectId, $options = [])
    {
        return ParseRoles::Delete(ParseHelpers::Credentials(), $objectId, $options);
    }
}
