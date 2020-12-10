<?php

namespace Parsequent\Users;

use Illuminate\Support\Facades\Session;
use Parsequent\ParseHelpers;
use Parsequent\ParseTools;
use Parsequent\Roles\ParseRoles;

class ParseUsers
{
    public static function SignIn($credentials, $username = '', $password = '', $options = [])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = [
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey']),
            "X-Parse-Revocable-Session: 1"
        ];

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/login?username=%s&password=%s", $protocol, $host, $port, $username, $password);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/login?username=%s&password=%s", $protocol, $host, $port, $username, $password);
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
            session([$credentials['storageKey'] . '.user' => $res->output]);
        }
        return $res;
    }

    public static function SignOut($credentials)
    { {
            $protocol = $credentials['protocol'];
            $host = $credentials['host'];
            $port = $credentials['port'];
            $database = $credentials['database'];
            $headers = array(
                sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
                sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey']),
                sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? '')
            );

            if ($database === '') {
                $url = sprintf("%s://%s:%s/logout", $protocol, $host, $port);
            } else {
                $url = sprintf("%s://%s:%s/" . $database . "/logout", $protocol, $host, $port);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, '');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $output = json_decode(curl_exec($ch));
            $httpCode = curl_getinfo($ch);
            curl_close($ch);

            $res = ParseHelpers::responseHandler($httpCode, $output);
            if ($res->status) {
                session()->forget($credentials['storageKey'] . ".user");
            }
        }
    }

    public static function VerifyingEmails($credentials, $email = '', $options = [])
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

        if ($database === '') {
            $url = sprintf("%s://%s:%s/verificationEmailRequest", $protocol, $host, $port);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/verificationEmailRequest", $protocol, $host, $port);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email]));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }

    public static function PasswordReset($credentials, $email = '', $options = [])
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

        if ($database === '') {
            $url = sprintf("%s://%s:%s/requestPasswordReset", $protocol, $host, $port);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/requestPasswordReset", $protocol, $host, $port);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['email' => $email]));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }

    public static function ValidatingSessionTokens($credentials, $sessionToken = '', $options = [])
    {
        $protocol = $credentials['protocol'];
        $host = $credentials['host'];
        $port = $credentials['port'];
        $database = $credentials['database'];
        $headers = array(
            sprintf($credentials['headerAppID'] . ": %s", $credentials['appId']),
            sprintf($credentials['headerRestKey'] . ": %s", $credentials['restKey']),
            sprintf($credentials['headerSessionToken'] . ": %s", $sessionToken),
        );

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/users/me", $protocol, $host, $port);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/users/me", $protocol, $host, $port);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 150);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        return ParseHelpers::responseHandler($httpCode, $output);
    }

    public static function CreateUser($credentials, $username, $password, $options = [
        'role' => '',
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
            "X-mesosfer-Revocable-Session: 1",
            "Content-Type: application/json"
        );

        if (isset($options['masterKey']) && $options['masterKey'] === true) {
            array_push($headers, sprintf($credentials['headerMasterKey'] . ": %s", $credentials['masterKey']));
        }

        if ($database === '') {
            $url = sprintf("%s://%s:%s/users", $protocol, $host, $port);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/users", $protocol, $host, $port);
        }

        $options['data']['username'] = $username;
        $options['data']['password'] = $password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options['data']));
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $output = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch);
        curl_close($ch);

        $res = ParseHelpers::responseHandler($httpCode, $output);
        if ($res->status) {
            if (isset($options['role']) && $options['role'] !== '') {
                $UserHasRole = ParseRoles::Update($credentials, $options['role'], [
                    'users' => [
                        'AddRelation' => [$res->output->objectId]
                    ],
                    'masterKey' => $options['masterKey'] ?? false
                ]);
                if (!$UserHasRole) {
                    return $UserHasRole;
                }
            }
        }
        return $res;
    }

    public static function UpdateUser($credentials, $objectId = '', $options = [
        'role' => '',
        'data' => [],
        'masterKey' => false
    ])
    {
        $res = [];
        if (isset($data)) {
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
            } else {
                array_push($headers, sprintf($credentials['headerSessionToken'] . ": %s", session($credentials['storageKey'] . '.user')->sessionToken ?? ''));
            }

            if ($database === '') {
                $url = sprintf("%s://%s:%s/users/%s", $protocol, $host, $port, $objectId);
            } else {
                $url = sprintf("%s://%s:%s/" . $database . "/users/%s", $protocol, $host, $port, $objectId);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options['data']));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $output = json_decode(curl_exec($ch));
            $httpCode = curl_getinfo($ch);
            curl_close($ch);

            return ParseHelpers::responseHandler($httpCode, $output);

            $res = ParseHelpers::responseHandler($httpCode, $output);
            if (!$res->status) {
                return $res;
            }
        }

        if (isset($options['role']) && $options['role'] !== '') {
            $res = ParseRoles::Update($credentials, $options['role'], [
                'users' => [
                    'AddRelation' => [$objectId]
                ],
                'masterKey' => $options['masterKey'] ?? false
            ]);
            if (!$res) {
                return $res;
            }
        }
        return $res;
    }

    public static function DeleteUser($credentials, $objectId = '', $options = [
        'masterKey' => false
    ])
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
            $url = sprintf("%s://%s:%s/users/%s", $protocol, $host, $port, $objectId);
        } else {
            $url = sprintf("%s://%s:%s/" . $database . "/users/%s", $protocol, $host, $port, $objectId);
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
}
