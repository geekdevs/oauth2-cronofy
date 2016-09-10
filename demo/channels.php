<?php
/**
 * @var Geekdevs\OAuth2\Client\Provider\Cronofy $provider
 */
$provider = require 'bootstrap.inc.php';
$searchToken = isset($_GET['token']) ? $_GET['token'] : null;
$accessToken = null;
$tokensHtml = '';

$accessToken = TokenUtils::findStoredAccessToken($searchToken);

if (!$accessToken) {
    die("Token was not found!");
}

$channels = $provider->getNotificationChannels($accessToken);
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<div class="container">
    <div class="row">
        <?php if (isset($channels)): ?>
            <?php var_dump(iterator_to_array($channels));die(); ?>
        <?php endif; ?>
    </div>
</div>