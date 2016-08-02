<?php
namespace Geekdevs\OAuth2\Client\Hydrator;
use Geekdevs\OAuth2\Client\Model\FreeBusy;

/**
 * Class FreeBusyHydrator
 * @package Geekdevs\OAuth2\Client\Hydrator
 */
class FreeBusyHydrator implements HydratorInterface
{
    /**
     * @param array $data
     *
     * @return FreeBusy
     */
    public function hydrate(array $data)
    {
        return new FreeBusy($data);
    }
}
