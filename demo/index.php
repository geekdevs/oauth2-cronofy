<?php
use Geekdevs\OAuth2\Client\Criteria;

/**
 * @var Geekdevs\OAuth2\Client\Provider\Cronofy $provider
 */
$provider = require 'bootstrap.inc.php';
use Somoza\Psr7\OAuth2Middleware;
use League\OAuth2\Client\Token\AccessToken;

$searchToken = isset($_GET['token']) ? $_GET['token'] : null;
$accessToken = null;
$tokensHtml = '';

$accessToken = TokenUtils::findStoredAccessToken($searchToken);

if ($accessToken) {

    //Callback to store updated token when it changes
    $provider->setTokenCallback(function(AccessToken $token, AccessToken $oldToken = null) {
        echo "<p>Updated token!</p>";

        if ($oldToken) {
            TokenUtils::removeStoredAccessToken($oldToken->getToken());
        }
        TokenUtils::storeAccessToken($token);
    });

    //Get account info
    echo '<h3>Account Info</h3>';
    $account = $provider->getAccount($accessToken);
    var_dump($account);

    //Get profiles
    echo '<h3>Profiles</h3>';
    $profiles = $provider->getProfiles(null, $accessToken);
    var_dump($profiles);

    //Get calendars
    echo '<h3>Calendars</h3>';
    $calendars = $provider->getCalendars(null, $accessToken);
    var_dump($calendars);

    die();
}


$accessTokens = TokenUtils::getStoredAccessTokens();
$tokensHtml = '';
foreach ($accessTokens as $accessToken) {
    $tokensHtml .= sprintf('<li><a href="?token=%s">%s</a></li>', $accessToken->getToken(), $accessToken->getToken());
}
?>

<h2>Tokens: </h2>
<ul>
    <?=$tokensHtml;?>
</ul>

<p><a href="connect.php">Add New Token</a></p>
