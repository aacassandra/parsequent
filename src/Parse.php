<?php

namespace Parsequent;

use Parsequent\Customs\ParseCustoms;
use Parsequent\Objects\ParseObjects;
use Parsequent\Queries\ParseQueries;
use Parsequent\Users\ParseUsers;
use Parsequent\ParseHelpers;

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

    /**
     * CountingObjects
     *
     * @param  string $className
     * @param  array $options
     * @return object
     */
    public static function CountingObjects(string $className, array $options = [])
    {
        return ParseQueries::CountingObjects(ParseHelpers::Credentials(), $className, $options);
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
        'masterKey' => false,
        'sessionToken' => ''
    ])
    {
        return ParseUsers::UpdateUser(ParseHelpers::Credentials(), $objectId, $data, $options);
    }

    public static function DeleteUser(string $objectId, array $options = [
        'masterKey' => false,
        'sessionToken' => ''
    ])
    {
        return ParseUsers::DeleteUser(ParseHelpers::Credentials(), $objectId, $options);
    }
}
