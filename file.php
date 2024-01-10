<?php

require_once 'vendor/autoload.php';

// Use the EmailVerificationClient
use mslm\mslm\EmailVerificationClient;

// Initialize the client with your API key
$apiKey = 'your-api-key';
$emailVerificationClient = new EmailVerificationClient($apiKey);

// Example: Single email verification
$email = 'adeelusmani@mslm.io';
$result = $emailVerificationClient->singleVerify($email);

// Display the result
var_dump($result);
