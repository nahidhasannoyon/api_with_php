<?php

function validateApiKey($apiKey)
{
    // todo: Replace 'your_secret_key' with the actual secret key you generated
    $validApiKey = 'your_secret_key';

    if ($apiKey !== $validApiKey) {
        throw new Exception("Invalid API key", 401);
    }
}

function validateSecurityCode($securityCode)
{
    // todo: Replace 'your_security_code' with the actual security code you want to use
    $validSecurityCode = 'your_security_code';

    if ($securityCode !== $validSecurityCode) {
        throw new Exception("Invalid security code", 401);
    }
}
