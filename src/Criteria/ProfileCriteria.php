<?php
namespace Geekdevs\OAuth2\Client\Criteria;

/**
 * Class ProfileCriteria
 * @package Geekdevs\OAuth2\Client\Criteria
 */
class ProfileCriteria implements CriteriaInterface
{
    /**
     * ProfileCriteria constructor.
     *
     * @param array $params
     */
    public function __construct(array $params)
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
