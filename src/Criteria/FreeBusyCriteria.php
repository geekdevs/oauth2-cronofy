<?php
namespace Geekdevs\OAuth2\Client\Criteria;

use Geekdevs\OAuth2\Client\Model\FreeBusy;
use InvalidArgumentException;

/**
 * Class FreeBusyCriteria
 * @package Geekdevs\OAuth2\Client\Criteria
 */
class FreeBusyCriteria extends EventCriteria
{
    /**
     * @var string
     */
    private $status;

    /**
     * FreeBusyCriteria constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        if (isset($params['status'])) {
            if (FreeBusy::isValidStatus($params['status'])) {
                $this->status = $params['status'];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Invalid status "%s"',
                    $params['status']
                ));
            }

            throw new \RuntimeException('Status filter is not currently supported');
        }
    }
}
