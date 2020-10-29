<?php

namespace mongodb\tests\unit;

use Codeception\Specify;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use Codeception\Test\Unit;
use mongodb\tests\helpers\models\CustomCollection;
use mongodb\tests\helpers\FixtureConstants;
use mongodb\tests\helpers\FixtureHelper;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument1;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument2;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument3;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument4;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument5;
use mongodb\Validator;
use yii\validators\RequiredValidator;

/**
 * Class FunctionalityTest
 * @package common\extensions\mongodb\tests\unit
 */
class FunctionalityTest extends Unit
{
    use Specify;

    /**
     * @return array
     */
    public function _fixtures()
    {
        print "fixtures";

        return FixtureHelper::fixtureLoader([
            FixtureConstants::CUSTOM_COLLECTION
        ]);
    }

    /**
     * Test functionality.
     */
    public function testSimpleAssignments()
    {
        $this->specify('Simple Assignments 1', function () {
            $model = new CustomCollection();
            $model->simple_attribute_string = 'Test';
            $date = new UTCDateTime();
            $model->simple_attribute_date = $date;
            $model->simple_attribute_number = 123;
            $model->simple_attribute_array = ['a', 'b'];

            $this->assertEquals('Test', $model->simple_attribute_string);
            $this->assertEquals($date, $model->simple_attribute_date);
            $this->assertEquals(123, $model->simple_attribute_number);
            $this->assertEquals(['a', 'b'], $model->simple_attribute_array);
            $this->assertEquals([
                'sub_document_1' => [
                    'sub_document_3' => [],
                ],
                'simple_attribute_string' => 'Test',
                'simple_attribute_date' => $date,
                'simple_attribute_number' => 123,
                'simple_attribute_array' => ['a', 'b'],
                'sub_document_2' => [],
                'sub_document_4' => [
                    [
                        'simple_attribute_string' => 'Init',
                        'simple_attribute_number' => 0,
                        'simple_attribute_boolean' => false,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a']
                    ],
                    [
                        'simple_attribute_string' => 'Init',
                        'simple_attribute_number' => 0,
                        'simple_attribute_boolean' => false,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a']
                    ]
                ],
                'sub_document_5' => [
                    'simple_attribute_string' => 'Init',
                    'simple_attribute_number' => 0,
                    'simple_attribute_boolean' => false,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a']
                ]
            ], $model->toArray());
        });

        $this->specify('Simple Assignments 2', function () {
            $model = new CustomCollection();
            $date = new UTCDateTime();
            $model->setAttributes([
                'simple_attribute_string' => 'Test',
                'simple_attribute_date' => $date,
                'simple_attribute_number' => 123
            ]);

            $this->assertEquals('Test', $model->simple_attribute_string);
            $this->assertEquals($date, $model->simple_attribute_date);
            $this->assertEquals(123, $model->simple_attribute_number);
        });

        $this->specify('Simple Assignments 3', function () {
            $model = new CustomCollection();
            $model->setAttribute('simple_attribute_string', 'Test');
            $model->setAttribute('simple_attribute_number', 123);

            $this->assertEquals('Test', $model->simple_attribute_string);
            $this->assertEquals(123, $model->simple_attribute_number);

            $model = new CustomCollection();
            $model->setAttributes([
                'simple_attribute_string' => 'Init',
                'simple_attribute_number' => 0,
                'simple_attribute_boolean' => false,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b' => 'c']]);

            $this->assertEquals([
                'simple_attribute_string' => 'Init',
                'simple_attribute_number' => 0,
                'simple_attribute_boolean' => false,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b' => 'c'],
                'sub_document_1' => [
                    'sub_document_3' => []
                ],
                'sub_document_2' => [],
                'sub_document_4' => [
                    [
                        'simple_attribute_string' => 'Init',
                        'simple_attribute_number' => 0,
                        'simple_attribute_boolean' => false,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a']
                    ],
                    [
                        'simple_attribute_string' => 'Init',
                        'simple_attribute_number' => 0,
                        'simple_attribute_boolean' => false,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a']
                    ]
                ],
                'sub_document_5' => [
                    'simple_attribute_string' => 'Init',
                    'simple_attribute_number' => 0,
                    'simple_attribute_boolean' => false,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a']
                ]
            ], $model->toArray());
        });
    }

    public function testSubDocuments()
    {
        $this->specify('sub-documents Assignments 1', function () {
            $model = new CustomCollection();
            $model->setAttributes([
                'sub_document_1.simple_attribute_string' => 'Test',
                'sub_document_1.simple_attribute_number' => 123,
            ]);

            $this->assertInstanceOf(CustomCollectionSubDocument1::class, $model->subDocument1);
            $this->assertEquals([
                'sub_document_3' => [],
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123
            ], $model->subDocument1->toArray());

            $model = new CustomCollection();
            $model->subDocument1->simple_attribute_string = 'Test';
            $this->assertInstanceOf(CustomCollectionSubDocument1::class, $model->subDocument1);
            $this->assertEquals('Test', $model->subDocument1->simple_attribute_string);
        });

        $this->specify('sub-documents Assignments 2', function () {
            $model = new CustomCollection();
            $model->setAttributes([
                'sub_document_1.sub_document_3.simple_attribute_string' => 'Test',
                'sub_document_1.sub_document_3.simple_attribute_number' => 123
            ]);

            $this->assertInstanceOf(CustomCollectionSubDocument1::class, $model->subDocument1);
            $this->assertInstanceOf(CustomCollectionSubDocument3::class, $model->subDocument1->subDocument3);

            $this->assertEquals([
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123
            ], $model->subDocument1->subDocument3->toArray());

            $model = new CustomCollection();
            $model->subDocument1->subDocument3->simple_attribute_string = 'Test';

            $this->assertEquals('Test', $model->subDocument1->subDocument3->simple_attribute_string);
        });

        $this->specify('sub-documents Assignments 3', function () {
            $model = new CustomCollection();
            $model->setAttributes([
                'sub_document_1.simple_attribute_string' => 'Test',
                'sub_document_1.simple_attribute_number' => 123,
                'sub_document_1.sub_document_3.simple_attribute_string' => 'Test',
                'simple_attribute_array' => ['a', ['b', 'c']]
            ]);

            $this->assertEquals('Test', $model->subDocument1->simple_attribute_string);
            $this->assertEquals(123, $model->subDocument1->simple_attribute_number);
            $this->assertEquals([
                'simple_attribute_string' => 'Test',
                'simple_attribute_date' => null,
                'simple_attribute_array' => null,
                'simple_attribute_boolean' => null,
                'simple_attribute_number' => null
            ], $model->subDocument1->subDocument3->attributes);
        });

        $this->specify('sub-documents Assignments 4', function () {
            $model = new CustomCollection();
            $model->setAttribute('sub_document_1.simple_attribute_string', 'Test');
            $model->setAttribute('sub_document_1.sub_document_3.simple_attribute_string', 'Test');

            $this->assertEquals('Test', $model->subDocument1->simple_attribute_string);
            $this->assertEquals('Test', $model->subDocument1->subDocument3->simple_attribute_string);
        });

        $this->specify('sub-documents Assignments 5', function () {
            $model = new CustomCollection();
            $model->setAttribute('sub_document_1.simple_attribute_string', 'Test');
            $model->setAttribute('sub_document_2', [
                [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_number' => 123,
                    'simple_attribute_boolean' => true,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                    'sub_document_3.simple_attribute_string' => 'Test',
                    'sub_document_3.simple_attribute_number' => 123,
                    'sub_document_3.simple_attribute_boolean' => true,
                    'sub_document_3.simple_attribute_date' => '2020-01-01',
                    'sub_document_3.simple_attribute_array' => ['a', 'b'],
                ],
                [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_number' => 123,
                    'simple_attribute_boolean' => true,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                    'sub_document_3' => [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                    ]
                ]
            ]);

            $this->assertEquals(
                [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_number' => 123,
                    'simple_attribute_boolean' => true,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                    'sub_document_3' => [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                    ]
                ],
                $model->subDocument2[0]->toArray()
            );
            $this->assertInstanceOf(CustomCollectionSubDocument2::class, $model->subDocument2[0]);

            $this->assertEquals(
                [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_number' => 123,
                    'simple_attribute_boolean' => true,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                    'sub_document_3' => [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                    ]
                ],
                $model->subDocument2[1]->toArray()
            );
            $this->assertInstanceOf(CustomCollectionSubDocument2::class, $model->subDocument2[1]);

            $model = new CustomCollection();
            $model->setAttribute('sub_document_5', ['simple_attribute_string' => 'Test2']);
            $this->assertEquals('Test2', $model->subDocument5->simple_attribute_string);
        });

        $this->specify('sub-documents Assignments 6', function () {
            $model = new CustomCollection();
            $model->subDocument1 = [
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
            ];

            $this->assertEquals('Test', $model->subDocument1->simple_attribute_string);
            $this->assertEquals([
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
                'sub_document_3' => []
            ], $model->subDocument1->toArray());
        });

        $this->specify('sub-documents Assignments 7', function () {
            $model = new CustomCollection();

            $model->setAttribute('sub_document_2', [
                [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_boolean' => true,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                    'sub_document_3.simple_attribute_string' => 'Test',
                    'sub_document_3.simple_attribute_number' => 123,
                    'sub_document_3.simple_attribute_boolean' => true,
                    'sub_document_3.simple_attribute_date' => '2020-01-01',
                    'sub_document_3.simple_attribute_array' => ['a', 'b'],
                ],
                [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_number' => 123,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                    'sub_document_3' => [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                    ]
                ]
            ]);

            $model->subDocument1 = [
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
            ];

            $this->assertEquals([
                'sub_document_3' => [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_number' => 123,
                    'simple_attribute_boolean' => true,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                ],
                'simple_attribute_string' => 'Test',
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b']
            ], $model->subDocument2[0]->toArray());
            $this->assertEquals([
                'sub_document_3' => [
                    'simple_attribute_string' => 'Test',
                    'simple_attribute_number' => 123,
                    'simple_attribute_boolean' => true,
                    'simple_attribute_date' => '2020-01-01',
                    'simple_attribute_array' => ['a', 'b'],
                ],
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b']
            ], $model->subDocument2[1]->toArray());
        });
    }

    public function testSave()
    {
        $this->specify('Functionality', function () {
            $model = new CustomCollection();
            $model->setAttributes([
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
                'sub_document_1.simple_attribute_string' => 'Test',
                'sub_document_1.simple_attribute_number' => 123,
                'sub_document_1.simple_attribute_boolean' => true,
                'sub_document_1.simple_attribute_date' => '2020-01-01',
                'sub_document_1.simple_attribute_array' => ['a', 'b'],
                'sub_document_1.sub_document_3.simple_attribute_string' => 'Test',
                'sub_document_1.sub_document_3.simple_attribute_number' => 123,
                'sub_document_1.sub_document_3.simple_attribute_boolean' => true,
                'sub_document_1.sub_document_3.simple_attribute_date' => '2020-01-01',
                'sub_document_1.sub_document_3.simple_attribute_array' => ['a', 'b'],
                'sub_document_2' => [
                    [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                        'sub_document_3.simple_attribute_string' => 'Test',
                        'sub_document_3.simple_attribute_number' => 123,
                        'sub_document_3.simple_attribute_boolean' => true,
                        'sub_document_3.simple_attribute_date' => '2020-01-01',
                        'sub_document_3.simple_attribute_array' => ['a', 'b'],
                    ],
                    [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                        'sub_document_3' => [
                            'simple_attribute_string' => 'Test',
                            'simple_attribute_number' => 123,
                            'simple_attribute_boolean' => true,
                            'simple_attribute_date' => '2020-01-01',
                            'simple_attribute_array' => ['a', 'b'],
                        ]
                    ]
                ]
            ]);

            $this->assertTrue($model->save());
            $model2 = CustomCollection::find()->where(['_id' => $model->_id])->one();
            $this->assertNotNull($model2);
        });

        $this->specify('Functionality - fail', function () {
            $model = new CustomCollection();
            $model->setAttributes([
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'sub_document_4' => [
                    [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                    ]
                ]
            ]);

            $this->assertFalse($model->save());
            $this->assertEquals([
                'simple_attribute_array' => ['Simple Array cannot be blank.'],
                'sub_document_1.simple_attribute_string' => ['Simple String cannot be blank.'],
                'sub_document_1.simple_attribute_number' => ['Simple Number cannot be blank.'],
                'sub_document_1.simple_attribute_boolean' => ['Simple Boolean cannot be blank.'],
                'sub_document_1.simple_attribute_date' => ['Simple Date cannot be blank.'],
                'sub_document_1.simple_attribute_array' => ['Simple Array cannot be blank.'],
                'sub_document_1.sub_document_3.simple_attribute_string' => ['Simple String cannot be blank.'],
                'sub_document_1.sub_document_3.simple_attribute_number' => ['Simple Number cannot be blank.'],
                'sub_document_1.sub_document_3.simple_attribute_boolean' => ['Simple Boolean cannot be blank.'],
                'sub_document_1.sub_document_3.simple_attribute_date' => ['Simple Date cannot be blank.'],
                'sub_document_1.sub_document_3.simple_attribute_array' => ['Simple Array cannot be blank.'],
                'sub_document_4.0.simple_attribute_array' => ['Simple Array cannot be blank.']
            ], $model->errors);
            $model2 = CustomCollection::find()->where(['_id' => $model->_id])->one();
            $this->assertNull($model2);
        });
    }

    public function testFind()
    {
        $this->specify('Functionality', function () {
            $model = CustomCollection::findOne(['_id' => '5e3ef36503d81c00071ea4d4']);

            $this->assertNotNull($model);
            $this->assertEquals([
                '_id' => new ObjectId('5e3ef36503d81c00071ea4d4'),
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
                'simpleCamelCase' => null,
                'sub_document_1' => $model->subDocument1,
                'sub_document_2' => $model->subDocument2,
                'sub_document_4' => $model->subDocument4,
                'sub_document_5' => $model->subDocument5
            ], $model->attributes);
            $this->assertInstanceOf(CustomCollectionSubDocument1::class, $model->subDocument1);
            $this->assertInstanceOf(CustomCollectionSubDocument2::class, $model->subDocument2[0]);
            $this->assertInstanceOf(CustomCollectionSubDocument2::class, $model->subDocument2[1]);
            $this->assertInstanceOf(CustomCollectionSubDocument4::class, $model->subDocument4[0]);
            $this->assertInstanceOf(CustomCollectionSubDocument4::class, $model->subDocument4[1]);
            $this->assertInstanceOf(CustomCollectionSubDocument5::class, $model->subDocument5);

            $this->assertEquals([
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
                'sub_document_3' => $model->subDocument1->subDocument3,
            ], $model->subDocument1->attributes);

            $this->assertInstanceOf(CustomCollectionSubDocument3::class, $model->subDocument1->subDocument3);

            $this->assertEquals([
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
            ], $model->subDocument1->subDocument3->attributes);
        });
    }

    public function testIsset()
    {
        $this->specify('Functionality', function () {
            $model = CustomCollection::findOne(['_id' => '5e3ef36503d81c00071ea4d4']);

            $this->assertTrue(isset($model->simple_attribute_number));
            $this->assertTrue(isset($model->subDocument1));
            $this->assertTrue(isset($model->subDocument1->simple_attribute_number));
            $this->assertTrue(isset($model->subDocument1->subDocument3->simple_attribute_number));
            $this->assertTrue(isset($model->subDocument2));
            $this->assertTrue(isset($model->subDocument2[0]->simple_attribute_number));
            $this->assertTrue(isset($model->{'sub_document_1.simple_attribute_number'}));
        });
    }

    public function testUnsset()
    {
        $this->specify('Functionality', function () {
            $model = CustomCollection::findOne(['_id' => '5e3ef36503d81c00071ea4d4']);

            $this->assertTrue(isset($model->simple_attribute_number));
            $this->assertTrue(isset($model->subDocument1));
            $this->assertTrue(isset($model->subDocument1->simple_attribute_number));
            $this->assertTrue(isset($model->subDocument1->subDocument3->simple_attribute_number));
            $this->assertTrue(isset($model->subDocument2));
            $this->assertTrue(isset($model->subDocument2[0]->simple_attribute_number));
            $this->assertTrue(isset($model->{'sub_document_1.simple_attribute_number'}));

            unset($model->simple_attribute_number);
            $this->assertFalse(isset($model->simple_attribute_number));

            unset($model->subDocument1->subDocument3->simple_attribute_number);
            $this->assertFalse(isset($model->subDocument1->subDocument3->simple_attribute_number));

            unset($model->subDocument1->simple_attribute_number);
            $this->assertFalse(isset($model->subDocument1->simple_attribute_number));

            unset($model->subDocument1);
            $this->assertFalse(isset($model->subDocument1));

            $subModel = $model->subDocument2[0];
            unset($subModel->simple_attribute_number);
            $this->assertFalse(isset($model->subDocument2[0]->simple_attribute_number));

            unset($model->subDocument2);
            $this->assertFalse(isset($model->subDocument2));
        });
    }

    public function testValidators()
    {
        $this->specify('Validators 1', function () {
            $model = new CustomCollection();

            $validator1 = new RequiredValidator();
            $validator1->attributes = [
                'simple_attribute_string',
                'simple_attribute_number',
                'simple_attribute_date',
                'simple_attribute_array',
                'simple_attribute_boolean'
            ];

            $validator2 = new Validator();
            $validator2->attributes = [
                'sub_document_1',
                'sub_document_2',
                'sub_document_4',
                'sub_document_5'
            ];
            $validator2->fullPath = true;
            $this->assertEquals([$validator1, $validator2], $model->getActiveValidators());
            $this->assertEquals([$validator1], $model->getActiveValidators('simple_attribute_string'));
            $this->assertEquals([$validator2], $model->getActiveValidators('sub_document_1'));
            $this->assertEquals([$validator1], $model->getActiveValidators('sub_document_1.simple_attribute_string'));
        });
    }

    public function testIsNewRecord()
    {
        $this->specify('Functionality', function () {
            $model = new CustomCollection();
            $this->assertEquals(true, $model->isNewRecord);

            $model = new CustomCollection();
            $model->setAttributes([
                'simple_attribute_string' => 'Test',
                'simple_attribute_number' => 123,
                'simple_attribute_boolean' => true,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a', 'b'],
                'sub_document_1.simple_attribute_string' => 'Test',
                'sub_document_1.simple_attribute_number' => 123,
                'sub_document_1.simple_attribute_boolean' => true,
                'sub_document_1.simple_attribute_date' => '2020-01-01',
                'sub_document_1.simple_attribute_array' => ['a', 'b'],
                'sub_document_1.sub_document_3.simple_attribute_string' => 'Test',
                'sub_document_1.sub_document_3.simple_attribute_number' => 123,
                'sub_document_1.sub_document_3.simple_attribute_boolean' => true,
                'sub_document_1.sub_document_3.simple_attribute_date' => '2020-01-01',
                'sub_document_1.sub_document_3.simple_attribute_array' => ['a', 'b'],
                'sub_document_2' => [
                    [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                        'sub_document_3.simple_attribute_string' => 'Test',
                        'sub_document_3.simple_attribute_number' => 123,
                        'sub_document_3.simple_attribute_boolean' => true,
                        'sub_document_3.simple_attribute_date' => '2020-01-01',
                        'sub_document_3.simple_attribute_array' => ['a', 'b'],
                    ],
                    [
                        'simple_attribute_string' => 'Test',
                        'simple_attribute_number' => 123,
                        'simple_attribute_boolean' => true,
                        'simple_attribute_date' => '2020-01-01',
                        'simple_attribute_array' => ['a', 'b'],
                        'sub_document_3' => [
                            'simple_attribute_string' => 'Test',
                            'simple_attribute_number' => 123,
                            'simple_attribute_boolean' => true,
                            'simple_attribute_date' => '2020-01-01',
                            'simple_attribute_array' => ['a', 'b'],
                        ]
                    ]
                ]
            ]);
            $this->assertEquals(true, $model->save());
            $this->assertEquals(false, $model->isNewRecord);
            $this->assertEquals(false, $model->subDocument1->isNewRecord);
        });
    }

    public function testGetLabel()
    {
        $this->specify('Functionality', function () {
            $model = new CustomCollection();

            $this->assertEquals('Simple String', $model->getAttributeLabel('simple_attribute_string'));
            $this->assertEquals('Simple String', $model->getAttributeLabel('sub_document_1.simple_attribute_string'));
            $this->assertEquals('Simple Boolean', $model->getAttributeLabel('sub_document_1.sub_document_3.simple_attribute_boolean'));
            $this->assertEquals('Simple String', $model->getAttributeLabel('sub_document_2[0].simple_attribute_string'));

            $this->assertEquals('Simple String', $model->subDocument1->getAttributeLabel('simple_attribute_string'));
            $this->assertEquals('Simple Boolean', $model->subDocument1->getAttributeLabel('sub_document_3.simple_attribute_boolean'));
        });
    }
}
