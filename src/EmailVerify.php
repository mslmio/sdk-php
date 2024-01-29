<?php

namespace mslm\mslm;

use GuzzleHttp\Client;

class EmailVerify
{    
    protected $http_client;
    public $user_agent;
    public $base_url;
    public $api_key;

    const BASE_URL = 'https://mslm.io';
    const USER_AGENT = 'mslm/php/1.0';

    public function __construct($opts = [])
    {
        $new_http_client = new Client();

        $settings = Common::requestOptions($opts);

        $this->http_client = $settings['http_client'] ?? $new_http_client;
        $this->user_agent = $settings['user_agent'] ?? self::USER_AGENT;
        $this->base_url = $settings['base_url'] ?? self::BASE_URL;
        $this->api_key = $settings['api_key'] ?? null;
    }

    public function single_verify($email, $apiKey, $opts = [])
    {
        $http_client = $opts['http_client'] ?? $this->http_client;
        $user_agent = $opts['user_agent'] ?? $this->user_agent;
        $base_url = $opts['base_url'] ?? $this->base_url;
        $apiKey =  $apiKey ?? $this->api_key;

       $url = $base_url . '/api/sv/v1/';
       $qp = [
           'email' => $email,
           'apikey' => $apiKey,
       ];
       $url .= '?' . http_build_query($qp);

       echo $url;

       $response = $http_client->get($url);

    return $response;
    }
}
