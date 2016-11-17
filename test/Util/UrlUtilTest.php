<?php
namespace Geekdevs\OAuth2\Client\Test\Util;
use Geekdevs\OAuth2\Client\Util\UrlUtil;

/**
 * @group utils
 */
class UrlUtilTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataSet
     * @param $data
     */
    public function testCreateQueryString($data)
    {
        $result = UrlUtil::createQueryString($data);

        $this->assertSame(
            'filter=something&number=4307&reset=1&empty=0&events%5B%5D=test_12312&events%5B%5D=test_32423&events%5B%5D=test_12312&demo%5B%5D=1231&demo%5B%5D=123&demo%5B%5D=works&demo%5Bz%5D=good',
            $result
        );
    }

    /**
     * @return array
     */
    public function dataSet()
    {
        return [
            [
                array(
                    'filter' => 'something',
                    'number' => 4307,
                    'reset' => true,
                    'empty' => false,
                    'events' => [
                        'test_12312',
                        'test_32423',
                        'test_12312',
                    ],
                    'demo' => [
                        5 => '1231',
                        6 => 123,
                        '7' => 'works',
                        'z' => 'good',
                    ]
                ),
            ],

        ];
    }
}
