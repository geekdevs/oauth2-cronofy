<?php
namespace Geekdevs\OAuth2\Client\Test\Criteria;
use Geekdevs\OAuth2\Client\Criteria\CalendarCriteria;

/**
 * @group criteria
 */
class CalendarCriteriaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toRaw
     */
    public function testToRaw()
    {
        $criteria = new CalendarCriteria([]);

        $this->assertSame([], $criteria->toRaw());
    }
}
