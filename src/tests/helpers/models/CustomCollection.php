<?php

namespace mongodb\tests\helpers\models;

use MongoDB\BSON\ObjectId;
use mongodb\mongodb\ActiveRecord;
use MongoDB\BSON\UTCDateTime;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument1;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument2;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument4;
use mongodb\tests\helpers\models\customCollection\CustomCollectionSubDocument5;
use Yii;

/**
 * Class CustomCollection
 * @package mongodb\tests\helpers\models
 *
 * @property ObjectID|string $_id
 * @property string $simple_attribute_string
 * @property integer simple_attribute_number
 * @property UTCDateTime $simple_attribute_date
 * @property array $simple_attribute_array
 * @property boolean $simple_attribute_boolean
 *
 * here are sub documents
 *
 * @property CustomCollectionSubDocument1 $subDocument1
 * @property CustomCollectionSubDocument2 $subDocument2
 * @property CustomCollectionSubDocument4 $subDocument4
 * @property CustomCollectionSubDocument5 $subDocument5
 *
 */
class CustomCollection extends ActiveRecord
{
    /**
     * @return string
     */
    public static function collectionName()
    {
        return 'custom_collection';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['simple_attribute_string', 'simple_attribute_number', 'simple_attribute_date', 'simple_attribute_array', 'simple_attribute_boolean'], 'required'],
            [['sub_document_1', 'sub_document_2', 'sub_document_4', 'sub_document_5'], 'mongodb\Validator', 'fullPath' => true],
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            '_id',
            'simple_attribute_string',
            'simple_attribute_number',
            'simple_attribute_date',
            'simple_attribute_array',
            'simple_attribute_boolean',

            'simpleCamelCase',

            'sub_document_1',
            'sub_document_2',
            'sub_document_4',
            'sub_document_5',
        ];
    }


    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'simple_attribute_string' => Yii::t('app', 'Simple String'),
            'simple_attribute_number' => Yii::t('app', 'Simple Number'),
            'simple_attribute_date' => Yii::t('app', 'Simple Date'),
            'simple_attribute_array' => Yii::t('app', 'Simple Array'),
            'simple_attribute_boolean' => Yii::t('app', 'Simple Boolean'),

            'simpleCamelCase' => Yii::t('app', 'Simple Camel Case'),

            'sub_document_1' => Yii::t('app', 'Sub Document 1'),
            'sub_document_2' => Yii::t('app', 'Sub Document 2'),
            'sub_document_4' => Yii::t('app', 'Sub Document 4'),
            'sub_document_5' => Yii::t('app', 'Sub Document 5'),
        ];
    }

    /**
     * @return ActiveRecord|object
     * @throws yii\base\InvalidConfigException
     */
    public function embedSubDocument1()
    {
        return $this->mapDocument([], CustomCollectionSubDocument1::class, 'sub_document_1');
    }

    /**
     * @return ActiveRecord[]
     * @throws yii\base\InvalidConfigException
     */
    public function embedSubDocument2()
    {
        return $this->mapDocumentList([], CustomCollectionSubDocument2::class, 'sub_document_2');
    }

    /**
     * @return ActiveRecord[]
     * @throws yii\base\InvalidConfigException
     */
    public function embedSubDocument4()
    {
        return $this->mapDocumentList(
            [
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
            CustomCollectionSubDocument4::class,
            'sub_document_4'
        );
    }

    /**
     * @return ActiveRecord|object
     * @throws yii\base\InvalidConfigException
     */
    public function embedSubDocument5()
    {
        return $this->mapDocument(
            [
                'simple_attribute_string' => 'Init',
                'simple_attribute_number' => 0,
                'simple_attribute_boolean' => false,
                'simple_attribute_date' => '2020-01-01',
                'simple_attribute_array' => ['a']
            ],
            CustomCollectionSubDocument5::class,
            'sub_document_5'
        );
    }
}
