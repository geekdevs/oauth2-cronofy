<?php
namespace Geekdevs\OAuth2\Client\Criteria;

use DateTime;
use DateTimeZone;

/**
 * Class EventCriteria
 * @package Geekdevs\OAuth2\Client\Criteria
 */
class EventCriteria implements CriteriaInterface
{
    /**
     * @var \DateTimeZone
     */
    protected $timezone;

    /**
     * @var \DateTime
     */
    protected $fromDate;

    /**
     * @var \DateTime
     */
    protected $toDate;

    /**
     * @var bool
     */
    protected $includeManaged = false;

    /**
     * Calendar IDs
     * @var string[] | null
     */
    protected $calendars = null;

    /**
     * @param array $params
     *   DateTimeZone $params['timezone']
     *   DateTime     $params['fromDate']
     *   DateTime     $params['toDate']
     *   string[]     $params['calendars']
     */
    public function __construct(array $params = [])
    {
        //timezone
        if (isset($params['timezone'])) {
            $timezone = $params['timezone'];
        } else {
            $timezone = new DateTimeZone('UTC');
        }
        $this->setTimezone($timezone);

        //fromDate
        if (isset($params['fromDate'])) {
            $fromDate = $params['fromDate'];
        } else {
            $fromDate = new DateTime('now', $timezone);
        }
        $this->setFromDate($fromDate);

        //toDate
        if (isset($params['toDate'])) {
            $toDate = $params['toDate'];
        } else {
            $toDate = new DateTime('+201days', $timezone); //cronofy default
        }
        $this->setToDate($toDate);

        //calendars
        if (isset($params['calendars'])) {
            $this->setCalendars($params['calendars']);
        }

        //Include managed
        if (isset($params['includeManaged'])) {
            $this->setIncludeManaged((bool) $params['includeManaged']);
        }
    }

    /**
     * @return bool
     */
    public function isIncludeManaged()
    {
        return $this->includeManaged;
    }

    /**
     * @param bool $includeManaged
     */
    public function setIncludeManaged($includeManaged)
    {
        $this->includeManaged = $includeManaged;
    }

    /**
     * @param array $calendars
     */
    public function setCalendars(array $calendars)
    {
        $this->calendars = $calendars;
    }

    /**
     * @return null|string[]
     */
    public function getCalendars()
    {
        return $this->calendars;
    }

    /**
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param DateTimeZone $timezone
     */
    public function setTimezone(DateTimeZone $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param DateTime $fromDate
     */
    public function setFromDate(DateTime $fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * @param DateTime $toDate
     */
    public function setToDate(DateTime $toDate)
    {
        $this->toDate = $toDate;
    }

    /**
     * @return array
     */
    public function toRaw()
    {
        $fromDate = clone $this->fromDate;
        $fromDate->setTimezone($this->timezone);

        $toDate = clone $this->toDate;
        $toDate->setTimezone($this->timezone);

        $requestParams = [
            'tzid'            => $this->timezone->getName(),
            'from'            => $fromDate->format('Y-m-d'),
            'to'              => $toDate->format('Y-m-d'),
        ];

        if ($this->includeManaged) {
            $requestParams['include_managed'] = true;
        }

        if ($this->calendars !== null) {
            $requestParams['calendar_ids'] = $this->calendars;
        }

        return $requestParams;
    }
}
