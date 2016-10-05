<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */
namespace components\jsonapi\tests;

use components\jsonapi\tests\data\ResourceModel;
use tuyakhov\jsonapi\Serializer;

class SerializerTest extends TestCase
{
    public function testSerializeModel()
    {
        $serializer = new Serializer();
        $model = new ResourceModel();
        $result = $serializer->serializeModel($model);
        $this->assertArrayHasKey('data', $result);
        $serializedModel = [
            'type' => 'resource-models',
            'attributes' => [
                'testAttribute' => 'testAttribute'
            ],
            'relationships' => [
                'testRelation' => [
                    'data' => [
                        'type' => 'resource-models',
                    ]
                ]
            ]
        ];
        $this->assertArraySubset($serializedModel, $result['data']);
    }
}
