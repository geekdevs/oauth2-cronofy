<?php
$provider = require 'bootstrap.inc.php';

$authorizationUrl = $provider->getAuthorizationUrl([
    'scope' => ['read_account', 'read_events']
]);

//+1 security (will be checked on callback from cronofy)
$_SESSION['oauth2state'] = $provider->getState();
?>

<a href="<?=$authorizationUrl;?>">Connect to Cronofy</a>