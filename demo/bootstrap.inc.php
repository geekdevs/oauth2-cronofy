<?php
ini_set('display_errors', 1);

require '../vendor/autoload.php';
require 'lib.inc.php';

if (file_exists('config.inc.php')) {
    $config = include 'config.inc.php';
} else {
    throw new \RuntimeException('Missing config.inc.php file, see example config at config.inc.php.dist');
}

session_start();

return new Geekdevs\OAuth2\Client\Provider\Cronofy($config);