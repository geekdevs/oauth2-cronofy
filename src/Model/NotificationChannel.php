<?php
namespace Geekdevs\OAuth2\Client\Model;

/**
 * Class NotificationChannel
 * @package Geekdevs\OAuth2\Client\Model
 */
class NotificationChannel
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $callbackUrl;

    /**
     * @var array
     */
    private $filters = [];

    public function __construct(array $data = [])
    {
        if ($data) {
            $this->populateFromArray($data);
        }
    }

    /**
     * @param array $data
     */
    private function populateFromArray(array $data)
    {
        if (empty($data['channel_id'])) {
            throw new \InvalidArgumentException('Invalid channel_id');
        }

        if (empty($data['callback_url'])) {
            throw new \InvalidArgumentException('Invalid callback_url');
        }

        $this->id = $data['channel_id'];
        $this->callbackUrl = $data['callback_url'];
        $this->filters = !empty($data['filters']) ? (array)$data['filters'] : [];
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
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }
}
