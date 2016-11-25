<?php
namespace Geekdevs\OAuth2\Client\Test\Hydrator;

use Geekdevs\OAuth2\Client\Hydrator\ArrayHydrator;

/**
 * @group hydrator
 */
class ArrayHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that array hydration returns array as is
     * @param $data
     * @dataProvider dataSet
     */
    public function testHydrate($data)
    {
        $hydrator = new ArrayHydrator();
        $result = $hydrator->hydrate($data);

        $this->assertSame($data, $result);
    }

    /**
     * Ensure it would fail on anything but array
     * @dataProvider badParams
     */
    public function testNoHydrate($data)
    {
        try {
            $hydrator = new ArrayHydrator();
            $hydrator->hydrate($data);
        } catch (\TypeError $e) {
            //php 7+
            return;
        } catch (\Exception $e) {
            //php < 7
            return;
        }

        $this->fail('Failed - Expected type exception, but nothing is thrown');
    }

    /**
     * @return array
     */
    public function dataSet()
    {
        return
            [
                [
                    [],
                ],
                [
                    ['test'],
                ],
                [
                    [123],
                ],
                [
                    [
                        'hello' => [
                            'dear' => [
                                'world',
                                'globe',
                                'Earth',
                            ],
                        ],
                    ],
                ],
            ];
    }

    /**
     * @return array
     */
    public function badParams()
    {
        return [
            ['hello'],
            [123],
            [12.34],
            [true],
            [new \stdClass(['test'=>'me'])]
        ];
    }
}
