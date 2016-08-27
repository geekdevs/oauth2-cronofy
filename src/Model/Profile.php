<?php
namespace Geekdevs\OAuth2\Client\Model;

use InvalidArgumentException;

/**
 * Class Profile
 * @package Geekdevs\OAuth2\Client\Model
 */
class Profile
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $providerName;

    /**
     * @var bool
     */
    private $connected = true;

    /**
     * @var string
     */
    private $relinkUrl;

    /**
     * Calendar constructor.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        if ($data) {
            $this->populateFromArray($data);
        }
    }

    /**
     * @param array $data
     */
    public function populateFromArray(array $data)
    {
        if (empty($data['profile_id'])) {
            throw new InvalidArgumentException('Invalid profile_id');
        }

        $this->id = $data['profile_id'];
        $this->name = $data['profile_name'];
        $this->providerName = $data['provider_name'];
        $this->connected = !empty($data['profile_connected']);
        $this->relinkUrl = isset($data['profile_relink_url']) ? $data['profile_relink_url'] : null;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @return boolean
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * @return string
     */
    public function getRelinkUrl()
    {
        return $this->relinkUrl;
    }
}
