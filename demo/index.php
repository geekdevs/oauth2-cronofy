<?php
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

    //Make some fun
    $owner = $provider->getResourceOwner($accessToken);

    echo '<h3>Owner Info</h3>';
    var_dump($owner->toArray());
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
