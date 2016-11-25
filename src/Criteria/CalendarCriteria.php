<?php
namespace Geekdevs\OAuth2\Client\Criteria;

/**
 * Class CalendarCriteria
 * @package Geekdevs\OAuth2\Client\Criteria
 */
class CalendarCriteria implements CriteriaInterface
{
    /**
     * CalendarCriteria constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
    }

    /**
     * @return array
     */
    public function toRaw()
    {
        return [];
    }
}
