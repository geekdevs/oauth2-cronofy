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
    $account = $provider->getAccount($accessToken);

    /**
     * Get profiles
     * @var \Geekdevs\OAuth2\Client\Model\Profile[] $profiles
     */
    $profiles = $provider->getProfiles($accessToken);

    /**
     * Get calendars
     * @var \Geekdevs\OAuth2\Client\Model\Calendar[] $calendars
     */
    $calendars = $provider->getCalendars($accessToken);

    /**
     * Get notification channels
     * @var \Geekdevs\OAuth2\Client\Model\NotificationChannel[] $channels
     */
    $channels = $provider->getNotificationChannels($accessToken);
}
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<div class="container">
    <div class="row">
        <?php if (isset($account)): ?>
            <h3>Account</h3>
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td><?=$account->getId();?></td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td><?=$account->getName();?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?=$account->getEmail();?></td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td><?=$account->getType();?></td>
                </tr>
                <tr>
                    <th>Scope</th>
                    <td><?=$account->getScope();?></td>
                </tr>
                <tr>
                    <th>Default Timezone</th>
                    <td><?=$account->getDefaultTimezoneName();?></td>
                </tr>
            </table>
        <?php endif; ?>

        <?php if (isset($profiles)): ?>
        <h3>Profiles</h3>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Provider Name</th>
                <th>Connected</th>
                <th>Relink URL</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($profiles as $profile): ?>
                <?php $pid = $profile->getId(); ?>
                <tr>
                    <td><?=$pid;?></td>
                    <td><?=$profile->getName();?></td>
                    <td><?=$profile->getProviderName();?></td>
                    <td><?=$profile->isConnected() ? 'yes' : 'no';?></td>
                    <td><?=$profile->getRelinkUrl();?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if (isset($calendars)): ?>
            <h3>Calendars</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Profile ID</th>
                        <th>Profile Name</th>
                        <th>Provider Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($calendars as $calendar): ?>
                    <?php $cid = $calendar->getId(); ?>
                    <tr>
                        <td>
                            <a href="events.php?calendar_id=<?=$cid;?>&token=<?=$searchToken;?>">
                                <?=$cid;?>
                            </a>
                        </td>
                        <td><?=$calendar->getName();?></td>
                        <td><?=$calendar->getProfileId();?></td>
                        <td><?=$calendar->getProfileName();?></td>
                        <td><?=$calendar->getProviderName();?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if (isset($channels)): ?>
            <h3>Channels</h3>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Callback URL</th>
                    <th>Filters</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($channels as $channel): ?>
                        <tr>
                            <td><?=$channel->getId();?></td>
                            <td><?=$channel->getCallbackUrl();?></td>
                            <td><?=json_encode($channel->getFilters());?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>


<?php
if ($accessToken) {
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
