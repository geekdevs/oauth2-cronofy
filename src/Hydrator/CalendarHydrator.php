<?php
namespace Geekdevs\OAuth2\Client\Hydrator;

use Geekdevs\OAuth2\Client\Model\Calendar;

/**
 * Class CalendarHydrator
 * @package Geekdevs\OAuth2\Client\Hydrator
 */
class CalendarHydrator implements HydratorInterface
{
    /**
     * @param array $data
     *
     * @return Calendar
     */
    public function hydrate(array $data)
    {
        return new Calendar($data);
    }
}
