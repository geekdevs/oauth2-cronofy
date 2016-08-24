<?php
namespace Geekdevs\OAuth2\Client\Model;

use InvalidArgumentException;

/**
 * Class Account
 * @package Geekdevs\OAuth2\Client\Model
 */
class Account
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
    private $email;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $scope;

    /**
     * @var string
     */
    private $defaultTimezoneName;

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
        if (empty($data['account_id'])) {
            throw new InvalidArgumentException('Invalid account_id');
        }

        $this->id = $data['account_id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->type = !empty($data['type']) ? $data['type'] : 'account';
        $this->scope = !empty($data['scope']) ? $data['scope'] : null;
        $this->defaultTimezoneName = $data['default_tzid'];
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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getDefaultTimezoneName()
    {
        return $this->defaultTimezoneName;
    }
}
