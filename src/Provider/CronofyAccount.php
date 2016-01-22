<?php
namespace Geekdevs\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Represents Cronofy resource owner for use with the CronofyProvider.
 */
class CronofyAccount implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $account;

    /**
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->account = $response['account'];
    }

    /**
     * Returns the identifier of the authorized resource owner.
     *
     * @return string (e.g. acc_90d15gb23e5a094ffe133954)
     */
    public function getId()
    {
        return $this->account['account_id'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->account['name'];
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->account['email'];
    }

    /**
     * @return string
     */
    public function getDefaultTimezone()
    {
        return $this->account['default_tzid'];
    }

    /**
     * Returns the raw resource owner response, e.g.:
     *
     *  [
     *     "account_id" => "acc_90d15gb23e5a094ffe133954"
     *     "email" =>"janed@company.com",
     *     "name" => "Jane Doe",
     *     "default_tzid" => "Europe/London"
     *  ]
     *
     * @return array
     */
    public function toArray()
    {
        return $this->account;
    }
}
