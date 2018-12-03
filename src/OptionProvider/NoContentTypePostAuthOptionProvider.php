<?php
namespace Geekdevs\OAuth2\Client\OptionProvider;

use League\OAuth2\Client\OptionProvider\PostAuthOptionProvider;

/**
 * Class NoContentTypePostAuthOptionProvider
 * @package Geekdevs\OAuth2\Client\OptionProvider
 */
class NoContentTypePostAuthOptionProvider extends PostAuthOptionProvider
{
    /**
     * @param string $method
     * @param array  $params
     *
     * @return array
     */
    public function getAccessTokenOptions($method, array $params)
    {
        $options = parent::getAccessTokenOptions($method, $params);

        unset($options['headers']['content-type']);

        return $options;
    }
}
