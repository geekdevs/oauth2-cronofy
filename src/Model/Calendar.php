<?php
namespace Geekdevs\OAuth2\Client\Model;

use InvalidArgumentException;

/**
 * Class Calendar
 * @package Geekdevs\OAuth2\Client\Model
 */
class Calendar
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
     * @var string
     */
    private $profileId;

    /**
     * @var string
     */
    private $profileName;

    /**
     * @var bool
     */
    private $readonly = false;

    /**
     * @var bool
     */
    private $deleted = false;

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
        if (empty($data['calendar_id'])) {
            throw new InvalidArgumentException('Invalid calendar_id');
        }

        $this->id = $data['calendar_id'];
        $this->name = $data['calendar_name'];
        $this->providerName = $data['provider_name'];
        $this->profileId = $data['profile_id'];
        $this->profileName = $data['profile_name'];
        $this->readonly = !empty($data['calendar_readonly']);
        $this->deleted = !empty($data['calendar_deleted']);
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
     * @return string
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return string
     */
    public function getProfileName()
    {
        return $this->profileName;
    }

    /**
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }
}
