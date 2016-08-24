<?php
namespace Geekdevs\OAuth2\Client\Hydrator;

use Geekdevs\OAuth2\Client\Model\Profile;

/**
 * Class ProfileHydrator
 * @package Geekdevs\OAuth2\Client\Hydrator
 */
class ProfileHydrator implements HydratorInterface
{
    /**
     * @param array $data
     *
     * @return Profile
     */
    public function hydrate(array $data)
    {
        return new Profile($data);
    }
}
