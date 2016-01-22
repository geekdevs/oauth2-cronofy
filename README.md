# Cronofy Provider for OAuth 2.0 Client

[![Build Status](https://travis-ci.org/geekdevs/oauth2-cronofy.png?branch=master)](https://travis-ci.org/geekdevs/oauth2-cronofy)

This package provides [Cronofy Calendar](https://www.cronofy.com/developers/api/) OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

This package is compliant with [PSR-1][], [PSR-2][], [PSR-4][], and [PSR-7][]. If you notice compliance oversights,
please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md
[PSR-7]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-7-http-message.md


## Requirements

The following versions of PHP are supported.

* PHP 5.5
* PHP 5.6
* PHP 7.0
* HHVM

## Installation

Add the following to your `composer.json` file.

```json
{
    "require": {
        "geekdevs/oauth2-cronofy": "dev-master"
    }
}
```

## Usage

### Authorization Code Flow

```php
session_start();

$provider = new Geekdevs\OAuth2\Client\Provider\Cronofy([
    'clientId'          => '{cronofy-app-id}',
    'clientSecret'      => '{cronofy-app-secret}',
    'redirectUri'       => 'https://example.com/callback-url'
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl([
        'scope' => ['read_account', '...', '...'],
    ]);
    $_SESSION['oauth2state'] = $provider->getState();
    
    echo '<a href="'.$authUrl.'">Log in with Cronofy!</a>';
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    echo 'Invalid state.';
    exit;

}

// Try to get an access token (using the authorization code grant)
$token = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

// Optional: Now you have a token you can look up a users profile data
try {

    // We got an access token, let's now get the account's details
    $account = $provider->getResourceOwner($token);

    // Use these details to create a new profile
    printf('Hello %s!', $account->getName());
    
    echo '<pre>';
    var_dump($account);
    # object(League\OAuth2\Client\Provider\CronofyAccount)#10 (1) { ...
    echo '</pre>';

} catch (Exception $e) {

    // Failed to get account details
    exit('Oh dear...');
}

echo '<pre>';
// Use this to interact with an API on the users behalf
var_dump($token->getToken());
# string(217) "CAADAppfn3msBAI7tZBLWg...

// Number of seconds until the access token will expire, and need refreshing
var_dump($token->getExpires());
# int(1436825866)
echo '</pre>';
```

### The CronofyAccount Entity

When using the `getResourceOwner()` method to obtain the account details, it will be returned as a `CronofyAccount` entity.

```php
$account = $provider->getResourceOwner($token);

$id = $account->getId();
var_dump($id);
# string(1) "acc_567236000909002"

$name = $account->getName();
var_dump($name);
# string(15) "Pavel Dubinin"

$email = $account->getEmail();
var_dump($email);
# string(15) "geekevs@gmail.com"

$timezone = $account->getDefaultTimezone();
var_dump($timezone);
# string(15) "Europe/London"
```

You can also get all the data from the Account node as a plain-old PHP array with `toArray()`.

```php
$accountData = $account->toArray();
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/geekdevs/oauth2-cronofy/blob/master/CONTRIBUTING.md) for details.


## Credits

- [Pavel Dubinin](https://github.com/geekdevs)
- [All Contributors](https://github.com/geekdevs/oauth2-cronofy/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/geekdevs/oauth2-cronofy/blob/master/LICENSE) for more information.