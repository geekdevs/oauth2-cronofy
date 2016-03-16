<?php
/**
 * @var Geekdevs\OAuth2\Client\Provider\Cronofy $provider
 */
$provider = require 'bootstrap.inc.php';

//Check incoming params (sent by cronofy)
if (!isset($_GET['code'])) {
    throw new \RuntimeException('Code not received!');
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    throw new \RuntimeException('State does not match!');
}

//All good, lets get access token
$accessToken = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);

//Save token into the file
TokenUtils::storeAccessToken($accessToken);

header('Location: index.php');
exit();
