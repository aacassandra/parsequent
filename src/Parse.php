<?php

namespace Parsequent;

use Parsequent\Customs\ParseCustoms;
use Parsequent\Objects\ParseObjects;
use Parsequent\Queries\ParseQueries;
use Parsequent\Users\ParseUsers;

class Parse
{
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
        return ParseObjects::Create(config('database.connections.parse'), $className, $data, $options);
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
        return ParseCustoms::Read(config('database.connections.parse'), $className, $options);
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
        return ParseCustoms::Update(config('database.connections.parse'), $className, $data, $options);
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
        return ParseCustoms::Delete(config('database.connections.parse'), $className, $options);
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
        return ParseObjects::Batch(config('database.connections.parse'), $data, $options);
    }

    /**
     * CountingObjects
     *
     * @param  string $className
     * @param  array $options
     * @return object
     */
    public static function CountingObjects(string $className, array $options = [])
    {
        return ParseQueries::CountingObjects(config('database.connections.parse'), $className, $options);
    }

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
        return ParseUsers::SignUp(config('database.connections.parse'), $username, $password, $data, $options);
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
        return ParseUsers::SignIn(config('database.connections.parse'), $username, $password, $options);
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
        return ParseUsers::VerifyingEmails(config('database.connections.parse'), $email, $options);
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
        return ParseUsers::PasswordReset(config('database.connections.parse'), $email, $options);
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
        return ParseUsers::ValidatingSessionTokens(config('database.connections.parse'), $sessionToken, $options);
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
        'masterKey' => false,
        'sessionToken' => ''
    ])
    {
        return ParseUsers::UpdateUser(config('database.connections.parse'), $objectId, $data, $options);
    }

    public static function DeleteUser(string $objectId, array $options = [
        'masterKey' => false,
        'sessionToken' => ''
    ])
    {
        return ParseUsers::DeleteUser(config('database.connections.parse'), $objectId, $options);
    }
}
