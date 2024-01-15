<?php

namespace mslm\mslm;

use GuzzleHttp\Client;

class EmailVerificationClient
{
    const BASE_URL = 'https://mslm.io';

    public function singleVerify($email, $apiKey) {
       // Prepare the URL with query parameters.
       $url = self::BASE_URL . '/api/sv/v1/';
       $qp = [
           'email' => $email,
           'apikey' => $apiKey,
       ];
       $url .= '?' . http_build_query($qp);

       // Make an HTTP request using GuzzleHttp.
       $client = new Client();
       $response = $client->get($url);

    return $response;
    }
}
