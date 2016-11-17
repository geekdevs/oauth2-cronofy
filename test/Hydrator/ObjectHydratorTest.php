<?php
namespace Geekdevs\OAuth2\Client\Test\Hydrator;

use Geekdevs\OAuth2\Client\Hydrator\ObjectHydrator;

/**
 * @group hydrator
 */
class ObjectHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $data
     * @dataProvider dataSet
     */
    public function testHydrate($data)
    {
        $hydrator = new ObjectHydrator(\stdClass::class);
        $result = $hydrator->hydrate($data);

        $this->assertEquals(new \stdClass($data), $result);
    }

    public function testNonExistentClass()
    {
        $this->setExpectedException('RuntimeException');
        new ObjectHydrator('NonExistentClass');
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
}
