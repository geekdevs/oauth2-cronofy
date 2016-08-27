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
    $events = $provider->getEvents($accessToken, new \Geekdevs\OAuth2\Client\Criteria\EventCriteria([
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
        <?php if (isset($events)): ?>
            <h3>Events</h3>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Calendar ID</th>
                    <th>Starts At</th>
                    <th>Ends At</th>
                    <th>Summary</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($events as $event): ?>
                    <?php $uid = $event->getUid(); ?>
                    <tr>
                        <td><?=$uid;?></td>
                        <td><?=$event->getCalendarId();?></td>
                        <td><?=$event->getStartsAt()->format('c');?></td>
                        <td><?=$event->getEndsAt()->format('c');?></td>
                        <td><?=$event->getSummary();?></td>
                        <td><?=$event->getDescription();?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>