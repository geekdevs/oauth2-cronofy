<?php
namespace Geekdevs\OAuth2\Client\Model;

use DateTime;
use Geekdevs\OAuth2\Client\Util\DateUtil;
use InvalidArgumentException;

/**
 * Class Event
 * @package Geekdevs\OAuth2\Client\Model
 */
class Event
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $summary;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $calendarId;

    /**
     * @var DateTime
     */
    private $startsAt;

    /**
     * @var DateTime
     */
    private $endsAt;

    /**
     * FreeBusy constructor.
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
        if (empty($data['event_uid'])) {
            throw new InvalidArgumentException('Invalid event_uid');
        }

        $this->uid         = $data['event_uid'];
        $this->calendarId  = $data['calendar_id'];
        $this->summary     = $data['summary'];
        $this->description = $data['description'];
        $this->startsAt    = DateUtil::createDateTime($data['start']);
        $this->endsAt      = DateUtil::createDateTime($data['end']);
    }

    /**
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCalendarId()
    {
        return $this->calendarId;
    }

    /**
     * @param string $calendarId
     */
    public function setCalendarId($calendarId)
    {
        $this->calendarId = $calendarId;
    }

    /**
     * @return DateTime
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * @param DateTime $startsAt
     */
    public function setStartsAt($startsAt)
    {
        $this->startsAt = $startsAt;
    }

    /**
     * @return DateTime
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * @param DateTime $endsAt
     */
    public function setEndsAt($endsAt)
    {
        $this->endsAt = $endsAt;
    }
}
