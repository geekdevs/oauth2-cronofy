<?php
namespace Geekdevs\OAuth2\Client\Model;

use DateTime;
use Geekdevs\OAuth2\Client\Util\DateUtil;
use InvalidArgumentException;

/**
 * Class FreeBusy
 * @package Geekdevs\OAuth2\Client\Model
 */
class FreeBusy
{
    /**
     * Free/busy statuses:
     */
    //the user is probably busy for this period of time
    const STATUS_TENTATIVE  = 'tentative';
    //the user is busy for this period of time
    const STATUS_BUSY       = 'busy';
    //the user is free for this period of time
    const STATUS_FREE       = 'free';
    //the status of the period is unknown (is not expected to be used)
    const STATUS_UNKNOWN    = 'unknown';

    /**
     * @var array
     */
    private static $statuses = [
        self::STATUS_TENTATIVE,
        self::STATUS_BUSY,
        self::STATUS_FREE,
        self::STATUS_UNKNOWN,
    ];

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
     * Free/busy status
     * @var string
     */
    private $status;

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
     * @return string
     */
    public function getCalendarId()
    {
        return $this->calendarId;
    }

    /**
     * @return DateTime
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * @return DateTime
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isBusy()
    {
        return ($this->status === self::STATUS_BUSY);
    }

    /**
     * @return bool
     */
    public function isFree()
    {
        return ($this->status === self::STATUS_FREE);
    }

    /**
     * @param array $data
     */
    public function populateFromArray(array $data)
    {
        if (empty($data['calendar_id'])) {
            throw new InvalidArgumentException('Invalid calendar_id');
        }

        $this->calendarId = $data['calendar_id'];
        $this->startsAt   = DateUtil::createDateTime($data['start']);
        $this->endsAt     = DateUtil::createDateTime($data['end']);
        $this->status     = self::normalizeStatus($data['free_busy_status']);
    }

    /**
     * @param string $status
     *
     * @return string
     */
    private static function normalizeStatus($status)
    {
        return self::isValidStatus($status) ? $status : self::STATUS_UNKNOWN;
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    private static function isValidStatus($status)
    {
        return in_array($status, self::$statuses);
    }
}
