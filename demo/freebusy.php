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

try {
    $freeBusyList = $provider->getFreeBusy($accessToken, new \Geekdevs\OAuth2\Client\Criteria\FreeBusyCriteria([
        'calendars' => [$_GET['calendar_id']],
        'toDate' => new \DateTime('+30days')
    ]));
} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
    var_dump($e->getMessage());
    var_dump($e->getResponseBody());
    die();
}
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<div class="container">
    <div class="row">
        <?php if (isset($freeBusyList)): ?>
            <h3>Events</h3>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Calendar ID</th>
                    <th>Starts At</th>
                    <th>Ends At</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($freeBusyList as $freeBusy): ?>
                    <tr>
                        <td><?=$freeBusy->getCalendarId();?></td>
                        <td><?=$freeBusy->getStartsAt()->format('c');?></td>
                        <td><?=$freeBusy->getEndsAt()->format('c');?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>