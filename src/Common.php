<?php

namespace mslm\mslm;

class Common
{
    protected static $http_client;
    protected static $user_agent;
    protected static $base_url;
    protected static $apikey;

    public static function requestOptions($opts = [])
    {
        self::$http_client = $opts['http_client'] ?? null;
        self::$user_agent = $opts['user_agent'] ?? null;
        self::$base_url = $opts['base_url'] ?? null;
        self::$apikey = $opts['apikey'] ?? null;

        return $opts;
    }
}
