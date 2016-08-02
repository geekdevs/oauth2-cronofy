<?php
namespace Geekdevs\OAuth2\Client\Hydrator;

/**
 * Interface HydratorInterface
 * @package Geekdevs\OAuth2\Client\Hydrator
 */
interface HydratorInterface
{
    /**
     * @param array $data
     *
     * @return mixed
     */
    public function hydrate(array $data);
}
