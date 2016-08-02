<?php
namespace Geekdevs\OAuth2\Client\Hydrator;

/**
 * Class ArrayHydrator
 * @package Geekdevs\OAuth2\Client\Hydrator
 */
class ArrayHydrator implements HydratorInterface
{
    /**
     * @param array $data
     *
     * @return array
     */
    public function hydrate(array $data)
    {
        return $data;
    }
}
