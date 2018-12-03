<?php
namespace Geekdevs\OAuth2\Client\Test\Util;
use Geekdevs\OAuth2\Client\Util\DateUtil;

/**
 * @group utils
 */
class DateUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDateTime()
    {
        $this->assertEquals(
            new \DateTime('2016-11-09T05:30:22Z'),
            DateUtil::createDateTime('2016-11-09T05:30:22Z')
        );

        $this->assertEquals(
            new \DateTime('2016-11-09', new \DateTimeZone('UTC')),
            DateUtil::createDateTime('2016-11-09T00:00:00Z')
        );

        //time stored in UTC, but presented in Europe/Minsk timezone
        $this->assertEquals(
            new \DateTime('2016-11-09 17:30:25', new \DateTimeZone('Europe/Minsk')), //+3 hours from UTC
            DateUtil::createDateTime([
                'time' => '2016-11-09T14:30:25Z',
                'tzid' => 'Europe/Minsk',
            ])
        );

        $this->assertEquals(
            new \DateTime('2016-11-09 00:00:00', new \DateTimeZone('Europe/Minsk')),
            DateUtil::createDateTime([
                'time' => '2016-11-09',
                'tzid' => 'Europe/Minsk',
            ])
        );
    }

    /**
     * @dataProvider badParams
     */
    public function testInvalidTime($params)
    {
        $this->setExpectedException('InvalidArgumentException');
        DateUtil::createDateTime($params);
    }

    /**
     * @return array
     */
    public function badParams()
    {
        return [
            [
                array(),
                array('time'=>'', 'tzid'=>''),
                array('time'=>'2016-11-09T14:30:25Z', 'tzid'=>''),
                array('time'=>'2016-11-09T14:30:25Z', 'tzid'=>'qwe'),
                array('time'=>'', 'tzid'=>'Europe/Minsk'),
                array('time'=>'2016', 'tzid'=>'Europe/Minsk'),
            ]
        ];
    }
}
