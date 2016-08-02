<?php
namespace Geekdevs\OAuth2\Client\Util;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Class DateUtil
 * @package Geekdevs\OAuth2\Client\Util
 */
class DateUtil
{
    /**
     * @param string|array $timestamp  Either timestamp in ISO8601 format or array(time, tzid)
     * @throws InvalidArgumentException
     * @return DateTime
     */
    public static function createDateTime($timestamp)
    {
        if (is_array($timestamp)) {
            if (empty($timestamp['time'])) {
                throw new InvalidArgumentException('Missing "time" argument for the timestamp');
            }

            if (empty($timestamp['tzid'])) {
                throw new InvalidArgumentException('Missing "tzid" argument for the timestamp');
            }

            $time = $timestamp['time'];
            $timezone = new DateTimeZone($timestamp['tzid']);

            if (!$timezone) {
                throw new InvalidArgumentException(sprintf(
                    'Cannot create DateTimeZone: invalid timezone "%s"', $timestamp['tzid']
                ));
            }
            $datetime = DateTime::createFromFormat(DateTime::ISO8601, $time, $timezone);
        } else {
            $datetime = DateTime::createFromFormat(DateTime::ISO8601, $timestamp);
        }

        if (!$datetime instanceof DateTime) {
            throw new InvalidArgumentException(sprintf(
                'Failed to create DateTime from the given input: "%s"',
                var_export($timestamp, true)
            ));
        }

        return $datetime;
    }
}
