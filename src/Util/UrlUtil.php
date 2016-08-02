<?php
namespace Geekdevs\OAuth2\Client\Util;

/**
 * Class UrlUtil
 * @package Geekdevs\OAuth2\Client\Util
 */
class UrlUtil
{
    /**
     * @param array $params
     *
     * @return string
     */
    public static function createQueryString(array $params)
    {
        $qs = http_build_query($params);

        //replaces array notation like ar[0] with ar[] as otherwise cronofy does not find it valid
        $qs = preg_replace('/\%5B\d+\%5D/', '%5B%5D', $qs);

        return $qs;
    }
}
