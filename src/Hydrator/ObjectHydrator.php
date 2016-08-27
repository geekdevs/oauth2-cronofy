<?php
namespace Geekdevs\OAuth2\Client\Hydrator;

use RuntimeException;

/**
 * Class ObjectHydrator
 * @package Geekdevs\OAuth2\Client\Hydrator
 */
class ObjectHydrator implements HydratorInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * ObjectHydrator constructor.
     *
     * @param string $className
     */
    public function __construct($className)
    {
        if (!class_exists($className)) {
            throw new RuntimeException(sprintf(
                'Unknown class %s provided to ObjectHydrator',
                $className
            ));
        }

        $this->className = $className;
    }

    /**
     * @param array $data
     *
     * @return object
     */
    public function hydrate(array $data)
    {
        $className = $this->className;

        return new $className($data);
    }
}
