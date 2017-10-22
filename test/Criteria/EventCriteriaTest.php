<?php
namespace Geekdevs\OAuth2\Client\Test\Criteria;

use Geekdevs\OAuth2\Client\Criteria\EventCriteria;
use DateTimeZone;
use DateTime;

/**
 * @group criteria
 */
class EventCriteriaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests constructor with params
     */
    public function testInit()
    {
        $params = [
            'timezone' => new DateTimeZone('Europe/Minsk'),
            'fromDate' => new DateTime('2016-05-06T10:20:00'),
            'toDate' => new DateTime('2016-11-12T10:20:00'),
            'calendars' => ['cal_one', 'cal_two', 'cal_three'],
        ];

        $criteria = new EventCriteria($params);
        $this->assertSame($params['timezone'], $criteria->getTimezone());
        $this->assertSame($params['fromDate'], $criteria->getFromDate());
        $this->assertSame($params['toDate'], $criteria->getToDate());
        $this->assertSame($params['calendars'], $criteria->getCalendars());
    }

    /**
     * Test constructor default values
     */
    public function testInitDefaults()
    {
        $utc = new DateTimeZone('Z');
        $now = new DateTime('now', $utc);
        $then = new DateTime('+201 days', $utc);

        $criteria = new EventCriteria();
        $this->assertEquals($utc, $criteria->getTimezone());
        $this->assertEquals($now, $criteria->getFromDate(), '', 1);
        $this->assertEquals($then, $criteria->getToDate(), '', 1);
        $this->assertSame(null, $criteria->getCalendars());
    }

    /**
     * Test getting criteria raw data
     */
    public function testToRaw()
    {
        $timezone = new DateTimeZone('Europe/Minsk');
        $fromDate = new DateTime('2016-05-06T10:20:00');
        $toDate = new DateTime('2016-11-12T10:20:00');
        $calendars = ['cal_one', 'cal_two', 'cal_three'];

        $criteria = new EventCriteria();
        $criteria->setTimezone($timezone);
        $criteria->setFromDate($fromDate);
        $criteria->setToDate($toDate);
        $criteria->setCalendars($calendars);

        $this->assertSame([
            'tzid'         => 'Europe/Minsk',
            'from'         => '2016-05-06',
            'to'           => '2016-11-12',
            'calendar_ids' => ['cal_one', 'cal_two', 'cal_three'],
        ], $criteria->toRaw());
    }
}
