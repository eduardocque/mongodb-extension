<?php

namespace mongodb\tests\unit;

use Codeception\Specify;
use Codeception\Test\Unit;
use mongodb\tests\helpers\brokenModels\CollectionWithoutInterface;
use mongodb\tests\helpers\FixtureConstants;
use mongodb\tests\helpers\FixtureHelper;
use mongodb\tests\helpers\models\CustomCollection;
use mongodb\Validator;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class ValidatorTest
 * @package mongodb\tests\unit
 */
class ValidatorTest extends Unit
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
    public function testFunctionality()
    {
        $this->specify('Functionality 1', function () {
            $validator = new Validator();
            $validator->fullPath = false;
            $model = new CustomCollection();

            $validator->validateAttribute($model, 'sub_document_1');
            $validator->validateAttribute($model, 'sub_document_2');
            $this->assertEquals([
                'simple_attribute_string' => ['Simple String cannot be blank.', 'Simple String cannot be blank.'],
                'simple_attribute_number' => ['Simple Number cannot be blank.', 'Simple Number cannot be blank.'],
                'simple_attribute_boolean' => ['Simple Boolean cannot be blank.', 'Simple Boolean cannot be blank.'],
                'simple_attribute_date' => ['Simple Date cannot be blank.', 'Simple Date cannot be blank.'],
                'simple_attribute_array' => ['Simple Array cannot be blank.', 'Simple Array cannot be blank.'],
                'sub_document_3.simple_attribute_string' => ['Simple String cannot be blank.', 'Simple String cannot be blank.'],
                'sub_document_3.simple_attribute_number' => ['Simple Number cannot be blank.', 'Simple Number cannot be blank.'],
                'sub_document_3.simple_attribute_boolean' => ['Simple Boolean cannot be blank.', 'Simple Boolean cannot be blank.'],
                'sub_document_3.simple_attribute_date' => ['Simple Date cannot be blank.', 'Simple Date cannot be blank.'],
                'sub_document_3.simple_attribute_array' => ['Simple Array cannot be blank.', 'Simple Array cannot be blank.']
            ], $model->subDocument1->errors);
        });

        $this->specify('Functionality 2', function () {
            $validator = new Validator();
            $validator->fullPath = true;
            $model = new CustomCollection();

            $validator->validateAttribute($model, 'sub_document_1');
            $validator->validateAttribute($model, 'sub_document_2');
            $this->assertEquals([
                'simple_attribute_string' => ['Simple String cannot be blank.'],
                'simple_attribute_number' => ['Simple Number cannot be blank.'],
                'simple_attribute_boolean' => ['Simple Boolean cannot be blank.'],
                'simple_attribute_date' => ['Simple Date cannot be blank.'],
                'simple_attribute_array' => ['Simple Array cannot be blank.'],
                'sub_document_3.simple_attribute_string' => ['Simple String cannot be blank.'],
                'sub_document_3.simple_attribute_number' => ['Simple Number cannot be blank.'],
                'sub_document_3.simple_attribute_boolean' => ['Simple Boolean cannot be blank.'],
                'sub_document_3.simple_attribute_date' => ['Simple Date cannot be blank.'],
                'sub_document_3.simple_attribute_array' => ['Simple Array cannot be blank.']
            ], $model->subDocument1->errors);
        });

        $this->specify('Functionality 3', function () {
            $validator = new Validator();
            $validator->fullPath = false;
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

            $validator->validateAttribute($model, 'sub_document_1');
            $validator->validateAttribute($model, 'sub_document_2');
            $this->assertEquals([
                'simple_attribute_string' => ['Simple String cannot be blank.', 'Simple String cannot be blank.'],
                'simple_attribute_number' => ['Simple Number cannot be blank.', 'Simple Number cannot be blank.'],
                'simple_attribute_boolean' => ['Simple Boolean cannot be blank.', 'Simple Boolean cannot be blank.'],
                'simple_attribute_date' => ['Simple Date cannot be blank.', 'Simple Date cannot be blank.'],
                'simple_attribute_array' => ['Simple Array cannot be blank.', 'Simple Array cannot be blank.'],
                'sub_document_3.simple_attribute_string' => ['Simple String cannot be blank.', 'Simple String cannot be blank.'],
                'sub_document_3.simple_attribute_number' => ['Simple Number cannot be blank.', 'Simple Number cannot be blank.'],
                'sub_document_3.simple_attribute_boolean' => ['Simple Boolean cannot be blank.', 'Simple Boolean cannot be blank.'],
                'sub_document_3.simple_attribute_date' => ['Simple Date cannot be blank.', 'Simple Date cannot be blank.'],
                'sub_document_3.simple_attribute_array' => ['Simple Array cannot be blank.', 'Simple Array cannot be blank.']
            ], $model->subDocument1->errors);
        });
    }

    /**
     * Test functionality.
     */
    public function testFunctionalityInvalid1()
    {
        $this->specify('Functionality - invalid', function () {
            $validator = new Validator();

            $this->expectException(InvalidConfigException::class);
            $validator->validateAttribute(new CollectionWithoutInterface(), '');
        });
    }

    /**
     * Test functionality.
     */
    public function testFunctionalityInvalid2()
    {
        $this->specify('Functionality - invalid', function () {
            $validator = new Validator();

            $this->expectException(InvalidConfigException::class);
            $this->expectExceptionMessage('Document object must be an instance or descendant of "' . Model::class . '".');
            $validator->validateAttribute(new CustomCollection(), 'simple_attribute_string');
        });
    }

    /**
     * Test functionality.
     */
    public function testFunctionalityInvalid3()
    {
        $this->specify('Functionality - invalid', function () {
            $validator = new Validator();
            $validator->fullPath = false;
            $model = new CustomCollection();

            $this->expectException(InvalidArgumentException::class);
            $model->setAttribute('a', 1);
        });
    }

    /**
     * Test functionality.
     */
    public function testFunctionalityInvalid4()
    {
        $this->specify('Functionality - invalid', function () {
            $validator = new Validator();
            $validator->fullPath = false;
            $model = new CustomCollection();

            $this->expectException(InvalidConfigException::class);
            $model->setAttribute('sub_document_2', [
                [
                    'a' => 1
                ]
            ]);
        });
    }
}
