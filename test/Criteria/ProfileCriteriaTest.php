<?php
namespace Geekdevs\OAuth2\Client\Test\Criteria;
use Geekdevs\OAuth2\Client\Criteria\ProfileCriteria;

/**
 * @group criteria
 */
class ProfileCriteriaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests toRaw
     */
    public function testToRaw()
    {
        $criteria = new ProfileCriteria([]);

        $this->assertSame([], $criteria->toRaw());
    }
}
