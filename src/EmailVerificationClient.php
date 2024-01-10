<?php

namespace mslm\mslm;

use GuzzleHttp\Client as GuzzleHttpClient;

class ReqOpts
{
    public $http;
    public $baseUrl;
    public $userAgent;
    public $apiKey;
    public $context;
}

class SingleVerifyReqOpts
{
    public $disableUrlEncode;
    public $reqOpts;

    public function __construct()
    {
        $this->reqOpts = new ReqOpts();
    }
}

class SingleVerifyResp
{
    public $email;
    public $username;
    public $domain;
    public $malformed;
    public $suggestion;
    public $status;
    public $hasMailbox;
    public $acceptAll;
    public $disposable;
    public $free;
    public $role;
    public $mx;

    public function __construct()
    {
        $this->mx = [];
    }
}

class SingleVerifyRespMx
{
    public $host;
    public $pref;
}

class SingleVerifyRespMxWrap extends \ArrayObject
{
    public function __toString()
    {
        return json_encode($this->getArrayCopy());
    }
}

class Client
{
    public function prepareOpts($opt)
    {
        if ($opt === null) {
            return new ReqOpts();
        }

        $httpC = $opt->http ?? null;
        $baseUrl = $opt->baseUrl ?? null;
        $userAgent = $opt->userAgent ?? null;
        $apiKey = $opt->apiKey ?? null;
        $context = $opt->context ?? null;

        return new ReqOpts($httpC, $baseUrl, $userAgent, $apiKey, $context);
    }
    
    public function setHttpClient($guzzle_opts)
    {
        $http = new GuzzleHttpClient($guzzle_opts);
        
        return $http;
    }
    
    public function setBaseUrl($baseUrlStr)
    {
        $baseUrl = $baseUrlStr;
        
        return $baseUrl;
    }

    public function setUserAgent($userAgentStr)
    {
        $userAgent = $userAgentStr;
        
        return $userAgent;
    }

    public function prepareUrl($urlPath, $qp, $opt)
    {
        $tUrl = $opt->baseUrl->withPath($urlPath);

        $tUrlQp = [];
        foreach ($qp as $k => $v) {
            $tUrlQp[$k] = $v;
        }
        $tUrlQp['apikey'] = $opt->apiKey;
        $tUrl = $tUrl->withQuery(http_build_query($tUrlQp));

        return $tUrl;
    }

    public function reqAndResp($method, $tUrl, $data, $respData, $opt)
    {
        $opts = [
            'http' => [
                'method' => $method,
                'header' => 'User-Agent: ' . $opt->userAgent,
                'content' => $data,
            ],
        ];
        $context = stream_context_create($opts);
        $response = file_get_contents($tUrl, false, $context);

        if ($response === false) {
            throw new \Exception("Request failed");
        }

        $decodedResponse = json_decode($response);
        if ($decodedResponse === null) {
            throw new \Exception("JSON decoding failed");
        }

        foreach ($decodedResponse as $key => $value) {
            $respData->$key = $value;
        }
    }
}

class EmailVerificationClient
{
    const DEFAULT_BASE_URL = 'https://mslm.io';
    const DEFAULT_USER_AGENT = 'mslm/php/1.0';
    const REQUEST_TIMEOUT_DEFAULT = 2; // seconds

    private $client;
    protected $http_client;

    public function __construct($apiKey = null)
    {
        $guzzle_opts = [
            'http_errors' => false,
            // 'headers' => $this->buildHeaders(),
            'timeout' => $settings['timeout'] ?? self::REQUEST_TIMEOUT_DEFAULT
        ];

        $this->client = new Client();
        $this->client->prepareOpts(new ReqOpts());
        $this->client->setHttpClient($guzzle_opts);
        $this->client->setBaseUrl(self::DEFAULT_BASE_URL);
        $this->client->setUserAgent(self::DEFAULT_USER_AGENT);
        $this->client->prepareUrl(self::DEFAULT_BASE_URL, [], new ReqOpts());
        $this->client->setApiKey($apiKey);
    }

    public function singleVerify($emailAddr, $opts = [])
    {
        $opt = new SingleVerifyReqOpts();
        if (!empty($opts)) {
            $opt = array_merge($opt, end($opts));
        }
        $opt->reqOpts = $this->client->prepareOpts($opt->reqOpts);

        if ($opt->disableUrlEncode !== null && !$opt->disableUrlEncode) {
            $emailAddr = urlencode($emailAddr);
        }
        
        $qp = ['email' => $emailAddr];
        $tUrl = $this->client->prepareUrl('api/sv/v1', $qp, $opt->reqOpts);        

        $svResp = new SingleVerifyResp();
        $this->client->reqAndResp('GET', $tUrl, null, $svResp, $opt->reqOpts);

        return $svResp;
    }

    public function setApiKey($apiKey)
    {
        $this->client->setApiKey($apiKey);
    }
}
