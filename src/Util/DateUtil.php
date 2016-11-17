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
                    'Cannot create DateTimeZone: invalid timezone "%s"',
                    $timestamp['tzid']
                ));
            }
        } else {
            $time = $timestamp;
            $timezone = new DateTimeZone('Z');
        }

        $usedFormat = null;
        $dateTime = self::createFromFormats(['Y-m-d', DateTime::ISO8601], $time, $timezone, $usedFormat);
        if ($usedFormat === 'Y-m-d') {
            $dateTime->setTime(0, 0, 0);
        }

        if (!$dateTime instanceof DateTime) {
            throw new InvalidArgumentException(sprintf(
                'Failed to create DateTime from the given input: "%s"',
                var_export($timestamp, true)
            ));
        }

        return $dateTime;
    }

    /**
     * Tries to create datetime from one of the given formats (stops when it finds one that matches the input).
     * Otherwise works identical to DateTime::createFromFormat
     *
     * @param array             $formats
     * @param                   $time
     * @param DateTimeZone|null $timezone
     * @param string            $usedFormat Output variable holding format which was used
     *
     * @return DateTime|false
     */
    public static function createFromFormats(array $formats, $time, DateTimeZone $timezone = null, &$usedFormat = null)
    {
        foreach ($formats as $format) {
            if ($timezone) {
                $datetime = DateTime::createFromFormat($format, $time, $timezone);
                if ($datetime) {
                    $datetime->setTimezone($timezone);
                }
            } else {
                $datetime = DateTime::createFromFormat($format, $time);
            }

            if ($datetime instanceof DateTime) {
                $usedFormat = $format;

                return $datetime;
            }
        }

        return false;
    }
}
